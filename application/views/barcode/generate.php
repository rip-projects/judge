<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
        "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>BARCODE</title>
</head>
<body>
<table width='50%' align='center' cellpadding='20'>
<tr>
<?php 

for($i=0 ;$i<count($barcodes);){
	if ($i % 3 ==0 and $i!=0)
	{
		echo '</tr><tr>';
	}	
	echo "<td><img src='../../../barcode/generate_image/".$barcodes[$i]."/".$texts[$i]."'/></td>";
	$i++;
}
?>
</tr>
</table>
</body>
</html>