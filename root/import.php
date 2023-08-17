<?php
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Mensaje de éxito o error
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_excel'])) {
    // Obtener información del archivo cargado
    $archivoExcel = $_FILES['archivo_excel'];
    $nombreArchivo = $archivoExcel['name'];
    $nombreTemporal = $archivoExcel['tmp_name'];

    // Validar si es un archivo Excel
    $tipoArchivo = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
    if ($tipoArchivo !== 'xlsx' && $tipoArchivo !== 'xls') {
        $mensaje = 'El archivo debe ser un archivo Excel (xlsx o xls).';
    } else {
        // Mover el archivo temporal a una ubicación permanente
        $ubicacionArchivo = 'uploads/' . $nombreArchivo;
        move_uploaded_file($nombreTemporal, $ubicacionArchivo);

        // Cargar la hoja de cálculo activa
        $hojaActiva = IOFactory::load($ubicacionArchivo)->getActiveSheet();

        // Recorrer las filas del archivo de Excel
        foreach ($hojaActiva->getRowIterator() as $fila) {
            $datosFila = [];

            // Obtener los datos de las celdas de la fila actual
            foreach ($fila->getCellIterator() as $celda) {
                $datosFila[] = $celda->getValue();
            }

            // Obtener los datos de cada columna
            $descripcion = $datosFila[1];
            $codigo = $datosFila[0];
            $equivalencia = $datosFila[2];
            $precio = str_replace('Q ', '', $datosFila[3]);

            // Verificar si la descripción o equivalencia son nombres de repuestos
            if (esNombreRepuesto($descripcion)) {
                $nombreRepuesto = $descripcion;
                $descripcionRepuesto = '';
            } else {
                $nombreRepuesto = '';
                $descripcionRepuesto = $descripcion;
            }

            if (esNombreRepuesto($equivalencia)) {
                $nombreRepuesto = $equivalencia;
            }

            // Crear nuevo repuesto
            // $nombreRepuesto = false;                    
            if ($nombreRepuesto) {
                $queryRepuesto = "INSERT INTO repuestos (nombre, descripcion, precio) VALUES ('$nombreRepuesto', '$descripcionRepuesto', $precio)";
                $nuevoRepuesto = $db->query($queryRepuesto);

                if ($nuevoRepuesto) {
                    // Obtener el ID del repuesto recién creado
                    $idRepuesto = $db->insert_id;

                    // Generar códigos y asignarlos al repuesto
                    generarCodigos($codigo, $idRepuesto);

                    // Generar códigos y equivalencias
                    if (esNombreRepuesto($equivalencia)) {
                        $queryEquivalencia = "UPDATE repuestos SET nombre='$nombreRepuesto', descripcion='$descripcionRepuesto', precio='$precio' WHERE id = $idRepuesto";
                        $db->query($queryEquivalencia);
                    } else {
                        generarCodigos($equivalencia, $idRepuesto);
                    }

                    // ASIGNAR
                    $infovehiculo = buscarInfoVehiculos($nombreRepuesto);
                    // var_dump(json_encode($infovehiculo));
                    foreach ($infovehiculo as $item) {
                        if ($item['nombre_marca'] && $item['nombre_modelo']) {
                            $queryAsignacion = "INSERT INTO repuesto_modelos (id_repuesto, id_modelo, marca_id, fecha_inicio, fecha_fin) VALUES (
                                                                            '".$idRepuesto."', '".$item['id_modelo']."', '".$item['id_marca']."', '".$item['anio_inicio']."', '".$item['anio_fin']."')";
                            $nuevaAsignacion = $db->query($queryAsignacion);
                        }
                        // echo "<br><b>Marca:</b> <u>".$item['nombre_marca']."</u><br>
                        //         <b>Modelo:</b> <u>".$item['nombre_modelo']."</u><br>
                        //         <b>Motor:</b> <u>".$item['tipo_motor']."</u><br>
                        //         <b>A&ntilde;os:</b> <u>".$item['anio_inicio']."-".$item['anio_fin']."</u>";
                    }

                } else {
                    echo 'No se creó el repuesto: ' . $db->error . '<br>';
                }
            } else {
                echo "El repuesto no contenía información: <br> Descripción: $descripcion <br> Equivalencia: $equivalencia, (".(esNombreRepuesto($equivalencia) ? 'true' : 'false').")<hr>";
            }
        }

        $mensaje = 'El archivo se ha importado correctamente.';
    }
}


