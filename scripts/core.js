/*
* @description "Plugin para generar modales y alertas"
*/
(function($) {
    $.fn.modalPlugin = function(options) {
        var defaults = {
            title: 'Modal Title',
            content: 'Modal Content',
            positiveBtnText: 'OK',
            negativeBtnText: 'Cancel',
            callback: null,
            alertType: false, // false = no alert, 'done' = success, 'reject' = danger
            customButtons: [] // array of objects { text, callback }
        };

        var settings = $.extend({}, defaults, options);

        return this.each(function() {
            var modalId = $(this).attr('data-modal-id');
            var $modal = $('#customModal');

            // Set modal title and content
            $('#modalTitle').text(settings.title);
            $('#modalDescription').html(settings.content);

            // Clear previous buttons
            $('#modalButtons').empty();

            if (settings.alertType) {
                if (settings.alertType) {
                    var alertClass = settings.alertType === 'done' ? 'btn-success' : 'btn-danger';
                    $('#modalButtons').append('<button type="button" class="btn ' + alertClass + '">' + settings.positiveBtnText + '</button>');
                } else {
                    $('#modalButtons').append('<button type="button" class="btn btn-primary">' + settings.positiveBtnText + '</button>');
                }
            }

            if (settings.negativeBtnText) {
                $('#modalButtons').append('<button type="button" class="btn btn-secondary" data-dismiss="modal">' + settings.negativeBtnText + '</button>');
            }

            // Add custom buttons
            settings.customButtons.forEach(function(button) {
                var buttonElement = $('<button type="button" class="btn ' + button.class + '">' + button.text + '</button>');
                $('#modalButtons').append(buttonElement);
                buttonElement.on('click', function() {
                    $('#customModal').modal('hide');
                    if (button.callback && typeof button.callback === 'function') {
                        button.callback();
                    }
                });
            });

            // Attach the callback function to the positive button click
            $('.modal-footer button.btn-primary').off('click').on('click', function() {
                $('#customModal').modal('hide');
                if (settings.callback && typeof settings.callback === 'function') {
                    settings.callback(true);
                }
            });

            // Show the modal
            $('#customModal').modal('show');
        });
    };
})(jQuery);

// Function to show alert modal
function showAlertModal(title, content, callback) {
    $('#customModal').modalPlugin({
        title: title,
        content: content,
        positiveBtnText: 'Accept',
        negativeBtnText: 'Reject',
        callback: function(accepted) {
            if (callback && typeof callback === 'function') {
                callback(accepted);
            }
        }
    });
}

