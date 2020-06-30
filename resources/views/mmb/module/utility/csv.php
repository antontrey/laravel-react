<?php

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename='.$title.'-data.'.strtotime(date("d-m-Y")).'.csv');


// create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');

	$head= array();
	foreach($fields as $f )
	{
		if($f['download'] =='1')
		{
			$limited = isset($field['limited']) ? $field['limited'] :'';
			if(\App\Library\SiteHelpers::filterColumn($limited ))
			{
				$head[] = $f['field'];

			}
		}
	}

	fputcsv($output, $head);

// fetch the data

	foreach ($rows as $row)
	{

		$content= array();
		foreach($fields as $f )
		{
			if($f['download'] =='1'):
				$limited = isset($field['limited']) ? $field['limited'] :'';
				if(\App\Library\SiteHelpers::filterColumn($limited ))
				{
					$content[]=  \App\Library\SiteHelpers::formatRows($row->{$f['field']},$f,$row) ;
				}

			endif;
		}


		//echo '<pre>';print_r($content);echo '</pre>';
		fputcsv($output,$content);
		//fputcsv($fp, $content);

	}
//fclose($file);
/*
// output headers so that the file is downloaded rather than displayed
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename='.$title.' '.date("d/m/Y").'.csv');
// create a file pointer connected to the output stream
$fp = fopen('php://output', 'w');
// loop over the rows, outputting them
*/
/*
foreach ($rows as $row)
{
	$content= array();
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
	//fputcsv($fp, $content);

}
return $content;
//fclose($fp);
//exit;

?>
