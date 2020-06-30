<?php //usort($tableGrid, "\App\Library\SiteHelpers::_sort"); ?> <div class="col-md-12">
<div class="box-header with-border">
        <div class="col-lg-3 col-xs-6">
            <div class="hpanel">
                <div class="panel-body">
                    <div class="stats-title pull-left">
                        <a
                            href="#">
                            <h4>{{ Lang::get('core.runningpackage') }}</h4>
                        </a>
                    </div>
                    <div class="stats-icon pull-right">
                        <i class="fa fa-bus fa-4x"></i>
                    </div>
                    <div class="m-t-xl">
                        <h1 class="text-green"></h1>
                    </div>
                </div>
            </div>
        </div>
		<div class="col-lg-3 col-xs-6">
            <div class="hpanel">
                <div class="panel-body">
                    <div class="stats-title pull-left">
                        <a
                            href="#">
                            <h4>{{ Lang::get('core.runningpackage') }}</h4>
                        </a>
                    </div>
                    <div class="stats-icon pull-right">
                        <i class="fa fa-bus fa-4x"></i>
                    </div>
                    <div class="m-t-xl">
                        <h1 class="text-green"></h1>
                    </div>
                </div>
            </div>
        </div>        <div class="col-lg-3 col-xs-6">
            <div class="hpanel">
                <div class="panel-body">
                    <div class="stats-title pull-left">
                        <a
                            href="#">
                            <h4>{{ Lang::get('core.runningpackage') }}</h4>
                        </a>
                    </div>
                    <div class="stats-icon pull-right">
                        <i class="fa fa-bus fa-4x"></i>
                    </div>
                    <div class="m-t-xl">
                        <h1 class="text-green">Test</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>




<div class="box box-primary">
	<div class="box-header with-border">

		@include( 'mmb/toolbar')
       
	</div>
	<div class="box-body">

	 {!! (isset($search_map) ? $search_map : '') !!}
