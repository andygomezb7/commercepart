<?php
// Incluimos las librerías
require('../../secure/library/fpdf186/fpdf.php');
require('../../secure/library/barcode.php');
require('../../secure/trun.php');

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','',7);
$resultado = $db->query("SELECT * FROM repuestos LIMIT 40");

$contador = 0;
$x = 5;
$espacio_x = 17;
$espacio_y = 0;
$codigosPorPagina = 3; // Número de códigos por página

foreach($resultado as $fila) {
    // código de pais, código de la empresa, código del producto
    $codigo = generate_barcode_text('740', '4174', sprintf("%05d", $fila['id']));
    $nombre = $fila['nombre'];

    barcode("temp.png", $codigo, 40, "horizontal", "code128", false, 1);
    $pdf->Image("temp.png", $x + $espacio_x, $pdf->GetY() + $espacio_y, 40);
    $pdf->SetXY($x + $espacio_x, $pdf->GetY() + $espacio_y);

    $pdf->Cell(40,30,$nombre,0,0,'C',false,null,true);
    $contador++;

    if ($contador == $codigosPorPagina) {
        $contador = 0;
        $x = 5;
        $pdf->SetY($pdf->GetY() + 45 + $espacio_y);
        if ($pdf->GetY() > 230) { // Si no cabe en la página actual, crea una nueva
            $pdf->AddPage();
        }
    } else {
        $x += 50 + $espacio_x;
    }
}
$pdf->Output();
?>