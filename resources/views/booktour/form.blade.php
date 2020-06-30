
@if($setting['form-method'] =='native')
<div class="box box-primary">
	<div class="box-header with-border">
			<div class="box-header-tools pull-right " >
				<a href="javascript:void(0)" class="collapse-close pull-right btn btn-xs btn-default" onclick="ajaxViewClose('#{{ $pageModule }}')"><i class="fa fa fa-times"></i></a>
			</div>
	</div>

	<div class="box-body">
@endif
			{!! Form::open(array('url'=>'booktour/save/'.\App\Library\SiteHelpers::encryptID($row['booktourID']), 'class'=>'form-horizontal','files' => true , 'parsley-validate'=>'','novalidate'=>' ','id'=> 'booktourFormAjax')) !!}
			<div class="col-md-12">
						<fieldset><legend>{{Lang::get('core.booktour')}}</legend>
								{!! Form::hidden('booktourID', $row['booktourID']) !!}
								{!! Form::hidden('bookingID', app('request')->input('bookingID') ) !!}
										<div class="form-group">
											<label for="Formula" class=" control-label col-md-4 text-left"> {{ Lang::get('core.formula') }}  <span class="asterix"> * </span> </label>
											<div class="col-md-6">
												<label class='radio radio-inline'>
												<input type='radio' name='formula' class="formula" id="formula" value ='0' required @if($row['formula'] == '0') checked="checked" @endif > {{ Lang::get('core.tour') }} </label>
												<label class='radio radio-inline'>
												<input type='radio' name='formula' class="formula" id="formula" value ='1' required @if($row['formula'] == '1') checked="checked" @endif > {{ Lang::get('core.package') }} </label>
											</div>
											<div class="col-md-2">
											</div>
										</div>
										<div class="" id="tour_part" style="display:none;">
											<div class="form-group  " >
											<label for="TourcategoriesID" class=" control-label col-md-4 text-left">{{ Lang::get('core.tourcategory') }}</label>
											<div class="col-md-4">
												<select name='tourcategoriesID' rows='5' id='tourcategoriesID' class='select2'>
												</select>
											 </div>
											 <div class="col-md-2">
											 </div>
											</div>
											<div class="form-group  " >
											<label for="TourID" class=" control-label col-md-4 text-left">{{ Lang::get('core.tour') }}</label>
											<div class="col-md-4">
												<select name='tourID' rows='5' id='tourID' class='select2'>
												</select>
											 </div>
											</div>
											<div class="form-group">
											<label for="Tour Date" class=" control-label col-md-4 text-left"> {{Lang::get('core.tourdate')}} <span class="asterix"> * </span></label>
											<div class="col-md-6">
											  <select name='tourdateID' rows='5' id='tourdateID' class='select2' required></select>
											 </div>
											 <div class="col-md-2">
											 </div>
										  </div>
										</div>

										<div class="" id="package_part" style="display:none;">
											<div class="form-group  " >
											<label for="TourcategoriesID" class=" control-label col-md-4 text-left">{{ Lang::get('core.packagecategory') }}</label>
											<div class="col-md-4">
												<select name='tourcategoriesID' rows='5' id='packagecategoriesID' class='select2'>
												</select>
											 </div>
											 <div class="col-md-2">
											 </div>
											</div>
											<div class="form-group  " >
											<label for="TourID" class=" control-label col-md-4 text-left">{{ Lang::get('core.package') }}</label>
											<div class="col-md-4">
												<select name='tourID' rows='5' id='packageID' class='select2'>
												</select>
											 </div>
											</div>
											<div class="form-group  " >
											<label for="roomfeatureID" class=" control-label col-md-4 text-left">{{ Lang::get('core.roomtype') }} <span class="asterix"> * </span></label>
											<div class="col-md-4">
												<select name='roomfeatureID' rows='5' id='roomfeatureID' class='select2'>
												</select>
											 </div>
											</div>
											<div class="form-group  " >
												<label for="airlineID" class=" control-label col-md-4 text-left">{{ Lang::get('core.flighttype') }} <span class="asterix"> * </span></label>
												<div class="col-md-4">
													<select name='airlineID' rows='5' id='airlineID' class='select2' disabled>
													</select>
												</div>
											</div>
											<div class="form-group  " >
												<label for="ticketID" class=" control-label col-md-4 text-left">{{ Lang::get('core.class') }} <span class="asterix"> * </span></label>
												<div class="col-md-4">
													<select name='ticketID' rows='5' id='ticketID' class='select2' >
													</select>
												</div>
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
packages = [];
packages_combo = [];
$(document).ready(function() {
	@foreach($packages as $package)
		packages['{{$package->packageID}}']=['{{$package->tourID}}','{{$package->tourcategoriesID}}'];
	@endforeach
	console.log("package=======");
	console.log(packages);
	console.log("package=======");
	@if($row['formula']==1)
		$("#tour_part").hide();
		$("#tourdateID").prop("required", false);
		$("#package_part").show();
		$("#roomfeatureID").prop("required", true);
		$("#ticketID").prop("required", true);
	@else
		$("#tour_part").show();
		$("#tourdateID").prop("required", true);
		$("#package_part").hide();
		$("#roomfeatureID").prop("required", false);
		$("#ticketID").prop("required", false);
	@endif
		$("#tourcategoriesID").jCombo("{!! url('booktour/comboselect?filter=def_tour_categories:tourcategoriesID:tourcategoryname&limit=WHERE:status:=:1') !!}",
		{  selected_value : '{{ $row["tourcategoriesID"] }}' });
		$("#packagecategoriesID").jCombo("{!! url('booktour/comboselect?filter=def_tour_categories:tourcategoriesID:tourcategoryname&limit=WHERE:status:=:1') !!}",
		{  selected_value : '{{ $row["tourcategoriesID"] }}' });
		$("#tourID").jCombo("{!! url('booktour/comboselect?filter=tours:tourID:tour_name&limit=WHERE:status:=:1') !!}&parent=tourcategoriesID:",
		{  parent: '#tourcategoriesID', selected_value : '{{ $row["tourID"] }}' });
		$("#packageID").jCombo("{!! url('booktour/comboselect?filter=packages:packageID:package_name&limit=WHERE:status:=:1 and packageID NOT IN (SELECT a.`packageID` FROM `packages` a JOIN `book_tour` b ON b.`packageID`=a.`packageID` JOIN `bookings` c ON c.`bookingsID`=b.`bookingID` AND c.`travellerID`='.$travellerID.')') !!}&parent=tourcategoriesID:",
		{  parent: '#packagecategoriesID', selected_value : '{{ $row["packageID"] }}' });

		$("#roomfeatureID").jCombo("{!! url('booktour/comboselect?filter=room_features JOIN def_room_types ON room_features.roomtypeID=def_room_types.roomtypeID:roomfeatureID:room_type&limit=WHERE:status:=:1 AND seat>(SELECT COUNT(*) FROM book_tour WHERE roomfeatureID=room_features.`roomfeatureID`)') !!}&parent=packageID:",
		{  parent: '#packageID', selected_value : '{{ $row["roomfeatureID"] }}' });


		$("#tourdateID").jCombo("{!! url('booktour/comboselect?filter=tour_date:tourdateID:start|end&limit=WHERE:status:=:1') !!}&parent=tourID:",
		{  parent: '#tourID', selected_value : '{{ $row["tourdateID"] }}' });

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
			var removeUrl = '{{ url("booktour/removefiles?file=")}}'+$(this).attr('url');
			$(this).parent().remove();
			$.get(removeUrl,function(response){});
			$(this).parent('div').empty();
			return false;
		});

	var form = $('#booktourFormAjax');
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
	$(".formula").on("ifChecked", function(){
		if($(this).val()==1){
			$("#tour_part").hide();
			$("#tourdateID").prop("required", false);
			$("#package_part").show();
			$("#roomfeatureID").prop("required", true);
			$("#ticketID").prop("required", true);
		}else{
			$("#tour_part").show();
			$("#tourdateID").prop("required", true);
			$("#package_part").hide();
			$("#roomfeatureID").prop("required", false);
			$("#ticketID").prop("required", false);
		}
	});
	function changeTour(){
		if($("#package").val()!=""){
			tourID = packages[$("#package").val()][0];
			tourcategoriesID = packages[$("#package").val()][1];
			$('#tourcategoriesID').select2("val", tourcategoriesID);
			setTimeout(function(){
			  $('#tourID').select2("val", tourID);
			}, 1000);
		}
	}
	$("#airlineID").change(function(){
		var packageID = $("#packageID").val();
		var airlineID = $("#airlineID").val();
		console.log(packageID);
		$.ajax({
			url:'{{route("tickets_of_package")}}',
			type: 'GET',
			data: { 
				packageID:packageID,
				airlineID:airlineID,
				csrf:$('meta[name="csrf-token"]').attr('content')
			},
			dataType: 'json',
			success: function(data){
				if(data.length==0){
					$("#ticketID").html("");
					$("#ticketID").prop("disabled", true);
				}
				else{
					$("#ticketID").html("<option>-- Please Select --</option>");
					for(var i=0; i<data.length; i++){
						var class_value = data[i]['class'];
						var class_data = "";
						if(class_value == '1'){
							class_data = '<?php echo __('core.economy'); ?>';
						}else if(class_value == '2'){
							class_data = '<?php echo __('core.premiumeconomy'); ?>';

						}else if(class_value == '3'){
							class_data = '<?php echo __('core.business'); ?>';

						}else if(class_value == '4'){
							class_data = '<?php echo  __('core.first'); ?>';

						}
							// $("#ticketID").append("<option value='"+data[i]['ticketID']+"'>"+data[i]['airline']+"</option>");
							$("#ticketID").append("<option value='"+data[i]['ticketID']+"'>"+class_data+"</option>");

					}
					$("#ticketID").prop("disabled", false);
				}

			},
			error: function(data){
				console.log(data);
			}
		});

	});

	$("#packageID").change(function(){
		var packageID = $("#packageID").val();
		$.ajax({
			url:'{{route("airlines_of_package")}}',
			type: 'GET',
			data: { 
				packageID:packageID,
				csrf:$('meta[name="csrf-token"]').attr('content')
			}, 
			dataType: 'json',
			success: function(data){
				if(data.length==0){
					$("#airlineID").html("");
					$("#ticketID").html("");
					$("#airlineID").prop("disabled", true);
					$("#ticketID").prop("disabled", true);
				}
				else{
					$("#airlineID").html("<option>-- Please Select --</option>");
					for(var i=0; i<data.length; i++){
							// $("#ticketID").append("<option value='"+data[i]['ticketID']+"'>"+data[i]['airline']+"</option>");
							$("#airlineID").append("<option value='"+data[i]['airlineID']+"'>"+data[i]['airline']+"</option>");

					}
					$("#airlineID").prop("disabled", false);
				}

			},
			error: function(data){
				console.log(data);
			}
		});

	});
	</script>
