var TransaccionesWebpay = function () {

    var oTable;
    var rowDelete = null;

    //Inicializa la tabla
    var initTable = function () {
        MyApp.block('#transaccion-table-editable');

        var table = $('#transaccion-table-editable');

        var aoColumns = [
            {
                field: "id",
                title: "#",
                sortable: false, // disable sort for this column
                width: 40,
                textAlign: 'center',
                selector: {class: 'm-checkbox--solid m-checkbox--brand'}
            },
            {
                field: "cotizacionId",
                title: "N°",
                sortable: false, // disable sort for this column
                width: 40,
                textAlign: 'center'
            },
            {
                field: "nombre",
                title: "Autor",
                width: 200,
                template: function (row) {
                    var stateNo = mUtil.getRandomInt(0, 7);
                    var states = ['success', 'brand', 'danger', 'accent', 'warning', 'metal', 'primary', 'info'];
                    var state = states[stateNo];

                    var output = '<div class="m-card-user m-card-user--sm">\
								<div class="m-card-user__pic">\
									<div class="m-card-user__no-photo m--bg-fill-' + state + '"><span>' + row.nombre.substring(0, 1) + '</span></div>\
								</div>\
								<div class="m-card-user__details">\
									<span class="m-card-user__name">' + row.nombre + '</span>\
									<a href="mailto: ' + row.email + ' ;" class="m-card-user__email m-link">' + row.email + '</a> <br> \
									<a href="tel:' + row.telefono + ' ;" class="m-card-user__email m-link">' + row.telefono + '</a>\
								</div>\
							</div>';
                    return output;
                }
            },
            {
                field: "cardNumber",
                title: "Card Number"
            },
            {
                field: "paymentTypeCode",
                title: "Payment Type Code"
            },
            {
                field: "amount",
                title: "Monto"
            },
            {
                field: "transactionDate",
                title: "Fecha",
                responsive: {visible: 'lg'},
                width: 120,
                textAlign: 'center'
            },
            {
                field: "acciones",
                width: 110,
                title: "Acciones",
                sortable: false,
                overflow: 'visible',
                textAlign: 'center'
            }
        ];
        oTable = table.mDatatable({
            // datasource definition
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: 'transaccion/listarTransaccion',
                    }
                },
                pageSize: 10,
                saveState: {
                    cookie: true,
                    webstorage: true
                },
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true
            },
            // layout definition
            layout: {
                theme: 'default', // datatable theme
                class: '', // custom wrapper class
                scroll: false, // enable/disable datatable scroll both horizontal and vertical when needed.
                //height: 550, // datatable's body's fixed height
                footer: false // display/hide footer
            },
            // column sorting
            sortable: true,
            pagination: true,
            // columns definition
            columns: aoColumns,
            // toolbar
            toolbar: {
                // toolbar items
                items: {
                    // pagination
                    pagination: {
                        // page size select
                        pageSizeSelect: [10, 25, 30, 50, -1] // display dropdown to select pagination size. -1 is used for "ALl" option
                    }
                }
            },
            //Tanslate
            translate: {
                records: {
                    processing: 'Cargando...',
                    noRecords: 'No se existen registros'
                },
                toolbar: {
                    pagination: {
                        items: {
                            info: 'Mostrando {{start}} - {{end}} de {{total}} registros'
                        }
                    }
                }
            }
        });

        //Events
        oTable
            .on('m-datatable--on-ajax-done', function () {
                mApp.unblock('#transaccion-table-editable');
            })
            .on('m-datatable--on-ajax-fail', function (e, jqXHR) {
                mApp.unblock('#transaccion-table-editable');
            })
            .on('m-datatable--on-goto-page', function (e, args) {
                MyApp.block('#transaccion-table-editable');
            })
            .on('m-datatable--on-reloaded', function (e) {
                MyApp.block('#transaccion-table-editable');
            })
            .on('m-datatable--on-sort', function (e, args) {
                MyApp.block('#transaccion-table-editable');
            })
            .on('m-datatable--on-check', function (e, args) {
                //eventsWriter('Checkbox active: ' + args.toString());
            })
            .on('m-datatable--on-uncheck', function (e, args) {
                //eventsWriter('Checkbox inactive: ' + args.toString());
            });

        //Busqueda
        var query = oTable.getDataSourceQuery();
        $('#lista-transaccion .m_form_search').on('keyup', function (e) {
            // shortcode to datatable.getDataSourceParam('query');
            var query = oTable.getDataSourceQuery();
            query.generalSearch = $(this).val().toLowerCase();

            var fechaInicial = $('#fechaInicial').val();
            var fechaFin = $('#fechaFin').val();

            query.fechaInicial = fechaInicial;
            query.fechaFin = fechaFin;

            // shortcode to datatable.setDataSourceParam('query', query);
            oTable.setDataSourceQuery(query);
            oTable.load();
        }).val(query.generalSearch);
    };
    //Filtrar
    var initAccionFiltrar = function () {

        $(document).off('click', "#btn-filtrar");
        $(document).on('click', "#btn-filtrar", function (e) {
            btnClickFiltrar();
        });

        function btnClickFiltrar() {
            var query = oTable.getDataSourceQuery();

            var generalSearch = $('#lista-transaccion .m_form_search').val();
            query.generalSearch = generalSearch;

            var fechaInicial = $('#fechaInicial').val();
            var fechaFin = $('#fechaFin').val();

            query.fechaInicial = fechaInicial;
            query.fechaFin = fechaFin;

            oTable.setDataSourceQuery(query);
            oTable.load();
        }

    };

    //Reset forms
    var resetForms = function () {
        $('#cotizacion-form input, #cotizacion-form textarea').each(function (e) {
            $element = $(this);
            $element.val('');

            $element.data("title", "").removeClass("has-error").tooltip("dispose");
            $element.closest('.form-group').removeClass('has-error').addClass('success');
        });

        //Reset productos
        productos = [];
        dibujarTablaProductos();

        //Mostrar el primer tab
        resetWizard();
    };

    //Cerrar form
    var initAccionCerrar = function () {
        $(document).off('click', ".cerrar-form-cotizacion");
        $(document).on('click', ".cerrar-form-cotizacion", function (e) {
            cerrarForms();
        });
    }
    //Cerrar forms
    var cerrarForms = function () {
        resetForms();
        $('#form-cotizacion').addClass('m--hide');
        $('#lista-transaccion').removeClass('m--hide');
    };
    //Editar
    var initAccionEditar = function () {
        $(document).off('click', "#transaccion-table-editable a.edit");
        $(document).on('click', "#transaccion-table-editable a.edit", function (e) {
            e.preventDefault();
            resetForms();


            var transaccion_id = $(this).data('id');
            $('#transaccion_id').val(transaccion_id);

            $('#form-cotizacion').removeClass('m--hide');
            $('#lista-transaccion').addClass('m--hide');

            editRow(transaccion_id);
        });

        function editRow(transaccion_id) {

            MyApp.block('#cotizacion-form');

            $.ajax({
                type: "POST",
                url: "transaccion/cargarDatos",
                dataType: "json",
                data: {
                    'transaccion_id': transaccion_id
                },
                success: function (response) {
                    mApp.unblock('#cotizacion-form');
                    if (response.success) {
                        //Datos cotizacion    

                        var formTitle = "Detalles transacción No. \"" + transaccion_id + "\":";
                        $('#form-cotizacion-title').html(formTitle);

                        //Datos cotizacion
                        $('#rut').val(response.transaccion.rut);
                        $('#telefono').val(response.transaccion.telefono);
                        $('#nombre').val(response.transaccion.nombre);
                        $('#apellidos').val(response.transaccion.apellidos);
                        $('#email').val(response.transaccion.email);
                        $('#calle').val(response.transaccion.calle);
                        $('#numero').val(response.transaccion.numero);

                        $('#comentario').val(response.transaccion.comentario);

                        productos = response.transaccion.productos;
                        dibujarTablaProductos();

                        //Datos de la transaccion
                        var transaccion = response.transaccion.transaccion;
                        if (transaccion != false) {
                            $('#transactionDate').val(transaccion.transactionDate);
                            $('#cardNumber').val(transaccion.cardNumber);
                            $('#cardExpirationDate').val(transaccion.cardExpirationDate);
                            $('#authorizationCode').val(transaccion.authorizationCode);
                            $('#paymentTypeCode').val(transaccion.paymentTypeCode);
                            $('#responseCode').val(transaccion.responseCode);
                            $('#amount').val(transaccion.amount);
                            $('#sharesAmount').val(transaccion.sharesAmount);
                            $('#sharesNumber').val(transaccion.sharesNumber);
                        }

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#cotizacion-form');

                    toastr.error(response.error, "Error !!!");
                }
            });

        }
    };

    //Eliminar
    var initAccionEliminar = function () {
        $(document).off('click', "#transaccion-table-editable a.delete");
        $(document).on('click', "#transaccion-table-editable a.delete", function (e) {
            e.preventDefault();

            rowDelete = $(this).data('id');
            $('#modal-eliminar').modal({
                'show': true
            });
        });

        $(document).off('click', "#btn-eliminar-transaccion");
        $(document).on('click', "#btn-eliminar-transaccion", function (e) {
            btnClickEliminar();
        });

        $(document).off('click', "#btn-delete");
        $(document).on('click', "#btn-delete", function (e) {
            btnClickModalEliminar();
        });

        $(document).off('click', "#btn-delete-selection");
        $(document).on('click', "#btn-delete-selection", function (e) {
            btnClickModalEliminarSeleccion();
        });

        function btnClickEliminar() {
            var ids = '';
            $('.m-datatable__cell--check .m-checkbox--brand > input[type="checkbox"]').each(function () {
                if ($(this).prop('checked')) {
                    var value = $(this).attr('value');
                    if (value != undefined) {
                        ids += value + ',';
                    }
                }
            });

            if (ids != '') {
                $('#modal-eliminar-seleccion').modal({
                    'show': true
                });
            } else {
                toastr.error('Seleccione los elementos a eliminar', "Error !!!");
            }
        };

        function btnClickModalEliminar() {
            var transaccion_id = rowDelete;

            MyApp.block('#transaccion-table-editable');

            $.ajax({
                type: "POST",
                url: "transaccion/eliminarTransaccion",
                dataType: "json",
                data: {
                    'transaccion_id': transaccion_id
                },
                success: function (response) {
                    mApp.unblock('#transaccion-table-editable');
                    if (response.success) {
                        oTable.load();

                        toastr.success(response.message, "Exito !!!");

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#transaccion-table-editable');

                    toastr.error(response.error, "Error !!!");
                }
            });
        };

        function btnClickModalEliminarSeleccion() {
            var ids = '';
            $('.m-datatable__cell--check .m-checkbox--brand > input[type="checkbox"]').each(function () {
                if ($(this).prop('checked')) {
                    var value = $(this).attr('value');
                    if (value != undefined) {
                        ids += value + ',';
                    }
                }
            });

            MyApp.block('#transaccion-table-editable');

            $.ajax({
                type: "POST",
                url: "transaccion/eliminarTransacciones",
                dataType: "json",
                data: {
                    'ids': ids
                },
                success: function (response) {
                    mApp.unblock('#transaccion-table-editable');
                    if (response.success) {
                        oTable.load();

                        toastr.success(response.message, "Exito !!!");

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#transaccion-table-editable');
                    toastr.error(response.error, "Error !!!");
                }
            });
        };
    };

    //initPortlets
    var initPortlets = function () {
        var portlet = new mPortlet('lista-transaccion');
        portlet.on('afterFullscreenOn', function (portlet) {
            $('.m-portlet').addClass('m-portlet--fullscreen');
        });

        portlet.on('afterFullscreenOff', function (portlet) {
            $('.m-portlet').removeClass('m-portlet--fullscreen');
        });
    }

    //Wizard
    var activeTab = 1;
    var totalTabs = 3;
    var initWizard = function () {
        $(document).off('click', "#form-producto .wizard-tab");
        $(document).on('click', "#form-producto .wizard-tab", function (e) {
            e.preventDefault();
            var item = $(this).data('item');
            activeTab = parseInt(item);

            if (activeTab < totalTabs) {
                $('#btn-wizard-finalizar').removeClass('m--hide').addClass('m--hide');
            }
            if (activeTab == 1) {
                $('#btn-wizard-anterior').removeClass('m--hide').addClass('m--hide');
                $('#btn-wizard-siguiente').removeClass('m--hide');
            }
            if (activeTab > 1) {
                $('#btn-wizard-anterior').removeClass('m--hide');
                $('#btn-wizard-siguiente').removeClass('m--hide');
            }
            if (activeTab == totalTabs) {
                $('#btn-wizard-finalizar').removeClass('m--hide');
                $('#btn-wizard-siguiente').removeClass('m--hide').addClass('m--hide');
            }

            if (activeTab == 2) {
                //bug visual de la tabla que muestra las cols corridas
                actualizarTableListaProductos();
            }

        });

        //siguiente
        $(document).off('click', "#btn-wizard-siguiente");
        $(document).on('click', "#btn-wizard-siguiente", function (e) {
            activeTab++;
            $('#btn-wizard-anterior').removeClass('m--hide');
            if (activeTab == totalTabs) {
                $('#btn-wizard-finalizar').removeClass('m--hide');
                $('#btn-wizard-siguiente').addClass('m--hide');
            }

            mostrarTab();
        });
        //anterior
        $(document).off('click', "#btn-wizard-anterior");
        $(document).on('click', "#btn-wizard-anterior", function (e) {
            activeTab--;
            if (activeTab == 1) {
                $('#btn-wizard-anterior').addClass('m--hide');
            }
            if (activeTab < totalTabs) {
                $('#btn-wizard-finalizar').addClass('m--hide');
                $('#btn-wizard-siguiente').removeClass('m--hide');
            }
            mostrarTab();
        });

    };
    var mostrarTab = function () {
        setTimeout(function () {
            switch (activeTab) {
                case 1:
                    $('#tab-general').tab('show');
                    break;
                case 2:
                    $('#tab-productos').tab('show');
                    //bug visual de la tabla que muestra las cols corridas
                    dibujarTablaProductos();
                    break;
                case 3:
                    $('#tab-transaccion').tab('show');
                    break;
            }
        }, 0);
    }
    var resetWizard = function () {
        activeTab = 1;
        mostrarTab();
        $('#btn-wizard-finalizar').removeClass('m--hide').addClass('m--hide');
        $('#btn-wizard-anterior').removeClass('m--hide').addClass('m--hide');
        $('#btn-wizard-siguiente').removeClass('m--hide');
    }

    //Productos
    var productos = [];
    var dibujarTablaProductos = function () {
        var tabla = '#table-productos'
        //Limpiar tabla
        $(tabla + ' tbody tr').each(function (e) {
            $(this).remove();
        });

        //registro en blanco
        if (productos.length == 0) {
            tr = '<tr>' +
                '<td colspan="7">No existen productos</td>' +
                '</tr>';

            $(tr).appendTo(tabla + ' tbody');
        }

        //Agregar elementos
        var total = 0;
        for (var i = 0; i < productos.length; i++) {

            var nombre = '<div class="m-card-user m-card-user--sm">\
								<div class="m-card-user__pic m-card-producto__pic">\
									<img src="' + productos[i].imagen + '" class="m--img-rounded m--marginless" alt="photo" style="max-width: 60px !important;border-radius: 0px;">\
								</div>\
								<div class="m-card-user__details">\
									<span class="m-card-user__name">' + productos[i].nombre + '</span>\
								</div>\
							</div>';

            var cantidad = productos[i].cantidad;
            var precio = productos[i].precio;
            var subtotal = cantidad * precio;
            total += subtotal;

            var output_precio = '<span class="m--font-danger">$' + MyApp.formatearNumero(precio, 0, ',', '.') + '</span>';
            var output_subtotal = '<span class="m--font-danger">$' + MyApp.formatearNumero(subtotal, 0, ',', '.') + '</span>';

            var tr = '<tr id="' + i + '">' +
                '<td class="text-center">' + (i + 1) + '</td>' +
                '<td>' + nombre + '</td>' +
                '<td>' + productos[i].categoria + '</td>' +
                '<td>' + productos[i].marca + '</td>' +
                '<td class="text-center">' + cantidad + '</td>' +
                '<td class="text-center">' + output_precio + '</td>' +
                '<td class="text-center">' + output_subtotal + '</td>' +
                '</tr>';


            $(tr).appendTo(tabla + ' tbody');

        }

        //total
        if (productos.length > 0) {
            var output_total = '<span class="m--font-danger">$' + MyApp.formatearNumero(total, 0, ',', '.') + '</span>';
            var tr = '<tr class="m--font-danger m--font-bold">' +
                '<td class="text-center"></td>' +
                '<td> Total </td>' +
                '<td></td>' +
                '<td class="text-center"></td>' +
                '<td class="text-center"></td>' +
                '<td class="text-center"></td>' +
                '<td class="text-center">' + output_total + '</td>' +
                '<td class="text-center"></td>' +
                '</tr>';


            $(tr).appendTo(tabla + ' tbody');
        }

    };

    return {
        //main function to initiate the module
        init: function () {

            initTable();
            initAccionFiltrar();

            initAccionCerrar();

            initAccionEditar();
            initAccionEliminar();

            initPortlets();

            initWizard();
        }

    };

}();