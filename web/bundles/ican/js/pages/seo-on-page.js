var SeoOnPage = function () {

    var oTable;
    var rowDelete = null;

    //Inicializa la tabla
    var initTable = function () {
        MyApp.block('#pagina-table-editable');

        var table = $('#pagina-table-editable');

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
                field: "seccion",
                title: "Sección",
                sortable: false, // disable sort for this column
            },
            {
                field: "titulo",
                title: "Título"
            },
            {
                field: "descripcion",
                title: "Descripción",
                responsive: {visible: 'lg'},
                width: 350
            },
            {
                field: "tags",
                title: "Tags",
                responsive: {visible: 'lg'}
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
                        url: 'seo-on-page/listarPagina',
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
                mApp.unblock('#pagina-table-editable');
            })
            .on('m-datatable--on-ajax-fail', function (e, jqXHR) {
                mApp.unblock('#pagina-table-editable');
            })
            .on('m-datatable--on-goto-page', function (e, args) {
                MyApp.block('#pagina-table-editable');
            })
            .on('m-datatable--on-reloaded', function (e) {
                MyApp.block('#pagina-table-editable');
            })
            .on('m-datatable--on-sort', function (e, args) {
                MyApp.block('#pagina-table-editable');
            })
            .on('m-datatable--on-check', function (e, args) {
                //eventsWriter('Checkbox active: ' + args.toString());
            })
            .on('m-datatable--on-uncheck', function (e, args) {
                //eventsWriter('Checkbox inactive: ' + args.toString());
            });

        //Busqueda
        var query = oTable.getDataSourceQuery();
        $('#lista-pagina .m_form_search').on('keyup', function (e) {

            var query = oTable.getDataSourceQuery();

            query.generalSearch = $(this).val().toLowerCase();

            oTable.setDataSourceQuery(query);
            oTable.load();
        }).val(query.generalSearch);
    };

    //Reset forms
    var resetForms = function () {
        $('#pagina-form input, #pagina-form textarea').each(function (e) {
            $element = $(this);
            $element.val('');

            $element.data("title", "").removeClass("has-error").tooltip("dispose");
            $element.closest('.form-group').removeClass('has-error').addClass('success');
        });

        $('#url').val('');
        $('#url').trigger('change');

        //Limpiar tags
        $('#tags_tagsinput span').each(function (e) {
            $(this).remove();
        });

        var $element = $('.tagsinput');
        $element.tooltip("dispose");
        $element.removeClass('has-error');
        $element.closest('.form-group')
            .removeClass('has-error');

        var $element = $('.select2');
        $element.removeClass('has-error').tooltip("dispose");

        $element.closest('.form-group')
            .removeClass('has-error');

        event_change = false;
    };

    //Validacion
    var initForm = function () {
        $("#pagina-form").validate({
            rules: {
                titulo: {
                    required: true,
                    maxlength: 60
                },
                descripcion: {
                    required: true,
                    maxlength: 160
                },
                tags: {
                    required: true
                }
            },
            messages: {
                titulo: {
                    required: "Este campo es obligatorio",
                    maxlength: "Por favor, no escribas más de {0} caracteres"
                },
                descripcion: {
                    required: "Este campo es obligatorio",
                    maxlength: "Por favor, no escribas más de {0} caracteres"
                },
                tags: "Este campo es obligatorio"
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
        $(document).off('click', "#btn-nuevo-pagina");
        $(document).on('click', "#btn-nuevo-pagina", function (e) {
            btnClickNuevo();
        });

        function btnClickNuevo() {
            resetForms();
            var formTitle = "¿Deseas crear una nueva página? Sigue los siguientes pasos:";
            $('#form-pagina-title').html(formTitle);
            $('#form-pagina').removeClass('m--hide');
            $('#lista-pagina').addClass('m--hide');
        };
    };
    //Salvar
    var initAccionSalvar = function () {
        $(document).off('click', "#btn-salvar-seo");
        $(document).on('click', "#btn-salvar-seo", function (e) {
            btnClickSalvarForm();
        });

        function btnClickSalvarForm() {
            mUtil.scrollTo();
            event_change = false;

            var pagina_id = $('#pagina_id').val();

            var tags = $('#tags').val();
            var url = $('#url').val();


            if ($('#pagina-form').valid() && url !== "" && tags != "") {

                var titulo = $('#titulo').val();
                var descripcion = $('#descripcion').val();

                MyApp.block('#form-pagina');

                $.ajax({
                    type: "POST",
                    url: "seo-on-page/salvarPagina",
                    dataType: "json",
                    data: {
                        'pagina_id': pagina_id,
                        'url': url,
                        'titulo': titulo,
                        'descripcion': descripcion,
                        'tags': tags
                    },
                    success: function (response) {
                        mApp.unblock('#form-pagina');
                        if (response.success) {

                            toastr.success(response.message, "Exito !!!");
                            cerrarForms();
                            oTable.load();
                        } else {
                            toastr.error(response.error, "Error !!!");
                        }
                    },
                    failure: function (response) {
                        mApp.unblock('#form-pagina');

                        toastr.error(response.error, "Error !!!");
                    }
                });

            } else {
                if (tags == "") {
                    var $element = $('.tagsinput');
                    $element.tooltip("dispose") // Destroy any pre-existing tooltip so we can repopulate with new tooltip content
                        .data("title", "Este campo es obligatorio")
                        .addClass("has-error")
                        .tooltip({
                            placement: 'top'
                        }); // Create a new tooltip based on the error messsage we just set in the title

                    $element.closest('.form-group')
                        .removeClass('has-success').addClass('has-error');
                }
                if (url == "") {

                    var $element = $('#select-url .select2');
                    $element.tooltip("dispose") // Destroy any pre-existing tooltip so we can repopulate with new tooltip content
                        .data("title", "Este campo es obligatorio")
                        .addClass("has-error")
                        .tooltip({
                            placement: 'bottom'
                        }); // Create a new tooltip based on the error messsage we just set in the title

                    $element.closest('.form-group')
                        .removeClass('has-success').addClass('has-error');
                    return;
                }
            }
        };
    }
    //Cerrar form
    var initAccionCerrar = function () {
        $(document).off('click', ".cerrar-form-pagina");
        $(document).on('click', ".cerrar-form-pagina", function (e) {
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
        $(document).off('click', "#pagina-table-editable a.edit");
        $(document).on('click', "#pagina-table-editable a.edit", function (e) {
            e.preventDefault();
            resetForms();


            var pagina_id = $(this).data('id');
            $('#pagina_id').val(pagina_id);

            $('#form-pagina').removeClass('m--hide');
            $('#lista-pagina').addClass('m--hide');

            editRow(pagina_id);
        });

        function editRow(pagina_id) {

            MyApp.block('#pagina-form');

            $.ajax({
                type: "POST",
                url: "seo-on-page/cargarDatos",
                dataType: "json",
                data: {
                    'pagina_id': pagina_id
                },
                success: function (response) {
                    mApp.unblock('#pagina-form');
                    if (response.success) {
                        //Datos pagina    

                        var formTitle = "¿Deseas actualizar la página \"" + response.pagina.titulo + "\" ? Sigue los siguientes pasos:";
                        $('#form-pagina-title').html(formTitle);

                        $('#titulo').val(response.pagina.titulo);
                        $('#descripcion').val(response.pagina.descripcion);

                        var tags = response.pagina.tags;
                        if (tags != "" && tags != null) {
                            tags = tags.split(',');
                            for (var i = 0; i < tags.length; i++)
                                $('#tags').addTag(tags[i]);
                        }

                        $('#url').val(response.pagina.url);
                        $('#url').trigger('change');

                        event_change = false;

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#pagina-form');

                    toastr.error(response.error, "Error !!!");
                }
            });

        }
    };
    //Eliminar
    var initAccionEliminar = function () {
        $(document).off('click', "#pagina-table-editable a.delete");
        $(document).on('click', "#pagina-table-editable a.delete", function (e) {
            e.preventDefault();

            rowDelete = $(this).data('id');
            $('#modal-eliminar').modal({
                'show': true
            });
        });

        $(document).off('click', "#btn-eliminar-pagina");
        $(document).on('click', "#btn-eliminar-pagina", function (e) {
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
            var pagina_id = rowDelete;

            MyApp.block('#pagina-table-editable');

            $.ajax({
                type: "POST",
                url: "seo-on-page/eliminarPagina",
                dataType: "json",
                data: {
                    'pagina_id': pagina_id
                },
                success: function (response) {
                    mApp.unblock('#pagina-table-editable');
                    if (response.success) {
                        oTable.load();

                        toastr.success(response.message, "Exito !!!");

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#pagina-table-editable');

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

            MyApp.block('#pagina-table-editable');

            $.ajax({
                type: "POST",
                url: "seo-on-page/eliminarPaginas",
                dataType: "json",
                data: {
                    'ids': ids
                },
                success: function (response) {
                    mApp.unblock('#pagina-table-editable');
                    if (response.success) {
                        oTable.load();

                        toastr.success(response.message, "Exito !!!");

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#pagina-table-editable');
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
        $('#form-pagina').addClass('m--hide');
        $('#lista-pagina').removeClass('m--hide');
    };

    //initPortlets
    var initPortlets = function () {
        var portlet = new mPortlet('lista-pagina');
        portlet.on('afterFullscreenOn', function (portlet) {
            $('.m-portlet').addClass('m-portlet--fullscreen');
        });

        portlet.on('afterFullscreenOff', function (portlet) {
            $('.m-portlet').removeClass('m-portlet--fullscreen');
        });
    }

    var initTagsInput = function () {

        $('.tags').tagsInput({
            width: 'auto',
            defaultText: 'Tag...',
        });

    }

    //Init selects
    var initSelects = function () {
        $('#url').select2();
    };

    return {
        //main function to initiate the module
        init: function () {

            initTable();
            initForm();

            initSelects();
            initTagsInput();

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