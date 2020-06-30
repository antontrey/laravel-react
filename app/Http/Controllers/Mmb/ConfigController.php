<?php namespace App\Http\Controllers\mmb;

use App\Http\Controllers\controller;
use App\Models\Core\Groups;
use App\User;
use Illuminate\Http\Request;
use Validator, Input, Redirect;

class ConfigController extends Controller {

    public function __construct()
    {
    	parent::__construct();
        $this->data = array(
            'pageTitle' =>  \Lang::get('core.t_generalsetting'),
            'pageNote'  =>   \Lang::get('core.t_generalsettingsmall'),
        );

    }

	public function getIndex()
	{
        $this->data = array(
            'pageTitle' =>  \Lang::get('core.t_generalsetting'),
            'pageNote'  =>   \Lang::get('core.t_generalsettingsmall'),

        );
		$this->data['active'] = '';
		return view('mmb.config.index',$this->data);
	}


	static function postSave( Request $request )
	{

		$rules = array(
			'cnf_comname'=>'required|min:2',
			'cnf_email'=>'required|email',
		);
		$validator = Validator::make($request->all(), $rules);
		if (!$validator->fails())
		{
			$logo = '';
			if(!is_null($request->file('logo')))
			{

				$file = $request->file('logo');
			 	$destinationPath = public_path().'/mmb/images/';
				$filename = $file->getClientOriginalName();
				$extension =$file->getClientOriginalExtension();
				$logo = 'logo.'.$extension;
				$uploadSuccess = $file->move($destinationPath, $logo);
			}

			$headerimage = '';
			if(!is_null($request->file('headerimage')))
			{

				$file = $request->file('headerimage');
			 	$destinationPath = public_path().'/uploads/images/';
				$filename = $file->getClientOriginalName();
				$extension =$file->getClientOriginalExtension();
				$headerimage = 'header.'.$extension;
				$uploadSuccess = $file->move($destinationPath, $headerimage);
			}

			$val  =		"<?php \n";
			$val .= 	"define('CNF_APPNAME','".$request->input('cnf_appname')."');\n";
			$val .= 	"define('CNF_APPDESC','".$request->input('cnf_appdesc')."');\n";
			$val .= 	"define('CNF_COMNAME','".$request->input('cnf_comname')."');\n";
			$val .= 	"define('CNF_ADDRESS','".$request->input('cnf_address')."');\n";
			$val .= 	"define('CNF_TEL','".$request->input('cnf_tel')."');\n";
			$val .= 	"define('CNF_EMAIL','".$request->input('cnf_email')."');\n";
			$val .= 	"define('CNF_FACEBOOK','".$request->input('cnf_facebook')."');\n";
			$val .= 	"define('CNF_TWITTER','".$request->input('cnf_twitter')."');\n";
			$val .= 	"define('CNF_INSTAGRAM','".$request->input('cnf_instagram')."');\n";
			$val .= 	"define('CNF_TRIPADVISOR','".$request->input('cnf_tripadvisor')."');\n";
			$val .= 	"define('CNF_TAGLINE','".$request->input('cnf_tagline')."');\n";
			$val .= 	"define('CNF_TEMPCOLOR','".$request->input('cnf_tempcolor')."');\n";
            $val .= 	"define('CNF_METAKEY','".CNF_METAKEY."');\n";
			$val .= 	"define('CNF_METADESC','".CNF_METADESC."');\n";
            $val .= 	"define('CNF_GROUP','".CNF_GROUP."');\n";
			$val .= 	"define('CNF_ACTIVATION','".CNF_ACTIVATION."');\n";
			$val .= 	"define('CNF_MAINTENANCE','".CNF_MAINTENANCE."');\n";
			$val .= 	"define('CNF_SHOWHELP','".CNF_SHOWHELP."');\n";
            $val .= 	"define('CNF_MULTILANG','".CNF_MULTILANG."');\n";
            $val .= 	"define('CNF_LANG','".CNF_LANG."');\n";
            $val .= 	"define('CNF_REGIST','".CNF_REGIST."');\n";
			$val .= 	"define('CNF_FRONT','".CNF_FRONT."');\n";
            $val .= 	"define('CNF_THEME','".CNF_THEME."');\n";
			$val .= 	"define('CNF_RECAPTCHA','".CNF_RECAPTCHA."');\n";
			$val .= 	"define('CNF_RECAPTCHAPUBLICKEY','".CNF_RECAPTCHAPUBLICKEY."');\n";
			$val .= 	"define('CNF_RECAPTCHAPRIVATEKEY','".CNF_RECAPTCHAPRIVATEKEY."');\n";
			$val .= 	"define('CNF_MODE','".CNF_MODE."');\n";
            $val .= 	"define('CNF_LOGO','".($logo !=''  ? $logo : CNF_LOGO )."');\n";
            $val .= 	"define('CNF_HEADERIMAGE','".($headerimage !=''  ? $headerimage : CNF_HEADERIMAGE )."');\n";
			$val .= 	"define('CNF_ALLOWIP','".CNF_ALLOWIP."');\n";
			$val .= 	"define('CNF_RESTRICIP','".CNF_RESTRICIP."');\n";
			$val .= 	"define('CNF_MAIL','".(defined('CNF_MAIL') ? CNF_MAIL:'phpmail')."');\n";
            $val .= 	"define('CNF_DATE','".(defined('CNF_DATE') ? CNF_DATE: 'Y-m-d' )."');\n";
            $val .= 	"define('CNF_APIKEY','".CNF_APIKEY."');\n";
            $val .= 	"define('CNF_CALENDARID','".CNF_CALENDARID."');\n";
            $val .= 	"define('CNF_ANALYTICS','".CNF_ANALYTICS."');\n";

			$val .= 	"?>";

			$filename = base_path().'/setting.php';
			$fp=fopen($filename,"w+");
			fwrite($fp,$val);
			fclose($fp);
			return Redirect::to('core/config')->with('messagetext',\Lang::get('core.settingsaved'))->with('msgstatus','success');
		} else {
			return Redirect::to('core/config')->with('messagetext', \Lang::get('core.followingerror'))->with('msgstatus','success')
			->withErrors($validator)->withInput();
		}

	}

