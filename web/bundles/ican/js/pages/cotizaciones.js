var Cotizaciones = function () {

    var oTable;
    var rowDelete = null;

    //Inicializa la tabla
    var initTable = function () {
        MyApp.block('#cotizacion-table-editable');

        var table = $('#cotizacion-table-editable');

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
                field: "calle",
                title: "Dirección"
            },
            {
                field: "comentario",
                title: "Comentario",
                responsive: {visible: 'lg'},
                width: 200
            },
            {
                field: "fecha",
                title: "Fecha",
                responsive: {visible: 'lg'},
                width: 120,
                textAlign: 'center'
            },
            {
                field: "estado",
                title: "Estado",
                responsive: {visible: 'lg'},
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
                        url: 'cotizacion/listarCotizacion',
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
                mApp.unblock('#cotizacion-table-editable');
            })
            .on('m-datatable--on-ajax-fail', function (e, jqXHR) {
                mApp.unblock('#cotizacion-table-editable');
            })
            .on('m-datatable--on-goto-page', function (e, args) {
                MyApp.block('#cotizacion-table-editable');
            })
            .on('m-datatable--on-reloaded', function (e) {
                MyApp.block('#cotizacion-table-editable');
            })
            .on('m-datatable--on-sort', function (e, args) {
                MyApp.block('#cotizacion-table-editable');
            })
            .on('m-datatable--on-check', function (e, args) {
                //eventsWriter('Checkbox active: ' + args.toString());
            })
            .on('m-datatable--on-uncheck', function (e, args) {
                //eventsWriter('Checkbox inactive: ' + args.toString());
            });

        //Busqueda
        var query = oTable.getDataSourceQuery();
        $('#lista-cotizacion .m_form_search').on('keyup', function (e) {
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

            var generalSearch = $('#lista-cotizacion .m_form_search').val();
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

        $('#usuario').val('');
        $('#usuario').trigger('change');

        event_change = false;

        //Reset productos
        productos = [];
        dibujarTablaProductos();

        //Mostrar el primer tab
        resetWizard();
    };

    //Validacion
    //Validacion y Inicializacion de ajax form
    jQuery.validator.addMethod("rut", function (value, element) {
        return this.optional(element) || $.Rut.validar(value);
    }, "Este campo debe ser un rut valido.");
    $("#rut").Rut({
        format_on: 'keyup',
        format: true,
        validation: true
    });
    var initForm = function () {
        $("#cotizacion-form").validate({
            rules: {
                nombre: {
                    required: true
                },
                apellidos: {
                    required: true
                },
                rut: {
                    required: true,
                    rut: true
                },
                telefono: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                calle: {
                    required: true
                },
                numero: {
                    required: true
                },
                comentario: {
                    required: true
                }
            },
            messages: {
                nombre: "Este campo es obligatorio",
                apellidos: "Este campo es obligatorio",
                comentario: "Este campo es obligatorio",
                telefono: "Este campo es obligatorio",
                calle: "Este campo es obligatorio",
                numero: "Este campo es obligatorio",
                email: {
                    required: "Este campo es obligatorio",
                    email: "El Email debe ser válido"
                },
                rut: {
                    required: "Este campo es obligatorio",
                    rut: "El rut debe ser válido Ej: 15125587-6 o 81201000-k"
                }
            },
            showErrors: function (errorMap, errorList) {
                // Clean up any tooltips for valid elements
                $.each(this.validElements(), function (index, element) {
                    var $element = $(element);

                    $element.data("title", "") // Clear the title - there is no error associated anymore
                        .removeClass("has-error")
                        .tooltip("dispose");

                    $element
                        .closest('.form-group')
                        .removeClass('has-error').addClass('success');
                });

                // Create new tooltips for invalid elements
                $.each(errorList, function (index, error) {
                    var $element = $(error.element);

                    $element.tooltip("dispose") // Destroy any pre-existing tooltip so we can repopulate with new tooltip content
                        .data("title", error.message)
                        .addClass("has-error")
                        .tooltip({
                            placement: 'bottom'
                        }); // Create a new tooltip based on the error messsage we just set in the title

                    $element.closest('.form-group')
                        .removeClass('has-success').addClass('has-error');

                });
            },
        });
    };

    //Nuevo
    var initAccionNuevo = function () {
        $(document).off('click', "#btn-nuevo-cotizacion");
        $(document).on('click', "#btn-nuevo-cotizacion", function (e) {
            btnClickNuevo();
        });

        function btnClickNuevo() {
            resetForms();
            var formTitle = "¿Deseas crear una nueva cotización? Sigue los siguientes pasos:";
            $('#form-cotizacion-title').html(formTitle);
            $('#form-cotizacion').removeClass('m--hide');
            $('#lista-cotizacion').addClass('m--hide');
        };
    };
    //Salvar
    var initAccionSalvar = function () {
        $(document).off('click', "#btn-wizard-finalizar");
        $(document).on('click', "#btn-wizard-finalizar", function (e) {
            btnClickSalvarForm();
        });

        function btnClickSalvarForm() {
            mUtil.scrollTo();
            event_change = false;

            if ($('#cotizacion-form').valid() && productos.length > 0) {

                var cotizacion_id = $('#cotizacion_id').val();

                var usuario_id = $('#usuario').val();

                var rut = $('#rut').val();
                var nombre = $('#nombre').val();
                var apellidos = $('#apellidos').val();
                var telefono = $('#telefono').val();
                var email = $('#email').val();
                var calle = $('#calle').val();
                var numero = $('#numero').val();
                var comentario = $('#comentario').val();

                MyApp.block('#form-cotizacion');

                $.ajax({
                    type: "POST",
                    url: "cotizacion/salvarCotizacion",
                    dataType: "json",
                    data: {
                        'cotizacion_id': cotizacion_id,
                        'usuario': usuario_id,
                        'rut': rut,
                        'nombre': nombre,
                        'apellidos': apellidos,
                        'telefono': telefono,
                        'email': email,
                        'calle': calle,
                        'numero': numero,
                        'comentario': comentario,
                        'productos': productos
                    },
                    success: function (response) {
                        mApp.unblock('#form-cotizacion');
                        if (response.success) {

                            toastr.success(response.message, "Exito !!!");

                            if (response.ruta != "" && response.ruta != null)
                                document.location = response.ruta;

                            cerrarForms();
                            oTable.load();
                        } else {
                            toastr.error(response.error, "Error !!!");
                        }
                    },
                    failure: function (response) {
                        mApp.unblock('#form-cotizacion');

                        toastr.error(response.error, "Error !!!");
                    }
                });

            } else {
                if (productos.length == 0) {
                    toastr.error("Debe agregar los productos de la cotización", "Error !!!");
                }
            }
        };
    }
    //Cerrar form
    var initAccionCerrar = function () {
        $(document).off('click', ".cerrar-form-cotizacion");
        $(document).on('click', ".cerrar-form-cotizacion", function (e) {
            cerrarForms();
        });
    }
    //Cerrar forms
    var cerrarForms = function () {
        if (!event_change) {
            cerrarFormsConfirmated();
        } else {
            $('#modal-salvar-cambios').modal({
                'show': true
            });
        }
    };
    //Editar
    var initAccionEditar = function () {
        $(document).off('click', "#cotizacion-table-editable a.edit");
        $(document).on('click', "#cotizacion-table-editable a.edit", function (e) {
            e.preventDefault();
            resetForms();


            var cotizacion_id = $(this).data('id');
            $('#cotizacion_id').val(cotizacion_id);

            $('#form-cotizacion').removeClass('m--hide');
            $('#lista-cotizacion').addClass('m--hide');

            editRow(cotizacion_id);
        });

        function editRow(cotizacion_id) {

            MyApp.block('#cotizacion-form');

            $.ajax({
                type: "POST",
                url: "cotizacion/cargarDatos",
                dataType: "json",
                data: {
                    'cotizacion_id': cotizacion_id
                },
                success: function (response) {
                    mApp.unblock('#cotizacion-form');
                    if (response.success) {
                        //Datos cotizacion    

                        var formTitle = "¿Deseas actualizar la cotización No. \"" + cotizacion_id + "\" ? Sigue los siguientes pasos:";
                        $('#form-cotizacion-title').html(formTitle);

                        //Datos cotizacion
                        $('#usuario').val(response.cotizacion.usuario_id);
                        $('#usuario').trigger('change');

                        $('#rut').val(response.cotizacion.rut);
                        $('#telefono').val(response.cotizacion.telefono);
                        $('#nombre').val(response.cotizacion.nombre);
                        $('#apellidos').val(response.cotizacion.apellidos);
                        $('#email').val(response.cotizacion.email);
                        $('#calle').val(response.cotizacion.calle);
                        $('#numero').val(response.cotizacion.numero);

                        $('#comentario').val(response.cotizacion.comentario);

                        productos = response.cotizacion.productos;
                        dibujarTablaProductos();

                        //Datos de la transaccion
                        var transaccion = response.cotizacion.transaccion;
                        if (transaccion != false) {
                            totalTabs = 3;
                            $('#li-tab-transaccion').removeClass('m--hide');

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

                        event_change = false;

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
        $(document).off('click', "#cotizacion-table-editable a.delete");
        $(document).on('click', "#cotizacion-table-editable a.delete", function (e) {
            e.preventDefault();

            rowDelete = $(this).data('id');
            $('#modal-eliminar').modal({
                'show': true
            });
        });

        $(document).off('click', "#btn-eliminar-cotizacion");
        $(document).on('click', "#btn-eliminar-cotizacion", function (e) {
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
            var cotizacion_id = rowDelete;

            MyApp.block('#cotizacion-table-editable');

            $.ajax({
                type: "POST",
                url: "cotizacion/eliminarCotizacion",
                dataType: "json",
                data: {
                    'cotizacion_id': cotizacion_id
                },
                success: function (response) {
                    mApp.unblock('#cotizacion-table-editable');
                    if (response.success) {
                        oTable.load();

                        toastr.success(response.message, "Exito !!!");

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#cotizacion-table-editable');

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

            MyApp.block('#cotizacion-table-editable');

            $.ajax({
                type: "POST",
                url: "cotizacion/eliminarCotizaciones",
                dataType: "json",
                data: {
                    'ids': ids
                },
                success: function (response) {
                    mApp.unblock('#cotizacion-table-editable');
                    if (response.success) {
                        oTable.load();

                        toastr.success(response.message, "Exito !!!");

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#cotizacion-table-editable');
                    toastr.error(response.error, "Error !!!");
                }
            });
        };
    };

    //Eventos change
    var event_change = false;
    var initAccionChange = function () {
        $(document).off('change', ".event-change");
        $(document).on('change', ".event-change", function (e) {
            event_change = true;
        });

        $(document).off('click', "#btn-save-changes");
        $(document).on('click', "#btn-save-changes", function (e) {
            cerrarFormsConfirmated();
        });
    };
    var cerrarFormsConfirmated = function () {
        resetForms();
        $('#form-cotizacion').addClass('m--hide');
        $('#lista-cotizacion').removeClass('m--hide');
    }

    //initPortlets
    var initPortlets = function () {
        var portlet = new mPortlet('lista-cotizacion');
        portlet.on('afterFullscreenOn', function (portlet) {
            $('.m-portlet').addClass('m-portlet--fullscreen');
        });

        portlet.on('afterFullscreenOff', function (portlet) {
            $('.m-portlet').removeClass('m-portlet--fullscreen');
        });
    }

    //Init selects
    var initSelects = function () {
        $('#filtro-categoria').select2({
            templateResult: function (data) {
                // We only really care if there is an element to pull classes from
                if (!data.element) {
                    return data.text;
                }

                var $element = $(data.element);

                var $wrapper = $('<span></span>');
                $wrapper.addClass($element[0].className);

                $wrapper.text(data.text);

                return $wrapper;
            }
        });
        $('#filtro-marca').select2();

        $('#usuario').select2();

        $('#usuario').change(function () {
            var usuario_id = $(this).val();
            if (usuario_id != '') {
                cargarInfoUsuario(usuario_id);
            } else {
                resetInfo();
            }
        });

        function resetInfo() {
            $('#rut').val('');
            $('#nombre').val('');
            $('#apellidos').val('');
            $('#telefono').val('');
            $('#email').val('');
            $('#calle').val('');
            $('#numero').val('');
        };

        function cargarInfoUsuario(usuario_id) {
            var rut = $('#usuario option[value="' + usuario_id + '"]').data("rut");
            $('#rut').val(rut);

            var nombre = $('#usuario option[value="' + usuario_id + '"]').data("nombre");
            $('#nombre').val(nombre);

            var apellidos = $('#usuario option[value="' + usuario_id + '"]').data("apellidos");
            $('#apellidos').val(apellidos);

            var telefono = $('#usuario option[value="' + usuario_id + '"]').data("telefono");
            $('#telefono').val(telefono);

            var email = $('#usuario option[value="' + usuario_id + '"]').data("email");
            $('#email').val(email);

            var calle = $('#usuario option[value="' + usuario_id + '"]').data("calle");
            $('#calle').val(calle);

            var numero = $('#usuario option[value="' + usuario_id + '"]').data("numero");
            $('#numero').val(numero);
        };
    };

    //Wizard
    var activeTab = 1;
    var totalTabs = 2;
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
            if (validWizard()) {
                activeTab++;
                $('#btn-wizard-anterior').removeClass('m--hide');
                if (activeTab == totalTabs) {
                    $('#btn-wizard-finalizar').removeClass('m--hide');
                    $('#btn-wizard-siguiente').addClass('m--hide');
                }

                mostrarTab();
            }
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
        $('#li-tab-transaccion').removeClass('m--hide').addClass('m--hide');
    }
    var validWizard = function () {
        var result = true;
        if (activeTab == 1) {

            if (!$('#cotizacion-form').valid()) {
                result = false;
            }

        }

        return result;
    }

    //Productos
    var oTableProductos;
    var productos = [];
    var initTableProductos = function () {
        MyApp.block('#productos-table-editable');

        var table = $('#productos-table-editable');

        var aoColumns = [
            {
                field: "nombre",
                title: "Nombre",
                width: 200,
                template: function (row) {

                    var output = '<div class="m-card-user m-card-user--sm">\
								<div class="m-card-user__pic m-card-producto__pic">\
									<img src="' + row.imagen + '" class="m--img-rounded m--marginless" alt="photo" style="max-width: 60px !important;border-radius: 0px;">\
								</div>\
								<div class="m-card-user__details">\
									<span class="m-card-user__name">' + row.nombre + '</span>\
								</div>\
							</div>';
                    return output;
                }
            },
            {
                field: "categoria",
                title: "Categoría",
                responsive: {visible: 'lg'},
            },
            {
                field: "marca",
                title: "Marca",
                responsive: {visible: 'lg'},
            },
            {
                field: "precio",
                title: "Precio",
                width: 60,
                responsive: {visible: 'lg'},
                textAlign: 'center'
            },
            {
                field: "fechapublicacion",
                title: "Fecha de Publicación",
                responsive: {visible: 'lg'},
                width: 120,
                textAlign: 'center'
            },
            {
                field: "acciones",
                width: 80,
                title: "Acciones",
                sortable: false,
                overflow: 'visible',
                textAlign: 'center'
            }
        ];
        oTableProductos = table.mDatatable({
            // datasource definition
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: 'producto/listarParaCotizacion',
                    }
                },
                pageSize: 10,
                saveState: {
                    cookie: false,
                    webstorage: false
                },
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true
            },
            // layout definition
            layout: {
                theme: 'default', // datatable theme
                class: '', // custom wrapper class
                scroll: true, // enable/disable datatable scroll both horizontal and vertical when needed.
                height: 310, // datatable's body's fixed height
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
                    processing: 'Por favor espere...',
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
        oTableProductos
            .on('m-datatable--on-ajax-done', function () {
                mApp.unblock('#productos-table-editable');
            })
            .on('m-datatable--on-ajax-fail', function (e, jqXHR) {
                mApp.unblock('#productos-table-editable');
            })
            .on('m-datatable--on-goto-page', function (e, args) {
                MyApp.block('#productos-table-editable');
            })
            .on('m-datatable--on-reloaded', function (e) {
                MyApp.block('#productos-table-editable');
            })
            .on('m-datatable--on-sort', function (e, args) {
                MyApp.block('#productos-table-editable');
            })
            .on('m-datatable--on-check', function (e, args) {
                //eventsWriter('Checkbox active: ' + args.toString());
            })
            .on('m-datatable--on-uncheck', function (e, args) {
                //eventsWriter('Checkbox inactive: ' + args.toString());
            });

        //Busqueda
        var query = oTableProductos.getDataSourceQuery();
        $('#lista-relacionado .m_form_search').on('keyup', function (e) {
            // shortcode to datatable.getDataSourceParam('query');
            var query = oTableProductos.getDataSourceQuery();
            query.generalSearch = $(this).val().toLowerCase();

            var categoria_id = $('#filtro-categoria').val();
            query.categoria_id = categoria_id;

            var marca_id = $('#filtro-marca').val();
            query.marca_id = marca_id;

            var productos_id = devolverProductosId();
            query.productos_id = productos_id;

            // shortcode to datatable.setDataSourceParam('query', query);
            oTableProductos.setDataSourceQuery(query);
            oTableProductos.load();
        }).val(query.generalSearch);

    };
    var btnClickFiltrarProductos = function () {

        var generalSearch = $('#lista-producto .m_form_search').val();

        var query = oTableProductos.getDataSourceQuery();

        query.generalSearch = generalSearch;

        var categoria_id = $('#filtro-categoria').val();
        query.categoria_id = categoria_id;

        var marca_id = $('#filtro-marca').val();
        query.marca_id = marca_id;

        var productos_id = devolverProductosId();
        query.productos_id = productos_id;

        oTableProductos.setDataSourceQuery(query);
        oTableProductos.load();

    };
    var devolverProductosId = function () {
        var lista = [];
        for (var i = 0; i < productos.length; i++) {
            lista.push(productos[i].producto_id);
        }

        return lista;
    }

    var ExisteProducto = function (id) {
        var result = false;
        for (var i = 0; i < productos.length; i++) {
            if (productos[i].producto_id == id) {
                result = true;
                break;
            }
        }
        return result;
    };
    var initAccionesProductos = function () {

        $(document).off('click', "#btn-agregar-productos");
        $(document).on('click', "#btn-agregar-productos", function (e) {
            $('#modal-productos').modal({
                'show': true
            });
            btnClickFiltrarProductos();
        });
        $(document).off('click', "#btn-filtrar-productos");
        $(document).on('click', "#btn-filtrar-productos", function (e) {
            btnClickFiltrarProductos();
        });

        $(document).off('click', "#productos-table-editable a.add");
        $(document).on('click', "#productos-table-editable a.add", function (e) {
            e.preventDefault();

            var producto_id = $(this).data('id');

            var nombre = $(this).data('nombre');
            var categoria = $(this).data('categoria');
            var marca = $(this).data('marca');
            var imagen = $(this).data('imagen');
            var precio = $(this).data('precio');

            var cantidad = 1;
            var posicion = productos.length;

            var existeProducto = ExisteProducto(producto_id);
            if (!existeProducto) {

                productos.push({
                    cotizacionproducto_id: '',
                    producto_id: producto_id,
                    nombre: nombre,
                    categoria: categoria,
                    marca: marca,
                    imagen: imagen,
                    cantidad: cantidad,
                    precio: precio,
                    posicion: posicion
                });
                //actualizar lista
                dibujarTablaProductos();

                btnClickFiltrarProductos();

            } else {
                toastr.error("Ya se ha agregado el producto seleccionado", "Error !!!");
            }
        });

        $(document).off('click', "#table-productos a.delete");
        $(document).on('click', "#table-productos a.delete", function (e) {

            e.preventDefault();
            var posicion = $(this).data('posicion');

            if (productos[posicion]) {

                var cotizacionproducto_id = productos[posicion].cotizacionproducto_id;

                if (cotizacionproducto_id !== '') {

                    MyApp.block('#table-productos');

                    $.ajax({
                        type: "POST",
                        url: "cotizacion/eliminarProducto",
                        dataType: "json",
                        data: {
                            'cotizacionproducto_id': cotizacionproducto_id
                        },
                        success: function (response) {
                            mApp.unblock('#table-productos');
                            if (response.success) {

                                toastr.success(response.message, "Exito !!!");
                                deleteProducto(posicion);
                                oTable.load();
                            } else {
                                toastr.error(response.error, "Error !!!");
                            }
                        },
                        failure: function (response) {
                            mApp.unblock('#table-productos');
                            toastr.error(response.error, "Error !!!");
                        }
                    });
                } else {
                    deleteProducto(posicion);
                }
            }
        });

        function deleteProducto(posicion) {
            //Eliminar
            productos.splice(posicion, 1);
            //actualizar posiciones
            for (var i = 0; i < productos.length; i++) {
                productos[i].posicion = i;
            }
            //actualizar lista
            dibujarTablaProductos();
        }

        $(document).off('change', "#table-productos input.m-input-cantidad");
        $(document).on('change', "#table-productos input.m-input-cantidad", function (e) {

            var posicion = $(this).data('posicion');
            if (productos[posicion]) {
                var cant = $(this).val();
                productos[posicion].cantidad = cant;
                dibujarTablaProductos();
            }
        });

        $(document).off('click', "#table-productos .addon-cantidad-mas");
        $(document).on('click', "#table-productos .addon-cantidad-mas", function (e) {

            var posicion = $(this).data('posicion');
            if (productos[posicion]) {

                var cant = $('#m-input-cantidad-' + posicion).val();
                cant = parseInt(cant) + 1;
                $('#m-input-cantidad-' + posicion).val(cant);

                productos[posicion].cantidad = cant;
                dibujarTablaProductos();
            }
        });

        $(document).off('click', "#table-productos .addon-cantidad-menos");
        $(document).on('click', "#table-productos .addon-cantidad-menos", function (e) {

            var posicion = $(this).data('posicion');
            if (productos[posicion]) {
                var cant = $('#m-input-cantidad-' + posicion).val();
                cant = parseInt(cant) - 1;

                if (cant > 0) {
                    $('#m-input-cantidad-' + posicion).val(cant);

                    productos[posicion].cantidad = cant;
                    dibujarTablaProductos();
                }
            }
        });


    };
    var dibujarTablaProductos = function () {
        var tabla = '#table-productos'
        //Limpiar tabla
        $(tabla + ' tbody tr').each(function (e) {
            $(this).remove();
        });

        //registro en blanco
        if (productos.length == 0) {
            tr = '<tr>' +
                '<td colspan="8">No existen productos</td>' +
                '</tr>';

            $(tr).appendTo(tabla + ' tbody');
        }

        //Agregar elementos
        var total = 0;
        for (var i = 0; i < productos.length; i++) {

            var acciones = '<a href="javascript:;" data-posicion="' + i + '" class="delete m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="Eliminar producto"><i class="la la-trash"></i></a>';

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

            var output_cantidad = '<div class="input-group m-input-group m-input-group--air">\n' +
                '<div class="input-group-prepend"><span class="input-group-text addon-cantidad-menos" data-posicion="' + i + '">-</span></div>\n' +
                '<input type="text" class="form-control m-input m-input-cantidad just-number text-center" placeholder="Cantidad" id="m-input-cantidad-' + i + '" value="' + cantidad + '" data-posicion="' + i + '">\n' +
                '<div class="input-group-prepend"><span class="input-group-text addon-cantidad-mas" data-posicion="' + i + '">+</span></div>\n' +
                '</div>';

            var output_precio = '<span class="m--font-danger">$' + MyApp.formatearNumero(precio, 0, ',', '.') + '</span>';
            var output_subtotal = '<span class="m--font-danger">$' + MyApp.formatearNumero(subtotal, 0, ',', '.') + '</span>';

            var tr = '<tr id="' + i + '">' +
                '<td class="text-center">' + (i + 1) + '</td>' +
                '<td>' + nombre + '</td>' +
                '<td>' + productos[i].categoria + '</td>' +
                '<td>' + productos[i].marca + '</td>' +
                '<td class="text-center">' + output_cantidad + '</td>' +
                '<td class="text-center">' + output_precio + '</td>' +
                '<td class="text-center">' + output_subtotal + '</td>' +
                '<td class="text-center">' + acciones + '</td>' +
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
            initForm();
            initSelects();
            initAccionFiltrar();

            initAccionNuevo();
            initAccionSalvar();
            initAccionCerrar();

            initAccionEditar();
            initAccionEliminar();

            initAccionChange();

            initPortlets();

            initWizard();

            //Productos
            initTableProductos();
            initAccionesProductos();
        }

    };

}();