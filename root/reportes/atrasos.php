<?php
require(__DIR__ . '/../../secure/trun.php');

require_once 'PHPExcel.php';
$objPHPExcel = new PHPExcel();

// Crear una nueva hoja de cálculo
$objPHPExcel->setActiveSheetIndex(0);
$sheet = $objPHPExcel->getActiveSheet();

$result = $db->query("SELECT
    p.fecha AS fecha_creacion,
    c.nombre AS cliente_nombre,
    c.observaciones AS cliente_obs,
    DATEDIFF(NOW(), DATE_ADD(p.fecha, INTERVAL p.dias_credito DAY)) AS dias_atraso,
    SUM(d.precio * d.cantidad) AS total_compra
FROM
    pedidos AS p
JOIN
    clientes AS c ON p.cliente_id = c.id
JOIN
    pedido_detalles AS d ON p.id = d.pedido_id
WHERE
    p.estado_id = 3
HAVING
    dias_atraso > 0
GROUP BY
    p.id
");
// Definir encabezados
$sheet->setCellValue('A1', 'Fecha');
$sheet->setCellValue('B1', 'Cliente Nombre');
$sheet->setCellValue('C1', 'Cliente Observaciones');
$sheet->setCellValue('D1', 'Días de Atraso');
$sheet->setCellValue('E1', 'Total de Compra');

// Obtener datos de la base de datos (usando la consulta SQL)
$row = 2; // Fila 2

while ($row_data = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $row, $row_data['fecha']);
    $sheet->setCellValue('B' . $row, $row_data['cliente_nombre']);
    $sheet->setCellValue('C' . $row, $row_data['cliente_obs']);
    $sheet->setCellValue('D' . $row, $row_data['dias_atraso']);
    $sheet->setCellValue('E' . $row, $row_data['total_compra']);
    $row++;
}

// Crear un archivo Excel
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('reporte.xlsx');
