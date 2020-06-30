@extends('layouts.app')

@section('content')

    <section class="content-header">
      <h1> {{ Lang::get('core.hotels') }}</h1>
    </section>

  <div class="content">
      <div class="box box-primary">
	<div class="box-header with-border">
		<div class="box-header-tools pull-left" >
			<a href="{{ url($pageModule.'?return='.$return) }}" class="tips"  title="{{ Lang::get('core.btn_back') }}" ><i class="fa  fa-arrow-left fa-2x"></i></a>
		</div>
		<div class="box-header-tools " >
			@if(Session::get('gid') ==1)

			@endif
		</div>
	</div>
	<div class="box-body">
		<ul class="parsley-error-list">
			@foreach($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
		</ul>

		 {!! Form::open(array('url'=>'hotels/save?return='.$return, 'class'=>'form-horizontal','files' => true , 'parsley-validate'=>'','novalidate'=>' ')) !!}
        {!! Form::hidden('hotelID', $row['hotelID']) !!}

            				<div class="form-group  " >
										<label for="Hotel Picture" class=" control-label col-md-4 text-left"> {{ Lang::get('core.hotelpicture') }}</label>
										<div class="col-md-7">
                                            <div class="btn btn-primary btn-file"><i class="fa fa-camera fa-2x"></i> {{ Lang::get('core.selectfile') }}
                                                <input  type='file' name='image' id='image' @if($row['image'] =='') @endif style='width:150px !important;'  /></div>
					 	<div >
				        @if(file_exists('./uploads/images/'.$row['image']) && $row['image'] !='')
<span class="pull-left removeMultiFiles "  url="/uploads/images/{{$row['image']}}">
<i class="fa fa-trash-o fa-2x text-red " data-toggle="confirmation" data-title="{{Lang::get('core.rusure')}}" data-content="{{ Lang::get('core.youwanttodeletethis') }}" title="{{ Lang::get('core.deletethisimage') }}" ></i>
            </span>
                        {!! \App\Library\SiteHelpers::showUploadedFile($row['image'],'/uploads/images/') !!}
                        @endif

						</div>

										 </div>
									  </div><div class="form-group  " >
										<label for="Hotel Name" class=" control-label col-md-4 text-left"> {{ Lang::get('core.hotelname') }} <span class="asterix"> * </span></label>
										<div class="col-md-6">
										  <input  type='text' name='hotel_name' id='hotel_name' value='{{ $row['hotel_name'] }}'
						required     class='form-control ' />
										 </div>
									  </div>
									  <div class="form-group  " >
										<label for="Hotel Description" class=" control-label col-md-4 text-left"> {{ Lang::get('core.description') }} <span class="asterix"> * </span></label>
										<div class="col-md-6">
										  <textarea name='hotel_description' rows='3' id='hotel_description' class='form-control '
				         required  >{{ $row['hotel_description'] }}</textarea>
										 </div>
									  </div>
									  <div class="form-group  " >
										<label for="Hotel Category" class=" control-label col-md-4 text-left"> {{ Lang::get('core.category') }} <span class="asterix"> * </span></label>
										<div class="col-md-2">
										  <select name='hotelcategoryID' rows='5' id='hotelcategoryID' class='select2 ' required  ></select>
										 </div>
										<label for="Hotel Type" class=" control-label col-md-2 text-left"> {{ Lang::get('core.type') }} <span class="asterix"> * </span></label>
										<div class="col-md-2">
										  <select name='hoteltypeID' rows='5' id='hoteltypeID' class='select2 ' required  ></select>
										 </div>
									  </div>
                <div class="form-group  " >
										<label for="Country" class=" control-label col-md-4 text-left"> {{ Lang::get('core.country') }} <span class="asterix"> * </span></label>
										<div class="col-md-2">
										  <select name='countryID' rows='5' id='countryID' class='select2 ' required  ></select>
										 </div>
										<label for="City" class=" control-label col-md-2 text-left"> {{ Lang::get('core.city') }} <span class="asterix"> * </span></label>
										<div class="col-md-2">
										  <select name='cityID' rows='5' id='cityID' class='select2 ' required  ></select>
										 </div>
									  </div>
                <div class="form-group  " >
										<label for="Address" class=" control-label col-md-4 text-left">{{ Lang::get('core.address') }}<span class="asterix"> * </span></label>
										<div class="col-md-6">
										  <textarea name='address' rows='2' id='address' class='form-control '
				         required  >{{ $row['address'] }}</textarea>
										 </div>
									  </div>
   <fieldset> <Legend>{{ Lang::get('core.contactinformation') }}</Legend>

                <div class="form-group  " >
										<label for="Web Site" class=" control-label col-md-4 text-left">{{ Lang::get('core.website') }}<span class="asterix"> * </span></label>
										<div class="col-md-6">
										  <input  type='text' name='web_site' id='web_site' value='{{ $row['web_site'] }}'
						required     class='form-control ' />
										 </div>
									  </div>
            				 <div class="form-group  " >
										<label for="Hotel Email" class=" control-label col-md-4 text-left"> {{ Lang::get('core.email') }} <span class="asterix"> * </span></label>
										<div class="col-md-6">
										  <input  type='text' name='hotel_email' id='hotel_email' value='{{ $row['hotel_email'] }}'
						required     class='form-control ' />
										 </div>
									  </div>
									  <div class="form-group  " >
										<label for="Phone" class=" control-label col-md-4 text-left"> {{ Lang::get('core.phone') }} <span class="asterix"> * </span></label>
										<div class="col-md-4">
										  <input  type='text' name='hotel_phone' id='hotel_phone' value='{{ $row['hotel_phone'] }}'
						required     class='form-control ' />
										 </div>
									  </div>
									  <div class="form-group  " >
										<label for="Fax" class=" control-label col-md-4 text-left"> {{ Lang::get('core.fax') }} </label>
										<div class="col-md-4">
										  <input  type='text' name='hotel_fax' id='hotel_fax' value='{{ $row['hotel_fax'] }}'
						     class='form-control ' />
										 </div>
									  </div>

								<div class="form-group  " >
										<label for="Person In Contact" class=" control-label col-md-4 text-left"> {{ Lang::get('core.personincontact') }} </label>
										<div class="col-md-4">
										  <input  type='text' name='person_in_contact' id='person_in_contact' value='{{ $row['person_in_contact'] }}'
						     class='form-control ' />
										 </div>
									  </div>
									  <div class="form-group  " >
										<label for="Contact Phone" class=" control-label col-md-4 text-left"> {{ Lang::get('core.phone') }} </label>
										<div class="col-md-4">
										  <input  type='text' name='contact_phone' id='contact_phone' value='{{ $row['contact_phone'] }}'
						     class='form-control ' />
										 </div>
									  </div>
									  <div class="form-group  " >
										<label for="Contact Email" class=" control-label col-md-4 text-left"> {{ Lang::get('core.email') }} </label>
										<div class="col-md-6">
										  <input  type='text' name='contact_email' id='contact_email' value='{{ $row['contact_email'] }}'
						     class='form-control ' />
										 </div>
									  </div>

        </fieldset>
   <fieldset> <Legend>{{ Lang::get('core.featuresfacilities') }}</Legend>

            									  <div class="form-group  " >
										<label for="Check in" class=" control-label col-md-4 text-left"> {{ Lang::get('core.checkin') }}</label>
										<div class="col-md-2">
										  <select name='checkin' rows='5' id='checkin' class='select2 '   ></select>
										 </div>
										<label for="Check out" class=" control-label col-md-2 text-left"> {{ Lang::get('core.checkout') }} </label>
										<div class="col-md-2">
										  <select name='checkout' rows='5' id='checkout' class='select2 '   ></select>
										 </div>
									  </div>
									  <div class="form-group  " >
										<label for="Facilities" class=" control-label col-md-4 text-left"> {{ Lang::get('core.facilities') }} <span class="asterix"> * </span></label>
										<div class="col-md-6">
										  <select name='facilities[]' multiple rows='5' id='facilities' class='select2 ' required  ></select>
										 </div>
									  </div>
									  <div class="form-group  " >
										<label for="Payment Options" class=" control-label col-md-4 text-left"> {{ Lang::get('core.paymentoptions') }} <span class="asterix"> * </span></label>
										<div class="col-md-6">
										  <select name='paymentoptions[]' multiple rows='5' id='paymentoptions' class='select2 ' required  ></select>
										 </div>
									  </div>
									  <div class="form-group  " >
										<label for="Terms & Conditions" class=" control-label col-md-4 text-left"> {{ Lang::get('core.tandc') }} </label>
										<div class="col-md-6">
										  <textarea name='policyandterms' rows='3' id='policyandterms' class='form-control '
				           >{{ $row['policyandterms'] }}</textarea>
										 </div>

									  </div>
       <div class="form-group  " >
										<label for="Similar Hotels" class=" control-label col-md-4 text-left"> {{ Lang::get('core.similarhotels') }}</label>
										<div class="col-md-6">
										  <select name='similarhotels[]' multiple rows='5' id='similarhotels' class='select2 '   ></select>
										 </div>
									  </div>

        </fieldset>
   <fieldset> <Legend>{{ Lang::get('core.socialmedia') }}</Legend>

            									  <div class="form-group  " >
										<label for="Tripadvisor" class=" control-label col-md-4 text-left"> Tripadvisor </label>
										<div class="col-md-6">
										  <input  type='text' name='tripadvisor' id='tripadvisor' value='{{ $row['tripadvisor'] }}'
						     class='form-control ' />
										 </div>
									  </div>
									  <div class="form-group  " >
										<label for="Facebook" class=" control-label col-md-4 text-left"> Facebook </label>
										<div class="col-md-6">
										  <input  type='text' name='facebook' id='facebook' value='{{ $row['facebook'] }}'
						     class='form-control ' />
										 </div>
									  </div>
									  <div class="form-group  " >
										<label for="Twitter" class=" control-label col-md-4 text-left"> Twitter </label>
										<div class="col-md-6">
										  <input  type='text' name='twitter' id='twitter' value='{{ $row['twitter'] }}'
						     class='form-control ' />
										 </div>
									  </div>
									  <div class="form-group  " >
										<label for="Instagram" class=" control-label col-md-4 text-left"> Instagram </label>
										<div class="col-md-6">
										  <input  type='text' name='instagram' id='instagram' value='{{ $row['instagram'] }}'
						     class='form-control ' />
										 </div>
									  </div>

                <div class="form-group  " >
										<label for="Status" class=" control-label col-md-4 text-left"> {{ Lang::get('core.status') }} <span class="asterix"> * </span></label>
										<div class="col-md-6">

					<label class='radio radio-inline'>
					<input type='radio' name='status' value ='1' required @if($row['status'] == '1') checked="checked" @endif > {{ Lang::get('core.fr_mactive') }} </label>
					<label class='radio radio-inline'>
					<input type='radio' name='status' value ='0' required @if($row['status'] == '0') checked="checked" @endif > {{ Lang::get('core.fr_minactive') }} </label>
										 </div>
									  </div>
        </fieldset>

				  <div class="form-group">
					<label class="col-sm-4 text-right">&nbsp;</label>
					<div class="col-sm-8">
					<button type="submit" name="apply" class="btn btn-info btn-sm" > {{ Lang::get('core.sb_apply') }}</button>
					<button type="submit" name="submit" class="btn btn-primary btn-sm" > {{ Lang::get('core.sb_save') }}</button>
					<button type="button" onclick="location.href='{{ URL::to('hotels?return='.$return) }}' " class="btn btn-danger btn-sm ">  {{ Lang::get('core.sb_cancel') }} </button>
					</div>
				  </div>

		 {!! Form::close() !!}
	</div>
</div>
</div>
		 			<div style="clear:both"></div>

   <script type="text/javascript">
	$(document).ready(function() {

		$("#hotelcategoryID").jCombo("{!! url('hotels/comboselect?filter=def_hotel_categories:hotelcategoryID:category_name&limit=WHERE:status:=:1') !!}",
		{  selected_value : '{{ $row["hotelcategoryID"] }}' });

		$("#hoteltypeID").jCombo("{!! url('hotels/comboselect?filter=def_hotel_type:hoteltypeID:type_name&limit=WHERE:status:=:1') !!}",
		{  selected_value : '{{ $row["hoteltypeID"] }}' });

		$("#countryID").jCombo("{!! url('hotels/comboselect?filter=def_country:countryID:country_name') !!}",
		{  selected_value : '{{ $row["countryID"] }}' });

		$("#cityID").jCombo("{!! url('hotels/comboselect?filter=def_city:cityID:city_name') !!}&parent=countryID:",
		{  parent: '#countryID', selected_value : '{{ $row["cityID"] }}' });

		$("#checkin").jCombo("{!! url('hotels/comboselect?filter=def_time_slots:timeslotID:time') !!}",
		{  selected_value : '{{ $row["checkin"] }}' });

		$("#checkout").jCombo("{!! url('hotels/comboselect?filter=def_time_slots:timeslotID:time') !!}",
		{  selected_value : '{{ $row["checkout"] }}' });

		$("#facilities").jCombo("{!! url('hotels/comboselect?filter=def_hotel_facilities:hotelfacilityID:facility&limit=WHERE:status:=:1') !!}",
		{  selected_value : '{{ $row["facilities"] }}' });

		$("#paymentoptions").jCombo("{!! url('hotels/comboselect?filter=def_payment_types:paymenttypeID:payment_type&limit=WHERE:status:=:1') !!}",
		{  selected_value : '{{ $row["paymentoptions"] }}' });

		$("#similarhotels").jCombo("{!! url('hotels/comboselect?filter=hotels:hotelID:hotel_name&limit=WHERE:status:=:1') !!}",
		{  selected_value : '{{ $row["similarhotels"] }}' });


		$('.removeMultiFiles').on('click',function(){
			var removeUrl = '{{ url("hotels/removefiles?file=")}}'+$(this).attr('url');
			$(this).parent().remove();
			$.get(removeUrl,function(response){});
			$(this).parent('div').empty();
			return false;
		});

    $('[data-toggle=confirmation]').confirmation({
    rootSelector: '[data-toggle=confirmation]',
    container: 'body'
  });


	});
</script>


@stop
