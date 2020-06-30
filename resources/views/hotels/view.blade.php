@extends('layouts.app') @section('content')
<?php
use \App\Http\Controllers\HotelsController;
?>
    <section class="content-header">
        <h1> {{ Lang::get('core.hotels') }}</h1>
    </section>
    <div class="box-header with-border">
        <div class="box-header-tools pull-left">
            <a href="{{ url('hotels?return='.$return) }}" class="tips" title="{{ Lang::get('core.btn_back') }}"><i class="fa  fa-arrow-left fa-2x"></i></a>
            @if($access['is_add'] ==1)
            <a href="{{ url('hotels/update/'.$id.'?return='.$return) }}" class="tips text-green" title="{{ Lang::get('core.btn_edit') }}"><i class="fa  fa-edit fa-2x"></i></a>
            <a href="{{ url('hotelsnote/update/?hotelID='.$id)}}" onclick="MmbModal(this.href,'{{ Lang::get('core.addnewnote') }}'); return false;" class="tips text-yellow" title="{{ Lang::get('core.addnewnote') }}"><i class="fa fa-sticky-note fa-2x"></i> </a>
            <a href="{{ url('hotelrates/update/?hotelID='.$id)}}" onclick="MmbModal(this.href,'{{ Lang::get('core.addnewroomrate') }}'); return false;" class="tips text-blue" title="{{ Lang::get('core.addnewroomrate') }}"><i class="fa fa-eur fa-2x"></i> </a>
            @endif
        </div>
        <div class="box-header-tools ">
            <a href="{{ ($prevnext['prev'] != '' ? url('hotels/show/'.$prevnext['prev'].'?return='.$return ) : '#') }}" class="tips"><i class="fa fa-arrow-left fa-2x"></i>  </a>
            <a href="{{ ($prevnext['next'] != '' ? url('hotels/show/'.$prevnext['next'].'?return='.$return ) : '#') }}" class="tips"> <i class="fa fa-arrow-right fa-2x"></i> </a>
        </div>
    </div>
<div class="row">
    <div class="col-md-3" id="sidebar" >
        <div class="theiaStickySidebar">
        <div class="box box-primary">
            <div class="box-body box-profile">
                <h3 class="text-center">
                @if(file_exists('./uploads/images/'.$row->image) && $row->image !='')
                    {!! \App\Library\SiteHelpers::formatRows($row->image,$fields['image'],$row ) !!}
                @else
                <img src=" {{ asset('/uploads/images/no-image-hotel.png') }}" />
                @endif
