@extends('layouts.app')
@section('content')
{{--*/ usort($tableGrid, "\App\Library\SiteHelpers::_sort") /*--}}
<section class="content-header">
    <h1>{{ Lang::get('core.packages') }}</h1>
</section>

<div class="content">
    <div class="box-header with-border">
        <div class="col-lg-3 col-xs-6">
            <div class="hpanel">
                <div class="panel-body">
                    <div class="stats-title pull-left">
                        <a
                            href="{{ url('packages?search=status:equal:1|start:smaller_equal:'.$today.'|end:bigger:'.$today) }}">
                            <h4>{{ Lang::get('core.runningpackage') }}</h4>
                        </a>
                    </div>
                    <div class="stats-icon pull-right">
                        <i class="fa fa-bus fa-4x"></i>
                    </div>
                    <div class="m-t-xl">
                        <h1 class="text-green">{{$running_tours}}</h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="hpanel">
                <div class="panel-body">
                    <div class="stats-title pull-left">
                        <h4>
                            <a href="{{ url('packages?search=status:equal:1|start:bigger:'.$today) }}">
                                {{ Lang::get('core.upcomingpackage') }}
                            </a>
                        </h4>
                    </div>
                    <div class="stats-icon pull-right">
                        <i class="fa fa-random fa-4x"></i>
                    </div>
                    <div class="m-t-xl">
                        <h1 class="text-yellow">{{$upcoming_tours}}</h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="hpanel">
                <div class="panel-body">
                    <div class="stats-title pull-left">
                        <h4><a href="{{ url('packages?search=status:equal:1|end:smaller:'.$today) }}">
                                {{ Lang::get('core.pastpackage') }}
                            </a></h4>
                    </div>
                    <div class="stats-icon pull-right">
                        <i class="fa fa-check-square fa-4x"></i>
                    </div>
                    <div class="m-t-xl">
                        <h1 class="text-blue">{{$old_tours}}</h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="hpanel">
                <div class="panel-body">
                    <div class="stats-title pull-left">
                        <h4> <a href="{{ url('packages?search=status:equal:2') }}">
                                {{ Lang::get('core.cancelledpackage') }}
                            </a>
                        </h4>
                    </div>
                    <div class="stats-icon pull-right">
                        <i class="fa fa-times fa-4x"></i>
                    </div>
                    <div class="m-t-xl">
                        <h1 class="text-red">{{$cancelled_tours}}</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="box box-primary">
        <div class="box-header with-border">
            @include( 'mmb/toolbarmain')
        </div>
        <div class="box-body">

            {!! Form::open(array('url'=>'packages/delete/', 'class'=>'form-horizontal' ,'id' =>'MmbTable' )) !!}
            <div class="table-responsive" style="min-height:300px; padding-bottom:60px; border: none !important">
                <table class="table table-striped table-bordered " id="{{ $pageModule }}Table">
                    <thead>
                        <tr>
                            <th width="20px" class="number"> No </th>
                            <th width="30px"> <input type="checkbox" class="checkall" /></th>
                            <th>{{ Lang::get('core.btn_action') }}</th>
                            <th>{{ Lang::get('core.packagecategory') }}</th>
                            <th>{{ Lang::get('core.packagename') }}</th>
                            <th>{{ Lang::get('core.packagecode') }}</th>
                            <th>{{ Lang::get('core.capacity') }}</th>
                            <!-- <th>{{ Lang::get('core.cost') }}</th> -->
                            <th>{{ Lang::get('core.start') }}</th>
                            <th>{{ Lang::get('core.end') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($rowData as $row)
                        <tr>
                            <td> {{ ++$i }} </td>
                            <td><input type="checkbox" class="ids minimal-red" name="ids[]"
                                    value="{{ $row->packageID }}" /> </td>
                            <td style="width: 190px;">
                                @if($access['is_detail'] ==1)
                                <a href="{{ url('packages/show/'.$row->packageID.'?return='.$return)}}" class="tips"
                                    title="{{ Lang::get('core.btn_view') }}"><i class="fa  fa-search fa-2x"></i> </a>
                                @endif
                                @if($access['is_edit'] ==1)
                                <a class="tips" @if( $row->start < $today) disabled
                                        title="{{ Lang::get('core.youcanteditthistour') }}" @else
                                        href="{{ url('packages/update/'.$row->packageID.'?return='.$return) }}"
                                        title="{{ Lang::get('core.btn_edit') }}" @endif><i class="fa fa-edit fa-2x"></i>
                                </a>
                                @endif
                                {!! \App\Library\SiteHelpers::Featured($row->featured) !!}
                                {!! \App\Library\SiteHelpers::definite_departure($row->definite_departure) !!}
                                {!!
                                \App\Library\GeneralStatuss::Tour_without_total($row->status,$row->start,$row->end,$row->packageID,
                                $row->definite_departure) !!}
                            </td>
                            @foreach($tours as $tour)
                            @if($tour->tourID==$row->tourID)
                            @break
                            @endif
                            @endforeach
                            <td>{{ \App\Library\SiteHelpers::formatLookUp($row->tourcategoriesID,'tourcategoriesID','1:def_tour_categories:tourcategoriesID:tourcategoryname') }}
                            </td>
                            <!-- <td>{{ $tour->tour_name }}</td> -->
                            <td>{{ $row->package_name }}</td>
                            <td>{{ $row->tour_code}}</td>
                            <td>{{ $row->total_capacity}} </td>
                            <!-- <td>{{ $row->cost }}{{ \App\Library\SiteHelpers::formatLookUp($row->currencyID,'currencyID','1:def_currency:currencyID:symbol') }} -->
                            </td>
                            <td>{{ \App\Library\SiteHelpers::TarihFormat($row->start)}}</td>
                            <td>{{ \App\Library\SiteHelpers::TarihFormat($row->end)}}</td>
                        </tr>
                        @endforeach

                    </tbody>

                </table>
                <input type="hidden" name="md" value="" />
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
<script>
$(document).ready(function() {

    $('.do-quick-search').click(function() {
        $('#MmbTable').attr('action', '{{ url("packages/multisearch")}}');
        $('#MmbTable').submit();
    });

    $('input[type="checkbox"],input[type="radio"]').iCheck({
        checkboxClass: 'icheckbox_square-red',
        radioClass: 'iradio_square-red',
    });

    $('#{{ $pageModule }}Table .checkall').on('ifChecked', function() {
        $('#{{ $pageModule }}Table input[type="checkbox"]').iCheck('check');
    });
    $('#{{ $pageModule }}Table .checkall').on('ifUnchecked', function() {
        $('#{{ $pageModule }}Table input[type="checkbox"]').iCheck('uncheck');
    });

    $('.copy').click(function() {
        var total = $('input[class="ids"]:checkbox:checked').length;
        $('#MmbTable').attr('action', '{{ url("packages/copy")}}');
        $('#MmbTable').submit(); // do the rest here
    })

});
</script>
<style>
.table th,
th {
    text-align: none !important;
}

.table th.right {
    text-align: right !important;
}

.table th.center {
    text-align: center !important;
}
</style>

<script>
  $(function () {
    $('#{{ $pageModule }}Table').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
    "lengthMenu": [ [25, 50, -1], [25, 50, "All"] ],
      "autoWidth": true
    });
  });
</script>

@stop