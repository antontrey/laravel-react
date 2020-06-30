	<div class="box-header-tools pull-left" >
			@if($pageModule!='booktour' && $pageModule!='bookhotel' && $pageModule!='bookflight' && $pageModule!='bookcar' && $pageModule!='bookcar'  && $pageModule!='bookextra' )
				@if($access['is_add'] ==1)
					@if(isset($page_singlename))
						<a href="{{ url('bookinggroups/update')}}"  onclick="MmbModal(this.href,'Add Group'); return false;"  class="tips text-green"><i class="fa fa-plus-square-o fa-2x"></i></a>
					@else
						@if(isset($accounting))
						@else
						{!! \App\Library\AjaxHelpers::buttonActionCreate($pageModule,$setting??'') !!}
						@endif
					@endif
				@endif
            @endif

			@if($access['is_clone'] ==1)
				@if(isset($accounting))
				@else
       			 <a href="javascript://ajax" class="tips text-blue" onclick="ajaxCopy('#{{ $pageModule }}','{{ $pageUrl }}')" title="{{ Lang::get('core.btn_copy') }}"><i class="fa fa-copy fa-2x" data-toggle="confirmation" data-title="{{Lang::get('core.rusure')}}"  data-content="{{ Lang::get('core.rusureyouwanttocopythis') }}"></i> </a>
				@endif
			@endif
			@if($access['is_remove'] ==1)
				@if(isset($accounting))
				@else
					<a href="javascript://ajax" class="tips text-red" title="{{ Lang::get('core.btn_remove') }}" onclick="ajaxRemove('#{{ $pageModule }}','{{ $pageUrl }}');"><i class="fa fa-trash-o fa-2x" data-toggle="confirmation" data-title="{{Lang::get('core.rusure')}}"  data-content="{{ Lang::get('core.rusuredelete') }}"></i></a>
				@endif
			@endif
        @if($access['is_excel'] ==1)
		    <a class="dropdown-toggle tips" title="{{ Lang::get('core.btn_download') }}" data-toggle="dropdown" href="#"><i class="fa fa-cloud-download fa-2x"></i>
		    </a>
			<ul class="dropdown-menu  pull-right">
			@if(isset($accounting))
			<li><a href="javascript:void(0)" id="pdf_accounting" class="tips" title="PDF"><i class="fa fa-file fa-2x"></i> PDF </a></li>
			<li><a href="javascript:void(0)" class="tips" id="excel_accounting" title="Excel"><i class="fa fa-file-excel-o fa-2x"></i> Excel </a></li>
			<li><a href="javascript:void(0)" class="tips" id="word_accounting" title="Word"><i class="fa fa-file-word-o fa-2x"></i> Word </a></li>
			<li><a href="javascript:void(0)" class="tips" id="csv_accounting" title="CSV"><i class="fa fa-file-code-o fa-2x"></i> CSV </a></li>
			<li><a href="{{ URL::to( $pageModule .'/export/print?return='.$return) }}" class="tips" onclick="ajaxPopupStatic(this.href); return false;" ><i class="fa fa-print fa-2x"></i> {{ Lang::get('core.btn_print') }} </a></li>

			<li><a href="{{ URL::to( $pageModule .'/expotion?return='.$return) }}" class="tips " onclick="MmbModal(this.href,'{{ Lang::get('core.downloadoption') }}'); return false" ><i class="fa fa-cog  fa-2x"></i> {{ Lang::get('core.moreoption') }} </a></li>

			@else
			<li><a href="{{ URL::to( $pageModule .'/export/pdf?return='.$return) }}" class="tips" title="PDF"><i class="fa fa-file fa-2x"></i> PDF </a></li>
			<li><a href="{{ URL::to( $pageModule .'/export/excel?return='.$return) }}" class="tips" title="Excel"><i class="fa fa-file-excel-o fa-2x"></i> Excel </a></li>
			<li><a href="{{ URL::to( $pageModule .'/export/word?return='.$return) }}" class="tips" title="Word"><i class="fa fa-file-word-o fa-2x"></i> Word </a></li>
			<li><a href="{{ URL::to( $pageModule .'/export/csv?return='.$return) }}" class="tips" title="CSV"><i class="fa fa-file-code-o fa-2x"></i> CSV </a></li>
			<li><a href="{{ URL::to( $pageModule .'/export/print?return='.$return) }}" class="tips" onclick="ajaxPopupStatic(this.href); return false;" ><i class="fa fa-print fa-2x"></i> {{ Lang::get('core.btn_print') }} </a></li>
			<li><a href="{{ URL::to( $pageModule .'/expotion?return='.$return) }}" class="tips " onclick="MmbModal(this.href,'{{ Lang::get('core.downloadoption') }}'); return false" ><i class="fa fa-cog  fa-2x"></i> {{ Lang::get('core.moreoption') }} </a></li>
			
			@endif
			</ul>
		@endif
	</div>

	<div class="box-header-tools pull-right" >

		<div class="pull-right">
                    <a href="javascript:void(0)" class="tips" title="{{ Lang::get('core.reload') }}" onclick="reloadData('#{{ $pageModule }}','{{ $pageModule }}/data?return={{ $return }}')"><i class="fa fa-refresh fa-2x"></i></a>
            @if(Session::get('gid') ==1)
		  <a href="{{ url( 'mmb/module/permission/'.$pageModule) }}"> <i class="fa fa-lock fa-2x tips" title="Permission Settings"></i></a>
            @endif
		</div>
	</div>
