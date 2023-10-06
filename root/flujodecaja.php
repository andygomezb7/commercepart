<?php
$mensaje = '';

$start_date = (@$_POST['startdate'] ? $_POST['startdate'] : date('Y-m-d', strtotime("-3 Months")));
$end_date = (@$_POST['enddate'] ? $_POST['enddate'] : date('Y-m-d'));

$postdates = ($start_date && $end_date ? '&start=' . $start_date .'&end=' . $end_date : '');

?>

<?php if (!empty($mensaje)) : ?>
    <!-- Mensaje de éxito o error -->
    <div class="alert alert-success" role="alert">
        <?php echo $mensaje; ?>
    </div>
<?php endif; ?>

<h5>Filtros</h5>
<form method="post" action="">
    <div class="form-row">
        <div class="form-group col-md-4" data-select2-id="select2-data-4-1vuz">
                <div class="input-group">
                    <input type="date" class="form-control" value="<?php echo $start_date; ?>" name="startdate">
                    <span class="input-group-addon"> &nbsp; <i class="fa fa-calendar"></i>  &nbsp; </span>
                    <input type="date" class="form-control" value="<?php echo $end_date; ?>" name="enddate">
                </div>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-success" value="Filtrar" />
        </div>
    </div>
</form>

<style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            text-align: center;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        .total-row {
            font-weight: bold;
        }
    </style>

<div id="tabla-flujo-caja"></div>

<script>
(function($) {
    $.fn.crearTablaFlujoCaja = function(datos) {
        var $tabla = $('<table>');
        var $thead = $('<thead>');
        var $tbody = $('<tbody>');

        var meses = obtenerMesesUnicos(datos);

        // Agregar encabezados de columna para los meses
        var $encabezadoMeses = $('<tr>');
        $encabezadoMeses.append($('<th>').text('Cuenta Contable'));
        $.each(meses, function(_, mes) {
            $encabezadoMeses.append($('<th>').text(mes));
        });
        $thead.append($encabezadoMeses);

        // Organizar los datos en una estructura anidada
        var datosAnidados = organizarDatosAnidados(datos);

        // Función recursiva para crear filas anidadas
        function crearFilasAnidadas(datos, $filaPadre, nivel) {
            $.each(datos, function(_, fila) {
                if (fila.nivel === nivel) {
                    var $fila = $('<tr>');
                    $fila.append($('<td>').text(fila.nombreCuenta));

                    $.each(meses, function(_, mes) {
                        if (fila.mes === mes) {
                            $fila.append($('<td>').text(fila.tipoCuenta === 'Egresos' ? 'Q' + fila.totalEgresos.toFixed(2) : 'Q' + fila.totalIngresos.toFixed(2)));
                        } else {
                            $fila.append($('<td>').text('Q0.00'));
                        }
                    });

                    $filaPadre.after($fila);

                    // Llamar recursivamente para crear subcuentas
                    crearFilasAnidadas(datos, $fila, nivel + 1);
                }
            });
        }

        // Crear filas iniciales
        $.each(datosAnidados, function(_, fila) {
            if (fila.nivel === 0) {
                var $fila = $('<tr>');
                $fila.append($('<td>').text(fila.nombreCuenta));

                $.each(meses, function(_, mes) {
                    if (fila.mes === mes) {
                        $fila.append($('<td>').text(fila.tipoCuenta === 'Egresos' ? 'Q' + fila.totalEgresos.toFixed(2) : 'Q' + fila.totalIngresos.toFixed(2)));
                    } else {
                        $fila.append($('<td>').text('Q0.00'));
                    }
                });

                $tbody.append($fila);

                // Llamar recursivamente para crear subcuentas
                crearFilasAnidadas(datosAnidados, $fila, 1);
            }
        });

        $tabla.append($thead, $tbody);
        this.append($tabla);
    };

    function obtenerMesesUnicos(datos) {
        var mesesUnicos = [];
        $.each(datos, function(_, fila) {
            if ($.inArray(fila.mes, mesesUnicos) === -1) {
                mesesUnicos.push(fila.mes);
            }
        });
        return mesesUnicos;
    }

    function organizarDatosAnidados(datos) {
        var datosAnidados = [];
        var cuentasAnidadas = {};

        $.each(datos, function(_, fila) {
            var cuentaId = fila.idCuenta;
            var cuentaPadre = fila.cuentaPadre;

            if (!cuentasAnidadas[cuentaId]) {
                cuentasAnidadas[cuentaId] = [];
            }

            var cuentaAnidada = {
                idCuenta: cuentaId,
                nombreCuenta: fila.nombreCuenta,
                tipoCuenta: fila.tipoCuenta,
                mes: fila.mes,
                totalIngresos: fila.totalIngresos,
                totalEgresos: fila.totalEgresos,
                nivel: 0
            };

            if (cuentaPadre !== 0) {
                cuentaAnidada.nivel = 1;
            }

            cuentasAnidadas[cuentaId].push(cuentaAnidada);
        });

        $.each(cuentasAnidadas, function(cuentaId, cuentas) {
            $.each(cuentas, function(_, cuenta) {
                datosAnidados.push(cuenta);
            });
        });

        return datosAnidados;
    }
})(jQuery);
$(document).ready(function() {
    $.ajax({
        url: 'ajax/get_data_table.php',
        method: 'GET',
        data: { method: 'flujodecaja', 'start': '<?php echo $start_date; ?>', 'end': '<?php echo $end_date; ?>' },
        dataType: 'json',
        success: function(datos) {
            // Llama al plugin para crear la tabla de flujo de caja
            $('#tabla-flujo-caja').crearTablaFlujoCaja(datos);
        },
        error: function() {
            console.error('Error al obtener los datos');
        }
    });
});
</script>