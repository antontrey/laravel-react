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


$word_xmlns = "xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'&#8221";;
	$word_xml_settings = "<xml><w:WordDocument><w:View>Print</w:View><w:Zoom>100</w:Zoom></w:WordDocument></xml>";
	$word_landscape_style = "@page {size:8.5in 11.0in; margin:0.5in 0.31in 0.42in 0.25in;} div.Section1{page:Section1;}";
	$word_landscape_div_start = "<div class='Section1'>";
	$word_landscape_div_end = "</div>";
	$content = '
	<html '.$word_xmlns.'>
	<head>'.$word_xml_settings.'<style type="text/css">
	'.$word_landscape_style.' table,td {border:1px solid #f9f9f9;} </style>
	</head>
	<body>'.$word_landscape_div_start.$content.$word_landscape_div_end.'</body>
	</html>
	';




	@header('Content-Type: application/msword');
	@header('Content-Length: '.strlen($content));
	@header('Content-disposition: inline; filename="'.$title.' '.date("d/m/Y").'.doc"');
	echo $content;
		exit;
?>
