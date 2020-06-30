@extends('layouts.app') @section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.min.js"></script>
<div class="content">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-calendar-check-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{Lang::get('core.totalbooking')}}</span>
                    <span class="info-box-number"><?php echo $totalbookings ; ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa fa-calendar-plus-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{Lang::get('core.todaysbooking')}}</span>
                    <span class="info-box-number"><?php echo $todaysbookings ; ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-calendar-minus-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{Lang::get('core.lastweekbooking')}}</span>
                    <span class="info-box-number"><?php echo $lastweeksbookings ; ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fa fa-calendar"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{Lang::get('core.lastmonthbooking')}}</span>
                    <span class="info-box-number"><?php echo $lastmonthssbookings ; ?></span>
                </div>
            </div>
        </div>
        
        <div class="row">
        <div class="col-md-9">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{Lang::get('core.monthlybookingreport')}}</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p class="text-center">
                                <strong></strong>
                            </p>
                            <div class="chart">
                                <canvas id="monthlyBooking" width="400" height="130"></canvas>

                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
            
            <div class="col-md-3">
          <div class="info-box bg-green">
            <span class="info-box-icon"><i class="fa fa-bus"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">{{ Lang::get('core.runningtours') }}</span>
              <span class="info-box-number"><?php echo $running_tours ; ?> </span>

              <div class="progress">
                <div class="progress-bar" style="width: 70%"></div>
              </div>
            </div>
            <!-- /.info-box-content -->
          </div><div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="fa fa-random"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">{{ Lang::get('core.upcomingtours') }}</span>
              <span class="info-box-number"><?php echo $upcoming_tours ; ?></span>

              <div class="progress">
                <div class="progress-bar" style="width: 70%"></div>
              </div>
            </div>
            <!-- /.info-box-content -->
          </div><div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="fa fa-history"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">{{ Lang::get('core.pasttours') }}</span>
              <span class="info-box-number"><?php echo $old_tours ; ?></span>

              <div class="progress">
                <div class="progress-bar" style="width: 70%"></div>
              </div>
            </div>
          </div>
        </div>
        </div>
                    <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>
                        <?php echo $activeagents ; ?>
                    </h3>
                    <p>{{Lang::get('core.activetravelagents')}}</p>
                </div>
                <div class="icon">
                    <i class="fa fa-text-width"></i>
                </div>
                <a href="{{ URL::to('travelagents') }}" class="small-box-footer">
              {{Lang::get('core.moreinfo')}} <i class="fa fa-arrow-circle-right"></i>
            </a>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>
                        <?php echo $activehotels ; ?>
                    </h3>
                    <p>{{Lang::get('core.activehotels')}}</p>
                </div>
                <div class="icon">
                    <i class="fa fa-bed"></i>
                </div>
                <a href="{{ URL::to('hotels') }}" class="small-box-footer">
              {{Lang::get('core.moreinfo')}} <i class="fa fa-arrow-circle-right"></i>
            </a>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>
                        <?php echo $activesupplier ; ?>
                    </h3>
                    <p>{{Lang::get('core.activesuppliers')}}</p>
                </div>
                <div class="icon">
                    <i class="fa fa-handshake-o"></i>
                </div>
                <a href="{{ URL::to('suppliers') }}" class="small-box-footer">
              {{Lang::get('core.moreinfo')}} <i class="fa fa-arrow-circle-right"></i>
            </a>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>
                        <?php echo $activeguides ; ?>
                    </h3>
                    <p>{{Lang::get('core.activeguides')}}</p>
                </div>
                <div class="icon">
                    <i class="fa fa-id-card"></i>
                </div>
                <a href="{{ URL::to('guide') }}" class="small-box-footer">
              {{Lang::get('core.moreinfo')}} <i class="fa fa-arrow-circle-right"></i>
            </a>
            </div>
        </div>            
    </div>
    </div>
    
<div style="clear:both"></div>	

<script>
var ctx = document.getElementById("monthlyBooking").getContext('2d');
var monthlyBooking = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [@foreach ($graph as $dat) 
                "{!! $dat->monthNum !!}",
                @endforeach],
        datasets: [{
            label: '{{Lang::get('core.numberofbookings')}}',
            data: [@foreach ($graph as $dat) 
                {!! $dat->totalbook !!},
                @endforeach],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255,99,132,1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});
</script>
@stop
