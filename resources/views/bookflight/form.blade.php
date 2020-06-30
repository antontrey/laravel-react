
@if($setting['form-method'] =='native')
<div class="box box-primary">
	<div class="box-header with-border">
			<div class="box-header-tools pull-right " >
				<a href="javascript:void(0)" class="collapse-close pull-right btn btn-xs btn-default" onclick="ajaxViewClose('#{{ $pageModule }}')"><i class="fa fa fa-times"></i></a>
			</div>
	</div>

	<div class="box-body">
@endif
			{!! Form::open(array('url'=>'bookflight/save/'.\App\Library\SiteHelpers::encryptID($row['bookflightID']), 'class'=>'form-horizontal','files' => true , 'parsley-validate'=>'','novalidate'=>' ','id'=> 'bookflightFormAjax')) !!}
			<div class="col-md-12">
				{!! Form::hidden('bookflightID', $row['bookflightID']) !!}
{!! Form::hidden('bookingID', app('request')->input('bookingID') ) !!}
									  <div class="form-group  " >
										<label for="Travellers" class=" control-label col-md-4 text-left"> {{Lang::get('core.travellers')}} <span class="asterix"> * </span></label>
										<div class="col-md-7">

										  <select name='travellersID[]' multiple rows='5' id='travellersID' class='select2 ' required  ></select>
										 </div>
									  </div>
									  <div class="form-group  " >
										<label for="Airline" class=" control-label col-md-4 text-left"> {{Lang::get('core.tickets')}} <span class="asterix"> * </span></label>
										<div class="col-md-7">
										  <select name='ticketID' rows='5' id='ticketID' class='select2 ' required  ></select>
										 </div>
									  </div>
									  <div class="form-group  return" >
										<label for="Round Trip" class=" control-label col-md-4 text-left"> {{Lang::get('core.roundtrip')}}<span class="asterix"> * </span></label>
										<div class="col-md-7">
											<!-- <label class='radio radio-inline'>
											<input type='radio' name='return' value ='1' id="return1" required @if($row['return'] == '1') checked="checked" @endif > {{Lang::get('core.yes')}} </label>
											<label class='radio radio-inline'>
											<input type='radio' name='return' value ='0' id="return0" required @if($row['return'] == '0') checked="checked" @endif > {{Lang::get('core.no')}} </label><br> -->
											<input  type='text' name='return_value' id='return_value' disabled value='' required class='form-control ' placeholder="{{Lang::get('core.roundtrip')}}" />
											<input  type='hidden' name='return' id='return'  value=''  class='form-control ' placeholder="{{Lang::get('core.roundtrip')}}" />
										 </div>
									  </div>
									  <div class="form-group  " >
										<label for="Airline" class=" control-label col-md-4 text-left"> {{Lang::get('core.airline')}} <span class="asterix"> * </span></label>
										<div class="col-md-7">
										  <select name='airlineID_value' rows='5' disabled id='airlineID' class='select2 ' readonly required  ></select>
										  <input  type='hidden' name='airlineID' id='airlineID_value'  value='' required class='form-control ' />

										 </div>
									  </div>
									  <div class="form-group  " >
										<label for="Class" class=" control-label col-md-4 text-left"> {{Lang::get('core.class')}} <span class="asterix"> * </span></label>
										<div class="col-md-7">
										  <input  type='hidden' name='class' id='class'  value='' class='form-control ' />
											<input  type='text' name='class_value' id='class_value' disabled value='' required class='form-control ' placeholder="{{Lang::get('core.class')}}" />
												<!-- <label class='radio radio-inline'>
												<input type='radio' name='class' value ='1' required @if($row['class'] == '1') checked="checked" @endif > {{Lang::get('core.economy')}} </label><br>
												<label class='radio radio-inline'>
												<input type='radio' name='class' value ='2' required @if($row['class'] == '2') checked="checked" @endif > {{Lang::get('core.premiumeconomy')}} </label><br>
												<label class='radio radio-inline'>
												<input type='radio' name='class' value ='3' required @if($row['class'] == '3') checked="checked" @endif > {{Lang::get('core.business')}} </label><br>
												<label class='radio radio-inline'>
												<input type='radio' name='class' value ='4' required @if($row['class'] == '4') checked="checked" @endif > {{Lang::get('core.first')}} </label> -->
										 </div>
									  </div>
                                    <div class="form-group  " >
										<label for="Departure Airport" class=" control-label col-md-4 text-left"> {{Lang::get('core.departureairport')}} <span class="asterix"> * </span></label>
										<div class="col-md-7">
										  <select name='depairportID_value' rows='5' disabled id='depairportID' class='select2 ' required  ></select>
										  <input  type='hidden' name='depairportID' id='depairportID_value'  value='' required class='form-control ' />

										 </div>
									  </div>
                                        <div class="form-group  " >
										<label for="Depart Date" class=" control-label col-md-4 text-left"> {{Lang::get('core.departuredate')}} <span class="asterix"> * </span></label>
										<div class="col-md-4">
											<div class="input-group m-b">
												<input  type='text' name='departing' id='departing'  value='' required class='form-control ' readonly placeholder="{{Lang::get('core.departuredate')}}" />
												<!-- {!! Form::text('departing', $row['departing'],array('class'=>'form-control datetime', 'style'=>'width:150px !important;')) !!}
												<span class="input-group-addon"><i class="fa fa-calendar"></i></span> -->
											</div>
										 </div>
                                    <div class="col-md-3">
										  <input  type='text' name='arrFlightNO' id='arrFlightNO' readonly  value='{{ $row['arrFlightNO'] }}' required class='form-control ' placeholder="{{Lang::get('core.flightNO')}}" />
										 </div>
									  </div>
									  <div class="form-group  " >
										<label for="Arrival Airport" class=" control-label col-md-4 text-left"> {{Lang::get('core.arrivalairport')}} <span class="asterix"> * </span></label>
										<div class="col-md-7">
										  <select name='arrairportID_value' rows='5' id='arrairportID' disabled class='select2 ' required  ></select>
										  <input  type='hidden' name='arrairportID' id='arrairportID_value'  value='' required class='form-control ' />
										 </div>
									  </div>
									  <div class="form-group  returndate" >
										<label for="Return Date" class=" control-label col-md-4 text-left"> {{Lang::get('core.returndate')}} </label>
										<div class="col-md-4">
											<div class="input-group m-b">
												<!-- {!! Form::text('returning', $row['returning'],array('class'=>'form-control datetime', 'style'=>'width:150px !important;')) !!}
												<span class="input-group-addon"><i class="fa fa-calendar"></i></span> -->
												<input  type='text' name='returning' id='returning' value='' required class='form-control ' placeholder="{{Lang::get('core.returning')}}" />
											</div>
										 </div>
                                        <div class="col-md-3">
										  <input  type='text' name='depFlightNO' id='depFlightNO' value='' required class='form-control ' placeholder="{{Lang::get('core.flightNO')}}" />
										  <!-- <input  type='text' name='depFlightNO' id='depFlightNO'  value='{{ $row['depFlightNO'] }}' required class='form-control ' placeholder="{{Lang::get('core.flightNO')}}" /> -->
										 </div>
									  </div>

									  <div class="form-group  status" >
										<label for="Status" class=" control-label col-md-4 text-left"> {{ Lang::get('core.status') }}</label>
										<div class="col-md-8">
											<label class='radio radio-inline'>
											<input type='radio' name='status' value ='2' required @if($row['status'] == '2') checked="checked" @endif > {{ Lang::get('core.fr_pending') }} </label>
											<label class='radio radio-inline'>
											<input type='radio' name='status' value ='1' required @if($row['status'] == '1') checked="checked" @endif > {{ Lang::get('core.confirmed') }} </label>
											<label class='radio radio-inline'>
											<input type='radio' name='status' value ='0' required @if($row['status'] == '0') checked="checked" @endif > {{ Lang::get('core.cancelled') }} </label>
										 </div>
									  </div>
                                        <div class="form-group  divpnr" >
										<label for="PNR" class=" control-label col-md-4 text-left"> {{Lang::get('core.pnr')}}</label>
										<div class="col-md-7">
										  <input  type='text' name='PNR' id='PNR' value='{{ $row['PNR'] }}'
						     class='form-control ' placeholder="{{Lang::get('core.pnr')}}" />
                                          </div>
									  </div>
									  <div class="form-group  diveticket" >
										<label for="Eticketno" class=" control-label col-md-4 text-left"> {{Lang::get('core.eticketno')}}</label>
										<div class="col-md-7">
										  <input  type='text' name='eticketno' id='eticketno' value='{{ $row['eticketno'] }}'
						     class='form-control ' placeholder="{{Lang::get('core.eticketno')}}" />
                                          </div>
									  </div>
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

		$("#travellersID").jCombo("{!! url('bookflight/comboselect?filter=travellers:travellerID:nameandsurname&limit=WHERE:status:=:1') !!}",
		{  selected_value : '{{ $row["travellersID"] }}' });

		$("#airlineID").jCombo("{!! url('bookflight/comboselect?filter=def_airlines:airlineID:airline&limit=WHERE:status:=:1') !!}",
		{  selected_value : '{{ $row["airlineID"] }}' });
		$("#ticketID").jCombo("{!! url('bookflight/comboselect?filter=tickets:ticketID:ticketID&limit=WHERE:status:=:1') !!}",
		{  selected_value : '{{ $row["ticketID"] }}' });

		$("#depairportID").jCombo("{!! url('bookflight/comboselect?filter=def_airports:airportID:airport_name&limit=WHERE:status:=:1') !!}",
		{  selected_value : '{{ $row["depairportID"] }}' });

		$("#arrairportID").jCombo("{!! url('bookflight/comboselect?filter=def_airports:airportID:airport_name&limit=WHERE:status:=:1') !!}",
		{  selected_value : '{{ $row["arrairportID"] }}' });


	$('.editor').summernote();
	$('.tips').tooltip();
	$(".select2").select2({ width:"100%" , dropdownParent: $('#mmb-modal-content')});
	$('.date').datetimepicker({format: 'yyyy-mm-dd', autoclose:true , todayBtn:true });
	$('.datetime').datetimepicker({format: 'yyyy-mm-dd hh:ii'});
	$('input[type="checkbox"],input[type="radio"]').iCheck({
		checkboxClass: 'icheckbox_square-red',
		radioClass: 'iradio_square-red',
	});
		$('.removeMultiFiles').on('click',function(){
			var removeUrl = '{{ url("bookflight/removefiles?file=")}}'+$(this).attr('url');
			$(this).parent().remove();
			$.get(removeUrl,function(response){});
			$(this).parent('div').empty();
			return false;
		});

	var form = $('#bookflightFormAjax');
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


	$("#ticketID").change(function(){
	var ticketID = $("#ticketID").val();
		if(ticketID){
					$.ajax({
					url:'{{route("ticket_datas")}}',
					type: 'GET',
					data: { 
						ticketID:ticketID,
						csrf:$('meta[name="csrf-token"]').attr('content')
					},
					dataType: 'json',
					success: function(data){
						
						console.log("success!");
						var ticketID = data[0]['ticketID'];
						var airlineID = data[0]['airlinesID'];
						console.log(data[0]);
						var depFlightNO = data[0]['depFlightNO'];
						$("#depFlightNO").val(depFlightNO);
						$("#depFlightNO").prop("readonly", true);
						var arrFlightNO = data[0]['arrFlightNO'];
						$("#arrFlightNO").val(arrFlightNO);
						$("#depFlightNO").prop("readonly", true);
						$("#airlineID").jCombo("{!! url('bookflight/comboselect?filter=def_airlines:airlineID:airline&limit=WHERE:status:=:1') !!}",
						{  selected_value : airlineID });
						$("#airlineID_value").val(airlineID);

						$("#airlineID").prop("readonly", true);
						var returnn = data[0]['returnn'];
						if(returnn == 0){
							$("#return_value").val("No");
							$('.returndate').hide();
						}else{
							$("#return_value").val("Yes");
							$('.returndate').show();
						}
						$("#return").prop("readonly", true);
						$("#return").val(returnn);
						var Classes = data[0]['class'];
						if(Classes == 1){
							$("#class_value").val('{{Lang::get('core.economy')}}');
							
						}else if(Classes == 2){
							$("#class_value").val('{{Lang::get('core.premiumeconomy')}}');
							
						}else if(Classes == 3){
							$("#class_value").val('{{Lang::get('core.business')}}');
							
						}else if(Classes == 4){
							$("#class_value").val('{{Lang::get('core.first')}}');
							
						}
						$("#class").val(Classes);
						
						$("#class").prop("readonly", true);
						var depairportID = data[0]['depairportID'];
						$("#depairportID").jCombo("{!! url('bookflight/comboselect?filter=def_airports:airportID:airport_name&limit=WHERE:status:=:1') !!}",
						{  selected_value : depairportID });
						$("#depairportID_value").val(depairportID);
						var departing = data[0]['departing'];
							$("#departing").val(departing);
						$("#departing").prop("readonly", true);
							
						var returning = data[0]['returning'];
						$("#returning").val(returning);
						$("#returning").prop("readonly", true);

						var arrairportID = data[0]['arrairportID'];
						$("#arrairportID").jCombo("{!! url('bookflight/comboselect?filter=def_airports:airportID:airport_name&limit=WHERE:status:=:1') !!}",
						{  selected_value : arrairportID });
						$("#arrairportID_value").val(arrairportID);

						console.log(returnn);

		
					},
					error: function(data){
						console.log(data);
					}
				});
		}


	});


    $('.divpnr').hide();
    $('.diveticket').hide();
    $('.returndate').hide();
	$('.status input:radio').on('ifClicked', function() {
	 	 val = $(this).val();
  			mType(val);

	});
	$('.return input:radio').on('ifClicked', function() {
	 	 val = $(this).val();
  			nType(val);

	});

	mType('<?php echo $row['status'] ?>');
	nType('<?php echo $row['return'] ?>');


});

function mType( val )
{
		if(val == '1') {
			// $('.divpnr').show();
			// $('.diveticket').show();
		}
		if(val != '1') {
			$('.divpnr').hide();
			$('.diveticket').hide();
		}
}

function nType( val )
{
		if(val == '1') {
			$('.returndate').show();
		}
		if(val == '0') {
			$('.returndate').hide();
		}
}

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