</h3>
                <h3 class="profile-username text-center">{{ $row->hotel_name}}</h3>

                <p class="text-muted text-center text-yellow">{{ \App\Library\SiteHelpers::formatLookUp($row->hotelcategoryID,'hotelcategoryID','1:def_hotel_categories:hotelcategoryID:category_name') }}</p>
                <p class="text-muted text-center">
                    @if ($row->tripadvisor != NULL )
                    <a href="http://{{ $row->tripadvisor}}" class="btn btn-social-icon btn-tripadvisor tips" target="_blank" title="Tripadvisor"><i class="fa fa-tripadvisor"></i></a> @endif @if ($row->facebook != NULL )
                    <a href="http://{{ $row->facebook}}" class="btn btn-social-icon btn-facebook tips" target="_blank" title="Facebook"><i class="fa fa-facebook"></i></a> @endif @if ($row->twitter != NULL )
                    <a href="http://{{ $row->twitter}}" class="btn btn-social-icon btn-twitter tips" target="_blank" title="Twitter"><i class="fa fa-twitter"></i></a> @endif @if ($row->instagram != NULL )
                    <a href="http://{{ $row->instagram}}" class="btn btn-social-icon btn-instagram tips" target="_blank" title="Instagram"><i class="fa fa-instagram"></i></a> @endif
                    <br>
                </p>
                <ul class="list-group list-group-unbordered">
                    <li class="list-group-item">
                        <b>{{ Lang::get('core.website') }}</b> <a href="http://{{ $row->web_site}}" target="_blank" class="pull-right">{{ $row->web_site}}</a>
                    </li>
                    <li class="list-group-item">
                        <b>{{ Lang::get('core.email') }}</b> <a href="mailto:{{ $row->hotel_email}}" class="pull-right">{{ $row->hotel_email}}</a>
                    </li>
                    <li class="list-group-item">
                        <b>{{ Lang::get('core.type') }}</b> <a href="{{ url('hotels?search=hoteltypeID:=:'.$row->hoteltypeID) }}" class="pull-right" target="_blank">{{ \App\Library\SiteHelpers::formatLookUp($row->hoteltypeID,'hoteltypeID','1:def_hotel_type:hoteltypeID:type_name') }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>{{ Lang::get('core.phone') }}</b> <a class="pull-right">{{ $row->hotel_phone}}</a>
                    </li>
                    <li class="list-group-item">
                        <b>{{ Lang::get('core.fax') }}</b> <a class="pull-right">{{ $row->hotel_fax}}</a>
                    </li>
                    <li class="list-group-item">
                        <b>{{ Lang::get('core.personincontact') }}</b> <a class="pull-right">{{ $row->person_in_contact}}</a>
                    </li>
                    <li class="list-group-item">
                        <b>{{ Lang::get('core.phone') }}</b> <a class="pull-right">{{ $row->contact_phone}}</a>
                    </li>
                    <li class="list-group-item">
                        <b>{{ Lang::get('core.email') }}</b> <a href="mailto:{{ $row->contact_email}}" class="pull-right">{{ $row->contact_email}}</a>
                    </li>
                    <li class="list-group-item">
                        <b>{{ Lang::get('core.status') }}</b> <a class="pull-right">{!! \App\Library\GeneralStatuss::Status($row->status) !!}</a>
                    </li>
                </ul>
            </div>
        </div>

    </div>
    </div>

    <div class="col-md-9">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#description" data-toggle="tab"><i class="fa fa-square text-green" aria-hidden="true"></i> {{ Lang::get('core.hotel') }}</a></li>
                <li><a href="#RoomRates" data-toggle="tab"><i class="fa fa-square text-green" aria-hidden="true"></i> {{ Lang::get('core.roomrates') }}</a></li>
                <li><a href="#Notes" data-toggle="tab"><i class="fa fa-square text-green" aria-hidden="true"></i> {{ Lang::get('core.notes') }}</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="description">
                    {{ $row->hotel_description}}
                    <hr>

                    <strong><i class="fa fa-map-marker fa-2x margin-r-5"></i> {{ Lang::get('core.location') }}</strong>

                    <p class="text-muted">{{ $row->address}}<br>{{ \App\Library\SiteHelpers::formatLookUp($row->cityID,'cityID','1:def_city:cityID:city_name') }}, {{ \App\Library\SiteHelpers::formatLookUp($row->countryID,'countryID','1:def_country:countryID:country_name') }}</p>

                    <hr>

                    <strong>{{ Lang::get('core.facilities') }}</strong>

                    <p class="text-muted">{!! HotelsController::hotelFacilities($row->facilities) !!}</p>
                    <hr>
                    <strong>{{ Lang::get('core.paymentoptions') }}</strong>

                    <p>{!! \App\Library\SiteHelpers::showPaymentOptions($row->paymentoptions) !!}</p>
                    <hr>
                    <strong>{{ Lang::get('core.similarhotels') }}</strong>

                    <p>{!! HotelsController::similarHotel($row->similarhotels) !!}</p>
                    <hr>
                    <strong>
                    {{ Lang::get('core.checkin') }} :</strong> {{ \App\Library\SiteHelpers::formatLookUp($row->checkin,'checkin','1:def_time_slots:timeslotID:time') }} <strong>{{ Lang::get('core.checkout') }} :</strong>{{ \App\Library\SiteHelpers::formatLookUp($row->checkout,'checkout','1:def_time_slots:timeslotID:time') }}
                    <hr>
                    <strong>{{ Lang::get('core.tandc') }}</strong>
                    <p> {{ $row->policyandterms}}</p>

                </div>
                <div class="tab-pane" id="RoomRates">

                    <table id="notes" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{ Lang::get('core.roomtype') }}</th>
                                <th>{{ Lang::get('core.season') }}</th>
                                <th style="width:80px;">{{ Lang::get('core.rate') }}</th>
                                <th style="width:22px;">{{ Lang::get('core.images') }}</th>
                                <th style="width:22px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($room as $rr)
                            <tr>
                                <td>{{ \App\Library\SiteHelpers::formatLookUp($rr['roomtypeID'],'roomtypeID','1:def_room_types:roomtypeID:room_type') }}</td>
                                <td>@if ($rr['season'] == 0) Low Season @elseif ($rr['season'] == 1) High Season @elseif ($rr['season'] == 3) High/Low Season @endif </td>
                                <td>{{ \App\Library\SiteHelpers::formatLookUp($rr['currency'],'currency','1:def_currency:currencyID:currency_sym|symbol') }} {{ $rr['rate'] }}</td>
                                <td>@if ($rr['images'] != NULL)
                                    <a href="{{ url('hotelrates/show/'.$rr['hotelrateid'])}}" onclick="MmbModal(this.href,'View Room Pictures'); return false;" class="tips" title="{{ Lang::get('core.roompictures') }}"> <i class="fa fa-picture-o fa-lg" aria-hidden="true"></i></a>
                                    @endif</td>
                                <td>@if($access['is_remove'] =='1')
                                    <a href="{{ url('hotels/roomratedelete/'.$rr['hotelID'].'/'.$rr['hotelrateid'])}}" class="btn btn-default btn-xs btn-flat pull-right " data-toggle="confirmation" data-title="{{Lang::get('core.rusure')}}" data-content="{{ Lang::get('core.youwanttodeletethis') }}"><i class="fa fa-trash-o text-navy"></i></a> @endif @if($access['is_edit'] =='1')
                                    <a href="{{ url('hotelrates/update/'.$rr['hotelrateid'].'/?hotelID='.$id)}}" onclick="MmbModal(this.href,'{{ Lang::get('core.editroomrate') }}'); return false;" class="btn btn-default btn-xs btn-flat pull-right tips" title="{{ Lang::get('core.edit') }}"><i class="fa fa-edit text-navy"></i></a> @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane" id="Notes">

                    <table id="hotelnotes" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width:20px;"></th>
                                <th>{{ Lang::get('core.title') }}</th>
                                <th>{{ Lang::get('core.note') }}</th>
                                <th style="width:60px;">{{ Lang::get('core.date') }}</th>
                                <th style="width:22px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notes as $hn)
                            <tr>
                                <td><a class="text-{{ $hn['style'] }} tips" title="{{ $hn['style'] }}" href="#"><i class="fa fa-square fa-2x"></i></a></td>
                                <td>{{ $hn['title'] }}</td>
                                <td>{{ $hn['note'] }}</td>
                                <td>{{ \App\Library\SiteHelpers::TarihFormat($hn['created_at'])}}</td>
                                <td>@if($access['is_remove'] =='1')
                                    <a href="{{ url('hotels/notedelete/'.$hn['hotelID'].'/'.$hn['hotel_noteID'])}}" class="btn btn-default btn-xs btn-flat pull-right" data-toggle="confirmation" data-title="{{Lang::get('core.rusure')}}" data-content="{{ Lang::get('core.youwanttodeletethis') }}"><i class="fa fa-trash-o text-navy"></i></a> @endif @if($access['is_edit'] =='1')
                                    <a href="{{ url('hotelsnote/update/'.$hn['hotel_noteID'].'/?hotelID='.$id)}}" onclick="MmbModal(this.href,'{{ Lang::get('core.editnote') }}'); return false;" class="btn btn-default btn-xs btn-flat pull-right tips" title="{{ Lang::get('core.edit') }}"><i class="fa fa-edit text-navy"></i></a> @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="clr clear"></div>
	<script>
		jQuery('#sidebar').theiaStickySidebar({
			additionalMarginTop: 60
		});
	</script>

    <script type="text/javascript">
        $(function() {
            $('.sectionDelete , .itemDelete').click(function() {
                if (confirm('Are you sure you want to remove this ?')) {
                    return true
                } else {
                    return false;
                }
                return false;
            })
            $('.editItem').click(function() {
                $('.displayItem').hide();
                $('.displayEdit').show();
            });
            $('.closeItem').click(function() {
                $('.displayItem').show();
                $('.displayEdit').hide();
            });
        $('[data-toggle=confirmation]').confirmation({
    rootSelector: '[data-toggle=confirmation]',
    container: 'body'
  });

        })
        $(function() {
            $("#hotelnotes,#notes").DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": false,
                "autoWidth": false
            });
        });

    </script>

    @stop
