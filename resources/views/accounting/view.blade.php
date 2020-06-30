@extends('layouts.app')
@section('content')
<section class="content-header page-title">
                <h1>
                    Boards <small></small>
                </h1>
                <ol class="breadcrumb" style="position: inherit;">
                    <li><a href="{{ url('bookinggroups?return=') }}"><i
                                class="fas fa-tachometer-alt"></i> Groups</a></li>
                    <li class="active"> View </li>
                </ol>
            </section>
<section class="content">

    <div class="row">

        <div class="col-md-12" data-layout-box="52">

            <div class="   ">


                <div class="box-body view ">


                    <div class="row">
                    @foreach($roomtypes as $roomtype)

                        <div class="col-md-4">
                            <div class="small-box bg-green">
                                <div class="inner">
                                    <h3>
                                   
                                    {{$roomtype}}
                                    </h3>

                                    <p>0 Groups</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-suitcase"></i>
                                </div>
                                <a href="{{ url('bookinggroups/board?return=') }}"
                                    class="small-box-footer">Open board <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        @endforeach

                    </div>
                </div>
            </div>

        </div>
    </div>


    <style>
    .js_topbar_timetracker {
        float: right;
        padding: 13px 20px 0 0;
    }

    .js_topbar_task_title {
        background: #e4e4e4;
        padding: 4px;
        font-size: 0.9em;
        border-radius: 3px;
        margin-right: 10px;
    }

    .js_topbar_task_title.working_on {
        background: #f39c12 !important;
    }
    </style>
    <div class="js_topbar_timetracker_container" style="display:none">

        <div class="js_topbar_timetracker">
            <span class="js_topbar_task_title">Resume: My first task</span>
            <a href="https://personal-kanban-board.firegui.com/personal-kanban-board/main/task_working_on/1/1"
                style="background-color:green;color:#ffffff" class="btn btn-xs green js_link_ajax" data-toggle="tooltip"
                title="Start time tracker"><i class="fa fa-play"></i></a>

        </div>
    </div>

    <script>
    console.log($('.js_topbar_timetracker', '.navbar').length);

    if ($('.js_topbar_timetracker', '.navbar').length == 0) {
        $('.navbar').append($('.js_topbar_timetracker_container').html());
    }

    var minutesLabel = document.getElementById("minutes");
    var secondsLabel = document.getElementById("seconds");
    var hoursLabel = document.getElementById("hours");


    function setTime() {
        ++totalSeconds;
        secondsLabel.innerHTML = pad(totalSeconds % 60);
        minutesLabel.innerHTML = pad(parseInt(totalSeconds / 60));
        hoursLabel.innerHTML = pad(parseInt(totalSeconds / 60 / 60));
    }

    function pad(val) {
        var valString = val + "";
        if (valString.length < 2) {
            return "0" + valString;
        } else {
            return valString;
        }
    }
    </script>


</section>
@stop