<?php
ob_start();
date_default_timezone_set('America/Regina');
require_once __DIR__ . '/../Resource/PhpWord/vendor/autoload.php';

use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\TablePosition;
use PhpOffice\PhpWord\SimpleType\DocProtect;
use PhpOffice\PhpWord\Element\Section;

$get_date_star = $_REQUEST['date_star'];
$get_date_end = $_REQUEST['date_end'];
$tipo_alumno = $_REQUEST['tipo_alumno'];
$datos1 = $_REQUEST['datos1'];
$datos2 = $_REQUEST['datos2'];

if ($get_date_star == 0) {
    $date_star = 0;
} else {
    $newstar = explode('/', $get_date_star);
    $getdaystar = $newstar[0];
    $getmonthstar = $newstar[1];
    $getyearstar = $newstar[2];
    $date_star = $getyearstar . '-' . $getmonthstar . '-' . $getdaystar;
}

if ($get_date_end == 0) {
    $date_end = 0;
} else {
    $newend = explode('/', $get_date_end);
    $getdayend = $newend[0];
    $getmonthend = $newend[1];
    $getyearend = $newend[2];
    $date_end = $getyearend . '-' . $getmonthend . '-' . $getdayend;
}

$aniostar = date("Y", strtotime($date_star));
$messtar = date("m", strtotime($date_star));
$diastar = date("d", strtotime($date_star));

$anioend = date("Y", strtotime($date_end));
$mesend = date("m", strtotime($date_end));
$diaend = date("d", strtotime($date_end));

$meses = array('ENERO', "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SEPTIEMBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE");
$mes_star = date("m", strtotime($date_star));
$mes_end = date("m", strtotime($date_end));

$fecha_star = $meses[date($mes_star) - 1];
$fecha_end = $meses[date($mes_end) - 1];

require_once '../models/Reportsheet.php';

$DBobj = new Reportsheet();

$obj = $DBobj->listar();
$rows = $obj->fetch_object();
$institucion = $rows->nombre;
$logo = $rows->logo;

$IMG = '../Resource/files/logo/' . $logo;

if ($datos1 == "" && $datos2 == "") {
    $rspta = $DBobj->listarall($date_star, $date_end, $tipo_alumno);
} else {
    $rspta = $DBobj->listarstudent($date_star, $date_end, $tipo_alumno, $datos1, $datos2);
}

$phpWord = new PhpOffice\PhpWord\PhpWord();
$phpWord->setDefaultFontSize(9);

$languageEsGb = new PhpOffice\PhpWord\Style\Language(\PhpOffice\PhpWord\Style\Language::ES_ES);
$phpWord->getSettings()->setThemeFontLang($languageEsGb);

//$documentProtection = $phpWord->getSettings()->getDocumentProtection();
//$documentProtection->setEditing(DocProtect::READ_ONLY);
//$documentProtection->setPassword('report');

$section = $phpWord->addSection(array("orientation" => "landscape"));

// LOGOOS
$table = $section->addTable();
$table->addRow();

// Celda izquierda para el primer logo
$cell1 = $table->addCell(3333, array('valign' => 'center', 'vMerge' => 'restart'));
$cell1->addImage("../resource/files/logo/logosep.png", array('width' => 130, 'height' => 60));

// Celda central vacía
$cell2 = $table->addCell(25000, array('valign' => 'center'));
$cell2->addText('');

// Celda derecha para el segundo logo
$cell3 = $table->addCell(3333, array('valign' => 'center', 'vMerge' => 'restart'));
$cell3->addImage("../resource/files/logo/logo2.png", array('width' => 155, 'height' => 70));


$fancyTableStyle = array('borderSize' => 6, 'borderColor' => '92D050', 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER);
$cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center', 'bgColor' => '#FFFFFF');
$cellRowContinue = array('vMerge' => 'continue');
$cellColSpan4 = array('gridSpan' => 4, 'bgColor' => 'FFFFCC');

$cellHCentered = array('spaceBefore' => 5, 'spaceAfter' => 5, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER);
$dafault = array('spaceBefore' => 5, 'spaceAfter' => 5);
$cellVCentered = array('valign' => 'center');

