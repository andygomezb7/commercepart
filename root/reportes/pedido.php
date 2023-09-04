<?php
// Incluimos la librería FPDF
require('../../secure/library/fpdf186/fpdf.php');

// Creamos un objeto FPDF
$pdf = new FPDF();

// Añadimos una página al documento
$pdf->AddPage();

// Establecemos la fuente Arial, negrita y tamaño 16
$pdf->SetFont('Arial','B',16);

// Escribimos el título del documento a la izquierda
$pdf->Cell(0,10,'Pedido',0,1,'L');

// Establecemos la fuente Arial, normal y tamaño 12
$pdf->SetFont('Arial','',12);

// Escribimos el texto del header con la información del pedido (simulada)
$pdf->Cell(50,10,'Pedido No: 1234567890',0,0,'L');
$pdf->Cell(50,10,'Fecha: 03/09/2023',0,1,'L');
$pdf->Cell(50,10,'Cliente: Juan Perez',0,0,'L');
$pdf->Cell(50,10,'NIT: 123456-7',0,1,'L');
$pdf->Cell(50,10,'Empresa: ABC S.A.',0,0,'L');
$pdf->Cell(50,10,'Dirección: Calle 1 #2-3',0,1,'L');
$pdf->Cell(50,10,'Teléfono: 12345678',0,0,'L');
$pdf->Cell(50,10,'Email: juan@abc.com',0,1,'L');

// Dibujamos una línea horizontal para separar el header de la tabla
$pdf->Line(10,80,200,80);

// Salto de línea para separar el header de la tabla
$pdf->Ln();

// Creamos un array con los nombres de las columnas de la tabla
$columnas = array('Codigo', 'Descripcion', 'Cantidad', 'Precio', 'Descuento', 'Sub-Total', 'IVA', 'Total');

// Creamos un array con los anchos de las columnas de la tabla
$anchos = array(20, 40, 20, 20, 20, 20, 20, 20);

// Creamos un array con los datos de la tabla (simulados)
$datos = array(
    array('MITSU01', 'Mitsubishi', '1', '100.00', '0.00', '100.00', '21.00', '121.00'),
    array('TOYOT01', 'Toyota', '2', '50.00', '10.00', '80.00', '16.80', '96.80'),
    array('LANCER01', 'Lancer', '3', '30.00', '5.00', '75.00', '15.75', '90.75'),
    // Añadir más filas según sea necesario
);

// Creamos un array con los totales de la tabla (simulados)
$totales = array('Sub-Total' => 255.00, 'IVA' => 53.55, 'Total' => 308.55);

// Establecemos el color de fondo negro para las celdas de las columnas
$pdf->SetFillColor(0);

// Establecemos el color de texto blanco para las celdas de las columnas
$pdf->SetTextColor(255);

// Recorremos el array de las columnas y escribimos sus nombres con el ancho correspondiente
foreach($columnas as $i => $columna) {
    $pdf->Cell($anchos[$i],10,$columna,0,0,'C',true);
}

// Salto de línea para empezar a escribir los datos
$pdf->Ln();

// Establecemos el color de texto negro para las celdas de los datos
$pdf->SetTextColor(0);

// Recorremos el array de los datos y escribimos cada fila con el ancho correspondiente a cada columna
foreach($datos as $fila) {
    foreach($fila as $i => $dato) {
        // Establecemos el grosor del borde a 0 mm para que no se vea ningún borde
        $pdf->SetLineWidth(0);
        $pdf->Cell($anchos[$i],10,$dato,0,0,'C');
    }
    // Salto de línea para pasar a la siguiente fila
    $pdf->Ln();
}

// Dibujamos una línea horizontal para separar los datos de los totales
$pdf->Line(10,140,200,140);

// Recorremos el array de los totales y escribimos cada uno
foreach($totales as $nombre => $valor) {
    // Escribimos el nombre del total con el ancho de las primeras 4 columnas
    $pdf->Cell(array_sum(array_slice($anchos, 0, 4)),10,$nombre,0,0,'R');
    // Escribimos el valor del total con el ancho de las últimas 4 columnas
    $pdf->Cell(array_sum(array_slice($anchos, 4)),10,$valor,0,0,'R');
    // Salto de línea para pasar al siguiente total
    $pdf->Ln();
}

// Escribimos el flete y el seguro a la izquierda con el ancho de las primeras 4 columnas
$pdf->Cell(array_sum(array_slice($anchos, 0, 4)),10,'Flete: 0.00',0,0,'L');
$pdf->Cell(array_sum(array_slice($anchos, 4)),10,'Seguro: 0.00',0,1,'L');

// Generamos el archivo PDF y lo mostramos en el navegador
$pdf->Output();
?>
