
@if($setting['form-method'] =='native')
<div class="box box-primary">
	<div class="box-header with-border">
			<div class="box-header-tools pull-right " >
				<a href="javascript:void(0)" class="collapse-close pull-right btn btn-xs btn-default" onclick="ajaxViewClose('#{{ $pageModule }}')"><i class="fa fa fa-times"></i></a>
			</div>
	</div>

	<div class="box-body">
@endif
			{!! Form::open(array('url'=>'bookhotel/save/'.\App\Library\SiteHelpers::encryptID($row['bookhotelID']), 'class'=>'form-horizontal','files' => true , 'parsley-validate'=>'','novalidate'=>' ','id'=> 'bookhotelFormAjax')) !!}
			<div class="col-md-12">
						<fieldset><legend> {{Lang::get('core.bookhotel')}}</legend>
				{!! Form::hidden('bookhotelID', $row['bookhotelID']) !!}
{!! Form::hidden('bookingID', app('request')->input('bookingID') ) !!}
									  <div class="form-group  " >
										<label for="Country" class=" control-label col-md-4 text-left"> {{Lang::get('core.country')}} <span class="asterix"> * </span></label>
										<div class="col-md-6">
										  <select name='countryID' rows='5' id='countryID' class='select2 ' required  ></select>
										 </div>
										 <div class="col-md-2">

										 </div>
									  </div>
									  <div class="form-group  " >
										<label for="City" class=" control-label col-md-4 text-left"> {{Lang::get('core.city')}} <span class="asterix"> * </span></label>
										<div class="col-md-6">
										  <select name='cityID' rows='5' id='cityID' class='select2 ' required  ></select>
										 </div>
										 <div class="col-md-2">

										 </div>
									  </div>
									  <div class="form-group  " >
										<label for="Hotel" class=" control-label col-md-4 text-left"> {{Lang::get('core.hotel')}} <span class="asterix"> * </span></label>
										<div class="col-md-6">
										  <select name='hotelID' rows='5' id='hotelID' class='select2 ' required  ></select>
										 </div>
										 <div class="col-md-2">

										 </div>
									  </div>
									  <div class="form-group  " >
										<label for="Hotel" class=" control-label col-md-4 text-left"> {{Lang::get('core.hotel')}} <span class="asterix"> * </span></label>
										<div class="col-md-6">
											<input  type='number' name='total_nights' id='total_nights' value="1" class='form-control ' min="1" />
										 </div>
										 <div class="col-md-2">
										 </div>
									  </div>
									  <div class="form-group  " >
										<label for="Check in Date" class=" control-label col-md-4 text-left"> {{Lang::get('core.checkindate')}} <span class="asterix"> * </span></label>
										<div class="col-md-6">

											<div class="input-group m-b" style="width:150px !important;">
												{!! Form::text('checkin', $row['checkin'],array('class'=>'form-control date')) !!}
												<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
											</div>
										 </div>
										 <div class="col-md-2">

										 </div>
									  </div>
									  <div class="form-group  " >
										<label for="Check out Date" class=" control-label col-md-4 text-left"> {{Lang::get('core.checkoutdate')}} <span class="asterix"> * </span></label>
										<div class="col-md-6">

											<div class="input-group m-b" style="width:150px !important;">
												{!! Form::text('checkout', $row['checkout'],array('class'=>'form-control date')) !!}
												<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
											</div>
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
									  </div> </fieldset>
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


</div>

<script type="text/javascript">
$(document).ready(function() {

		$("#countryID").jCombo("{!! url('bookhotel/comboselect?filter=def_country:countryID:country_name&limit=WHERE:status:=:1') !!}",
		{  selected_value : '{{ $row["countryID"] }}' });

		$("#cityID").jCombo("{!! url('bookhotel/comboselect?filter=def_city:cityID:city_name&limit=WHERE:status:=:1') !!}&parent=countryID:",
		{  parent: '#countryID', selected_value : '{{ $row["cityID"] }}' });

		$("#hotelID").jCombo("{!! url('bookhotel/comboselect?filter=hotels:hotelID:hotel_name&limit=WHERE:status:=:1') !!}&parent=cityID:",
		{  parent: '#cityID', selected_value : '{{ $row["hotelID"] }}' });


	$('.editor').summernote();
	$('.tips').tooltip();
	$(".select2").select2({ width:"100%" , dropdownParent: $('#mmb-modal-content')});
	$('.date').datetimepicker({format: 'yyyy-mm-dd', autoclose:true , minView:2 , startView:2 , todayBtn:true });
	$('.datetime').datetimepicker({format: 'yyyy-mm-dd hh:ii'});
	$('input[type="checkbox"],input[type="radio"]').iCheck({
		checkboxClass: 'icheckbox_square-red',
		radioClass: 'iradio_square-red',
	});
		$('.removeMultiFiles').on('click',function(){
			var removeUrl = '{{ url("bookhotel/removefiles?file=")}}'+$(this).attr('url');
			$(this).parent().remove();
			$.get(removeUrl,function(response){});
			$(this).parent('div').empty();
			return false;
		});

	var form = $('#bookhotelFormAjax');
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
