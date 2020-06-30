<?php  namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Library\Markdown;
use Mail;
use Validator, Input, Redirect ;

class HomeController extends Controller {

	public function __construct()
	{
		parent::__construct();

		$this->data['pageLang'] = 'en';
		if(\Session::get('lang') != '')
		{
			$this->data['pageLang'] = \Session::get('lang');
		}
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */

	public function index(  Request $request  )
	{
		
		if(CNF_FRONT =='false' && $request->segment(1) =='' ) :
			return Redirect::to('dashboard');
		endif;

		if(CNF_MAINTENANCE =='ON') :
			return view('errors.maintenance');
		endif;

		$page = $request->segment(1);
		if($page !='') :
			$content = \DB::table('tb_pages')->where('alias','=',$page)->where('status','=','enable')->get();
			\DB::table('tb_pages')->where('alias','=',$page)->update(array('views'=> \DB::raw('views+1')));



			if(count($content) >=1)
			{

				$row = $content[0];
				$this->data['pageTitle']    = $row->title;
				$this->data['pageImage']    = ($row->image !='' ? $row->image : CNF_HEADERIMAGE) ;
				$this->data['pageNote']     = $row->note;
				$this->data['pageMetakey']  = ($row->metakey !='' ? $row->metakey : CNF_METAKEY) ;
				$this->data['pageMetadesc'] = ($row->metadesc !='' ? $row->metadesc : CNF_METADESC) ;
				$this->data['homepage']     = $row->default;

				$this->data['breadcrumb'] = 'active';

				if($row->access !='')
				{
					$access = json_decode($row->access,true)	;
				} else {
					$access = array();
				}

				// If guest not allowed
				if($row->allow_guest !=1)
				{
					$group_id = \Session::get('gid');
					$isValid =  (isset($access[$group_id]) && $access[$group_id] == 1 ? 1 : 0 );
					if($isValid ==0)
					{
						return Redirect::to('')
							->with('message', \App\Library\SiteHelpers::alert('error',\Lang::get('core.note_restric')));
					}
				}

				if(file_exists(base_path().'/resources/views/layouts/'.CNF_THEME.'/template/'.$row->filename.'.blade.php') && $row->filename !='')
				{
					$page_template = 'layouts.'.CNF_THEME.'.template.'.$row->filename;
				} else {
					$page_template = 'layouts.'.CNF_THEME.'.template.page';
				}

				$this->data['content'] = \App\Library\PostHelpers::formatContent($row->note);
				$this->data['filename'] = $row->filename;

			//	echo '<pre>';print_r($this->data);echo '</pre>'; exit;
				if($row->template =='backend')
				{
					$this->data['pageNote'] = 'View';
					 return view($page_template,$this->data);
				} else {

					$this->data['pages'] = $page_template;
					$page = 'layouts.'.CNF_THEME.'.index';
					return view($page,$this->data);
				}




			} else {
				return Redirect::to('')
					->with('message', \App\Library\SiteHelpers::alert('error',\Lang::get('core.note_noexists')));
			}


		else :

			$sql = \DB::table('tb_pages')->where('default',1)->get();
			if(count($sql)>=1)
			{
				$row = $sql[0];

				$this->data['pageTitle'] 	=  $row->title;
				$this->data['pageImage']    =  ($row->image !='' ? $row->image : CNF_HEADERIMAGE) ;
				$this->data['pageNote'] 	=  $row->note;
				$this->data['breadcrumb'] 	= 'inactive';
				$this->data['pageMetakey'] 	=  $row->metakey ;
				$this->data['pageMetadesc'] =  $row->metadesc ;
				$this->data['filename'] 	=  $row->filename;
				$this->data['homepage'] 	=  $row->default;

				if(file_exists(base_path().'/resources/views/layouts/'.CNF_THEME.'/template/'.$row->filename.'.blade.php') && $row->filename !='')
				{
					$page_template = 'layouts.'.CNF_THEME.'.template.'.$row->filename;
				} else {
					$page_template = 'layouts.'.CNF_THEME.'.template.page';
				}

				$this->data['pages'] = $page_template;
				$this->data['content'] = \App\Library\PostHelpers::formatContent($row->note);
				$page = 'layouts.'.CNF_THEME.'.index';
				return view($page,$this->data);

			} else {

				return ' No Default page set up !';
			}

		endif;

	}

	public function  getLang($lang='en')
	{
		\Session::put('lang', $lang);
		return  Redirect::back();
	}

	public function  getSkin($skin='mmb')
	{
		\Session::put('themes', $skin);
		return  Redirect::back();
	}

	public  function  postContact( Request $request)
	{

		$rules = array(
				'name'		=>'required',
				'subject'	=>'required',
				'message'	=>'required|min:20',
				'sender'	=>'required|email'
		);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->passes())
		{

			$data = array('name'=>$request->input('name'),'sender'=>$request->input('sender'),'subject'=>$request->input('subject'),'notes'=>$request->input('message'));
			$message = view('emails.contact', $data);
			$data['to'] = CNF_EMAIL;
			if(defined('CNF_MAIL') && CNF_MAIL =='swift')
			{
				Mail::send('user.emails.contact', $data, function ($message) use ($data) {
		    		$message->to($data['to'])->subject($data['subject']);
		    	});

			}  else {

				$headers  	= 'MIME-Version: 1.0' . "\r\n";
				$headers 	.= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$headers 	.= 'From: '.$request->input('name').' <'.$request->input('sender').'>' . "\r\n";
					//mail($data['to'],$data['subject'], $message, $headers);
			}




			return Redirect::to($request->input('redirect'))->with('message', \App\Library\SiteHelpers::alert('success','Thank You , Your message has been sent !'));

		} else {
			return Redirect::to($request->input('redirect'))->with('message', \App\Library\SiteHelpers::alert('error','The following errors occurred'))
			->withErrors($validator)->withInput();
		}
	}




