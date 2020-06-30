
@if($setting['form-method'] =='native')
<div class="box box-primary">
	<div class="box-header with-border">
			<div class="box-header-tools pull-right " >
				<a href="javascript:void(0)" class="collapse-close pull-right btn btn-xs btn-default" onclick="ajaxViewClose('#{{ $pageModule }}')"><i class="fa fa fa-times"></i></a>
			</div>
	</div>

	<div class="box-body">
@endif
			{!! Form::open(array('url'=>'bookroom/save/'.\App\Library\SiteHelpers::encryptID($row['roomID']), 'class'=>'form-horizontal','files' => true , 'parsley-validate'=>'','novalidate'=>' ','id'=> 'bookroomFormAjax')) !!}
			<div class="col-md-12">
						<fieldset><legend> {{Lang::get('core.bookroom')}}</legend>
							{!! Form::hidden('roomID', $row['roomID']) !!}
              {!! Form::hidden('bookingID', app('request')->input('bookingID')) !!}
							  <div class="form-group">
								<label for="Room Type" class=" control-label col-md-4 text-left"> {{Lang::get('core.roomtype')}}<span class="asterix"> * </span></label>
								<div class="col-md-6">
									<select name='roomtypeID[]' id='roomtypeID' class='select2 ' required></select>
								 </div>
							  </div>
							  <div class="form-group">
								<label for="Travellers" class=" control-label col-md-4 text-left"> {{Lang::get('core.travellers')}} <span class="asterix"> * </span></label>
								<div class="col-md-6">
								  <select name='travellers[]' multiple rows='5' id='travellers' class='select2 ' required  ></select>
								 </div>
								 <div class="col-md-2">
								 </div>
							  </div>
                <div class="form-group">
								<label for="Remarks" class=" control-label col-md-4 text-left"> {{Lang::get('core.remarks')}} </label>
								<div class="col-md-6">
								  <textarea name='remarks' rows='5' id='remarks' class='form-control '
		           >{{ $row['remarks'] }}</textarea>
								 </div>
								 <div class="col-md-2">

								 </div> 
							  </div>
<div class="form-group  " >
							<label for="Status" class=" control-label col-md-4 text-left"> {{ Lang::get('core.status') }} <span class="asterix"> * </span></label>
							<div class="col-md-8">
					<label class='radio radio-inline'>
					<input type='radio' name='status' value ='2' required @if($row['status'] == '2') checked="checked" @endif > {{ Lang::get('core.fr_pending') }} </label>
					<label class='radio radio-inline'>
					<input type='radio' name='status' value ='1' required @if($row['status'] == '1') checked="checked" @endif > {{ Lang::get('core.confirmed') }} </label>
					<label class='radio radio-inline'>
					<input type='radio' name='status' value ='0' required @if($row['status'] == '0') checked="checked" @endif > {{ Lang::get('core.cancelled') }} </label>
										 </div>
									  </div>  </fieldset>
			</div>




			<div style="clear:both"></div>

			<div class="form-group">
				<label class="col-sm-4 text-right">&nbsp;</label>
				<div class="col-sm-8">
					<button type="submit" class="btn btn-success btn-sm ">  {{ Lang::get('core.sb_save') }} </button>
					<button type="button" onclick="ajaxViewClose('#{{ $pageModule }}')" class="btn btn-danger btn-sm">  {{ Lang::get('core.sb_cancel') }} </button>
				</div>
			</div>
			{!! Form::close() !!}


@if($setting['form-method'] =='native')
	</div>
</div>
@endif


<script type="text/javascript">
$(document).ready(function() {

    $("#travellers").jCombo("{!! url('bookroom/comboselect?filter=travellers:travellerID:nameandsurname&limit=WHERE:status:=:1') !!}",
		{  selected_value : '{{ $row["travellers"] }}' });
		var packageIDforroomtype = '{{$packageID}}';
		// if()
		console.log("packageIDforroomtype");
		console.log(packageIDforroomtype);
		if(packageIDforroomtype){
			$("#roomtypeID").jCombo("{!! url('bookroom/comboselect?filter=room_features JOIN def_room_types ON room_features.roomtypeID=def_room_types.roomtypeID:roomtypeID:room_type&limit=WHERE:status:=:1 AND room_features.packageID = '.$packageID.' AND seat>(SELECT COUNT(*) FROM book_tour WHERE roomfeatureID=room_features.`roomfeatureID`)') !!}",
			{  selected_value : '{{ $row["roomtype"] }}' });
		}else{
				$("#roomtypeID").jCombo("{!! url('bookroom/comboselect?filter=hotel_rates JOIN def_room_types ON hotel_rates.roomtypeID=def_room_types.roomtypeID:roomtypeID:room_type&limit=WHERE:status:=:1 AND hotel_rates.hotelID IN ('.$hotelID.')') !!}",
					{  selected_value : '{{ $row["roomtype"] }}' });
		}


	$('.editor').summernote();
	$('.tips').tooltip();
	$(".select2").select2({ width:"100%" , maximumSelectionLength:3 ,dropdownParent: $('#mmb-modal-content')});
    $('.date').datetimepicker({format: 'yyyy-mm-dd', autoclose:true , minView:2 , startView:2 , todayBtn:true });
	$('.datetime').datetimepicker({format: 'yyyy-mm-dd hh:ii:ss'});
	$('input[type="checkbox"],input[type="radio"]').iCheck({
		checkboxClass: 'icheckbox_square-red',
		radioClass: 'iradio_square-red',
	});
		$('.removeMultiFiles').on('click',function(){
			var removeUrl = '{{ url("bookroom/removefiles?file=")}}'+$(this).attr('url');
			$(this).parent().remove();
			$.get(removeUrl,function(response){});
			$(this).parent('div').empty();
			return false;
		});

	var form = $('#bookroomFormAjax');
	form.parsley();
	form.submit(function(){

		if(form.parsley('isValid') == true){
			var options = {
				dataType:      'json',
				beforeSubmit :  showRequest,
				success:       showResponse
			}
			$(this).ajaxSubmit(options);
			return false;

		} else {
			return false;
		}

	});

});

function showRequest()
{
	$('.ajaxLoading').show();
}
function showResponse(data)  {

	if(data.status == 'success')
	{
		ajaxViewClose('#{{ $pageModule }}');
		ajaxFilter('#{{ $pageModule }}','{{ $pageUrl }}/data');
        notyMessage(data.message);
		$('#mmb-modal').modal('hide');
        setTimeout(location.reload.bind(location), 3000);
	} else {
		notyMessageError(data.message);
		$('.ajaxLoading').hide();
		return false;
	}
}

</script>
