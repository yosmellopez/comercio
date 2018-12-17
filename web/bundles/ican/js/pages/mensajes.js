var Mensajes = function () {

    var oTable;
    var rowDelete = null;

    //Inicializa la tabla
    var initTable = function () {
        MyApp.block('#mensaje-table-editable');

        var table = $('#mensaje-table-editable');

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
                field: "asunto",
                title: "Asunto"
            },
            {
                field: "descripcion",
                title: "Mensaje",
                responsive: {visible: 'lg'},
                width: 350
            },
            {
                field: "fecha",
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
                        url: 'mensaje/listarMensaje',
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
                mApp.unblock('#mensaje-table-editable');
            })
            .on('m-datatable--on-ajax-fail', function (e, jqXHR) {
                mApp.unblock('#mensaje-table-editable');
            })
            .on('m-datatable--on-goto-page', function (e, args) {
                MyApp.block('#mensaje-table-editable');
            })
            .on('m-datatable--on-reloaded', function (e) {
                MyApp.block('#mensaje-table-editable');
            })
            .on('m-datatable--on-sort', function (e, args) {
                MyApp.block('#mensaje-table-editable');
            })
            .on('m-datatable--on-check', function (e, args) {
                //eventsWriter('Checkbox active: ' + args.toString());
            })
            .on('m-datatable--on-uncheck', function (e, args) {
                //eventsWriter('Checkbox inactive: ' + args.toString());
            });

        //Busqueda
        var query = oTable.getDataSourceQuery();
        $('#lista-mensaje .m_form_search').on('keyup', function (e) {
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

            var generalSearch = $('#lista-mensaje .m_form_search').val();
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
        $('#mensaje-form input, #mensaje-form textarea').each(function (e) {
            $element = $(this);
            $element.val('');

            $element.data("title", "").removeClass("has-error").tooltip("dispose");
            $element.closest('.form-group').removeClass('has-error').addClass('success');
        });

        event_change = false;
    };

    //Validacion
    var initForm = function () {
        $("#mensaje-form").validate({
            rules: {
                nombre: {
                    required: true
                },
                telefono: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                asunto: {
                    required: true
                },
                descripcion: {
                    required: true
                }
            },
            messages: {
                nombre: "Este campo es obligatorio",
                descripcion: "Este campo es obligatorio",
                telefono: "Este campo es obligatorio",
                asunto: "Este campo es obligatorio",
                email: {
                    required: "Este campo es obligatorio",
                    email: "El Email debe ser válido"
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
        $(document).off('click', "#btn-nuevo-mensaje");
        $(document).on('click', "#btn-nuevo-mensaje", function (e) {
            btnClickNuevo();
        });

        function btnClickNuevo() {
            resetForms();
            var formTitle = "¿Deseas crear un nuevo mensaje? Sigue los siguientes pasos:";
            $('#form-mensaje-title').html(formTitle);
            $('#form-mensaje').removeClass('m--hide');
            $('#lista-mensaje').addClass('m--hide');
        };
    };
    //Salvar
    var initAccionSalvar = function () {
        $(document).off('click', "#btn-salvar-mensaje");
        $(document).on('click', "#btn-salvar-mensaje", function (e) {
            btnClickSalvarForm();
        });

        function btnClickSalvarForm() {
            mUtil.scrollTo();
            event_change = false;

            if ($('#mensaje-form').valid()) {

                var mensaje_id = $('#mensaje_id').val();

                var nombre = $('#nombre').val();
                var telefono = $('#telefono').val();
                var email = $('#email').val();
                var asunto = $('#asunto').val();
                var descripcion = $('#descripcion').val();

                MyApp.block('#form-mensaje');

                $.ajax({
                    type: "POST",
                    url: "mensaje/salvarMensaje",
                    dataType: "json",
                    data: {
                        'mensaje_id': mensaje_id,
                        'nombre': nombre,
                        'telefono': telefono,
                        'email': email,
                        'asunto': asunto,
                        'descripcion': descripcion
                    },
                    success: function (response) {
                        mApp.unblock('#form-mensaje');
                        if (response.success) {

                            toastr.success(response.message, "Exito !!!");
                            cerrarForms();
                            oTable.load();
                        } else {
                            toastr.error(response.error, "Error !!!");
                        }
                    },
                    failure: function (response) {
                        mApp.unblock('#form-mensaje');

                        toastr.error(response.error, "Error !!!");
                    }
                });

            }
        };
    }
    //Cerrar form
    var initAccionCerrar = function () {
        $(document).off('click', ".cerrar-form-mensaje");
        $(document).on('click', ".cerrar-form-mensaje", function (e) {
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
        $(document).off('click', "#mensaje-table-editable a.edit");
        $(document).on('click', "#mensaje-table-editable a.edit", function (e) {
            e.preventDefault();
            resetForms();


            var mensaje_id = $(this).data('id');
            $('#mensaje_id').val(mensaje_id);

            $('#form-mensaje').removeClass('m--hide');
            $('#lista-mensaje').addClass('m--hide');

            editRow(mensaje_id);
        });

        function editRow(mensaje_id) {

            MyApp.block('#mensaje-form');

            $.ajax({
                type: "POST",
                url: "mensaje/cargarDatos",
                dataType: "json",
                data: {
                    'mensaje_id': mensaje_id
                },
                success: function (response) {
                    mApp.unblock('#mensaje-form');
                    if (response.success) {
                        //Datos mensaje    

                        var formTitle = "¿Deseas actualizar el mensaje \"" + response.mensaje.asunto + "\" ? Sigue los siguientes pasos:";
                        $('#form-mensaje-title').html(formTitle);

                        $('#nombre').val(response.mensaje.nombre);
                        $('#telefono').val(response.mensaje.telefono);
                        $('#email').val(response.mensaje.email);
                        $('#asunto').val(response.mensaje.asunto);
                        $('#descripcion').val(response.mensaje.descripcion);

                        event_change = false;

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#mensaje-form');

                    toastr.error(response.error, "Error !!!");
                }
            });

        }
    };

    //Eliminar
    var initAccionEliminar = function () {
        $(document).off('click', "#mensaje-table-editable a.delete");
        $(document).on('click', "#mensaje-table-editable a.delete", function (e) {
            e.preventDefault();

            rowDelete = $(this).data('id');
            $('#modal-eliminar').modal({
                'show': true
            });
        });

        $(document).off('click', "#btn-eliminar-mensaje");
        $(document).on('click', "#btn-eliminar-mensaje", function (e) {
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
            var mensaje_id = rowDelete;

            MyApp.block('#mensaje-table-editable');

            $.ajax({
                type: "POST",
                url: "mensaje/eliminarMensaje",
                dataType: "json",
                data: {
                    'mensaje_id': mensaje_id
                },
                success: function (response) {
                    mApp.unblock('#mensaje-table-editable');
                    if (response.success) {
                        oTable.load();

                        toastr.success(response.message, "Exito !!!");

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#mensaje-table-editable');

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

            MyApp.block('#mensaje-table-editable');

            $.ajax({
                type: "POST",
                url: "mensaje/eliminarMensajes",
                dataType: "json",
                data: {
                    'ids': ids
                },
                success: function (response) {
                    mApp.unblock('#mensaje-table-editable');
                    if (response.success) {
                        oTable.load();

                        toastr.success(response.message, "Exito !!!");

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#mensaje-table-editable');
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
        $('#form-mensaje').addClass('m--hide');
        $('#lista-mensaje').removeClass('m--hide');
    }

    //initPortlets
    var initPortlets = function () {
        var portlet = new mPortlet('lista-mensaje');
        portlet.on('afterFullscreenOn', function (portlet) {
            $('.m-portlet').addClass('m-portlet--fullscreen');
        });

        portlet.on('afterFullscreenOff', function (portlet) {
            $('.m-portlet').removeClass('m-portlet--fullscreen');
        });
    }

    return {
        //main function to initiate the module
        init: function () {

            initTable();
            initForm();

            initAccionFiltrar();

            initAccionNuevo();
            initAccionSalvar();
            initAccionCerrar();

            initAccionEditar();
            initAccionEliminar();

            initAccionChange();

            initPortlets();
        }

    };

}();