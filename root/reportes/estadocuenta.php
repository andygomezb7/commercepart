<?php
// Incluimos la librería FPDF
require('../../secure/library/fpdf186/fpdf.php');

// Creamos un objeto FPDF
$pdf = new FPDF();

// Añadimos una página al documento
$pdf->AddPage();

// Establecemos la fuente Arial, negrita y tamaño 16
$pdf->SetFont('Arial','B',16);

// Creamos un rectángulo negro en la parte superior de la página
$pdf->SetFillColor(0);
$pdf->Rect(0,0,210,20,'F');

// Escribimos el título del documento en blanco sobre el rectángulo negro
$pdf->SetTextColor(255);
$pdf->Cell(0,10,'CXC - Estado de Cuenta',0,1,'C');

// Establecemos el color de texto negro para el resto del documento
$pdf->SetTextColor(0);

// Establecemos la fuente Arial, normal y tamaño 12
$pdf->SetFont('Arial','',12);

// Escribimos el encabezado con los datos del cliente (simulados)
$pdf->Cell(40,10,'Cliente: Juan Perez',0,0);
$pdf->Cell(40,10,'NIT: 123456-7',0,0);
$pdf->Cell(40,10,'Fecha: 03/09/2023',0,1);

// Salto de línea para separar el encabezado de la tabla
$pdf->Ln();

// Creamos un array con los nombres de las columnas de la tabla
$columnas = array('Documento', 'Fecha', 'Referencia', 'Descripcion', 'Dias', 'Cargo', 'Abono', 'Saldo');

// Creamos un array con los anchos de las columnas de la tabla
$anchos = array(25, 25, 25, 50, 15, 20, 20, 20);

// Creamos un array con los datos de la tabla (simulados)
$datos = array(
    array('FAC-001', '01/09/2023', '1234567890', 'Venta de productos', '30', '1000.00', '0.00', '1000.00'),
    array('REC-001', '02/09/2023', '1234567891', 'Pago parcial', '29', '0.00', '500.00', '500.00'),
    array('FAC-002', '03/09/2023', '1234567892', 'Venta de servicios', '28', '2000.00', '0.00', '2500.00'),
    array('REC-002', '04/09/2023', '1234567893', 'Pago total', '27', '0.00', '-2500.00', '0.00'),
    // Añadir más filas según sea necesario
);

// Establecemos el color de fondo gris para las celdas de las columnas
$pdf->SetFillColor(200);

// Recorremos el array de las columnas y escribimos sus nombres con el ancho correspondiente
foreach($columnas as $i => $columna) {
    $pdf->Cell($anchos[$i],10,$columna,1,0,'C',true);
}

// Salto de línea para empezar a escribir los datos
$pdf->Ln();

// Recorremos el array de los datos y escribimos cada fila con el ancho correspondiente a cada columna
foreach($datos as $fila) {
    foreach($fila as $i => $dato) {
        // Establecemos el grosor del borde a 0.2 mm para que sea más fino que el predeterminado
        $pdf->SetLineWidth(0.2);
        $pdf->Cell($anchos[$i],10,$dato,1,0,'C');
    }
    // Salto de línea para pasar a la siguiente fila
    $pdf->Ln();
}

// Generamos el archivo PDF y lo mostramos en el navegador
$pdf->Output();
?>
