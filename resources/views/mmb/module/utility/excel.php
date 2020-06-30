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
	$content .= '<table border="1">';
	$content .= '<tr>';
	foreach($fields as $f )
	{
		if($f['download'] =='1')
		{
			$limited = isset($field['limited']) ? $field['limited'] :'';
			if(\App\Library\SiteHelpers::filterColumn($limited ))
			{
				$content .= '<th style="background:#f9f9f9;">'. $f['label'] . '</th>';

			}
		}
	}
	$content .= '</tr>';

	foreach ($rows as $row)
	{
		$content .= '<tr>';
		foreach($fields as $f )
		{
			if($f['download'] =='1'):
				$limited = isset($field['limited']) ? $field['limited'] :'';
				if(\App\Library\SiteHelpers::filterColumn($limited ))
				{
					$content .= '<td> '. \App\Library\SiteHelpers::formatRows($row->{$f['field']},$f,$row) . '</td>';
				}
			endif;
		}
		$content .= '</tr>';
	}
	$content .= '</table>';

	@header('Content-Type: application/ms-excel');
	@header('Content-Length: '.strlen($content));
	@header('Content-disposition: inline; filename="'.$title.' '.date("d/m/Y").'.xls"');

	echo $content;
	exit;

/*

$content = $title;
$content .= '<table border="1">';
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
		if($f['download'] =='1')	$content .= '<td>'. $row[$f['field']] . '</td>';
	}
	$content .= '</tr>';
}
$content .= '</table>';

@header('Content-Type: application/ms-excel');
@header('Content-Length: '.strlen($content));
@header('Content-disposition: inline; filename="'.$title.' '.date("d/m/Y").'.xls"');

echo $content;
*/
?>
