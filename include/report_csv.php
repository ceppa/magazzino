<?

$filename=sprintf("report.xml");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=$filename;");
header("Content-Type: application/ms-excel");
header("Pragma: no-cache");
header("Expires: 0");


$header='<?xml version="1.0" encoding="UTF-8"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns:c="urn:schemas-microsoft-com:office:component:spreadsheet" 
	xmlns:html="http://www.w3.org/TR/REC-html40" 
	xmlns:o="urn:schemas-microsoft-com:office:office" 
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
	xmlns="urn:schemas-microsoft-com:office:spreadsheet" 
	xmlns:x2="http://schemas.microsoft.com/office/excel/2003/xml" 
	xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" 
	xmlns:x="urn:schemas-microsoft-com:office:excel">
	<OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office">
		<Colors>
			<Color>
				<Index>3</Index>
				<RGB>#c0c0c0</RGB>
			</Color>
			<Color>
				<Index>4</Index>
				<RGB>#ff0000</RGB>
			</Color>
		</Colors>
	</OfficeDocumentSettings>
	<ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
		<WindowHeight>9000</WindowHeight>
		<WindowWidth>13860</WindowWidth>
		<WindowTopX>240</WindowTopX>
		<WindowTopY>75</WindowTopY>
		<ProtectStructure>False</ProtectStructure>
		<ProtectWindows>False</ProtectWindows>
	</ExcelWorkbook>
	<Styles>
		<Style ss:ID="Default" ss:Name="Default"/>
		<Style ss:ID="Result" ss:Name="Result">
			<Font ss:Bold="1" ss:Italic="1" ss:Underline="Single"/>
		</Style>
		<Style ss:ID="Result2" ss:Name="Result2">
			<Font ss:Bold="1" ss:Italic="1" ss:Underline="Single"/>
			<NumberFormat ss:Format="Currency"/>
		</Style>
		<Style ss:ID="Heading" ss:Name="Heading">
			<Font ss:Bold="1" ss:Italic="1" ss:Size="16"/>
		</Style>
		<Style ss:ID="Heading1" ss:Name="Heading1">
			<Font ss:Bold="1" ss:Italic="1" ss:Size="16"/>
		</Style>
		<Style ss:ID="co1"/>
		<Style ss:ID="ta1"/>
		<Style ss:ID="ta_extref"/>
	</Styles>
	<ss:Worksheet ss:Name="gniolatino">
		<Table ss:StyleID="ta1">
';

	echo $header;
	for($i=0;$i<count($columns);$i++)
		echo '<Column ss:Width="64"/>';

	echo '<Row ss:AutoFitHeight="1">';
	foreach($columns as $column)
		echo '<Cell>
					<Data ss:Type="String">'.$column["text"].'</Data>
				</Cell>';
	echo '</Row>';


	foreach($out as $line)
	{
		echo '<Row ss:AutoFitHeight="1">';
		foreach($columns as $column)
			echo '<Cell>
					<Data ss:Type="String">'.$line[$column["name"]].'</Data>
				</Cell>';
		echo '</Row>';
	}
echo '

		</Table>
		<x:WorksheetOptions/>
	</ss:Worksheet>
</Workbook>';
die();
?>
