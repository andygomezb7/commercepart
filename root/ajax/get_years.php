<?php

$currentYear = date('Y');
for ($year = 1940; $year <= $currentYear; $year++) {
     $years[] = $year;
}

// Eliminar duplicados y ordenar los años
$years = array_unique($years);
sort($years);

// Devolver los años en formato JSON
echo json_encode($years);
?>