/*
* @description "Agregar pedidos de un listado"
*/
(function($) {
    $.fn.repuestoDropdown = function(options) {
        const settings = $.extend({
            url: '',  // URL para cargar los repuestos
            callback: null,  // Callback cuando se agrega un repuesto
            stock: false
        }, options);

        this.each(function() {
            const $input = $(this);

            // Limpiar eventos anteriores
            $input.off('.repuestoDropdown');

            // Crear elementos para el dropdown
            const repuestoSearch = $('<input type="text" class="repuesto-search form-control" placeholder="Buscar repuesto">');
            const repuestoList = $('<div class="repuesto-list"></div>');
            const father = $('<div class="repuesto-dropdown"></div>');

            $input.after(father);
            $input.appendTo(father);

            $input.after(repuestoSearch);
            repuestoSearch.after(repuestoList);

            repuestoSearch.on('input', function() {
                const filter = $(this).val();
                fillDropdown(filter, false);
            });

            let repuestosLoaded = 1, // Número de repuestos ya cargados
            repuestosTotal = 0;   // Número total de repuestos

            function fillDropdown(filter, scroll) {
                if (repuestosLoaded >= repuestosTotal && scroll) {
                    return; // Ya se cargaron todos los repuestos
                }
                if (!scroll) {
                    repuestosLoaded = 1;
                    repuestoList.empty();
                } else {
                    repuestosLoaded += 1;
                }

                $.ajax({
                    url: settings.url,
                    data: { search: filter, offset: repuestosLoaded, limit: 10  },
                    method: 'POST',
                    success: function(repuestosData) {
                        repuestosData = JSON.parse(repuestosData);
                        const repuestos = repuestosData.repuestos;
                        repuestosTotal = repuestosData.total;

                        repuestosReturn = '';
                        repuestos.forEach(repuesto => {
                            let codes = '';
                            repuesto.codigos.split(',').forEach(function(element) {
                                codes += '<span class="badge badge-secondary ml-1">'+element+'</span>';
                            });
                            let bodegas = '', cantidad = 0;
                            repuesto.diponibilidad = 0;
                            repuesto.reserva = 0;
                            // maximos en mi bodega
                            repuesto.myReserva = 0;
                            repuesto.myDisponibilidad = 0;
                            if (repuesto.bodegas.length) {
                                repuesto.bodegas.forEach(function(bodega) {
                                    if (bodega.bodegaid == repuesto.myBodega) {
                                        repuesto.myDisponibilidad += parseInt(bodega.cantidad);
                                        repuesto.myReserva += parseInt(bodega.reserva);
                                    }
                                    // bodega.cantidad -= parseInt(bodega.reserva);
                                    repuesto.diponibilidad += parseInt(bodega.cantidad);
                                    repuesto.reserva += parseInt(bodega.reserva);
                                    bodegas += `<button type="button" class="btn btn-light btn-sm">${bodega.bodeganame} <span class="badge badge-primary">${bodega.cantidad}</span></button>
                                                ${(bodega.reserva>0?`<button type="button" class="btn btn-light btn-sm p-0 pl-2">${bodega.fecha_estimada} <span class="badge badge-dark">${bodega.reserva}</span></button>`:'')}`;
                                })
                            } else {
                                bodegas += 'Sin inventario';
                            }
                            repuestosReturn += `
                                <div class="list-element d-flex justify-content-between align-items-center mb-2 flex-column flex-md-row">
                                    <div class="list-element-content d-flex align-items-center col-md-${(!settings.stock && repuesto.reserva > 0 ?'5':'6')} mb-3 mb-md-0">
                                        <img src="${repuesto.imagen}" alt="${repuesto.nombre}" class="mr-3" style="max-width: 50px;${(repuesto.diponibilidad==0&&!settings.stock?'filter: grayscale(1);':'')}">
                                        <div>
                                            <h6>${repuesto.nombre}</h6>
                                            <p class="mb-0">${repuesto.descripcion}</p>
                                            <p class="mb-0">${codes}</p>
                                        </div>
                                    </div>
                                    <div class="list-element-actions col-md-3 d-flex align-items-center">
                                        <ul>
                                            ${bodegas}
                                        </ul>
                                    </div>
                                    ${(!settings.stock && repuesto.reserva > 0 ? `
                                        <div class="list-element-actions actions-reserva col-md-1 d-flex justify-content-center mb-2">
                                            <input type="number" data-id="${repuesto.id}" data-max="${repuesto.myReserva}" min="0" max="${repuesto.myReserva}" value="${(repuesto.myReserva>0?'1':'0')}" class="form-control mr-2 repuesto-cantidad-reserva col-3 col-md-12 border border-dark">
                                        </div>
                                        ` : `<input type="hidden" data-id="${repuesto.id}" data-max="0" min="0" max="0" value="0" class="form-control mr-2 repuesto-cantidad-reserva col-3 col-md-12 border border-dark">`)}
                                    <div class="list-element-actions col-md-3 d-flex justify-content-center">
                                        <button type="button" ${(repuesto.diponibilidad==0&&!settings.stock?'disabled':'')} href="javascript:void(0)" class="btn btn-light btn-sm repuesto-cantidad-btn mr-2" data-max="${repuesto.diponibilidad}" data-action="decrease"><i class="fas fa-minus"></i></button>
                                        <input type="number" data-id="${repuesto.id}" min="0" ${(!settings.stock ? `max="${repuesto.myDisponibilidad}"`:'')} value="${(repuesto.myDisponibilidad==0?'0':'1')}" data-max="${repuesto.myDisponibilidad}" ${(repuesto.diponibilidad==0&&!settings.stock?'disabled':'')} class="form-control mr-2 repuesto-cantidad col-3">
                                        <button type="button" ${(repuesto.diponibilidad==0&&!settings.stock?'disabled':'')} href="javascript:void(0)" class="btn btn-light btn-sm repuesto-cantidad-btn mr-2" data-max="${repuesto.diponibilidad}" data-action="increase"><i class="fas fa-plus"></i></button>
                                        <a href="javascript:void(0)" class="btn btn-success btn-sm repuesto-agregar" data-titulo="${repuesto.nombre}" data-descripcion="${repuesto.descripcion}" data-codigos="${repuesto.codigos}" data-valor="${repuesto.valor}">Agregar</a>
                                    </div>
                                </div>
                            `;
                        });
                        repuestoList.append(repuestosReturn);
                    }
                });

                // Desvincular eventos anteriores antes de vincular nuevos eventos
                repuestoList.off('.repuestoDropdown');

                repuestoList.on('click.repuestoDropdown', '.repuesto-cantidad-btn', function() {
                    const cantidadInput = $(this).siblings('.repuesto-cantidad');
                    const action = $(this).data('action');
                    const max = $(this).data('max');
                    let cantidad = parseInt(cantidadInput.val());

                    if (action === 'increase') {
                        if (cantidad < max && !settings.stock || settings.stock) {
                            cantidad++;
                        }
                    } else if (action === 'decrease') {
                        if (cantidad > 1) {
                            cantidad--;
                        }
                    }

                    cantidadInput.val(cantidad);
                });

                repuestoList.on('click.repuestoDropdown', '.repuesto-agregar', function() {
                    const cantidadInput = $(this).siblings('.repuesto-cantidad');
                    const cantidadReservaInput = $(this).parent().siblings('.actions-reserva').children('.repuesto-cantidad-reserva');
                    const cantidad = parseInt(cantidadInput.val());
                    const cantidadReserva = (cantidadReservaInput.val()?parseInt(cantidadReservaInput.val()): 0);
                    const repuestoId = cantidadInput.data('id');
                    // disponible
                    const disponible = cantidadInput.data('max');
                    const reserva = cantidadReservaInput.data('max');

                    if (!isNaN(cantidad) && cantidad > 0 || !isNaN(cantidadReserva) && cantidadReserva > 0) {
                        if (settings.callback && typeof settings.callback === 'function') {
                            console.log('Cantidad: ', cantidad);
                            console.log('Reserva: ', reserva);
                            let dataReturn = {
                                id: cantidadInput.data('id'),
                                titulo: $(this).data('titulo'),
                                descripcion: $(this).data('descripcion'),
                                codigos: $(this).data('codigos'),
                                cantidad: cantidad,
                                reserva: cantidadReserva,
                                valor: $(this).data('valor'),
                                maxDisponible: disponible,
                                maxReserva: reserva
                            };
                            settings.callback(repuestoId, dataReturn);
                        }
                        if (cantidadReservaInput) {
                            cantidadReservaInput.val('1');
                        }
                        if (cantidad > 0) {
                            cantidadInput.val('1');
                        }
                    }
                });

                // Agregar eventos para ocultar y mostrar el repuesto-list
                $(document).on('click.repuestoDropdown', function(event) {
                    if (!$(event.target).closest('.repuesto-dropdown').length) {
                        repuestoList.hide();
                    }
                });

                repuestoSearch.on('click.repuestoDropdown', function() {
                    repuestoList.show();
                });
            }
            repuestoList.on('scroll', function() {
                if ($(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight) {
                    // Llegamos al final, cargar más repuestos
                    const filter = repuestoSearch.val();
                    fillDropdown(filter, true);
                }
            });
        });

        return this;
    };
}(jQuery));

// Plugin jQuery para el generador de códigos
(function($) {
    $.fn.generarCodigos = function(options) {
        return this.each(function() {
            const $container = $(this);

            // Crear el grupo de entrada (input-group) de Bootstrap
            const $inputGroup = $('<div>').addClass('input-group mb-3');

            // Crear el campo de entrada
            const $input = $('<input>')
                .attr({
                    type: 'text',
                    class: 'form-control',
                    placeholder: "Código",
                });

            // Crear el botón para generar códigos
            const $generarButton = $('<button type="button">')
                .addClass('btn btn-outline-primary')
                .attr({
                    type: 'button',
                    id: 'button-addon2'
                })
                .text('Asignar');

            // Crear una lista para mostrar los códigos generados
            const $listaCodigos = $('<ul>');

            // Función para agregar un código a la lista y almacenarlo en el campo oculto
            function agregarCodigo(codigo) {
                // Agregar el código a la lista
                const $nuevoElemento = $('<li>').html('<label class="badge badge-white mr-1">' + codigo + '</label>');

                // Crear un botón para eliminar el código
                const $botonEliminar = $('<button type="button">')
                    .addClass('btn btn-danger btn-sm')
                    .text('Eliminar')
                    .on('click', function() {
                        $("input[name='codigos[" + codigo + "]']").remove();
                        $nuevoElemento.remove();
                        // actualizarCodigos();
                    });

                const $codigosInput = $('<input>')
                .attr({
                    type: 'hidden',
                    name: 'codigos[' + codigo + ']', // El nombre debe ser un array
                    value: codigo
                });
                $container.append($codigosInput);

                $nuevoElemento.append($botonEliminar);
                $listaCodigos.append($nuevoElemento);

                // Almacenar los códigos en el campo oculto
                // actualizarCodigos();
            }

            // Función para agregar códigos pre-cargados
            function agregarCodigosPreCargados(codigosPreCargados) {
                console.log('Codigos pre cargados', codigosPreCargados);
                codigosPreCargados.forEach(function(codigo) {
                    agregarCodigo(codigo);
                });
            }

            // Manejar el clic en el botón para generar códigos
            $generarButton.on('click', function() {
                const codigo = $input.val();
                agregarCodigo(codigo);
                $input.val('');
            });

            // Agregar elementos al grupo de entrada y al contenedor
            $inputGroup.append($input);
            $inputGroup.append($('<div>').addClass('input-group-append').append($generarButton));
            $container.append($inputGroup);
            $container.append($listaCodigos);

            // Agregar códigos pre-cargados si se proporcionan
            if (options && options.codigosPreCargados && Array.isArray(options.codigosPreCargados)) {
                agregarCodigosPreCargados(options.codigosPreCargados);
            }
        });
    };
})(jQuery);

// READY FOR THE DOCUMENT
$(document).ready(function() {
    $('select[class=form-control]').select2();
});