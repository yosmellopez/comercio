var Newsletters = function () {

    var oTable;
    var rowDelete = null;

    //Inicializa la tabla
    var initTable = function () {
        MyApp.block('#newsletter-table-editable');

        var table = $('#newsletter-table-editable');

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
                field: "email",
                title: "Email",
                width: 200,
                template: function (row) {
                    return '<a class="m-link" href="mailto:' + row.email + '">' + row.email + '</a>';
                }
            },
            {
                field: "estado",
                title: "Estado",
                responsive: {visible: 'lg'},
                width: 60,
                // callback function support for column rendering
                template: function (row) {
                    var status = {
                        1: {'title': 'Activo', 'class': ' m-badge--success'},
                        0: {'title': 'Inactivo', 'class': ' m-badge--danger'}
                    };
                    return '<span class="m-badge ' + status[row.estado].class + ' m-badge--wide">' + status[row.estado].title + '</span>';
                }
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
                        url: 'newsletter/listarNewsletter',
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
                mApp.unblock('#newsletter-table-editable');
            })
            .on('m-datatable--on-ajax-fail', function (e, jqXHR) {
                mApp.unblock('#newsletter-table-editable');
            })
            .on('m-datatable--on-goto-page', function (e, args) {
                MyApp.block('#newsletter-table-editable');
            })
            .on('m-datatable--on-reloaded', function (e) {
                MyApp.block('#newsletter-table-editable');
            })
            .on('m-datatable--on-sort', function (e, args) {
                MyApp.block('#newsletter-table-editable');
            })
            .on('m-datatable--on-check', function (e, args) {
                //eventsWriter('Checkbox active: ' + args.toString());
            })
            .on('m-datatable--on-uncheck', function (e, args) {
                //eventsWriter('Checkbox inactive: ' + args.toString());
            });

        //Busqueda
        var query = oTable.getDataSourceQuery();
        $('#lista-newsletter .m_form_search').on('keyup', function (e) {
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

            var generalSearch = $('#lista-newsletter .m_form_search').val();
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
        $('#newsletter-form input, #newsletter-form textarea').each(function (e) {
            $element = $(this);
            $element.val('');

            $element.data("title", "").removeClass("has-error").tooltip("dispose");
            $element.closest('.form-group').removeClass('has-error').addClass('success');
        });

        event_change = false;
    };

    //Validacion
    var initForm = function () {
        $("#newsletter-form").validate({
            rules: {
                email: {
                    required: true,
                    email: true
                }
            },
            messages: {
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
        $(document).off('click', "#btn-nuevo-newsletter");
        $(document).on('click', "#btn-nuevo-newsletter", function (e) {
            btnClickNuevo();
        });

        function btnClickNuevo() {
            resetForms();
            var formTitle = "¿Deseas crear una nueva suscripción? Sigue los siguientes pasos:";
            $('#form-newsletter-title').html(formTitle);
            $('#form-newsletter').removeClass('m--hide');
            $('#lista-newsletter').addClass('m--hide');
        };
    };
    //Salvar
    var initAccionSalvar = function () {
        $(document).off('click', "#btn-salvar-newsletter");
        $(document).on('click', "#btn-salvar-newsletter", function (e) {
            btnClickSalvarForm();
        });

        function btnClickSalvarForm() {
            mUtil.scrollTo();
            event_change = false;

            if ($('#newsletter-form').valid()) {

                var newsletter_id = $('#newsletter_id').val();

                var email = $('#email').val();
                var estado = ($('#estadoactivo').prop('checked')) ? 1 : 0;

                MyApp.block('#form-newsletter');

                $.ajax({
                    type: "POST",
                    url: "newsletter/salvarNewsletter",
                    dataType: "json",
                    data: {
                        'newsletter_id': newsletter_id,
                        'email': email,
                        'estado': estado
                    },
                    success: function (response) {
                        mApp.unblock('#form-newsletter');
                        if (response.success) {

                            toastr.success(response.message, "Exito !!!");
                            cerrarForms();
                            oTable.load();
                        } else {
                            toastr.error(response.error, "Error !!!");
                        }
                    },
                    failure: function (response) {
                        mApp.unblock('#form-newsletter');

                        toastr.error(response.error, "Error !!!");
                    }
                });

            }
        };
    }
    //Cerrar form
    var initAccionCerrar = function () {
        $(document).off('click', ".cerrar-form-newsletter");
        $(document).on('click', ".cerrar-form-newsletter", function (e) {
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
        $(document).off('click', "#newsletter-table-editable a.edit");
        $(document).on('click', "#newsletter-table-editable a.edit", function (e) {
            e.preventDefault();
            resetForms();


            var newsletter_id = $(this).data('id');
            $('#newsletter_id').val(newsletter_id);

            $('#form-newsletter').removeClass('m--hide');
            $('#lista-newsletter').addClass('m--hide');

            editRow(newsletter_id);
        });

        function editRow(newsletter_id) {

            MyApp.block('#newsletter-form');

            $.ajax({
                type: "POST",
                url: "newsletter/cargarDatos",
                dataType: "json",
                data: {
                    'newsletter_id': newsletter_id
                },
                success: function (response) {
                    mApp.unblock('#newsletter-form');
                    if (response.success) {
                        //Datos newsletter    

                        var formTitle = "¿Deseas actualizar la suscripción \"" + response.newsletter.email + "\" ? Sigue los siguientes pasos:";
                        $('#form-newsletter-title').html(formTitle);

                        $('#email').val(response.newsletter.email);
                        if (!response.newsletter.estado) {
                            $('#estadoactivo').prop('checked', false);
                            $('#estadoinactivo').prop('checked', true);
                        }

                        event_change = false;

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#newsletter-form');

                    toastr.error(response.error, "Error !!!");
                }
            });

        }
    };

    //Eliminar
    var initAccionEliminar = function () {
        $(document).off('click', "#newsletter-table-editable a.delete");
        $(document).on('click', "#newsletter-table-editable a.delete", function (e) {
            e.preventDefault();

            rowDelete = $(this).data('id');
            $('#modal-eliminar').modal({
                'show': true
            });
        });

        $(document).off('click', "#btn-eliminar-newsletter");
        $(document).on('click', "#btn-eliminar-newsletter", function (e) {
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
            var newsletter_id = rowDelete;

            MyApp.block('#newsletter-table-editable');

            $.ajax({
                type: "POST",
                url: "newsletter/eliminarNewsletter",
                dataType: "json",
                data: {
                    'newsletter_id': newsletter_id
                },
                success: function (response) {
                    mApp.unblock('#newsletter-table-editable');
                    if (response.success) {
                        oTable.load();

                        toastr.success(response.message, "Exito !!!");

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#newsletter-table-editable');

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

            MyApp.block('#newsletter-table-editable');

            $.ajax({
                type: "POST",
                url: "newsletter/eliminarNewsletters",
                dataType: "json",
                data: {
                    'ids': ids
                },
                success: function (response) {
                    mApp.unblock('#newsletter-table-editable');
                    if (response.success) {
                        oTable.load();

                        toastr.success(response.message, "Exito !!!");

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#newsletter-table-editable');
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
        $('#form-newsletter').addClass('m--hide');
        $('#lista-newsletter').removeClass('m--hide');
    }

    //initPortlets
    var initPortlets = function () {
        var portlet = new mPortlet('lista-newsletter');
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