<?
require_once('File/PDF.php');
require_once("pdf.php");
error_reporting(E_ALL ^ E_STRICT);

class My_File_PDF extends File_PDF 
{
	function footer()
	{
		$this->setY(-10);
		$this->setFont('arial', 'I', 8);
		$this->cell(0, 10, 'Page '.$this->getPageNo().' of {nb}', 0, 0, 'C');
	}
	function getRowHeight($line,$columns,$cellHeight)
	{
		$pdf2 = clone $this;
		$pdf2->addPage();
		$height=0;

		foreach($columns as $column)
		{
			$pdf2->setY(0);
			$newline=false;
			if(isset($column["newline"]))
				$newline=$column["newline"];
			$h=$pdf2->safeCell(pdfstring($line[$column["name"]],$newline)
				,$column["width"],$cellHeight,$column["align"],$cellHeight);
			if($h>$height)
				$height=$h;
		
		}
		unset($pdf2);
		return $height;
	}

	function myStringWidth($string)
	{
		if(strpos($string,"\n")===false)
			$out=1+$this->getStringWidth($string);
		else
		{
			$exploded=explode("\n",$string);
			$out=0;
			foreach($exploded as $ex)
				if($this->getStringWidth($ex)>$out)
					$out=1+$this->getStringWidth($ex);
		}
		return $out;
	}

	function safeCell($string,$cellWidth,$cellHeight,$align,$rowHeight=0)
	{
		$y=$this->getY();

		if($rowHeight==0)
			$rowHeight=$cellHeight;

		if($this->getStringWidth($string)+2<=$cellWidth)
		{
			$this->cell($cellWidth,$rowHeight,$string,1,0,$align);
			$h=$rowHeight;
		}
		else
		{
			$x=$this->getX();
			$y=$this->getY();
			$this->multiCell($cellWidth,$cellHeight/2,$string,1,$align);
			$h = $this->getY()-$y;
			$this->setXY($x+$cellWidth,$y);
		}
		return $h;
	}
}

function do_report($columns,$data,$title,$orientation)
{
	$top=11;
	$margin=10;
	$pageWidth=297;
	$cellHeight=7;

	// create new PDF document
	$File_PDF=new File_PDF();
	$pdf = $File_PDF->factory
	(
		array
		(
			'orientation' => $orientation,
			'unit' => 'mm',
			'format' => 'A4'
		)
		,('My_File_PDF')
	);
	// set document information
	$pdf->setAutoPageBreak(true,15);
	$pdf->aliasNbPages();
	$pdf->setMargins($margin,$top);
	$pdf->addPage();
	$pdf->setDrawColor('gray',0);
	$pdf->setTextColor('gray',0);
	$pdf->setFont("arial","",7);
	$columns=fixColumnWidth($data,$columns,$pageWidth-2*$margin,$pdf);
	$pdf->setLineWidth(0.2);
	$pdf->setFont("arial","BI",15);
	$pdf->cell($pageWidth-2*$margin, $cellHeight, $title,0,1,'C');

	$pdf->setFont("arial","B",7);
	$pdf->setFillColor('gray',0.9);
	foreach($columns as $column)
		$pdf->cell($column["width"], $cellHeight, $column["text"],1,0,'C',1);
	$pdf->newLine($cellHeight);

	$pdf->setFont("arial","",7);


	foreach($data as $line)
	{
		$height=$pdf->getRowHeight($line,$columns,$cellHeight);
		foreach($columns as $column)
		{
			$newline=false;
			if(isset($column["newline"]))
				$newline=$column["newline"];
			$pdf->safeCell(pdfstring($line[$column["name"]],$newline),
				$column["width"],$cellHeight,$column["align"],$height);
		}
		$pdf->newLine($height);
	}
	$pdf->Output(str_replace(" ","_",$title.".pdf"), "I");
}

function fixColumnWidth($data,$columns,$pageWidth,$pdf)
{
	foreach($data as $line)
	{
		foreach($columns as $id=>$column)
		{
			$newline=false;
			if(isset($column["newline"]))
				$newline=$column["newline"];
			$width=$pdf->myStringWidth(pdfstring($line[$column["name"]],$newline));
			if($width+2>$column["width"])
				$columns[$id]["width"]=$width+2;
		}
	}

	$tot=0;
	foreach($columns as $column)
		$tot+=$column["width"];

	if($tot<$pageWidth)
	{
		$scale=$pageWidth/$tot;
		foreach($columns as $id=>$column)
			$columns[$id]["width"]=$column["width"]*$scale;
	}
	else
	{
		$widthArray=array();
		foreach($columns as $id=>$column)
			$widthArray[$id]=$column["width"];
		arsort($widthArray);
		$excludedIndexes=array();
		do
		{
			$excluded=key($widthArray);
			$excludedIndexes[]=$excluded;
			unset($widthArray[$excluded]);
			$newWidth=($pageWidth-array_sum($widthArray))/count($excludedIndexes);
		}
		while($newWidth<current($widthArray));

		foreach($excludedIndexes as $index)
			$columns[$index]["width"]=$newWidth;
	}
	return $columns;
}

?>
