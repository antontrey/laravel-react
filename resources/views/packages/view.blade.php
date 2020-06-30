@extends('layouts.app')
@section('content')
<?php
use \App\Http\Controllers\PackagesController;
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.min.js"></script>
<section class="content-header">
    <h1> {{ \App\Library\SiteHelpers::formatLookUp($row->tourcategoriesID,'tourcategoriesID','1:def_tour_categories:tourcategoriesID:tourcategoryname') }}
    </h1>
</section>
<div class="box-header with-border">
    <div class="box-header-tools pull-left">
        <a href="{{ url('packages?return='.$return) }}" class="tips" title="{{ Lang::get('core.btn_back') }}"><i
                class="fa fa-arrow-left fa-2x"></i></a>
        @if($total!=0)

        <a href="{{ url('packages/show/'.$id.'?bookinglist=true')}}" target="_blank" class="btn btn-xs btn-default tips"
            title="{{ Lang::get('core.bookinglist') }}"><i class="fa fa-file-pdf-o fa-lg text-red"></i>
            {{ Lang::get('core.bookinglist') }}</a>
        <a href="{{ url('packages/show/'.$id.'?passportlist=true')}}" target="_blank"
            class="btn btn-xs btn-default tips" title="{{ Lang::get('core.passportlist') }}"><i
                class="fa fa-file-pdf-o fa-lg text-red"></i> {{ Lang::get('core.passportlist') }}</a>
        <a href="{{ url('packages/show/'.$id.'?emergencylist=true')}}" target="_blank"
            class="btn btn-xs btn-default tips" title="{{ Lang::get('core.otherdetails') }}"><i
                class="fa fa-file-pdf-o fa-lg text-red"></i> {{ Lang::get('core.otherdetails') }}</a>
        @endif
    </div>

    <div class="box-header-tools pull-right ">
        <a href="{{ ($prevnext['prev'] != '' ? url('packages/show/'.$prevnext['prev'].'?return='.$return ) : '#') }}"
            class="tips" title="{{ Lang::get('core.previous') }}"><i class="fa fa-arrow-left fa-2x"></i> </a>
        <a href="{{ ($prevnext['next'] != '' ? url('packages/show/'.$prevnext['next'].'?return='.$return ) : '#') }}"
            class="tips" title="{{ Lang::get('core.next') }}"> <i class="fa fa-arrow-right fa-2x"></i> </a>
    </div>
</div>
<div class="col-md-3">
    <div class="box box-primary">
        <div class="box-body box-profile">
            <canvas id="booking"></canvas>

            <h3 class="profile-username text-center">
                {{ \App\Library\SiteHelpers::formatLookUp($row->tourID,'tourID','1:tours:tourID:tour_name') }}</h3>
            <p class="text-muted text-center">{{ $row->tour_code}}</p>
            <p class="text-muted text-center">{{ date("d-m-Y", strtotime(\App\Library\SiteHelpers::TarihFormat($row->start)))}} -
                {{ date("d-m-Y", strtotime(\App\Library\SiteHelpers::TarihFormat($row->end)))}}</p>
            <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                    <b>{{ Lang::get('core.status') }}</b> <a href="#" class="pull-right">{!!
                        \App\Library\GeneralStatuss::Tour_package($row->status,$row->start,$row->end,$row->packageID,
                        $row->total_capacity) !!}</a>
                </li>
                <!-- <li class="list-group-item">
                  <b>{{-- Lang::get('core.guide') }}</b> <a href="{{ url('guide/show/'.$row->guideID)}}" target="_blank" class="pull-right">{{ \App\Library\SiteHelpers::formatLookUp($row->guideID,'guideID','1:guides:guideID:name') --}}</a>
                </li> -->
                <li class="list-group-item">
                    <b>{{ Lang::get('core.featured') }}</b> <a href="#" class="pull-right">{!!
                        \App\Library\SiteHelpers::Featured($row->featured) !!}</a>
                </li>
                <li class="list-group-item">
                    <b>{{ Lang::get('core.definitedeparture') }}</b> <a href="#" class="pull-right">{!!
                        \App\Library\SiteHelpers::Definite_departure($row->definite_departure) !!}</a>
                </li>
                <li class="list-group-item">
                    <b>{{ Lang::get('core.capacity') }}</b> <a href="#" class="pull-right">{{ $row->total_capacity}}</a>
                </li>
                <li class="list-group-item">
                    <b>{{ Lang::get('core.book_left') }}</b> <a href="#" class="pull-right">{{ $room_triple}}</a>
                </li>
                <li class="list-group-item">
                    <b>{{ Lang::get('core.booked') }}</b> <a href="#" class="pull-right">{{$total}}</a>
                </li>
                <!-- <li class="list-group-item">
                  <b>{{-- Lang::get('core.singleroom') }}</b> <a href="#" class="pull-right">{{ $row->cost_single}} {{ \App\Library\SiteHelpers::formatLookUp($row->currencyID,'currencyID','1:def_currency:currencyID:currency_sym|symbol') --}}</a>
                </li>
                <li class="list-group-item">
                  <b>{{-- Lang::get('core.doubleroom') }}</b> <a href="#" class="pull-right">{{ $row->cost_double}} {{ \App\Library\SiteHelpers::formatLookUp($row->currencyID,'currencyID','1:def_currency:currencyID:currency_sym|symbol') --}}</a>
                </li>
                <li class="list-group-item">
                  <b>{{-- Lang::get('core.tripleroom') }}</b> <a href="#" class="pull-right">{{ $row->cost_triple}} {{ \App\Library\SiteHelpers::formatLookUp($row->currencyID,'currencyID','1:def_currency:currencyID:currency_sym|symbol') --}}</a>
                </li> -->
                <!-- <li class="list-group-item">
                  <b>{{-- Lang::get('core.child') }}</b> <a href="#" class="pull-right">{{ $row->cost_child}} {{ \App\Library\SiteHelpers::formatLookUp($row->currencyID,'currencyID','1:def_currency:currencyID:currency_sym|symbol') --}}</a>
                </li> -->
            </ul>
        </div>
    </div>