<?php echo Form::open(array('url'=>'bookinggroups/delete/', 'class'=>'form-horizontal test' ,'id' =>'MmbTable'  ,'data-parsley-validate'=>'' )) ;?>
<div class="table-responsive" style="min-height:300px; padding-bottom:60px; border: none !important">
	@if(count($rowData)>=1)
    <table class="table table-bordered table-striped " class="display compact" id="{{ $pageModule }}Table">
        <thead>
			<tr>
				<th width="20"> No </th>
				<th width="30"> <input type="checkbox" class="checkall" /></th>
				@if($setting['view-method']=='expand')<th width="50" style="width: 50px;">  </th> @endif
				<th width="50"><?php echo Lang::get('core.btn_action') ;?></th>
				<th >Name</th>
				<th width="300"><?php echo Lang::get('core.category');?></th>
				<th width="300"><?php echo Lang::get('core.formula') ;?></th>
                <th width="300"><?php echo Lang::get('core.date');?></th>
				<th width="300">Nbrs of Travellers</th>
				<th width="50"><?php echo Lang::get('core.status') ;?></th>
			  </tr>
        </thead>
  
        <tbody>
        	
        @if($access['is_add'] =='1' && $setting['inline']=='true')
			<tr id="form-0" >
				<td> # </td>
				<td> </td>
				@if($setting['view-method']=='expand') <td> </td> @endif
				<td >
					<button onclick="saved('form-0')" class="btn btn-success btn-xs" type="button"><i class="fa fa-play-circle"></i></button>
				</td>
				@foreach ($tableGrid as $t)
					@if($t['view'] =='1')
					<?php $limited = isset($t['limited']) ? $t['limited'] :''; ?>
						@if(\App\Library\SiteHelpers::filterColumn($limited ))
						<td data-form="{{ $t['field'] }}" data-form-type="{{ \App\Library\AjaxHelpers::inlineFormType($t['field'],$tableForm)}}">
							{!! \App\Library\SiteHelpers::transForm($t['field'] , $tableForm) !!}
						</td>
						@endif
					@endif
				@endforeach

			  </tr>
			  @endif

           		<?php $i=0; foreach ($rowData as $row) :
           			  $id = $row->group_id;
           		?>
                <tr class="editable" id="form-{{ $row->group_id }}">
					<td class="number"> <?php echo ++$i;?>  </td>
					<td ><input type="checkbox" class="ids" name="ids[]" value="<?php echo $row->group_id ;?>" />  </td>
					@if($setting['view-method']=='expand')
					<td><a href="javascript:void(0)" class="expandable"><i class="fa fa-plus-square " ></i></a></td>
					@endif
				 <td data-values="action" data-key="<?php echo $row->group_id ;?>"  >
					<!-- {!! \App\Library\AjaxHelpers::buttonAction('bookinggroups',$access,$id ,$setting) !!} -->
					<!-- {!! \App\Library\AjaxHelpers::buttonActionInline($row->group_id,'id') !!} -->
                    
                                <!-- <a class="tips" title="{{ Lang::get('core.btn_view') }}"><i class="fa  fa-search fa-2x"></i> </a> -->
                                <a href="{{ url('bookinggroups/show/'.$id)}}" class="tips" title="{{ Lang::get('core.btn_view') }}"><i class="fa  fa-search fa-2x"></i> </a>
                              
				</td>
                <?php foreach ($tableGrid as $field) :
					 	if($field['view'] =='1') :
							$value = \App\Library\SiteHelpers::formatRows($row->{$field['field']}, $field , $row);
						 	?>
						 	<?php $limited = isset($field['limited']) ? $field['limited'] :''; ?>
						 	@if(\App\Library\SiteHelpers::filterColumn($limited ))
								 <td align="<?php echo $field['align'];?>" data-values="{{ $row->{$field['field']} }}" data-field="{{ $field['field'] }}" data-format="{{ htmlentities($value) }}">
									 @if($field['field'] == 'status')
 												@if($row->{$field['field']})
 														 <i class="fa fa-fw fa-2x fa-check-circle text-green tips" title="" data-original-title="Active"></i>
 												@else
 														<i class="fa fa-fw fa-2x fa-exclamation-circle text-yellow tips" title="" data-original-title="Inactive"></i>
 												@endif
 									 @elseif($field['field'] == 'groupnameID')
 												<span>{{ $row->{$field['field']} }}</span>
									@elseif($field['field'] == 'travellerCount')
											<span>{{ $row->{$field['field']} }}</span>
									 @elseif($field['field'] == 'date')
									 		
											  	<span> {{ date('Y-m-d',strtotime($row->{$field['field']})) }}</span>
								
									@elseif($field['field'] == 'categoryID')
									 		
											 <span> {{ $row->{$field['field']} }} </span>
						   
 									 @elseif($field['field'] == 'formula')
									 		@if($row->{$field['field']} =="0")
 												<span>{{Lang::get('core.tour')}}</span>
                                            @else
 												<span>{{Lang::get('core.package')}}</span>

                                            @endif
 									
 									 @endif
								 </td>
							@endif
						 <?php endif;
						endforeach;
					  ?>
                </tr>
                @if($setting['view-method']=='expand')
                <tr style="display:none" class="expanded" id="row-{{ $row->group_id }}">
                	<td class="number"></td>
                	<td></td>
                	<td></td>
                	<td colspan="{{ $colspan}}" class="data"></td>
                	<td></td>
                </tr>
                @endif
            <?php endforeach;?>

        </tbody>

    </table>
	@else

	<div style="margin:100px 0; text-align:center;">

		<p> {{ Lang::get('core.norecord') }} </p>
	</div>

	@endif

	</div>
	<?php echo Form::close() ;?>

	</div>
</div>

	</div>	 	                  			<div style="clear: both;"></div>  	@if($setting['inline'] =='true') @include('mmb.module.utility.inlinegrid') @endif
<script>
$(document).ready(function() {
	$('.tips').tooltip();
	$('input[type="checkbox"],input[type="radio"]').iCheck({
		checkboxClass: 'icheckbox_square-red',
		radioClass: 'iradio_square-red',
	});
	$('#{{ $pageModule }}Table .checkall').on('ifChecked',function(){
		$('#{{ $pageModule }}Table input[type="checkbox"]').iCheck('check');
	});
	$('#{{ $pageModule }}Table .checkall').on('ifUnchecked',function(){
		$('#{{ $pageModule }}Table input[type="checkbox"]').iCheck('uncheck');
	});

	$('#{{ $pageModule }}Paginate .pagination li a').click(function() {
		var url = $(this).attr('href');
		reloadData('#{{ $pageModule }}',url);
		return false ;
	});

	<?php if($setting['view-method'] =='expand') :
			echo \App\Library\AjaxHelpers::htmlExpandGrid();
		endif;
	 ?>
	 $('#{{ $pageModule }}Table').DataTable({
		 "paging": true,
		 "lengthChange": true,
		 "searching": true,
		 "ordering": false,
		 "info": false,
		 "autoWidth": false
	 });
});
</script>
<style>
.table th { text-align: none !important;  }
.table th.right { text-align:right !important;}
.table th.center { text-align:center !important;}

</style>
