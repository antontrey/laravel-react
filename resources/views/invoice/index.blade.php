@extends('layouts.app')

@section('content')
{{--*/ usort($tableGrid, "\App\Library\SiteHelpers::_sort") /*--}}
    <section class="content-header">
      <h1>{{Lang::get('core.invoices')}}</h1>
    </section>

  <div class="content">

<div class="box box-primary">
	<div class="box-header with-border">
		  @include( 'mmb/toolbarmain')
	</div>
	<div class="box-body">

	 {!! Form::open(array('url'=>'invoice/delete/', 'class'=>'form-horizontal' ,'id' =>'MmbTable' )) !!}
	 <div class="table-responsive" style="min-height:300px; padding-bottom:60px; border: none !important">
    <table class="table table-striped table-bordered " id="{{ $pageModule }}Table">
        <thead>
			<tr>
				<th class="number"> No </th>
				<th> <input type="checkbox" class="checkall" /></th>
				<th width="50" style="width: 50px;">{{ Lang::get('core.btn_action') }}</th>
				<th>{{Lang::get('core.invoiceno')}}</th>
				<th>{{Lang::get('core.bookingno')}}</th>
				<th>{{Lang::get('core.total')}}</th>
				<th>{{Lang::get('core.traveller')}}</th>
				<th>{{Lang::get('core.issuedate')}}</th>
				<th width="30">{{Lang::get('core.duedate')}}</th>
				<th width="30">{{Lang::get('core.status')}}</th>
			  </tr>
        </thead>

        <tbody>
            @foreach ($rowData as $row)
                <tr>
					<td width="30"> {{ ++$i }} </td>
					<td width="50"><input type="checkbox" class="ids minimal-red" name="ids[]" value="{{ $row->invoiceID }}" />  </td>
					<td width="100">

						 	@if($access['is_detail'] ==1)
							<a href="{{ url('invoice/show/'.$row->invoiceID.'?return='.$return)}}" class="tips" title="{{ Lang::get('core.btn_view') }}"><i class="fa  fa-search fa-2x"></i> </a>
							@endif
							@if($access['is_edit'] ==1)
							<a  href="{{ url('invoice/update/'.$row->invoiceID.'?return='.$return) }}" class="tips" title="{{ Lang::get('core.btn_edit') }}"><i class="fa fa-edit fa-2x"></i> </a>
							@endif
							<a  href="{{ url('invoice/show/'.$row->invoiceID.'?pdf=true') }}" target="_blank" class="tips text-red" title="PDF"><i class="fa fa-file-pdf-o fa-2x"></i> </a>

					</td>
                    <td><a href="{{ url('invoice/show/'.$row->invoiceID.'?return='.$return) }}">{{ $row->invoiceID }}</a>
                    </td>
                    <td><a href="{{ url('createbooking/show/'.$row->bookingID.'?return='.$return) }}" target="_blank">{{ \App\Library\SiteHelpers::formatLookUp($row->bookingID,'bookingID','1:bookings:bookingsID:bookingno') }}</a>
                    </td>
                    <td>{{ $row->InvTotal}} {{ \App\Library\SiteHelpers::formatLookUp($row->currency,'currencyID','1:def_currency:currencyID:currency_sym') }}
                    </td>
                    <td><a href="{{ url('travellers/show/'.$row->travellerID.'?return='.$return) }}" target="_blank">{{ \App\Library\SiteHelpers::formatLookUp($row->travellerID,'travellerID','1:travellers:travellerID:nameandsurname') }}</a>
                    </td>
                    <td>{{ \App\Library\SiteHelpers::TarihFormat($row->DateIssued)}}
                    </td>
                    <td>{!! \App\Library\InvoiceStatus::paymentstatus($row->DueDate) !!}
                    </td>
                    <td><?php
    $payment = DB::table('invoice_payments')->where('invoiceID', $row->invoiceID )->sum('amount');
    $Total = $row->InvTotal ;
                        ?>{!! \App\Library\InvoiceStatus::Payments($payment , $Total) !!}</td>

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
$(document).ready(function(){

	$('.do-quick-search').click(function(){
		$('#MmbTable').attr('action','{{ url("invoice/multisearch")}}');
		$('#MmbTable').submit();
	});

	$('input[type="checkbox"],input[type="radio"]').iCheck({
		checkboxClass: 'icheckbox_square-red',
		radioClass: 'iradio_square-red',
	});

	$('#{{ $pageModule }}Table .checkall').on('ifChecked',function(){
		$('#{{ $pageModule }}Table input[type="checkbox"]').iCheck('check');
	});

    	$('#{{ $pageModule }}Table .checkall').on('ifUnchecked',function(){
		$('#{{ $pageModule }}Table input[type="checkbox"]').iCheck('uncheck');
	});

	$('.copy').click(function() {
		var total = $('input[class="ids"]:checkbox:checked').length;
		if(confirm('{{ Lang::get('core.rusureyouwanttocopythis') }}'))
		{
				$('#MmbTable').attr('action','{{ url("invoice/copy")}}');
				$('#MmbTable').submit();// do the rest here
		}
	})

});
</script>
<style>
.table th , th { text-align: none !important;  }
.table th.right { text-align:right !important;}
.table th.center { text-align:center !important;}

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