/**
 * Verifica si una cadena es un nombre de repuesto.
 *
 * @param string $cadena Cadena a verificar
 * @return bool True si es un nombre de repuesto, False en caso contrario
 */
function esNombreRepuesto($cadena)
{
    $numeros = preg_replace('/[^0-9]/', '', $cadena);
    $letras = preg_replace('/[^a-zA-Z]/', '', $cadena);

    // strlen($numeros) <= 5 && 
    return strlen($letras)>= 1 && !preg_match('/[A-Z]{2}-\d{4}(?:\/\d{4})?/', $cadena) && (strlen($letras) > strlen($numeros) || substr_count($cadena, ' ') >= 2 || substr_count($texto, '-') <= 0);
    // (empty(strlen($numeros) > strlen($letras)) && substr_count($cadena, 'K') <= 0)
}

function buscarInfoVehiculos($texto) {
    global $db; // Supongo que ya tienes una conexión a la base de datos en esta variable $db

    // Inicializar el arreglo para almacenar la información de los vehículos
    $infoVehiculos = array();

    // Buscar todas las ocurrencias de tipo de motor en el texto
    preg_match_all('/\b\d+(\.\d+)?L\b/', $texto, $matchesTipoMotores);
    $tiposMotores = !empty($matchesTipoMotores[0]) ? $matchesTipoMotores[0] : array(null);

    // Buscar todas las ocurrencias de rango de años en el texto
    preg_match_all('/\b\d{2}-\d{2}\b/', $texto, $matchesRangoAnios);
    $rangoAnios = !empty($matchesRangoAnios[0]) ? $matchesRangoAnios[0] : array(null);

    // Eliminar los tipos de motor y rango de años del texto para buscar marcas y modelos
    $textoSinTipoMotores = preg_replace('/\b\d+(\.\d+)?L\b/', '', $texto);
    $textoSinAnios = preg_replace('/\b\d{2}-\d{2}\b/', '', $textoSinTipoMotores);
    $textoSinAnios = trim($textoSinAnios);

    // Separar el texto en partes por espacios
    $partes = preg_split('/\s+/', $textoSinAnios);

    // Buscar las marcas y modelos en el texto
    $marcaActual = null;
    $modeloActual = null;

    foreach ($partes as $parte) {
        $nombreLimpio = preg_replace('/[^\p{L}\p{N}\s]/u', '', $parte); // Eliminar caracteres especiales
        if (!empty($nombreLimpio)) {
            // Verificar si la parte actual es un modelo
            $queryModelo = "SELECT id, marca_id, nombre FROM modelos WHERE nombre = '$nombreLimpio'";
            $resultModelo = $db->query($queryModelo);
            if ($resultModelo->num_rows > 0) {
                $rowModelo = $resultModelo->fetch_assoc();
                $modeloActual = array(
                    'id' => $rowModelo['id'],
                    'nombre' => $rowModelo['nombre'],
                    'marca_id' => $rowModelo['marca_id']
                );
                continue;
            }
            // Verificar si la parte actual es una marca
            $queryMarca = "SELECT id, nombre FROM marcas WHERE nombre = '$nombreLimpio'";
            $resultMarca = $db->query($queryMarca);
            if ($resultMarca->num_rows > 0) {
                $rowMarca = $resultMarca->fetch_assoc();
                $marcaActual = array(
                    'id' => $rowMarca['id'],
                    'nombre' => $rowMarca['nombre']
                );
                continue;
            } else if($modeloActual) {
                $queryMarca = "SELECT id, nombre FROM marcas WHERE id = '".$modeloActual['marca_id']."'";
                $resultMarca = $db->query($queryMarca);
                $rowMarca = $resultMarca->fetch_assoc();
                $marcaActual = array(
                    'id' => $rowMarca['id'],
                    'nombre' => $rowMarca['nombre']
                );
                continue;
            }


            // Verificar si la parte actual es un tipo de motor
            if (preg_match('/\b\d+(\.\d+)?L\b/', $parte)) {
                $tiposMotores[] = $parte;
                continue;
            }

            // Verificar si la parte actual es un rango de años
            if (preg_match('/\b\d{2}-\d{2}\b/', $parte)) {
                $rangoAnios[] = $parte;
                continue;
            }
        }
    }

    // Generar todas las combinaciones posibles
    foreach ($tiposMotores as $tipoMotor) {
        foreach ($rangoAnios as $rangoAnio) {
            $infoVehiculo = array(
                'id_marca' => $marcaActual ? $marcaActual['id'] : ($modeloActual ? $modeloActual['marca_id'] : null),
                'nombre_marca' => $marcaActual ? $marcaActual['nombre'] : null,
                'id_modelo' => $modeloActual ? $modeloActual['id'] : null,
                'nombre_modelo' => $modeloActual ? $modeloActual['nombre'] : null,
                'tipo_motor' => $tipoMotor,
                'anio_inicio' => null,
                'anio_fin' => null
            );

            // Si hay rango de años, separarlos y guardarlos en el array
            if (!empty($rangoAnios)) {
                $anios = explode('-', $rangoAnio);
                $infoVehiculo['anio_inicio'] = correctyear($anios[0]);
                $infoVehiculo['anio_fin'] = correctyear($anios[1]);
            }

            $infoVehiculos[] = $infoVehiculo;
        }
    }

    return $infoVehiculos;
}

