@extends('layouts.app')

@section('content')
<?php
use Carbon\Carbon;
?>
<style>

</style>
    <section class="content-header">
      <h1> {{ Lang::get('core.packages') }}</h1>
    </section>

  <div class="content">

<div class="box box-primary">
	<div class="box-header with-border">
		<div class="box-header-tools pull-left" >
			<a href="{{ url($pageModule.'?return='.$return) }}" class="tips"  title="{{ Lang::get('core.btn_back') }}" ><i class="fa  fa-arrow-left fa-2x"></i></a>
		</div>
	</div>
	<div class="box-body">

		<ul class="parsley-error-list">
			@foreach($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
		</ul>
		 {!! Form::open(array('url'=>'packages/save?return='.$return, 'class'=>'form-horizontal','files' => true, 'parsley-validate'=>'' ,'novalidate'=>' ')) !!}
     <!-- 'parsley-validate'=>'', -->
  		{!! Form::hidden('packageID', $row['packageID']) !!}
              <div class="form-group" >
                  <label for="Tour Category" class=" control-label col-md-4 text-left"> {{ Lang::get('core.packagecategory') }} <span class="asterix"> * </span></label>
  									<div class="col-md-4">
  									  <select name='tourcategoriesID' rows='5' id='tourcategoriesID' class='select2 ' required></select>
  									 </div>
  								  </div>
									  <div class="form-group" >
										<label for="Tour Name" class=" control-label col-md-4 text-left"> {{ Lang::get('core.packagename') }} <span class="asterix"> * </span></label>
										<div class="col-md-4">
                      <input  type='text' name='package_name' id='package_name' value='{{ $row['package_name'] }}' required class='form-control ' />
										 </div>
									  </div>
									  <div class="form-group" >
										<label for="Tour Code" class=" control-label col-md-4 text-left"> {{ Lang::get('core.packagecode') }} <span class="asterix"> * </span></label>
										<div class="col-md-4">
										  <input  type='text' name='tour_code' id='tour_code' value='{{ $row['tour_code'] }}' required class='form-control ' />
										 </div>
									  </div>
									  <div class="form-group" >
                      <label for="Start Date" class="control-label col-md-4 text-left"> {{ Lang::get('core.start') }} <span class="asterix"> * </span></label>
                        <div class="col-md-2">
                            <div class="input-group" style="width:150px !important;" id="dpd1">
                              {!! Form::text('start', $row['start'], array('class'=>'form-control date', 'required'=>'required', 'autocomplete'=>'off')) !!}
                              <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                        <div class="col-md-2">
                          <div class="form-group">
                            <label for="End Date" class=" control-label col-md-4 text-left"> {{ Lang::get('core.end') }} <span class="asterix"> * </span></label>
                            <div class="col-md-7">
                              <div class="input-group" style="width:150px !important;" id="dpd2">
                                {!! Form::text('end', $row['end'],array('class'=>'form-control date', 'required'=>'required', 'autocomplete'=>'off')) !!}
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                              </div>
                            </div>
                          </div>
                        </div>
									  </div>
                  <div class="form-group" >
                    <label for="Tour Category" class=" control-label col-md-4 text-left"> {{ Lang::get('core.flight') }} <span class="asterix"> * </span></label>
                      <div class="col-md-4">
                          <select name="flight[]" multiple="" rows="5" id="flight"
                                  class="select2 parsley-validated selection_disabled" required
                                  tabindex="-1" aria-hidden="true">
                              <option value="">-- Please Select --</option>
                              @php
                                  $ticketIds = json_decode($row['flight']);
                              @endphp
                              @foreach($tickets as $ticket)
                                  <option {{ $ticketIds?(in_array($ticket->ticketID,$ticketIds)?'selected':''):''}} value="{{$ticket->ticketID}}">
                                  {{"ID:". $ticket->ticketID}}
                                  </option>
                              @endforeach
                          </select>
                      </div>
                      <div class="col-md-1" style="text-align: end;">
                        <button type="button" name="button" class="btn btn-success" id="tickets_modal_btn" data-toggle="modal" data-target="#tickets_modal">{{ Lang::get('core.choose') }}</button>
                      </div>
                  </div>
                  <div class="form-group" >
									<label for="total_capacity" class=" control-label col-md-4 text-left"> {{ Lang::get('core.capacity') }} <span class="asterix"> * </span></label>
  									 <div class="col-md-2">
  									    <input  type='text' name='total_capacity' id='total_capacity' value='{{ $row['total_capacity'] }}' required class='form-control ' />
  									 </div>
                     <div class="col-md-3">
                        <label for="parts" class=" control-label col-md-4 text-left"> {{ Lang::get('core.parts') }}</label>
  										  <input  type='text' name='parts' id='parts' value='{{ $parts->count() }}' required class='form-control ' />
  									 </div>
								  </div>
                <div class="form-group" >
										<label for="Featured" class=" control-label col-md-4 text-left"> {{ Lang::get('core.featured') }} </label>
										<div class="col-md-1">
										  <?php $featured = explode(",",$row['featured']); ?>
                      <label class='checked checkbox-inline'>
                      <input type='checkbox' name='featured' value ='1'   class=''
                      @if(in_array('1',$featured))checked @endif
                      />  </label>
										 </div>
                     <label for="Definite Departure" class=" control-label col-md-2 text-left"> {{ Lang::get('core.definitedeparture') }} </label>
										<div class="col-md-1">
										  <?php $definite_departure = explode(",",$row['definite_departure']); ?>
                      <label class='checked checkbox-inline'>
                        <input type='checkbox' name='definite_departure' value ='2'   class=''
                        @if(in_array('2',$definite_departure))checked @endif
                        /> </label>
										 </div>
									  </div>

                  <div class="row" id="part_lists">
                  @for($index = 0; $index < 5; $index++)
                  <fieldset id="part_{{$index}}" style="display:none;"><legend>{{ Lang::get('core.part') }}_{{$index+1}}</legend>
                    <input type="text" name="tour_feature_id[]" id="tour_feature_id_{{$index}}" style="display:none">
                    <div class="form-group" >
          						<label for="Country" class=" control-label col-md-4 text-left"> {{ Lang::get('core.country') }} </label>
          						<div class="col-md-5">
          						  <select name='countryID[]' rows='5' id='countryID_{{$index}}' class='select2 country'></select>
        						  </div>
        					  </div>
                    <div class="form-group" >
            					<label for="City" class="control-label col-md-4 text-left"> {{ Lang::get('core.city') }} </label>
            					<div class="col-md-5">
            					  <select name='cityID[]' rows='5' id='cityID_{{$index}}' class='select2 city'   ></select>
            					</div>
        					  </div>
                  
        					  <div class="form-group" >
        						  <label for="Transfer" class=" control-label col-md-4 text-left"> {{ Lang::get('core.vehicletypes') }} </label>
                      <div class="col-md-5" style="padding: 0;">
                        <div class="row">
                          <div class="col-md-3">
                            <select name='vehicleID[]' rows='5' id='vehicleID_{{$index}}' class='select2 vehicle'></select>
                          </div>
                          <label for="Hotel" class=" control-label col-md-1 text-left"> {{ Lang::get('core.hotel') }}  </label>
                          <div class="col-md-3">
                            <select name='hotelID[]' rows='5' id='hotelID_{{$index}}' onchange="onchangehotel({{$index}})" class='select2 hotelID'></select>
                          </div>
                          <label for="Hotel" class=" control-label col-md-2 text-left"> {{ Lang::get('core.nights') }}  </label>
                          <div class="col-md-3">
                            <input  type='text' name='total_nights[]' id='total_nights_{{$index}}' value="1" class='form-control ' />
                          </div>
                        </div>
                      </div>
        					  </div>
                    <div class="form-group" >
                      <label for="Tour Category" class=" control-label col-md-4 text-left"> {{ Lang::get('core.roomtype') }} </label>
                        <div class="col-md-4">
                            <select name="roomTypes_{{$index}}[]" multiple="" rows="5" id="roomTypes_{{$index}}"
                                    class="select2 parsley-validated selection_disabled"
                                    tabindex="-1" aria-hidden="true">
                                <option value="">-- Please Select --</option>
                                @php
                                  $roomtypes_id = json_decode($row['roomtypes']);
                                  
                              @endphp
                             
                                @foreach($roomTypes as $roomType)
                                    <option {{ $roomtypes_id[$index]?(in_array($roomType->roomtypeID,$roomtypes_id[$index])?'selected':''):''}} value="{{$roomType->roomtypeID}}">
                                      <!-- {{$loop->index+1}} -->
                                      {{$roomType->room_type}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!-- <input  type='hidden' name='room_data[]' id='room_data{{$index}}' value="" class='form-control ' /> -->
                        <div class="col-md-1" style="text-align: end;">
                          <button type="button" name="button" class="btn btn-success" onclick="saveCurrentPartNumber({{$index}})" id="flight_modal_btn" data-toggle="modal" data-target="#roomTypes_modal">{{ Lang::get('core.choose') }}</button>
                        </div>
                    </div>
                  </fieldset>
                  @endfor
                  </div>
                  <div class="form-group" >
                    <label for="Remarks" class=" control-label col-md-4 text-left"> {{ Lang::get('core.description') }}*</label>
                    <div class="col-md-6">
                      <textarea name='remarks' rows='5' id='remarks' class='form-control editor' required>{{ $row['remarks'] }}</textarea>
                    </div>
                  </div>
                  <div class="form-group" >
										<label for="Tour Inclusions" class=" control-label col-md-4 text-left"> {{ Lang::get('core.included') }}</label>
										<div class="col-md-6">
										  <select name='inclusions[]' multiple rows='5' id='inclusions' class='select2 '   ></select>
										 </div>
										 <div class="col-md-2">
										 </div>
									  </div>
									  <div class="form-group" >
										<label for="Similar Tours" class=" control-label col-md-4 text-left"> Similar Package </label>
										<div class="col-md-6">
										  <select name='similartours[]' multiple rows='5' id='similartours' class='select2 '   >

                      </select>
										 </div>
										 <div class="col-md-2">
										 </div>
									  </div>
                    <div class="row">
                      @php
                        $index = 0;
                      @endphp
                      @foreach($roomfeatures as $roomfeature)
                          <div id="room_feature_{{$index}}" class="form-group" style="display:none" >
                            <label for="room_type" class=" control-label col-md-4 text-left"> {{ Lang::get('core.room_type') }}</label>
                            <div class="col-md-2">
                              <select name='room_typeID[]' rows='5' id='room_typeID_{{$index}}' class='select2 room_typeID'>
                                <option value>-- Please Select --</option>
                              </select>
                            </div>
                            <div class="col-md-2">
                              <div class="row">
                                <div class="col-md-4">
                                  <label for="Cost" class=" control-label text-left"> {{ Lang::get('core.cost') }} <span class="asterix"> * </span></label>
                                </div>
                                <div class="col-md-8">
                                  <input  type='text' name='cost[]' id='cost_{{$index}}' value="{{$roomfeature->cost}}" class='form-control ' />
                                </div>
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="row">
                                <div class="col-md-4">
                                  <label for="seat" class=" control-label text-left"> {{ Lang::get('core.seat') }} <span class="asterix"> * </span></label>
                                </div>
                                <div class="col-md-8">
                                  <input  type='text' name='seat[]' id='seat_{{$index}}' value="{{$roomfeature->seat}}" class='form-control ' />
                                </div>
                              </div>
                            </div>
                         </div>
                        <input name="room_feature_id[]" value="{{$roomfeature->roomfeatureID}}" style="display:none"/>
                        @php
                          $index ++;
                        @endphp
                    @endforeach
                    @for($i=$index; $i<5; $i++)
                        <div id="room_feature_{{$i}}" class="form-group" style="display:none" >
                          <label for="room_type" class=" control-label col-md-4 text-left"> {{ Lang::get('core.room_type') }}</label>
                          <div class="col-md-2">
                            <select name='room_typeID[]' rows='5' id='room_typeID_{{$i}}' class='select2 room_typeID' >
                              <option value>-- Please Select --</option>
                            </select>
                          </div>
                          <div class="col-md-2">
                            <div class="row">
                              <div class="col-md-4">
                                <label for="Cost" class=" control-label text-left"> {{ Lang::get('core.cost') }} <span class="asterix"> * </span></label>
                              </div>
                              <div class="col-md-8">
                                <input  type='text' name='cost[]' id='cost_{{$i}}' value="0" class='form-control'/>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="row">
                              <div class="col-md-4">
                                <label for="seat" class=" control-label text-left"> {{ Lang::get('core.seat') }} <span class="asterix"> * </span></label>
                              </div>
                              <div class="col-md-8">
                                <input  type='text' name='seat[]' id='seat_{{$i}}' value="0" class='form-control'/>
                              </div>
                            </div>
                          </div>
                       </div>
                      <input type="hidden" name="room_feature_id[]" value="" />
                    @endfor
                      <!-- </div> -->
                      <!-- <div class="col-md-1"> -->
                        <div class="col-md-offset-4" style="float:left">
                          <input type="hidden" name="counter" id="counter" value="{{$roomfeatures->count()}}"/>
                          <a href="javascript:void(0)" id="addRoomFeature" class="btn btn-xs btn-primary" ><i class="fa fa-plus-square fa-2x tips" title="{{ Lang::get('core.cost') }}" ></i></a>
                          <a href="javascript:void(0)" id="removeRoomFeature" class="btn btn-xs btn-primary" ><i class="fa fa-minus-square fa-2x tips" title="{{ Lang::get('core.cost') }}" ></i></a>
                        </div>
                    <!-- </div> -->
                    </div>
                    <div class="form-group" id="full_capacity_cost" style="display:none;">
                        <label for="Error" class=" control-label col-md-4 text-left"></label>
                        <div class="col-md-6">
                        <p >Full Capacity!</p>
                        </div>
                        <div class="col-md-2">
                        </div>
									  </div>
									  <div class="form-group" >
										<label for="Payment Options" class=" control-label col-md-4 text-left"> {{ Lang::get('core.paymentoptions') }} <span class="asterix"> * </span></label>
										<div class="col-md-6">
										  <select name='payment_options[]' multiple rows='5' id='payment_options' class='select2 ' required  ></select>
										 </div>
										 <div class="col-md-2">
										 </div>
									  </div>
									  <div class="form-group" >
										<label for="Currency" class=" control-label col-md-4 text-left"> {{ Lang::get('core.currency') }} <span class="asterix"> * </span></label>
										<div class="col-md-2">
                          <select name='currencyID' rows='5' id='currencyID' class='select2 ' required  ></select>
										 </div>
										 <div class="col-md-2">
										 </div>
									  </div>
									  <div class="form-group" >
										<label for="Term & Conditions" class=" control-label col-md-4 text-left"> {{ Lang::get('core.tandc') }} <span class="asterix"> * </span></label>
										<div class="col-md-3">
										  <select name='policyandterms' rows='5' id='policyandterms' class='select2 ' required  ></select>
										 </div>
										 <div class="col-md-2">
										 </div>
									  </div>
                    <div class="form-group" >
										<label for="Tourimage" class=" control-label col-md-4 text-left"> Package Image <span class="asterix"> * </span></label>
  										 <div class="col-md-6">
                          <div class="btn btn-primary btn-file"><i class="fa fa-camera fa-2x"></i>
    										    <input  type='file' name='tourimage' id='tourimage' @if($row['packageimage'] =='') class='required' @endif style='width:150px !important;'  />
                          </div>
              					 	<div>
              						{!! \App\Library\SiteHelpers::showUploadedFile($row['packageimage'],'/uploads/images/') !!}
              						</div>
  										 </div>
										 <div class="col-md-2">
										 </div>
									  </div>
									  <div class="form-group" >
                      <label for="Gallery" class=" control-label col-md-4 text-left"> {{ Lang::get('core.gallery') }} </label>
                      <div class="col-md-6">
                        <a href="javascript:void(0)" class="btn btn-xs btn-primary pull-right" onclick="addMoreFiles('gallery')"><i class="fa fa-plus-square fa-2x tips" title="{{ Lang::get('core.addimage') }}" ></i></a>
                        <div class="galleryUpl">
                          <input  type='file' name='gallery[]'  />
                        </div>
                        <ul class="uploadedLists " style="margin-top: 10px;" >
                          <?php $cr= 0;
                          $row['gallery'] = explode(",",$row['gallery']);
                          ?>
                          @foreach($row['gallery'] as $files)
                            @if(file_exists('./uploads/images/'.$files) && $files !='')
                            <li id="cr-<?php echo $cr;?>" class="list-group-item">
                              <a href="{{ url('/uploads/images/'.$files) }}" target="_blank" class="tips" title="{{ $files }}" >{!! \App\Library\SiteHelpers::showUploadedFile($files,'/uploads/images/') !!}</a>
                              <span class="removeMultiFiles" rel="cr-<?php echo $cr;?>" url="/uploads/images/{{$files}}">
                              <i class="fa fa-trash-o fa-2x"                                data-toggle="confirmation"
                                              data-title="{{Lang::get('core.rusure')}}"
                                              data-content="{{ Lang::get('core.youwanttodeletethis') }}"></i></span>
                              <input type="hidden" name="currgallery[]" value="{{ $files }}"/>
                              <?php ++$cr;?>
                            </li>
                            @endif

                          @endforeach
                        </ul>
                      </div>
                      <div class="col-md-2">
                      </div>
					          </div>
            <div class="form-group" >
							<label for="Status" class=" control-label col-md-4 text-left"> {{ Lang::get('core.status') }} <span class="asterix"> * </span></label>
							<div class="col-md-7">

    					<label class='radio radio-inline'>
    					<input type='radio' name='status' value ='0' required @if($row['status'] == '0') checked="checked" @endif > {{ Lang::get('core.fr_minactive') }} </label>
    					<label class='radio radio-inline'>
    					<input type='radio' name='status' value ='1' required @if($row['status'] == '1') checked="checked" @endif > {{ Lang::get('core.fr_mactive') }} </label>
    					<label class='radio radio-inline'>
    					<!-- <input type='radio' name='status' value ='2' required @if($row['status'] == '2') checked="checked" @endif > {{ Lang::get('core.cancelled') }} </label> -->
						 </div>
					  </div>
			    <div style="clear:both"></div>
				  <div class="form-group">
  					<label class="col-sm-4 text-right">&nbsp;</label>
  					<div class="col-sm-8">
    					<button type="submit" name="apply" class="btn btn-info btn-sm" > {{ Lang::get('core.sb_apply') }}</button>
    					<button type="submit" name="submit" class="btn btn-primary btn-sm" > {{ Lang::get('core.sb_save') }}</button>
    					<button type="button" onclick="location.href='{{ URL::to('packages?return='.$return) }}' " class="btn btn-danger btn-sm ">  {{ Lang::get('core.sb_cancel') }} </button>
  					</div>
				  </div>
		 {!! Form::close() !!}
	</div>
</div> 

<div class="modal fade in" id="roomTypes_modal"  role="dialog" style=" padding-right: 16px;">
    <div class="modal-dialog" style="width:1200px;">
        <div class="modal-content">
            <div class="modal-header bg-default">
                <button type="button " class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">{{__('core.choose')}}</h4>
            </div>
            <div class="modal-body" id="edit-modal-content">
              <div class="table-responsive" style="min-height:300px; min-width:600px; padding-bottom:60px; border: none !important">
                @if(count($rowData)>=1)
                  <table class="table table-striped " id="ticketsTable">
                      <thead>
                      <tr>
                      <th width="10"> No </th>
                      <th width="30"></th>
                      <th>{{Lang::get('core.roomtype')}}</th>
                      <th width= "200">{{Lang::get('core.capacity')}}</th>
                      <th width="30">{{Lang::get('core.status')}}</th>
                      </tr>
                      </thead>
                      <tbody>
                      <?php $i = 0; foreach ($roomTypes as $row_rt) :
                          $id = $row_rt->roomtypeID;
                      ?>
                        <tr class="editable" id="form-{{ $row_rt->roomtypeID }}">
                        <td class="number"> <?php echo ++$i;?>  </td>
                        <td >
                       <input type="checkbox" class="roomtype_ids roomtype_ids{{ $row_rt->roomtypeID }}" name="roomtype_ids[]" value="<?php echo $row_rt->roomtypeID ;?>" />  </td>

                         <td data-values="{{ $row_rt->room_type }}" data-field="departing">
                            <span>{{$row_rt->room_type}}</span>
                         </td>
                         <td data-values="{{ $row_rt->capacity }}" data-field="arrFlightNO">
                            <span id="capacity{{ $row_rt->roomtypeID }}" >{{$row_rt->capacity}}</span>
                         </td>
                         <td data-values="{{ $row_rt->status }}" data-field="status">
                           @if($row_rt->status == '2')
                               <span class="label label-warning">{{ __('core.fr_pending') }}</span>
                           @elseif($row_rt->status == '1')
                               <span class="label label-success">{{ __('core.confirmed') }}</span>
                           @elseif($row_rt->status == '0')
                               <span class="label label-danger">{{ __('core.cancelled') }}</span>
                           @endif
                         </td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                  </table>
                @else

                <div style="margin:100px 0; text-align:center;">
                  <p> {{ Lang::get('core.norecord') }} </p>
                </div>

                @endif
                <div class="form-group">
                    <label class="col-sm-5 text-right" id="error_text">&nbsp;</label>
                    <div class="col-sm-7">
                        <button type="button" id="storeBtnRoomtype" class="btn btn-success btn-sm"> Save </button>
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"> Cancel </button>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
</div>

<div class="modal fade in" id="tickets_modal"  role="dialog" style=" padding-right: 16px;">
    <div class="modal-dialog" style="width:1200px;">
        <div class="modal-content">
            <div class="modal-header bg-default">
                <button type="button " class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">{{__('core.choose')}}</h4>
            </div>
            <div class="modal-body" id="edit-modal-content">
              <div class="table-responsive" style="min-height:300px; min-width:600px; padding-bottom:60px; border: none !important">
                @if(count($rowData)>=1)
                  <table class="table table-striped " id="ticketsTable">
                      <thead>
                      <tr>
                      <th width="10"> No </th>
                      <th width="30"></th>
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
                      <?php $i = 0; foreach ($rowData as $row_f) :
                          $id = $row_f->ticketID;
                      ?>
                        <tr class="editable" id="form-{{ $row_f->ticketID }}">
                        <td class="number"> <?php $i++; echo $row_f->ticketID ;?> </td>
                        <td ><input type="checkbox" class="ids" name="ids[]" value="<?php echo $row_f->ticketID ;?>" />  </td>
                       <td data-values="{{ $row_f->airlinesID }}" data-field="airlinesID">
                         @foreach($airlines as $a)
                             <?php
                             $airlineIds = $row_f->airlinesID;
                               if($airlineIds == $a->airlineID)
                                 echo "<span>".$a->airline??''."</span>";
                             ?>
                         @endforeach 
                       </td>
                       <td data-values="{{ $row_f->depairportID }}" data-field="depairportID">
                           @foreach($airports as $a)
                               <?php
                                 if($row_f->depairportID == $a->airportID){
                                   echo "<span>".$a->airport_name??''."</span>";
                                 }
                               ?>
                           @endforeach
                        </td>
                        <td data-values="{{ $row_f->arrairportID }}" data-field="arrairportID">
                            @foreach($airports as $a)
                                <?php
                                  if($row_f->arrairportID == $a->airportID){
                                    echo "<span>".$a->airport_name??''."</span>";
                                  }
                                ?>
                            @endforeach
                         </td>
                         <td data-values="{{ $row_f->departing }}" data-field="departing">
                            <span>{{$row_f->departing}}</span>
                         </td>
                         <td data-values="{{ $row_f->arrFlightNO }}" data-field="arrFlightNO">
                            <span>{{$row_f->arrFlightNO}}</span>
                         </td>
                         <td data-values="{{ $row_f->returning }}" data-field="returning">
                            <span>{{$row_f->returning}}</span>
                         </td>
                         <td data-values="{{ $row_f->depFlightNO }}" data-field="depFlightNO">
                            <span>{{$row_f->depFlightNO}}</span>
                         </td>
                         <td data-values="{{ $row_f->seats }}" data-field="seats">
                            <span>{{$row_f->seats}}</span>
                         </td>
                         <td data-values="{{ $row_f->available_seats }}" data-field="available_seats">
                            <span>{{$row_f->available_seats}}</span>
                         </td>
                         <td data-values="{{ $row_f->class }}" data-field="class">
                           @if($row_f->class == '1')
                               {{ __('core.economy')}}
                           @elseif($row_f->class == '2')
                               {{ __('core.premiumeconomy')}}
                           @elseif($row_f->class == '3')
                               {{ __('core.business') }}
                           @elseif($row_f->class == '4')
                               {{ __('core.first')}}
                           @endif
                         </td>
                         <td data-values="{{ $row_f->status }}" data-field="status">
                           @if($row_f->status == '2')
                               <span class="label label-warning">{{ __('core.fr_pending') }}</span>
                           @elseif($row_f->status == '1')
                               <span class="label label-success">{{ __('core.confirmed') }}</span>
                           @elseif($row_f->status == '0')
                               <span class="label label-danger">{{ __('core.cancelled') }}</span>
                           @endif
                         </td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                  </table>
                @else

                <div style="margin:100px 0; text-align:center;">
                  <p> {{ Lang::get('core.norecord') }} </p>
                </div>

                @endif
                <div class="form-group">
                    <label class="col-sm-5 text-right">&nbsp;</label>
                    <div class="col-sm-7">
                        <button type="button" id="storeBtn" class="btn btn-success btn-sm"> Save </button>
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"> Cancel </button>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
</div>
</div>

   <script type="text/javascript">

   var parts = [];
   var room_types = [];
   var rest_room_types = [];
   var pre_val = "";
   var pre_text = "";
   var currentPartNumber = 0;
   var room_types_all = [];
   var parts_all = [];
   var hotel_roomtypes = [];
   var hotel_Ids_length = "";
   var hotel_roomtype_cout = [];
   var hotel_roomtype_all = [];
   var roomtype_modal = [];
   var hotel_IDs = [];
   var hotelid = "";
   function onchangehotel(index){
    // var test = $(this).attr("id");
    console.log("changed hotel:"+index);

    if($("#select2-hotelID_"+index+"-results").html() != undefined){
      console.log("changed hotel:"+index);
      var nulls = [];
      $("#roomTypes_"+index).val(nulls);
      $("#roomTypes_"+index).select2({ width:"100%" , dropdownParent: $('#mmb-modal-content')});
    }
    
   }
   function saveCurrentPartNumber(index){
     currentPartNumber = index;
     console.log(currentPartNumber);
     var dataroom = $("#roomTypes_"+currentPartNumber).val();
     var hotelID = $("#hotelID_"+currentPartNumber).val();
     var roomtype_length = $(".roomtype_ids").val();
     var roomtype_length_count = room_types_all.length;
     var room_length = room_types_all.length;
     for( var i = 0; i< roomtype_length_count; i++){
      $("#form-"+room_types_all[i]['roomtypeID']).attr("style", "display: none;");
      $(".roomtype_ids"+room_types_all[i]['roomtypeID']).iCheck('uncheck');
      }
       
      for( var i = 1; i<= roomtype_length_count; i++){
        $(".roomtype_ids"+i).attr("class","roomtype_ids"+" "+"roomtype_ids"+i);
       
        }
      var temp_array = [];
      var merged_array = [];
      for( var y = 0; y< hotel_roomtype_all[hotelID].length; y++){
          for( var i = 0; i< roomtype_length_count; i++){
                        if(room_types_all[i]['roomtypeID'] !== hotel_roomtype_all[hotelID][y]){
                        }else{
                          temp_array.push(room_types_all[i]['roomtypeID']);
                          merged_array.concat(temp_array);
                              $("#form-"+room_types_all[i]['roomtypeID']).attr("style", "display: table-row;");
                              $(".roomtype_ids"+room_types_all[i]['roomtypeID']).attr("class", "modal_roomtype"+" "+"roomtype_ids"+" "+"roomtype_ids"+room_types_all[i]['roomtypeID']);
                        }
                  }
     }

   }
   @php
      $i = 0;
   @endphp
   @foreach($roomTypes as $roomType)
      room_types_all[{{$i}}] = [];
      room_types_all[{{$i}}]['roomtypeID'] = '{{$roomType->roomtypeID}}';
      room_types_all[{{$i}}]['room_type'] = '{{$roomType->room_type}}';
      room_types_all[{{$i}}]['capacity'] = '{{$roomType->capacity}}';
      @php
        $i++;
      @endphp
   @endforeach



    @for ($i = 0; $i < $hotel_Ids_length; $i++)
                          @php
                            $hotelid = $hotel_IDs[$i];
                            
                          @endphp
                          hotel_roomtype_all[{{$hotelid}}] = [];
                    @if($hotel_roomtype_cout[$i] == 0)
                    @else
                            @for($y = 0; $y < $hotel_roomtype_cout[$i]; $y++)
                                hotel_roomtype_all[{{$hotelid}}][{{$y}}] = '{{$hotel_roomtypes[$i][$y]->roomtypeID}}'; 
                                
                              
                              @endfor
                    @endif
                   
    @endfor
   rest_room_types = room_types.slice(0);
	$(document).ready(function() {
    setTimeout(function () {
      if($("#parts").val()>5){
        $("#parts").val(5);
      }
      var count = $("#parts").val()
      for( var i = 0; i < count; i++){
        $("#part_"+i).show();
      }
      for( var i = count; i< 5; i++){
        $("#part_"+i).hide();
      }
      if($("#counter").val()>5){
        $("#counter").val(5);
      }
      var counter = {{$roomfeatures->count()}}
      if(counter>0){
        @php
          $i = 0;
        @endphp
        @foreach($roomfeatures as $roomfeature)
          $("#room_feature_{{$i}}").show();
          for(room_type of room_types){
            if(room_type['roomtypeID'] != '{{$roomfeature->roomtypeID}}')
              $("#room_typeID_{{$i}}").append("<option value='"+room_type['roomtypeID']+"'>"+room_type['room_type']+"</option>");
            else {
              $("#room_typeID_{{$i}}").append("<option value='"+room_type['roomtypeID']+"' selected>"+room_type['room_type']+"</option>");
            }
          }
          change_room_feature($("#room_feature_{{$i}}"));
          @php
            $i++;
          @endphp
        @endforeach
        for( var i = counter; i< 5; i++){
          $("#room_feature_"+i).hide();
        }
      }
      else{
        $("#counter").val(1);
        console.log(room_types);
        for(room_type of room_types){
          $("#room_typeID_0").append("<option value='"+room_type['roomtypeID']+"'>"+room_type['room_type']+"</option>");
        }
        $("#room_feature_0").show();
        $("#room_typeID_0").prop('required',true);
      }
      $("#addMorecost").show();

    }, 2000);

    $(".selection_disabled").select2({ width:"100%" , dropdownParent: $('#mmb-modal-content')});

		$("#tourcategoriesID").jCombo("{!! url('tours/comboselect?filter=def_tour_categories:tourcategoriesID:tourcategoryname&limit=WHERE:status:=:1') !!}",
    {  selected_value : '{{ $row["tourcategoriesID"] }}' });
    $("#currencyID").jCombo("{!! url('extraexpenses/comboselect?filter=def_currency:currencyID:currency_sym|symbol&limit=WHERE:status:=:1') !!}",
    {  selected_value : '{{ $row["currencyID"] }}' });

    var index = 0;
    @foreach($parts as $part)
      $("#tour_feature_id_"+index).val({{$part->tour_feature_id}});
      // $("#part_start_"+index).val('{{$part->part_start}}');
      $("#total_nights_"+index).val('{{$part->total_nights}}');
      $("#countryID_"+index).jCombo("{!! url('packages/comboselect?filter=def_country:countryID:country_name') !!}",
      { selected_value: '{{$part->countryID}}' });
      $("#cityID_"+index).jCombo("{!! url('packages/comboselect?filter=def_city:cityID:city_name') !!}&parent=countryID:",
      { parent: '#countryID_'+index, selected_value: '{{$part->cityID}}' });
      $("#vehicleID_"+index).jCombo("{!! url('packages/comboselect?filter=def_vehicle:vehicleID:vehicle_name') !!}",
      { selected_value: '{{$part->vehicleID}}' });
      $("#hotelID_"+index).jCombo("{!! url('packages/comboselect?filter=hotels:hotelID:hotel_name') !!}",
      { selected_value: '{{$part->hotelID}}' });
      index += 1;
    @endforeach


    var index_cost = 0;
    @foreach($roomfeatures as $roomfeature)
      $("#room_feature_id_"+index_cost).val({{$roomfeature->roomfeatureID}});
      $("#cost_"+index_cost).val('{{$roomfeature->cost}}');
      $("#seat"+index_cost).val('{{$roomfeature->seat}}');
      index_cost += 1;
    @endforeach


    for(var i ={{$parts->count()}}; i< 5-{{$parts->count()}}; i++){
      $("#countryID_"+i).jCombo("{!! url('packages/comboselect?filter=def_country:countryID:country_name') !!}");
      $("#cityID_"+i).jCombo("{!! url('packages/comboselect?filter=def_city:cityID:city_name') !!}&parent=countryID:",
      {  parent: '#countryID_'+i });
      $("#vehicleID_"+i).jCombo("{!! url('packages/comboselect?filter=def_vehicle:vehicleID:vehicle_name') !!}");
      $("#hotelID_"+i).jCombo("{!! url('packages/comboselect?filter=hotels:hotelID:hotel_name') !!}");
    }

		$("#inclusions").jCombo("{!! url('tours/comboselect?filter=def_inclusions:inclusionID:inclusion') !!}",
    {  selected_value : '{{ $row["inclusions"] }}' });

    $("#similartours").jCombo("{!! url('packages/comboselect?filter=packages:packageID:package_name') !!}",
		{  selected_value : '{{ $row["similarpackage"] }}' });

		$("#payment_options").jCombo("{!! url('tours/comboselect?filter=def_payment_types:paymenttypeID:payment_type&limit=WHERE:status:=:1') !!}",
		{  selected_value : '{{ $row["payment_options"] }}' });

		$("#policyandterms").jCombo("{!! url('tours/comboselect?filter=termsandconditions:tandcID:title&limit=WHERE:status:=:1') !!}",
		{  selected_value : '{{ $row["policyandterms"] }}' });

    $("#currencyID").jCombo("{!! url('extraexpenses/comboselect?filter=def_currency:currencyID:currency_sym|symbol&limit=WHERE:status:=:1') !!}",
    {  selected_value : '{{ $row["currencyID"] }}' });

		$('.removeMultiFiles').on('click',function(){
			var removeUrl = '{{ url("packages/removefiles?file=")}}'+$(this).attr('url');
			$(this).parent().remove();
			$.get(removeUrl,function(response){});
			$(this).parent('div').empty();
			return false;
		});
	});
  $("#parts").change(function (){
    if($(this).val()>5){
      $(this).val(5);
    }
    var count = $(this).val();

    for( var i = 0; i < count; i++){
      $("#part_"+i).show();
    }
    for( var i = count; i< 5; i++){
      $("#part_"+i).hide();
    }
  });

  $(".date").datetimepicker({
      format: 'yyyy-mm-dd',
      startDate: '{{ Carbon::today()->format('Y-m-d') }}',
      autoclose:true ,
      minView:2 ,
      startView:2 ,
      todayBtn:true
  });
  $('#ticketsTable').DataTable({
    "paging": false,
    "lengthChange": false,
    "searching": true,
    "ordering": false,
    "info": true,
    "autoWidth": false
  });
  $("#storeBtn").click(function(){
    var ids = [];
    $('.ids').each(function(i, obj) {
      if(obj.checked)
        ids.push(obj.value);

    });
    $(".ids").iCheck('uncheck');
    $("#flight").val(ids);
    $("#flight").select2({ width:"100%" , dropdownParent: $('#mmb-modal-content')});
    $("#tickets_modal").modal('hide');
  });
  $("#storeBtnRoomtype").click(function(){
    var roomtype_ids = [];
    
    $('.modal_roomtype').each(function(i, obj) {
      if(obj.checked){
          roomtype_ids.push(obj.value);
      }
		console.log(obj);

    });
    var roomtype_capacity_modal = "";
    var slash = "0";
            for(var j = 0; j < roomtype_ids.length; j++){
                      var first_capacity_modal = $('#capacity'+roomtype_ids[j]).text();
                      for(var z = 0; z < roomtype_ids.length; z++){
                        var second_capacity_modal = $('#capacity'+roomtype_ids[z]).text();
                        if(roomtype_ids[j] == roomtype_ids[z]){
                        }else{
                          if(first_capacity_modal == second_capacity_modal){
                            var slash = first_capacity_modal;
                          }
                        }
                      }
            }
            var dupli_capacity = slash;
            // if(dupli_capacity !== "0"){
            //   $('#error_text').text("Capacity"+" "+dupli_capacity+" was duplicatied!" );
            //   var slash = "0";
            // }else{
                  $('#error_text').text("");
                  console.log(roomtype_ids);
                  $("#roomTypes_"+currentPartNumber).val(roomtype_ids);
                  $("#roomTypes_"+currentPartNumber).select2({ width:"100%" , dropdownParent: $('#mmb-modal-content')});
                  $("#roomTypes_modal").modal('hide');
            // }
  });

  $("#addRoomFeature").click(function(){
    var next = $("#counter").val();
    if(next < 5){
      for(room_type of rest_room_types){
        $("#room_typeID_"+next).append("<option value='"+room_type['roomtypeID']+"'>"+room_type['room_type']+"</option>");
      }
      $("#room_feature_"+next).show();
      $("#room_typeID_"+next).prop('required',true);
      $("#counter").val(parseInt(next) + 1);
    }
  });
  $("#removeRoomFeature").click(function(){
    var last = $("#counter").val()-1;
    if(last > 0){
      $("#room_feature_"+last).hide();
      $("#cost_"+last).val(0);
      $("#seat_"+last).val(0);
      $("#room_typeID_"+last).prop('required',false);
      if($("#room_feature_"+last).val() > 0){
        is_end = true;
        for(var i=0;i<rest_room_types.length; i++){
          if(rest_room_types[i]['roomtypeID'] > $("#room_typeID_"+last).val()){
            var room_type = [];
            room_type['roomtypeID'] = $("#room_typeID_"+last).val();
            room_type['room_type'] = $("#room_typeID_"+last).find("option:selected").text();
            rest_room_types.splice( i, 0, room_type );
            is_end = false;
            break;
          }
        }
        if(is_end){
          var room_type = [];
          room_type['roomtypeID'] = $("#room_typeID_"+last).val();
          room_type['room_type'] = $("#room_typeID_"+last).find("option:selected").text();
          rest_room_types.splice( rest_room_types.length, 0, room_type );
        }
      }
      $("#room_typeID_"+last).html("<option value>-- Please Select --</option>");
      $("#counter").val(last);
      for(var i = 0; i < $("#counter").val(); i++){
        var pre_seleted_val = $("#room_typeID_"+i).val();
        var pre_seleted_text = $("#room_typeID_"+i).find("option:selected").text();
        var temp_rest_room_types = rest_room_types.slice(0);
        if($("#room_typeID_"+i).val() > 0){
          is_end = true;
          for(var j=0;j<temp_rest_room_types.length; j++){
            if(temp_rest_room_types[j]['roomtypeID'] > $("#room_typeID_"+i).val()){
              var room_type = [];
              room_type['roomtypeID'] = $("#room_typeID_"+i).val();
              room_type['room_type'] = $("#room_typeID_"+i).find("option:selected").text();
              temp_rest_room_types.splice( j, 0, room_type );
              is_end = false;
              break;
            }
          }
          if(is_end){
            var room_type = [];
            room_type['roomtypeID'] = $("#room_typeID_"+i).val();
            room_type['room_type'] = $("#room_typeID_"+i).find("option:selected").text();
            temp_rest_room_types.splice( temp_rest_room_types.length, 0, room_type );
          }
        }
        $("#room_typeID_"+i).html("<option value>-- Please Select --</option>");
        for(var j = 0; j<temp_rest_room_types.length; j++ ){
          if(temp_rest_room_types[j]['roomtypeID'] != pre_seleted_val){
            $("#room_typeID_"+i).append("<option value='"+temp_rest_room_types[j]['roomtypeID']+"'>"+temp_rest_room_types[j]['room_type']+"</option>");
          }
          else{
            $("#room_typeID_"+i).append("<option value='"+temp_rest_room_types[j]['roomtypeID']+"' selected>"+temp_rest_room_types[j]['room_type']+"</option>");
          }
        }
      }
    }
  });
  $(".room_typeID").change(function(){
    change_room_feature($(this));
    console.log("++++++++");
    console.log($(this));
    console.log("++++++++");
  });
  function change_room_feature(element){
    var selected_types = [];
    rest_room_types = room_types.slice(0);
    for(var i=0;i<$("#counter").val();i++){
      selected_types.push($("#room_typeID_"+i).val());
    }
    for(var i=0; i<rest_room_types.length; i++){
      if(selected_types.includes(rest_room_types[i]['roomtypeID'].toString())){
          rest_room_types.splice(i, 1);
          i--;
      }
    }
    console.log("room_types");
    console.log(room_types);
    console.log("selected_types");
    console.log(selected_types);
    console.log("rest_room_types");
    console.log(rest_room_types);
    for(var i = 0; i < $("#counter").val(); i++){
      if(element.attr('id') == "room_typeID_"+i) continue;
      var pre_seleted_val = $("#room_typeID_"+i).val();
      var pre_seleted_text = $("#room_typeID_"+i).find("option:selected").text();
      var temp_rest_room_types = rest_room_types.slice(0);
      if($("#room_typeID_"+i).val() > 0){
        is_end = true;
        for(var j=0;j<temp_rest_room_types.length; j++){
          if(temp_rest_room_types[j]['roomtypeID'] > $("#room_typeID_"+i).val()){
            var room_type = [];
            room_type['roomtypeID'] = $("#room_typeID_"+i).val();
            room_type['room_type'] = $("#room_typeID_"+i).find("option:selected").text();
            temp_rest_room_types.splice( j, 0, room_type );
            is_end = false;
            break;
          }
        }
        if(is_end){
          var room_type = [];
          room_type['roomtypeID'] = $("#room_typeID_"+i).val();
          room_type['room_type'] = $("#room_typeID_"+i).find("option:selected").text();
          temp_rest_room_types.splice( temp_rest_room_types.length, 0, room_type );
        }
      }
      $("#room_typeID_"+i).html("<option value>-- Please Select --</option>");
      for(var j = 0; j<temp_rest_room_types.length; j++ ){
        if(temp_rest_room_types[j]['roomtypeID'] != pre_seleted_val){
          $("#room_typeID_"+i).append("<option value='"+temp_rest_room_types[j]['roomtypeID']+"'>"+temp_rest_room_types[j]['room_type']+"</option>");
        }
        else{
          $("#room_typeID_"+i).append("<option value='"+temp_rest_room_types[j]['roomtypeID']+"' selected>"+temp_rest_room_types[j]['room_type']+"</option>");
        }
      }
    }
  }

  $(".hotelID ").change(function(){
    

    room_types = new Array();
    $(".hotelID ").each(function(){
      if($(this).val())
      $.ajax({
        type: "Get",
        async: false,
        url: "{{url('/hotels/get_roomtypes_from_hotel')}}"+'/'+$(this).val(),
        dataType: "json",
        success: function (result) {
          for(var i=0;i<result.length; i++){
            is_new = true;
            for(var j=0;j<room_types.length;j++){
              if(room_types[j]['roomtypeID']==result[i]['roomtypeID']){
                  is_new = false;
                  break;
              }
            }
            if(is_new){
              room_types.push(result[i]);
            }
          }
        },
        error: function (result) {
          console.log(result);
        },
      });
    });
    var selected_types = [];
    rest_room_types = room_types.slice(0);
    for(var i=0;i<$("#counter").val();i++){
      var is_inculed = false;
      for (var j=0;j<room_types.length;j++){
        if(room_types[j]['roomtypeID'] == $("#room_typeID_"+i).val())
        is_inculed = true;
      }
      if(!is_inculed)
        $("#room_typeID_"+i).val('');
      else
        selected_types.push($("#room_typeID_"+i).val());
    }

    for(var i=0; i<rest_room_types.length; i++){
      if(selected_types.includes(rest_room_types[i]['roomtypeID'].toString())){
          rest_room_types.splice(i, 1);
          i--;
      }
    }
    for(var i = 0; i < $("#counter").val(); i++){
      var pre_seleted_val = $("#room_typeID_"+i).val();
      var pre_seleted_text = $("#room_typeID_"+i).find("option:selected").text();
      var temp_rest_room_types = rest_room_types.slice(0);
      if($("#room_typeID_"+i).val() > 0){
        is_end = true;
        for(var j=0;j<temp_rest_room_types.length; j++){
          if(temp_rest_room_types[j]['roomtypeID'] > $("#room_typeID_"+i).val()){
            var room_type = [];
            room_type['roomtypeID'] = $("#room_typeID_"+i).val();
            room_type['room_type'] = $("#room_typeID_"+i).find("option:selected").text();
            temp_rest_room_types.splice( j, 0, room_type );
            is_end = false;
            break;
          }
        }
        if(is_end){
          var room_type = [];
          room_type['roomtypeID'] = $("#room_typeID_"+i).val();
          room_type['room_type'] = $("#room_typeID_"+i).find("option:selected").text();
          temp_rest_room_types.splice( temp_rest_room_types.length, 0, room_type );
        }
      }
      $("#room_typeID_"+i).html("<option value>-- Please Select --</option>");
      for(var j = 0; j<temp_rest_room_types.length; j++ ){
        if(temp_rest_room_types[j]['roomtypeID'] != pre_seleted_val){
          $("#room_typeID_"+i).append("<option value='"+temp_rest_room_types[j]['roomtypeID']+"'>"+temp_rest_room_types[j]['room_type']+"</option>");
        }
        else{
          $("#room_typeID_"+i).append("<option value='"+temp_rest_room_types[j]['roomtypeID']+"' selected>"+temp_rest_room_types[j]['room_type']+"</option>");
        }
      }
    }



  });

  $("input[name='total_capacity'],input[name='total_nights[]'],input[name='cost'], input[name='cost_single[]'], input[name='cost_double[]'], input[name='cost_triple[]'], input[name='cost_child[]'], input[name='parts'], input[name='seat[]'], input[name='cost[]']").TouchSpin();

</script>
@stop
