<?php //usort($tableGrid, "\App\Library\SiteHelpers::_sort"); ?> <div class="col-md-12">
<style>
    .info-box-number{
        font-size: 30px!important;
    }
    .info-box-content{
        color: black;
    }
</style>


    <div class="box-header with-border">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-calendar-check-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ Lang::get('core.total') }}</span>
                    <span class="info-box-number">{{ number_format((float)$grandtotal, 2) }} {{$currency}}</span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-orange"><i class="fa fa-calendar"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text"> {{ Lang::get('core.turnover') }}</span>
                    <span class="info-box-number">{{number_format((float)$Box2_sum, 2)}} {{$currency}}</span>
                    <!-- <span class="">{{number_format((float)$turnover, 2)}} {{$currency}}</span> -->
                    <!-- <span class="">{{number_format((float)$tour_sum, 2)}} {{$currency}}</span> -->
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa fa-calendar-minus-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ Lang::get('core.expenses') }}</span>
                    <span class="info-box-number">{{ number_format((float)$expensestotal, 2) }} {{$currency}}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-calendar-plus-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ Lang::get('core.payments') }}</span>
                    <span class="info-box-number">{{ number_format((float)$invoice_payment_total, 2)}} {{$currency}}</span>
                </div>
            </div>
        </div>
    </div>


    <div class="box box-primary">
        <div class="box-header with-border">
                @if(isset($accounting))
                    <div class="box-header-tools pull" style="margin-bottom: -33px; margin-top: 4px;">
                        <div class="form-group  ">
                            <div class="col-md-4">
                            </div>
                            <div class="col-md-4">
                                    <form action="{{ url('accounting/search')}}" id="myForm">
                                    @csrf
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <div class="input-group m-b">
                                                        <input class="form-control date" id="start_date" type="text" value='{{ $start_date }}'>
                                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="input-group m-b">
                                                        <input class="form-control date" id="end_date" type="text" value='{{ $end_date }}'>
                                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                        <!-- <a href="javascript:void(0)" onclick="FilerAccountsByDate()" class="btn btn-primary">  submit </a> -->
				                                    	<button onclick="FilerAccountsByDate()" class="btn btn-success" type="button">submit</button>

                                                </div>
                                            </div>
                                    </form>
                            </div>
                                    <form action="{{ url('accounting/exportDoc')}}" method="POST" id="myForm_hidden">
                                        @csrf
                                         <input class="form-control" name="ids_hidden" id="ids_hidden" type="hidden">
                                         <input class="form-control" name="format_hidden" id="format_hidden" type="hidden">
                                         <input class="form-control" name="invoice_payment_total" id="invoice_payment_total" value="{{ number_format((float)$invoice_payment_total, 2)}} {{$currency}}" type="hidden">
                                         <input class="form-control" name="expensestotal" id="expensestotal" type="hidden" value="{{ number_format((float)$expensestotal, 2) }} {{$currency}}">
                                         <input class="form-control" name="Box2_sum" id="Box2_sum" type="hidden" value="{{number_format((float)$Box2_sum, 2)}} {{$currency}}">
                                         <input class="form-control" name="grandtotal" id="grandtotal" type="hidden" value="{{ number_format((float)$grandtotal, 2) }} {{$currency}}">
                                         <input class="form-control" name="submit_value" id="submit_value" type="hidden" value="1">

                                    </form>

                            <div class="col-md-4">
                            </div>
                        </div>
                    </div>
                @else
                @endif             
            @include( 'mmb/toolbar')

        </div>
        <div class="box-body">

            {!! (isset($search_map) ? $search_map : '') !!}
            <?php echo Form::open(array('url'=>'accounting/export/', 'class'=>'form-horizontal test' ,'id' =>'MmbTable'  ,'data-parsley-validate'=>'' )) ;?>

            <div class="table-responsive" style="min-height:300px; padding-bottom:60px; border: none !important">

                @if(count($rowData)>=1)
                <table class="table table-bordered table-striped " class="display compact" id="{{ $pageModule }}Table">
                    <thead>
                        <tr>
                            <th width="20"> No </th>
                            <th width="30"> <input type="checkbox" class="checkall" /></th>
                            @if($setting['view-method']=='expand')<th width="50" style="width: 50px;"> </th> @endif
                            <!-- <th width="50"><?php echo Lang::get('core.btn_action') ;?></th> -->
                            <th ><?php echo Lang::get('core.date');?></th>
                            <th width="230"><?php echo Lang::get('core.price') ;?></th>
                            <th width="230"><?php echo Lang::get('core.currency') ;?></th>
                            <th width="230"><?php echo Lang::get('core.type') ;?></th>
                            <th width="230"><?php echo Lang::get('core.category') ;?></th>
                        </tr>
                    </thead>

                    <tbody>

                        @if($access['is_add'] =='1' && $setting['inline']=='true')
                        <tr id="form-0">
                            <td> # </td>
                            <td> </td>
                            @if($setting['view-method']=='expand') <td> </td> @endif
                            <td>
                                <button onclick="saved('form-0')" class="btn btn-success btn-xs" type="button"><i
                                        class="fa fa-play-circle"></i></button>
                            </td>
                            @foreach ($tableGrid as $t)
								@if($t['view'] =='1')
								<?php $limited = isset($t['limited']) ? $t['limited'] :''; ?>
									@if(\App\Library\SiteHelpers::filterColumn($limited ))
									<td data-form="{{ $t['field'] }}"
										data-form-type="{{ \App\Library\AjaxHelpers::inlineFormType($t['field'],$tableForm)}}">
										{!! \App\Library\SiteHelpers::transForm($t['field'] , $tableForm) !!}
									</td>
									@endif
								@endif
                            @endforeach

                        </tr>
                        @endif


						<?php $i=0; foreach ($rowData as $row) :  $id = $row->accountingID; ?>
                <tr class="editable" id="form-{{ $row->accountingID }}">
					<td class="number"> <?php echo ++$i;?>  </td>
					<td ><input type="checkbox" class="ids" name="ids[]" value="<?php echo $row->accountingID ;?>" />  </td>
					@if($setting['view-method']=='expand')
					<td><a href="javascript:void(0)" class="expandable"><i class="fa fa-plus-square " ></i></a></td>
					@endif
				
                <?php foreach ($tableGrid as $field) :
					 	if($field['view'] =='1') :
							$value = \App\Library\SiteHelpers::formatRows($row->{$field['field']}, $field , $row);
						 	?>
						 	<?php $limited = isset($field['limited']) ? $field['limited'] :''; ?>
						 	@if(\App\Library\SiteHelpers::filterColumn($limited ))
								 <td align="<?php echo $field['align'];?>" data-values="{{ $row->{$field['field']} }}" data-field="{{ $field['field'] }}" data-format="{{ htmlentities($value) }}">
                                            @if( $field['field'] == 'date' )
                                            <span> {{ date("d-m-Y", strtotime($row->{$field['field']}))  }}</span>
                                            @elseif( $field['field'] == 'price' )
                                            <span> {{ $row->{$field['field']} }} </span>
                                            @else
                                            <span> {{ $row->{$field['field']} }} </span>

                                            @endif
									

								 </td>
							@endif
						 <?php endif;
						endforeach;
					  ?>
                </tr>
                @if($setting['view-method']=='expand')
                <tr style="display:none" class="expanded" id="row-{{ $row->accountingID }}">
                	<td class="number"></td>
                	<td></td>
                	<td></td>
                	<td colspan="{{ $colspan}}" class="data"></td>
                	<td></td>
                </tr>
                @endif
            <?php endforeach;?>




                    </tbody>

                </table>
                @else

                <div style="margin:100px 0; text-align:center;">

                    <p> {{ Lang::get('core.norecord') }} </p>
                </div>

                @endif

            </div>
            <?php echo Form::close() ;?>

        </div>
    </div>


