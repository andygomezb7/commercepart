/*
* @description "Plugin para ordenar pedidos"
*/
(function($) {
    // Función constructora para el objeto de orden
    function Orden(id, titulo, descripcion, codigos, tieneDescuento, costo, cantidad, reserva = 0, maxdisponible = 0, maxreserva = 0) {
        this.id = id;
        this.titulo = titulo;
        this.descripcion = descripcion;
        this.codigos = codigos;
        this.tieneDescuento = tieneDescuento;
        this.costo = costo;
        this.cantidad = cantidad;
        this.reserva = reserva;
        this.maxDisponible = maxdisponible;
        this.maxReserva = maxreserva;
    }

    // Función para crear una nueva orden y agregarla al arreglo de órdenes
    // $.fn.agregarOrden = function(id, titulo, descripcion, codigos, tieneDescuento, costo, cantidad) {
    //     const nuevaOrden = new Orden(id, titulo, descripcion, codigos, tieneDescuento, costo, cantidad);
    //     let ordenes = this.data('ordenes') || [];
    //     ordenes.push(nuevaOrden);
    //     this.data('ordenes', ordenes);
    //     this.trigger('ordenesActualizadas');
    // };

    $.fn.agregarOrden = function(id, titulo, descripcion, codigos, tieneDescuento, costo, cantidad = 0, reserva = 0, stock = false, maxdisponible = 0, maxreserva = 0) {
        const self = this; // Capturamos 'this' para usarlo dentro de la función de callback

        // Verificar si ya existe una orden con el mismo ID
        const ordenExistente = self.data('ordenes')?.find(orden => orden.id === id) || null;

        $.post('ajax/get_data_info.php?method=detectarbodegas&id='+id+'&cantidad='+(parseInt(cantidad)+parseInt(reserva)), function(data) {
            let info = JSON.parse(data);
            let cantidadDisponible = 0, cantidadDisponibleReserva = 0;

            if (Array.isArray(info)) {
                info.forEach(function(data) {
                    cantidadDisponible += data.cantidad;
                    cantidadDisponibleReserva += data.reserva;
                });
            }

            if (((cantidadDisponible >= cantidad&&cantidad!=0) || (cantidadDisponibleReserva >= reserva&&reserva!=0)) && !stock || stock) {
                if (ordenExistente) {
                    ordenExistente.reserva = parseInt(ordenExistente.reserva);
                    //
                    if (cantidadDisponible >= cantidad && !stock) ordenExistente.cantidad += cantidad;
                    if (cantidadDisponibleReserva >= reserva && !stock) ordenExistente.reserva += reserva;
                } else {

                    if (cantidadDisponible < cantidad && !stock) cantidad = 0;
                    if (cantidadDisponibleReserva < reserva && !stock) reserva = 0;
                    const ordenNueva = {
                        id: id,
                        titulo: titulo,
                        descripcion: descripcion,
                        codigos: codigos,
                        tieneDescuento: tieneDescuento,
                        costo: costo,
                        cantidad: cantidad,
                        reserva: reserva,
                        maxdisponible: maxdisponible,
                        maxreserva: maxreserva
                    };
                    const ordenes = self.data('ordenes') || [];
                    ordenes.push(ordenNueva);
                    self.data('ordenes', ordenes);
                    toastr.success(`${titulo} (${codigos}) Agregado correctamente`);
                }
                // Llamar a una función para manejar las órdenes actualizadas
                self.trigger('ordenesActualizadas');
            } else {
                console.log('No hay suficiente inventario para ' + titulo);
                toastr.error(`No hay suficiente inventario para agregar a ${titulo} en tu bodega`);
            }
        });
    };

    // Función para eliminar una orden del arreglo por id
    $.fn.eliminarOrden = function(id) {
        let ordenes = this.data('ordenes') || [];
        ordenes = ordenes.filter(orden => orden.id !== id);
        this.data('ordenes', ordenes);
        this.trigger('ordenesActualizadas');
    };

    // Función para modificar una orden por id
    $.fn.modificarOrden = function(id, tieneDescuento, costo, cantidad, reserva) {
        let ordenes = this.data('ordenes') || [];
        const orden = ordenes.find(orden => orden.id === id);
        if (orden) {
            if (tieneDescuento) orden.tieneDescuento = tieneDescuento;
            if (costo) orden.costo = costo;
            if (reserva) orden.reserva = reserva;
            if (cantidad) orden.cantidad = cantidad;
            this.trigger('ordenesActualizadas');
        }
    };

    $.fn.modificarCostoOrden = function(id, nuevoCosto) {
        let ordenes = this.data('ordenes') || [];
        const orden = ordenes.find(orden => orden.id === id);
        if (orden) {
            orden.costo = nuevoCosto;
            this.trigger('ordenesActualizadas');
        }
    };

    // Función para obtener una orden por id
    $.fn.obtenerOrden = function(id) {
        const ordenes = this.data('ordenes') || [];
        return ordenes.find(orden => orden.id === id);
    };

    // Función para obtener el arreglo completo de órdenes
    $.fn.obtenerOrdenes = function() {
        return this.data('ordenes') || [];
    };

    // Función para calcular el total de costo de todas las órdenes
    $.fn.calcularTotalCosto = function(isStock = false) {
        const ordenes = this.data('ordenes') || [];
        let totalCosto = 0;
        ordenes.forEach(orden => {
            totalCosto += orden.costo * orden.cantidad;
            if (orden.reserva>0) totalCosto += orden.costo * orden.reserva;
        });
        return totalCosto;
    };

    // Función para calcular el total con impuestos (12%)
    $.fn.calcularTotalConImpuestos = function(isStock = false) {
        const totalCosto = this.calcularTotalCosto();
        const impuestos = totalCosto * 0.12; // 12% de impuestos
        const totalConImpuestos = totalCosto + impuestos;
        return totalConImpuestos;
    };

    // Función para obtener el total de costo y el total con impuestos
    $.fn.obtenerTotales = function(isStock = false) {
        const totalCosto = this.calcularTotalCosto(isStock);
        const totalConImpuestos = this.calcularTotalConImpuestos(isStock);
        return {
            totalCosto: totalCosto,
            totalConImpuestos: totalConImpuestos
        };
    };
})(jQuery);