</div>
<div class="col-md-9">

    <div class="box box-warning">

        <div class="box-body">
            <h3 class="profile-username">
                <!-- {{ Lang::get('core.bookinglist') }} -->
            </h3>
            <style>
                .info-box-content {
                padding: 10px 10px;}
                .info-box-icon {
                width: 100px;}
            </style>


            <div class="row">
            <div class="col-lg-4 col-xs-6">
            <div class="hpanel">
                <div class="panel-body">
                    <div class="stats-title pull-left">
                        <a
                           >
                            <h4>{{ Lang::get('core.cost_price') }}</h4>
                        </a>
                    </div>
                    <div class="stats-icon pull-right">
                        <i class="fa fa-money fa-4x"></i>
                    </div>
                    <div class="m-t-xl">
                        <h1 class="text-green">{{number_format((float)$cost_price, 2)}} {{$currency}}</h1>
                    </div>
                </div>
            </div>
            </div>
            <div class="col-lg-4 col-xs-6">
            <div class="hpanel">
                <div class="panel-body">
                    <div class="stats-title pull-left">
                        <a>
                            <h4>{{ Lang::get('core.turnover') }}</h4>
                        </a>
                    </div>
                    <div class="stats-icon pull-right">
                        <i class="fa fa-random fa-4x"></i>
                    </div>
                    <div class="m-t-xl">
                        <h1 class="text-yellow">{{number_format((float)$turnover, 2)}} {{$currency}}</h1>
                    </div>
                </div>
            </div>
            </div>
            <div class="col-lg-4 col-xs-12">
            <div class="hpanel">
                <div class="panel-body">
                    <div class="stats-title pull-left">
                        <a
                           >
                            <h4>{{ Lang::get('core.earning') }}</h4>
                        </a>
                    </div>
                    <div class="stats-icon pull-right">
                        <i class="fa fa-dollar fa-4x"></i>
                    </div>
                    <div class="m-t-xl">
                        <h1 class="text-blue">{{number_format((float)$earning, 2)}} {{$currency}}</h1>
                    </div>
                </div>
            </div>
            </div>
            <div class="col-lg-6 col-xs-6">
            <div class="hpanel">
                <div class="panel-body">
                    <div class="stats-title pull-left">
                        <a
                           >
                            <h4>{{ Lang::get('core.paid') }}</h4>
                        </a>
                    </div>
                    <div class="stats-icon pull-right">
                        <i class="fa fa-credit-card fa-4x"></i>
                    </div>
                    <div class="m-t-xl">
                        <h1 class="text-green">                            <?php

                            $invtotal = 0;
                            ?>
                            @foreach($bkList as $bl)
                            <?php

                                $invtotal += PackagesController::travelersDetail_unpaid($bl['travellers'], $packageID);

                            ?>
                            @endforeach
                            {{number_format((float)$invtotal * $rate, 2)}} {{$currency}}</h1>
                    </div>
                </div>
            </div>
            </div>
            <div class="col-lg-6 col-xs-6">
              <div class="hpanel">
                  <div class="panel-body">
                      <div class="stats-title pull-left">
                          <a
                             >
                              <h4>{{ Lang::get('core.unpaid') }}</h4>
                          </a>
                      </div>
                      <div class="stats-icon pull-right">
                          <i class="fa fa-sort-amount-asc fa-4x"></i>
                      </div>
                      <div class="m-t-xl">
                          <h1 class="text-red">
                            <?php $invtotal = 0; ?>
                              @foreach($bkList as $bl)
                              <?php
                                  $invtotal += PackagesController::travelersDetail_paid($bl['travellers'], $packageID);
                              ?>
                              @endforeach
                              {{number_format((float)$invtotal * $rate, 2)}} {{$currency}}</h1>
                      </div>
                  </div>
              </div>
            </div>
                @if($total==0)
                {{ Lang::get('core.nobookingmade') }}
                @else

                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>
                                <div class='col-md-3'>{{ Lang::get('core.namesurname') }}</div>
                                <div class='col-md-3'>{{ Lang::get('core.payment_done') }}</div>
                                <div class='col-md-3'>{{ Lang::get('core.payment_due') }}</div>
                                <div class='col-md-3'>{{ Lang::get('core.status') }}</div>
                            </th>
                            <!-- <th><div class='col-md-6'>{{ Lang::get('core.namesurname') }}</div><div class='col-md-2'>{{ Lang::get('core.country') }}</div><div class='col-md-4'>{{ Lang::get('core.remarks') }}</div> </th> -->
                        </tr>
                        <?php $count = 1; ?>
                        @foreach($bkList as $bl)
                        <tr>
                            <th><?php echo $count ; $count++ ; ?></th>
                            <td>{!! PackagesController::travelersDetail($bl['travellers'], $packageID) !!}
                            </td>
                            <!-- <td>{{$bl['travellers']}}</td> -->

                        </tr>
                        @endforeach

                    </tbody>
                </table>
                @endif

            </div>
        </div>

    </div>



    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title text-danger"> Related Expense</h3>
        </div>
        <div class="box-body">


                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>
                                <div class='col-md-2'>{{ Lang::get('core.staffname') }}</div>
                                <div class='col-md-1'>{{ Lang::get('core.amount') }}</div>
                                <div class='col-md-1'>{{ Lang::get('core.paymenttype') }}</div>
                                <div class='col-md-2'>{{ Lang::get('core.date') }}</div>
                                <div class='col-md-2'>{{ Lang::get('core.extraexpense') }}</div>
                                <div class='col-md-1'>{{ Lang::get('core.category') }}</div>
                                <div class='col-md-2'>{{ Lang::get('core.notes') }}</div>
                                <div class='col-md-1'>{{ Lang::get('core.attached') }}</div>
                            </th>
                            <!-- <th><div class='col-md-6'>{{ Lang::get('core.namesurname') }}</div><div class='col-md-2'>{{ Lang::get('core.country') }}</div><div class='col-md-4'>{{ Lang::get('core.remarks') }}</div> </th> -->
                        </tr>
                        <?php $count = 1; ?>
                        @foreach($eList as $el)
                        <tr>
                            <th><?php echo $count ; $count++ ; ?></th>
                            <td>{!! PackagesController::travelersDetail_expense($el['expenseID']) !!}</td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>

        </div>
    </div>


    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title text-danger"> Related Application</h3>
        </div>
        <div class="box-body">


                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>
                                <div class='col-md-3'>{{ Lang::get('core.namesurname') }}</div>
                                <div class='col-md-3'>{{ Lang::get('core.applicationdate') }}</div>
                                <div class='col-md-3'>{{ Lang::get('core.processtime') }}</div>
                                <div class='col-md-3'>{{ Lang::get('core.status') }}</div>
                            </th>
                            <!-- <th><div class='col-md-6'>{{ Lang::get('core.namesurname') }}</div><div class='col-md-2'>{{ Lang::get('core.country') }}</div><div class='col-md-4'>{{ Lang::get('core.remarks') }}</div> </th> -->
                        </tr>
                        <?php $count = 1; ?>
                        @foreach($visa_List as $visal)
                        <tr>
                            <th><?php echo $count ; $count++ ; ?> </th>
                            <td>{!! PackagesController::travelersDetail_visa($visal['applicationID']) !!}
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>

        </div>
    </div>
    @if($row->remarks !=NULL)
    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title text-danger">{{ Lang::get('core.remarks') }}</h3>
        </div>
        <div class="box-body">
            {!! $row->remarks !!}
        </div>
    </div>
    @endif
    <!-- /.box -->
</div>


<div style="clear: both;"></div>
<script>
var ctx = document.getElementById("booking");
var booking = new Chart(ctx, {
    type: 'doughnut',
    data: {
            labels: ["{{ Lang::get('core.booked') }}","{{ Lang::get('core.available') }}"],
    datasets: [{
        data: [{{$total}}, {{ $row->total_capacity}}-{{$total}}],
        backgroundColor: [
                '#fb6b5b',
                '#65bd77'
        ],

    }],
}});
</script>

@stop