	function postProccess( Request $request , $formID )
	{
		//$row = $this->model->find($formID);
		$sql = \DB::table('tb_forms')->where('formID',$formID)->get();
		if(count($sql)<=0)
			return Redirect::back()->with('message', \App\Library\SiteHelpers::alert('error','Form not Found !'));

		$row = $sql[0];
		$configuration = json_decode($row->configuration,true);

		$rules = \FormHelpers::validateForm( $configuration);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->passes()) {
			$data = \FormHelpers::validatePost($request , $configuration);
			if($row->method =='table')
			{

				\DB::table($row->tablename)->insert($data);
				if($row->redirect !='')
				{
					echo '<script> window.location.href= "'.$row->redirect.'" </script>';
				} else {
					return Redirect::back()->with('message', \App\Library\SiteHelpers::alert('success',$row->success));
				}

			} else {
				$message = '';
				foreach($configuration as $conf)
				{
					$message .='
						<b>'.$conf['label'].'</b> : '. $request->input($conf['field']).' <br />
					';
				}

				$data			 = array('email'=>$row->email,'name'=>$row->name);
				$data['message'] = $message;

				$message = view('user.emails.form', $data);


                if(defined('CNF_MAIL') && CNF_MAIL =='swift')
                {
				$data['message'] = $message;
				\Mail::send('user.emails.form', $data, function ($message) use ($row) {
		    		$message->to($row->email)->subject('Submitted Form :  '. $row->name);
		    	});

                } else {
					    	$message = view('user.emails.form',$data);
							$headers  = 'MIME-Version: 1.0' . "\r\n";
							$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
							$headers .= 'From: '.CNF_COMNAME.' <'.CNF_EMAIL.'>' . "\r\n";
								mail($row->email, 'Submitted Form :  '. $row->name, $message, $headers);
					    }
/*
                if ($row->sendcopy==1)
                {
                            $message = view('user.emails.form',$data);
							$headers  = 'MIME-Version: 1.0' . "\r\n";
							$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
							$headers .= 'From: '.CNF_COMNAME.' <'.CNF_EMAIL.'>' . "\r\n";
				            mail($request->input($conf['eposta']), 'Submitted Form :  '. $row->name, $message, $headers);

                }
*/

				if($row->redirect !='')
				{
					echo '<script> window.location.href= "'.$row->redirect.'" </script>';
				} else {
					return Redirect::back()->with('message', \App\Library\SiteHelpers::alert('success',$row->success));
				}


			}


		} else {

			//Redirect::back();
			return Redirect::back()->with('message', \App\Library\SiteHelpers::alert('error','The following errors occurred'))
			->withErrors($validator)->withInput();

		}

	}

}
