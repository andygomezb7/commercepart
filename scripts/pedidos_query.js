/*
* @description "Plugin para ordenar pedidos"
*/
(function($) {
    // Función constructora para el objeto de orden
    function Orden(id, titulo, descripcion, codigos, tieneDescuento, costo, cantidad) {
        this.id = id;
        this.titulo = titulo;
        this.descripcion = descripcion;
        this.codigos = codigos;
        this.tieneDescuento = tieneDescuento;
        this.costo = costo;
        this.cantidad = cantidad;
    }

    // Función para crear una nueva orden y agregarla al arreglo de órdenes
    // $.fn.agregarOrden = function(id, titulo, descripcion, codigos, tieneDescuento, costo, cantidad) {
    //     const nuevaOrden = new Orden(id, titulo, descripcion, codigos, tieneDescuento, costo, cantidad);
    //     let ordenes = this.data('ordenes') || [];
    //     ordenes.push(nuevaOrden);
    //     this.data('ordenes', ordenes);
    //     this.trigger('ordenesActualizadas');
    // };

    $.fn.agregarOrden = function(id, titulo, descripcion, codigos, tieneDescuento, costo, cantidad) {
        const ordenes = this.data('ordenes') || [];

        // Verificar si ya existe una orden con el mismo ID
        const ordenExistente = ordenes.find(orden => orden.id === id);

        if (ordenExistente) {
            ordenExistente.cantidad += cantidad; // Sumar la cantidad al existente
        } else {
            ordenes.push({
                id: id,
                titulo: titulo,
                descripcion: descripcion,
                codigos: codigos,
                tieneDescuento: tieneDescuento,
                costo: costo,
                cantidad: cantidad
            });
        }

        this.data('ordenes', ordenes);
        this.trigger('ordenesActualizadas'); // Disparar el evento de actualización
    };

    // Función para eliminar una orden del arreglo por id
    $.fn.eliminarOrden = function(id) {
        let ordenes = this.data('ordenes') || [];
        ordenes = ordenes.filter(orden => orden.id !== id);
        this.data('ordenes', ordenes);
        this.trigger('ordenesActualizadas');
    };

    // Función para modificar una orden por id
    $.fn.modificarOrden = function(id, tieneDescuento, costo, cantidad) {
        let ordenes = this.data('ordenes') || [];
        const orden = ordenes.find(orden => orden.id === id);
        if (orden) {
            if (tieneDescuento) orden.tieneDescuento = tieneDescuento;
            if (costo) orden.costo = costo;
            orden.cantidad = cantidad;
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
    $.fn.calcularTotalCosto = function() {
        const ordenes = this.data('ordenes') || [];
        let totalCosto = 0;
        ordenes.forEach(orden => {
            totalCosto += orden.costo * orden.cantidad;
        });
        return totalCosto;
    };

    // Función para calcular el total con impuestos (12%)
    $.fn.calcularTotalConImpuestos = function() {
        const totalCosto = this.calcularTotalCosto();
        const impuestos = totalCosto * 0.12; // 12% de impuestos
        const totalConImpuestos = totalCosto + impuestos;
        return totalConImpuestos;
    };

    // Función para obtener el total de costo y el total con impuestos
    $.fn.obtenerTotales = function() {
        const totalCosto = this.calcularTotalCosto();
        const totalConImpuestos = this.calcularTotalConImpuestos();
        return {
            totalCosto: totalCosto,
            totalConImpuestos: totalConImpuestos
        };
    };
})(jQuery);