  public function getCurrency(){
    return view('mmb.config.currency',$this->data);
  }

  public function postCurrency(){
    echo "asdfasdf";
  }

	public function getEmail()
	{

		$regEmail     = base_path()."/resources/views/user/emails/registration.blade.php";
		$resetEmail   = base_path()."/resources/views/user/emails/auth/reminder.blade.php";
		$formEmail    = base_path()."/resources/views/user/emails/form.blade.php";
		$this->data   = array(
			'groups'	    => Groups::all(),
			'pageTitle'	    => \Lang::get('core.t_emailtemplate'),
			'pageNote'	    => \Lang::get('core.t_emailtemplatesmall'),
			'regEmail' 	    => file_get_contents($regEmail),
			'resetEmail'	=> file_get_contents($resetEmail),
			'formEmail'	    => file_get_contents($formEmail),
			'active'		=> 'email',
		);
		return view('mmb.config.email',$this->data);

	}

	function postEmail( Request $request)
	{

		//print_r($_POST);exit;
		$rules = array(
			'regEmail'		    => 'required|min:10',
			'resetEmail'		=> 'required|min:10',
			'formEmail'		    => 'required|min:10',
		);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->passes())
		{
			$regEmailFile    = base_path()."/resources/views/user/emails/registration.blade.php";
			$resetEmailFile  = base_path()."/resources/views/user/emails/auth/reminder.blade.php";
			$formEmailFile   = base_path()."/resources/views/user/emails/form.blade.php";

			$fp=fopen($regEmailFile,"w+");
			fwrite($fp,$_POST['regEmail']);
			fclose($fp);

			$fp=fopen($resetEmailFile,"w+");
			fwrite($fp,$_POST['resetEmail']);
			fclose($fp);

			$fp=fopen($formEmailFile,"w+");
			fwrite($fp,$_POST['formEmail']);
			fclose($fp);

			return Redirect::to('core/config/email')->with('messagetext',\Lang::get('core.emailupdated'))->with('msgstatus','success');

		}	else {

			return Redirect::to('core/config/email')->with('messagetext', \Lang::get('core.followingerror'))->with('msgstatus','success')
			->withErrors($validator)->withInput();
		}

	}

	public function getSecurity()
	{

		$this->data = array(
			'groups'	=> Groups::all(),
			'pageTitle'	=> \Lang::get('core.t_loginsecurity'),
			'pageNote'	=> \Lang::get('core.t_loginsecuritysmall'),
			'active'	=> 'security'

		);


		return view('mmb.config.security',$this->data);

	}




	public function postLogin( Request $request)
	{

		$rules = array(

		);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->passes()) {
			$val  =		"<?php \n";
			$val .= 	"define('CNF_APPNAME','".CNF_APPNAME."');\n";
			$val .= 	"define('CNF_APPDESC','".CNF_APPDESC."');\n";
			$val .= 	"define('CNF_COMNAME','".CNF_COMNAME."');\n";
			$val .= 	"define('CNF_ADDRESS','".CNF_ADDRESS."');\n";
			$val .= 	"define('CNF_TEL','".CNF_TEL."');\n";
			$val .= 	"define('CNF_EMAIL','".CNF_EMAIL."');\n";
			$val .= 	"define('CNF_FACEBOOK','".CNF_FACEBOOK."');\n";
			$val .= 	"define('CNF_TWITTER','".CNF_TWITTER."');\n";
			$val .= 	"define('CNF_INSTAGRAM','".CNF_INSTAGRAM."');\n";
			$val .= 	"define('CNF_TRIPADVISOR','".CNF_TRIPADVISOR."');\n";
			$val .= 	"define('CNF_TAGLINE','".CNF_TAGLINE."');\n";
			$val .= 	"define('CNF_TEMPCOLOR','".CNF_TEMPCOLOR."');\n";
      $val .= 	"define('CNF_API_CURRENCY','".$request->input('cnf_api_currency')."');\n";
      $val .= 	"define('CNF_BASE_CURRENCY','".$request->input('cnf_base_currency')."');\n";
      $val .= 	"define('CNF_DEF_CURRENCY','".$request->input('default_currency')."');\n";
			$val .= 	"define('CNF_METAKEY','".$request->input('cnf_metakey')."');\n";
			$val .= 	"define('CNF_METADESC','".$request->input('cnf_metadesc')."');\n";
			$val .= 	"define('CNF_GROUP','".$request->input('CNF_GROUP')."');\n";
			$val .= 	"define('CNF_ACTIVATION','".$request->input('CNF_ACTIVATION')."');\n";
			$val .= 	"define('CNF_MAINTENANCE','".$request->input('CNF_MAINTENANCE')."');\n";
			$val .= 	"define('CNF_SHOWHELP','".$request->input('CNF_SHOWHELP')."');\n";
			$val .= 	"define('CNF_MULTILANG','".(!is_null($request->input('cnf_multilang')) ? 1 : 0 )."');\n";
            $val .= 	"define('CNF_LANG','".$request->input('cnf_lang')."');\n";
			$val .= 	"define('CNF_REGIST','".(!is_null($request->input('CNF_REGIST')) ? 'true':'false')."');\n";
			$val .= 	"define('CNF_FRONT','".(!is_null($request->input('CNF_FRONT')) ? 'true':'false')."');\n";
			$val .= 	"define('CNF_RECAPTCHA','".(!is_null($request->input('CNF_RECAPTCHA')) ? 'true':'false')."');\n";
            $val .= 	"define('CNF_THEME','".$request->input('cnf_theme')."');\n";
			$val .= 	"define('CNF_RECAPTCHAPUBLICKEY','');\n";
			$val .= 	"define('CNF_RECAPTCHAPRIVATEKEY','');\n";
            $val .= 	"define('CNF_MODE','".(!is_null($request->input('cnf_mode')) ? 'production' : 'development' )."');\n";
			$val .= 	"define('CNF_LOGO','".CNF_LOGO."');\n";
			$val .= 	"define('CNF_HEADERIMAGE','".CNF_HEADERIMAGE."');\n";
			$val .= 	"define('CNF_ALLOWIP','".$request->input('CNF_ALLOWIP')."');\n";
			$val .= 	"define('CNF_RESTRICIP','".$request->input('CNF_RESTRICIP')."');\n";
			$val .= 	"define('CNF_MAIL','".(!is_null($request->input('CNF_MAIL')) ? $request->input('CNF_MAIL'):'phpmail')."');\n";
            $val .= 	"define('CNF_DATE','".(!is_null($request->input('cnf_date')) ? $request->input('cnf_date') : 'Y-m-d' )."');\n";
			$val .= 	"define('CNF_APIKEY','".$request->input('cnf_apikey')."');\n";
			$val .= 	"define('CNF_CALENDARID','".$request->input('cnf_calendarid')."');\n";
			$val .= 	"define('CNF_ANALYTICS','".$request->input('cnf_analytics')."');\n";


			$val .= 	"?>";

			$filename = '../setting.php';
			$fp=fopen($filename,"w+");
			fwrite($fp,$val);
			fclose($fp);
      sleep(3);
			return Redirect::to('core/config/security')->with('messagetext',\Lang::get('core.settingsaved'))->with('msgstatus','success');
		} else {
			return Redirect::to('core/config/security')->with('messagetext', \Lang::get('core.followingerror'))->with('msgstatus','error')
			->withErrors($validator)->withInput();
		}
	}

	public function getLog( $type = null)
	{


		$this->data = array(
			'pageTitle'	=> \Lang::get('core.m_clearcache'),
			'pageNote'	=> \Lang::get('core.dash_clearcache'),
			'active'	=> 'log'
		);
		return view('mmb.config.log',$this->data);
	}


	public function getClearlog()
	{

		$dir = base_path()."/storage/logs";
		foreach(glob($dir . '/*') as $file) {
			if(is_dir($file))
			{
				//removedir($file);
			} else {

				unlink($file);
			}
		}

		$dir = base_path()."/storage/framework/views";
		foreach(glob($dir . '/*') as $file) {
			if(is_dir($file))
			{
				//removedir($file);
			} else {

				unlink($file);
			}
		}

		return response()->json(array(
			'status'	=>\Lang::get('core.note_t_success'),
			'message'	=>  \Lang::get('core.note_success_action')
		));


		//return Redirect::to('mmb/config/log')->with('messagetext','Cache has been cleared !')->with('msgstatus','success');
	}

	function removeDir($dir) {
		foreach(glob($dir . '/*') as $file) {
			if(is_dir($file))
				removedir($file);
			else
				unlink($file);
		}
		rmdir($dir);
	}

	public function getTranslation( Request $request, $type = null)
	{
		if(!is_null($request->input('edit')))
		{
			$file = (!is_null($request->input('file')) ? $request->input('file') : 'core.php');
			$files = scandir(base_path()."/resources/lang/".$request->input('edit')."/");

			//$str = serialize(file_get_contents('./protected/app/lang/'.$request->input('edit').'/core.php'));
			$str = \File::getRequire(base_path()."/resources/lang/".$request->input('edit').'/'.$file);


			$this->data = array(
				'pageTitle'	=> 'Translation',
				'pageNote'	=> 'Add Multilanguage Option',
				'stringLang'	=> $str,
				'lang'			=> $request->input('edit'),
				'files'			=> $files ,
				'file'			=> $file ,
                'active'	=> 'translation',

			);
			$template = 'edit';

		} else {

			$this->data = array(
				'pageTitle'	=> 'Translation',
				'pageNote'	=> 'Add Multilangues Option',
				'active'	=> 'translation',
			);
			$template = 'index';

		}

		return view('mmb.config.translation.'.$template,$this->data);
	}

	public function getAddtranslation()
	{
		return view("mmb.config.translation.create");
	}

	public function postAddtranslation( Request $request)
	{
		$rules = array(
			'name'		=> 'required',
			'folder'	=> 'required|alpha',
			'author'	=> 'required',
		);
		$validator = Validator::make($request->all(), $rules);
		if ($validator->passes()) {

			$template = base_path();

			$folder = $request->input('folder');
			mkdir( $template."/resources/lang/".$folder ,0777 );

			$info = json_encode(array("name"=> $request->input('name'),"folder"=> $folder , "author" => $request->input('author')));
			$fp=fopen(  $template.'/resources/lang/'.$folder.'/info.json',"w+");
			fwrite($fp,$info);
			fclose($fp);

			$files = scandir( $template .'/resources/lang/en/');
			foreach($files as $f)
			{
				if($f != "." and $f != ".." and $f != 'info.json')
				{
					copy( $template .'/resources/lang/en/'.$f, $template .'/resources/lang/'.$folder.'/'.$f);
				}
			}
			return Redirect::to('core/config/translation')->with('messagetext',\Lang::get('core.translationadded'))->with('msgstatus','success');	;

		} else {
			return Redirect::to('core/config/translation')->with('messagetext',\Lang::get('core.translationfailed') )->with('msgstatus','error')->withErrors($validator)->withInput();
		}

	}

	public function postSavetranslation( Request $request)
	{
		$template = base_path();

		$form  	= "<?php \n";
		$form 	.= "return array( \n";
		foreach($_POST as $key => $val)
		{
			if($key !='_token' && $key !='lang' && $key !='file')
			{
				if(!is_array($val))
				{
					$form .= '"'.$key.'"=> "'.strip_tags($val).'", '." \n ";

				} else {
					$form .= '"'.$key.'"=> array( '." \n ";
					foreach($val as $k=>$v)
					{
							$form .= '      "'.$k.'"=> "'.strip_tags($v).'", '." \n ";
					}
					$form .= "), \n";
				}
			}

		}
		$form .= ');';
		//echo $form; exit;
		$lang = $request->input('lang');
		$file	= $request->input('file');
		$filename = $template .'/resources/lang/'.$lang.'/'.$file;
	//	$filename = 'lang.php';
		$fp=fopen($filename,"w+");
		fwrite($fp,$form);
		fclose($fp);
		return Redirect::to('core/config/translation?edit='.$lang.'&file='.$file)
		->with('messagetext',\Lang::get('core.translationsaved'))->with('msgstatus','success');

	}

	public function getRemovetranslation( $folder )
	{
		self::removeDir( base_path()."/resources/lang/".$folder);
		return Redirect::to('core/config/translation')->with('messagetext',\Lang::get('core.translationremoved'))->with('msgstatus','success');

	}


}