function correctyear($year) {
    $prefix = ($year >= 70) ? '19' : '20'; // Agregar "19" para años 70 o mayores, "20" para años menores a 70
    return $prefix . str_pad($year, 2, '0', STR_PAD_LEFT);
}

function generarCodigos($codigo, $idRepuesto)
{
    global $db;
    $codigos = explode(',', $codigo);
    $codigoGenerado = '';

    foreach ($codigos as $index => $codigo) {
        $codigo = trim($codigo);

        // Verificar si el código contiene solo letras (primer bloque)
        if (!preg_match('/[0-9]/', $codigo)) {
            // Si ya hay un código generado, lo insertamos antes de unir las letras
            if (!empty($codigoGenerado)) {
                $queryCodigo = "INSERT INTO codigos_repuesto (codigo, id_repuesto) VALUES ('$codigoGenerado', $idRepuesto)";
                $db->query($queryCodigo);
            }
            // Unimos el bloque de letras en el código generado
            $codigoGenerado = $codigo;
        } else {
            // Verificar si el código contiene una '/' para reemplazar los últimos dígitos (segundo bloque)
            if (strpos($codigo, '/') !== false) {
                $partes = explode('/', $codigo);
                $codigoBase = $partes[0];
                $cantidadDigitos = $partes[1];
                $codigoGenerado .= '-' . substr($codigoBase, 0, -intval($cantidadDigitos));
                $codigoGenerado .= substr($codigoBase, -$cantidadDigitos);
            } else {
                // Si no hay un bloque de letras previo, simplemente agregamos el código actual
                if (empty($codigoGenerado)) {
                    $codigoGenerado = $codigo;
                } else {
                    // Si ya hay un código generado, lo insertamos antes de agregar el bloque de números
                    $queryCodigo = "INSERT INTO codigos_repuesto (codigo, id_repuesto) VALUES ('$codigoGenerado', $idRepuesto)";
                    $db->query($queryCodigo);
                    // Iniciamos el código generado con el bloque de números actual
                    $codigoGenerado = $codigo;
                }
            }
        }
    }

    // Insertar el último código generado si existe
    if (!empty($codigoGenerado)) {
        $queryCodigo = "INSERT INTO codigos_repuesto (codigo, id_repuesto) VALUES ('$codigoGenerado', $idRepuesto)";
        $db->query($queryCodigo);
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importar Repuestos desde Excel</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h1 class="text-center">Importar Repuestos desde Excel</h1>

        <?php if (!empty($mensaje)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data" class="text-center">
            <div class="form-group">
                <label for="archivo_excel">Seleccione un archivo Excel:</label>
                <input type="file" name="archivo_excel" id="archivo_excel" required>
            </div>
            <button type="submit" class="btn btn-primary">Importar</button>
        </form>
    </div>
</body>

</html>