$ii = 1;
if ($rows > 0) {
    $table = $section->addTable("tables");
    $table->setWidth(100 * 50);
    $phpWord->addTableStyle('tables', $fancyTableStyle);

    $table->addRow();
    $cell1 = $table->addCell(2000, $cellRowSpan);
    $textrun1 = $cell1->addTextRun($cellHCentered);
    $textrun1->addImage($IMG, array(
        'width' => \PhpOffice\PhpWord\Shared\Converter::cmToPixel(1),
        'height' => \PhpOffice\PhpWord\Shared\Converter::cmToPixel(0.8),
        'positioning' => \PhpOffice\PhpWord\Style\Image::POSITION_RELATIVE,
        'posHorizontal' => \PhpOffice\PhpWord\Style\Image::POSITION_HORIZONTAL_CENTER,
        'posHorizontalRel' => \PhpOffice\PhpWord\Style\Image::POSITION_RELATIVE_TO_COLUMN,
        'posVertical' => \PhpOffice\PhpWord\Style\Image::POSITION_VERTICAL_TOP,
        'posVerticalRel' => \PhpOffice\PhpWord\Style\Image::POSITION_RELATIVE_TO_LINE,
    ));

    $cell2 = $table->addCell(4000, $cellColSpan4);
    $textrun2 = $cell2->addTextRun($cellHCentered);
    $textrun2->addText($institucion, null, $cellHCentered);

    $table->addRow();
    $table->addCell(null, $cellRowContinue);
    $cell2 = $table->addCell(4000, $cellColSpan4);
    $textrun2 = $cell2->addTextRun($cellHCentered);
    $textrun2->addText('LISTADO DE ASISTENCIAS', null, $cellHCentered);

    $table->addRow();
    $table->addCell(null, $cellRowContinue);
    $table->addCell(2000, array('bgColor' => 'FFFFCC'), $cellVCentered)->addText('GRADO ', null, $cellHCentered);
    $table->addCell(6000)->addText($datos1, null, $cellHCentered);
    $table->addCell(2000, array('bgColor' => 'FFFFCC'), $cellVCentered)->addText('GRUPO', null, $cellHCentered);
    $table->addCell(6000)->addText($datos2, null, $cellHCentered);

    $section->addTextBreak(1);

    $styleTable = array('borderSize' => 6, 'borderColor' => '92D050', 'size' => 8, 'cellMargin' => 30, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER);
	$styleFirstRow = array('borderBottomColor' => 'FFFFFF', 'bgColor' => 'FFFFCC');
	
	$cellColor2 = array('gridSpan' => 2); // SE TUVO QUE MODIFICAR PARA QUE LAS CELDAS DE FALTO E INASISTENCIA NO SE VIERA DE COLOR VERDE
	$cellColor5 = array('gridSpan' => 5, 'bgColor' => '006B3D');
	
	$table = $section->addTable("table");
	$table->setWidth(100 * 50);
	
	$table->addRow();
	$table->addCell(null, $cellColor5)->addText('DATOS DE LOS ALUMNOS', array('color' => 'FFFFFF'), $cellHCentered);
	$table->addCell(null, $cellColor5)->addText($diastar . ' ' . $fecha_star . ' - ' . $diaend . ' ' . $fecha_end, array('color' => 'FFFFFF'), $cellHCentered);
	//$table->addCell(null, $cellColor3)->addText($diastar . ' ' . $fecha_star . ' - ' . $diaend . ' ' . $fecha_end, array('color' => 'FFFFFF'), $cellHCentered);
	
	$table->addRow();
	$table->addCell(NULL, array('borderBottomColor' => 'FFFFFF', 'bgColor' => '006B3D'))->addText('ID', array('color' => 'FFFFFF', 'size' => 8), $cellHCentered);
	$table->addCell(NULL, array('borderBottomColor' => 'FFFFFF', 'bgColor' => '006B3D'))->addText('IDALU', array('color' => 'FFFFFF', 'size' => 8), $cellHCentered);
	$table->addCell(NULL, array('borderBottomColor' => 'FFFFFF', 'bgColor' => '006B3D'))->addText('CURP', array('color' => 'FFFFFF', 'size' => 8), $cellHCentered);
	$table->addCell(NULL, array('borderBottomColor' => 'FFFFFF', 'bgColor' => '006B3D'))->addText('APELLIDOS', array('color' => 'FFFFFF', 'size' => 8), $cellHCentered);
	$table->addCell(NULL, array('borderBottomColor' => 'FFFFFF', 'bgColor' => '006B3D'))->addText('NOMBRES', array('color' => 'FFFFFF', 'size' => 8), $cellHCentered);
	$table->addCell(NULL, array('borderBottomColor' => 'FFFFFF', 'bgColor' => '006B3D'))->addText('GRADO', array('color' => 'FFFFFF', 'size' => 8), $cellHCentered);
	$table->addCell(NULL, array('borderBottomColor' => 'FFFFFF', 'bgColor' => '006B3D'))->addText('GRUPO', array('color' => 'FFFFFF', 'size' => 8), $cellHCentered);
	$table->addCell(NULL, array('borderBottomColor' => 'FFFFFF', 'bgColor' => '006B3D'))->addText('FECHA', array('color' => 'FFFFFF', 'size' => 8), $cellHCentered);
	$table->addCell(NULL, array('borderBottomColor' => 'FFFFFF', 'bgColor' => '006B3D'))->addText('H. ENTRADA', array('color' => 'FFFFFF', 'size' => 8), $cellHCentered);
	$table->addCell(NULL, array('borderBottomColor' => 'FFFFFF', 'bgColor' => '006B3D'))->addText('H. SALIDA', array('color' => 'FFFFFF', 'size' => 8), $cellHCentered);
	
	$time_star = "";
	$time_end = "";
	$i = 1;
	while ($reg = $rspta->fetch_object()) {
		if ($reg->kind_id == 1) {
			$time_star = $reg->time_star;
			$time_end = $reg->time_end;
		} else if ($reg->kind_id == 2) {
			$time_star = 'Justificación';
		} else if ($reg->kind_id == 3) {
			$time_star = 'Faltó';
		}
	
		$table->addRow();
		$table->addCell()->addText($i, null, $cellHCentered);
		$table->addCell()->addText($reg->idalu, array('color' => '000000'), $dafault);
		$table->addCell()->addText($reg->curp, array('color' => '000000'), $dafault);
		$table->addCell()->addText($reg->apellidos, array('color' => '000000'), $dafault);
		$table->addCell()->addText($reg->nombre, array('color' => '000000'), $dafault);
		$table->addCell()->addText($reg->datos1, array('color' => '#000000'), $cellHCentered);
		$table->addCell()->addText($reg->datos2, array('color' => '#000000'), $cellHCentered);
		$table->addCell()->addText($reg->fecha, array('color' => '#000000'), $cellHCentered);
	
		if ($reg->kind_id == 2) {
			$table->addCell(NULL, $cellColor2)->addText($time_star, array('color' => '#1129E0'), $cellHCentered);
		} else if ($reg->kind_id == 3) {
			$table->addCell(NULL, $cellColor2)->addText($time_star, array('color' => '#FF0000'), $cellHCentered);
		} else {
			$table->addCell()->addText($time_star, array('color' => '#000000'), $cellHCentered);
			$table->addCell()->addText($time_end, array('color' => '#000000'), $cellHCentered);
		}
		$i++;
	}
	} else {
	echo "<script>alert('No hay alumno seleccionado')</script>";
	echo "<script>window.close();</script>";
	exit;
	}
	
	$phpWord->addTableStyle('table', $styleTable, $styleFirstRow);
	
	$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
	header("Content-type: application/vnd.ms-word");
	header("Content-Disposition: attachment;Filename=DATA.docx");
	header('Cache-Control: max-age=0');
	header('Cache-Control: max-age=1');
	ob_end_clean();
	$objWriter->save('php://output');