</div>
<div style="clear: both;"></div> @if($setting['inline'] =='true') @include('mmb.module.utility.inlinegrid') @endif
<script>
$(document).ready(function() {
    $('.tips').tooltip();
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

    $('#{{ $pageModule }}Paginate .pagination li a').click(function() {
        var url = $(this).attr('href');
        reloadData('#{{ $pageModule }}', url);
        return false;
    });
    $('.date').datetimepicker({format: 'yyyy-mm-dd', autoclose:true , minView:2 , startView:2 , todayBtn:true });
    <?php
    if ($setting['view-method'] == 'expand'):
        echo\ App\ Library\ AjaxHelpers::htmlExpandGrid();
    endif; ?>
    $('#{{ $pageModule }}Table').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": true
    });
});
      $("#pdf_accounting").click(function(){
            var ids = [];
        $('.ids').each(function(i, obj) {
            if(obj.checked)
                ids.push(obj.value);
            });
            $("#ids_hidden").val(ids);
           var ids_hidden = $("#ids_hidden").val();
           $("#format_hidden").val("pdf");
            console.log(ids_hidden);
            $("#myForm_hidden").submit();
            console.log(ids_hidden);

    });

    $("#word_accounting").click(function(){
            var ids = [];
        $('.ids').each(function(i, obj) {
            if(obj.checked)
                ids.push(obj.value);
            });
            $("#ids_hidden").val(ids);
           var ids_hidden = $("#ids_hidden").val();
           $("#format_hidden").val("word");
            console.log(ids_hidden);
            $("#myForm_hidden").submit();
    });
    $("#csv_accounting").click(function(){
            var ids = [];
        $('.ids').each(function(i, obj) {
            if(obj.checked)
                ids.push(obj.value);
            });
            $("#ids_hidden").val(ids);
           var ids_hidden = $("#ids_hidden").val();
           $("#format_hidden").val("csv");
            console.log(ids_hidden);
            $("#myForm_hidden").submit();
    });
    $("#print_accounting").click(function(){
            var ids = [];
        $('.ids').each(function(i, obj) {
            if(obj.checked)
                ids.push(obj.value);
            });
            $("#ids_hidden").val(ids);
           var ids_hidden = $("#ids_hidden").val();
           $("#format_hidden").val("print");
            console.log(ids_hidden);
            $("#myForm_hidden").submit();
    });
    $("#excel_accounting").click(function(){
            var ids = [];
        $('.ids').each(function(i, obj) {
            if(obj.checked)
                ids.push(obj.value);
            });
            $("#ids_hidden").val(ids);
           var ids_hidden = $("#ids_hidden").val();
           $("#format_hidden").val("");
            console.log(ids_hidden);
            $("#myForm_hidden").submit();
    });
    
function FilerAccountsByDate(){
    requests = {
        "start_date": $("#start_date").val(),
        "end_date": $("#end_date").val()
    };

    reloadDataFilerByDate('#accounting', 'accounting/data?return=', requests);
    
}
</script>
<style>
.table th {
    text-align: none !important;
}

.table th.right {
    text-align: right !important;
}

.table th.center {
    text-align: center !important;
}
</style>