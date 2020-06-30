
<div style="width:760px !important; ;">
<?php
		
	$content = $title;
		if($title == "Accounting"):
			if(isset($submit)):
				$content .= '<table  class="table">';
				$content .= '<tr>';
				$content .= '<th style="background:#26A65B;">Grand Total</th>';
				$content .= '<th style="background:#26A65B;">Turnover</th>';
				$content .= '<th style="background:#26A65B;">Expenses</th>';
				$content .= '<th style="background:#26A65B;">Payments</th>';
				$content .= '</tr>';
				$content .= '<tr>';
				$content .= '<td>'. $grandtotal .'</td>';
				$content .= '<td>'. $Box2_sum .'</td>';
				$content .= '<td>'. $expensestotal .'</td>';
				$content .= '<td>'. $invoice_payment_total .'</td>';
				$content .= '</tr>';
				$content .= '</table>';
			endif;
		endif;
		$content .= '<table  class="table">';
		$content .= '<tr>';

	foreach($fields as $f )
	{
		if($f['download'] =='1') $content .= '<th style="background:#f9f9f9;">'. $f['label'] . '</th>';
	}
	$content .= '</tr>';
	
	foreach ($rows as $row)
	{
		$content .= '<tr>';
		foreach($fields as $f )
		{
			if($f['download'] =='1'):			
				$content .= '<td> '. \App\Library\SiteHelpers::formatRows($row->{$f['field']},$f,$row) . '</td>';
			endif;	
		}
		$content .= '</tr>';
	}
	$content .= '</table>';
	echo $content;
?>
</div>
<style>
body {
font-size: 15px;
color: #34495e;

  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  font-family: Arial, sans-serif;
  overflow-x: hidden;
  overflow-y: auto;
}

.table {  border: 1px solid #EBEBEB; width: 90%;}
.table   tr  th { font-size: 11px; }
.table   tr  td {
  border-top: 1px solid #e7eaec;
  line-height: 1.42857;
 
  font-size:11px;
 	
  vertical-align: top; 
}
	
</style>
