<?php usort($tableGrid, "\App\Library\SiteHelpers::_sort"); ?>
<div class="box box-primary col-md-12">
	<div class="box-header with-border">
		@include( 'mmb/toolbar')
	</div>
	<div class="box-body">

	 {!! (isset($search_map) ? $search_map : '') !!}

	 <?php echo Form::open(array('url'=>'tickets', 'class'=>'form-horizontal' ,'id' =>'MmbTable'  ,'data-parsley-validate'=>'' )) ;?>
<div class="table-responsive" style="min-height:300px; padding-bottom:60px; border: none !important">
	@if(count($rowData)>=1)
    <table class="table table-striped " id="{{ $pageModule }}Table">
        <thead>
				<tr>
				<th width="10"> No </th>
				<th width="10"> <input type="checkbox" class="checkall" /></th>
				@if($setting['view-method']=='expand')<th width="50" style="width: 50px;">  </th> @endif
        <th width="50">{{Lang::get('core.btn_action')}}</th>
				<th>{{Lang::get('core.airlines')}}</th>
				<th>{{Lang::get('core.from')}}</th>
				<th>{{Lang::get('core.to')}}</th>
				<th>{{Lang::get('core.departuredate')}}</th>
				<th>{{Lang::get('core.flightNO')}}</th>
				<th>{{Lang::get('core.returndate')}}</th>
				<th>{{Lang::get('core.flightNO')}}</th>
				<th>{{Lang::get('core.seats')}}</th>
				<th>{{Lang::get('core.seatsavailable')}}</th>
				<th>{{Lang::get('core.class')}}</th>
				<th width="30">{{Lang::get('core.status')}}</th>
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

           		<?php $i = 0; foreach ($rowData as $row) :
           			  $id = $row->ticketID;
           		?>
                <tr class="editable" id="form-{{ $row->ticketID }}">
					<td class="number"> <?php echo ++$i;?>  </td>
					<td ><input type="checkbox" class="ids" name="ids[]" value="<?php echo $row->ticketID ;?>" />  </td>
					@if($setting['view-method']=='expand')
					<td><a href="javascript:void(0)" class="expandable" rel="#row-{{ $row->ticketID }}" data-url="{{ url('tickets/show/'.$id) }}"><i class="fa fa-plus " ></i></a></td>
					@endif
				 <td data-values="action" data-key="<?php echo $row->ticketID ;?>"  >
					{!! \App\Library\AjaxHelpers::buttonAction('tickets',$access,$id ,$setting) !!}
					{!! \App\Library\AjaxHelpers::buttonActionInline($row->ticketID,'ticketID') !!}

				</td>
					 <?php foreach ($tableGrid as $field) :
					 	if($field['view'] =='1'):
							$value = \App\Library\SiteHelpers::formatRows($row->{$field['field']}, $field , $row);
						 	?>
						 	<?php $limited = isset($field['limited']) ? $field['limited'] :''; ?>
						 	@if(\App\Library\SiteHelpers::filterColumn($limited ))
								 <td align="<?php echo $field['align'];?>" data-values="{{ $row->{$field['field']} }}" data-field="{{ $field['field'] }}" data-format="{{ htmlentities($value) }}">
									 @if($field['field'] == 'airlinesID')
											@foreach($airlines as $a)
											 		<?php
													$airlineIds = $row->{$field['field']};
														if($airlineIds == $a->airlineID)
															echo "<span>".$a->airline??''."</span>";
													?>
											@endforeach
									 @elseif($field['field'] == 'depairportID')
										 @foreach($airports as $a)
												 <?php
													 if($row->{$field['field']} == $a->airportID){
														 echo "<span>".$a->airport_name??''."</span>";
													 }
												 ?>
										 @endforeach
									@elseif($field['field'] == 'arrairportID')
  										 @foreach($airports as $a)
  												 <?php
  													 if($row->{$field['field']} == $a->airportID){
  														 echo "<span>".$a->airport_name??''."</span>";
  													 }
  												 ?>
  										 @endforeach
									 @elseif($field['field'] == 'class')
											 @if($row->{$field['field']} == '1')
													 {{ __('core.economy')}}
											 @elseif($row->{$field['field']} == '2')
													 {{ __('core.premiumeconomy')}}
											 @elseif($row->{$field['field']} == '3')
													 {{ __('core.business') }}
											 @elseif($row->{$field['field']} == '4')
													 {{ __('core.first')}}
											 @endif
								   @elseif(strtolower($field['field']) == 'status')
											 @if($row->{$field['field']} == '2')
													 <span class="label label-warning">{{ __('core.fr_pending') }}</span>
											 @elseif($row->{$field['field']} == '1')
													 <span class="label label-success">{{ __('core.confirmed') }}</span>
											 @elseif($row->{$field['field']} == '0')
													 <span class="label label-danger">{{ __('core.cancelled') }}</span>
											 @endif
									 @elseif($field['field'] == 'available_seats')
									 		<?php $available_seats = $row->{$field['field']}  ?>
									  	<input class="select-alt" onchange="javascript:change_availableSeats({{$row->ticketID}})" id="change_availableSeats_{{$row->ticketID}}" type="text" style="width:50px;"  value="{{$available_seats}}">
									 @else
									 		{!! $value !!}
									 @endif
								 </td>
							@endif
						 <?php endif;
						endforeach;
					  ?>
                </tr>
                @if($setting['view-method']=='expand')
                <tr style="display:none" class="expanded" id="row-{{ $row->ticketID }}">
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

	@if($setting['inline'] =='true') @include('mmb.module.utility.inlinegrid') @endif
<script>
$(document).ready(function() {
	// $('#search').val($('#ajaxsearch').val());
	// $("#MmbTable").submit(function(){
	// 	alert();
	// 	ajaxFilter('#<?php echo $pageModule;?>','{{ $pageUrl }}/data')
	// 	event.preventDefault();
	// });

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
	$('#search').change(function(){
		$('#ajaxsearch').val($('#search').val());
	});
	<?php if($setting['view-method'] =='expand'):
			echo \App\Library\AjaxHelpers::htmlExpandGrid();
		endif;
	 ?>
	 $('#{{ $pageModule }}Table').DataTable({
		 "paging": true,
		 "lengthChange": false,
		 "searching": true,
		 "ordering": false,
		 "info": false,
		 "autoWidth": false
	 });
});
function change_availableSeats(ticketID){
	available_seats = $("#change_availableSeats_"+ticketID).val();
	$.post('tickets/change_availableSeats/'+ticketID, {available_seats: available_seats}).done(function (data) {
			console.log("success");
	});
}
</script>
<style>
.table th { text-align: none !important;}
.table th.right { text-align:right !important;}
.table th.center { text-align:center !important;}
</style>
