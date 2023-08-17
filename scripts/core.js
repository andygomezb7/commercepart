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
            callback: null  // Callback cuando se agrega un repuesto
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
                            repuestosReturn += `
                                <div class="list-element d-flex justify-content-between align-items-center mb-2">
                                    <div class="list-element-content d-flex align-items-center">
                                        <img src="${repuesto.imagen}" alt="${repuesto.nombre}" class="mr-3" style="max-width: 50px;">
                                        <div>
                                            <h6>${repuesto.nombre}</h6>
                                            <p class="mb-0">${repuesto.descripcion}</p>
                                            <p class="mb-0">${codes}</p>
                                        </div>
                                    </div>
                                    <div class="list-element-actions col-3 d-flex align-items-center">
                                        <a href="javascript:void(0)" class="btn btn-light btn-sm repuesto-cantidad-btn mr-2" data-action="decrease"><i class="fas fa-minus"></i></a>
                                        <input type="number" data-id="${repuesto.id}" class="form-control mr-2 repuesto-cantidad col-3" value="1">
                                        <a href="javascript:void(0)" class="btn btn-light btn-sm repuesto-cantidad-btn mr-2" data-action="increase"><i class="fas fa-plus"></i></a>
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
                    let cantidad = parseInt(cantidadInput.val());

                    if (action === 'increase') {
                        cantidad++;
                    } else if (action === 'decrease') {
                        if (cantidad > 1) {
                            cantidad--;
                        }
                    }

                    cantidadInput.val(cantidad);
                });

                repuestoList.on('click.repuestoDropdown', '.repuesto-agregar', function() {
                    const cantidadInput = $(this).siblings('.repuesto-cantidad');
                    const cantidad = parseInt(cantidadInput.val());
                    const repuestoId = cantidadInput.data('id');

                    if (!isNaN(cantidad) && cantidad > 0) {
                        if (settings.callback && typeof settings.callback === 'function') {
                            let dataReturn = {
                                id: cantidadInput.data('id'),
                                titulo: $(this).data('titulo'),
                                descripcion: $(this).data('descripcion'),
                                codigos: $(this).data('codigos'),
                                cantidad: cantidad,
                                valor: $(this).data('valor'),
                            };
                            settings.callback(repuestoId, dataReturn);
                        }

                        cantidadInput.val('1');
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


// READY FOR THE DOCUMENT
$(document).ready(function() {
    $('select[class=form-control]').select2();
});