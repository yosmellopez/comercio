var Descuentos = function () {

    var oTable;
    var rowDelete = null;

    //Inicializa la tabla
    var initTable = function () {
        MyApp.block('#descuento-table-editable');

        var table = $('#descuento-table-editable');

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
                title: "Nombre",
            },
            {
                field: "valor",
                title: "Descuento",
                responsive: {visible: 'lg'},
                textAlign: 'center',
                template: function (row) {
                    return row.valor + '%';
                }
            },
            {
                field: "fechainicio",
                title: "Inicio",
                responsive: {visible: 'lg'},
                width: 120,
                textAlign: 'center'
            },
            {
                field: "fechafin",
                title: "Fin",
                responsive: {visible: 'lg'},
                width: 120,
                textAlign: 'center'
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
                        url: 'descuento/listarDescuento',
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
        oTable
            .on('m-datatable--on-ajax-done', function () {
                mApp.unblock('#descuento-table-editable');
            })
            .on('m-datatable--on-ajax-fail', function (e, jqXHR) {
                mApp.unblock('#descuento-table-editable');
            })
            .on('m-datatable--on-goto-page', function (e, args) {
                MyApp.block('#descuento-table-editable');
            })
            .on('m-datatable--on-reloaded', function (e) {
                MyApp.block('#descuento-table-editable');
            })
            .on('m-datatable--on-sort', function (e, args) {
                MyApp.block('#descuento-table-editable');
            })
            .on('m-datatable--on-check', function (e, args) {
                //eventsWriter('Checkbox active: ' + args.toString());
            })
            .on('m-datatable--on-uncheck', function (e, args) {
                //eventsWriter('Checkbox inactive: ' + args.toString());
            });

        //Busqueda
        var query = oTable.getDataSourceQuery();
        $('#lista-descuento .m_form_search').on('keyup', function (e) {

            var query = oTable.getDataSourceQuery();

            query.generalSearch = $(this).val().toLowerCase();

            oTable.setDataSourceQuery(query);
            oTable.load();
        }).val(query.generalSearch);
    };

    //Reset forms
    var resetForms = function () {
        $('#descuento-form input').each(function (e) {
            $element = $(this);
            $element.val('');

            $element.data("title", "").removeClass("has-error").tooltip("dispose");
            $element.closest('.form-group').removeClass('has-error').addClass('success');
        });

        var fecha_actual = new Date();
        var fecha_aux = new Date();
        fecha_aux.setHours(120 + fecha_aux.getHours()); // Por defecto 5 dias para la descuento.

        $('#fechainicio').val(fecha_actual.format("d/m/Y H:i"));
        $('#fechafin').val(fecha_aux.format("d/m/Y H:i"));

        $('#porciento').val('');
        $('#porciento').trigger('change');

        $('#estadoactivo').prop('checked', true);

        var $element = $('.select2');
        $element.removeClass('has-error').tooltip("dispose");

        $element.closest('.form-group')
            .removeClass('has-error');

        event_change = false;

        //Reset productos
        productos = [];
        actualizarTableListaProductos();

        //Mostrar el primer tab
        resetWizard();
    };

    //Validacion
    var initForm = function () {
        $("#descuento-form").validate({
            rules: {
                nombre: {
                    required: true
                }
            },
            messages: {
                nombre: "Este campo es obligatorio"
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
        $(document).off('click', "#btn-nuevo-descuento");
        $(document).on('click', "#btn-nuevo-descuento", function (e) {
            btnClickNuevo();
        });

        function btnClickNuevo() {
            resetForms();
            var formTitle = "¿Deseas crear un nuevo descuento especial? Sigue los siguientes pasos:";
            $('#form-descuento-title').html(formTitle);
            $('#form-descuento').removeClass('m--hide');
            $('#lista-descuento').addClass('m--hide');
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

            var descuento_id = $('#descuento_id').val();
            var porciento_id = $('#porciento').val();

            if ($('#descuento-form').valid() && porciento_id != "" && productos.length > 0) {

                var nombre = $('#nombre').val();
                var estado = ($('#estadoactivo').prop('checked')) ? 1 : 0;

                var fechainicio = $('#fechainicio').val();
                var fechafin = $('#fechafin').val();

                MyApp.block('#form-descuento');

                $.ajax({
                    type: "POST",
                    url: "descuento/salvarDescuento",
                    dataType: "json",
                    data: {
                        'descuento_id': descuento_id,
                        'porciento': porciento_id,
                        'nombre': nombre,
                        'productos': productos,
                        'estado': estado,
                        'fechainicio': fechainicio,
                        'fechafin': fechafin
                    },
                    success: function (response) {
                        mApp.unblock('#form-descuento');
                        if (response.success) {

                            toastr.success(response.message, "Exito !!!");
                            cerrarForms();
                            oTable.load();
                        } else {
                            toastr.error(response.error, "Error !!!");
                        }
                    },
                    failure: function (response) {
                        mApp.unblock('#form-descuento');

                        toastr.error(response.error, "Error !!!");
                    }
                });

            } else {
                if (porciento_id == "") {

                    var $element = $('#select-porciento .select2');
                    $element.tooltip("dispose") // Destroy any pre-existing tooltip so we can repopulate with new tooltip content
                        .data("title", "Este campo es obligatorio")
                        .addClass("has-error")
                        .tooltip({
                            placement: 'bottom'
                        }); // Create a new tooltip based on the error messsage we just set in the title

                    $element.closest('.form-group')
                        .removeClass('has-success').addClass('has-error');
                }
                if (productos.length == 0) {
                    toastr.error("Debe agregar los productos del descuento especial", "Error !!!");
                }
            }
        };
    }
    //Cerrar form
    var initAccionCerrar = function () {
        $(document).off('click', ".cerrar-form-descuento");
        $(document).on('click', ".cerrar-form-descuento", function (e) {
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
        $(document).off('click', "#descuento-table-editable a.edit");
        $(document).on('click', "#descuento-table-editable a.edit", function (e) {
            e.preventDefault();
            resetForms();


            var descuento_id = $(this).data('id');
            $('#descuento_id').val(descuento_id);

            $('#form-descuento').removeClass('m--hide');
            $('#lista-descuento').addClass('m--hide');

            editRow(descuento_id);
        });

        function editRow(descuento_id) {

            MyApp.block('#descuento-form');

            $.ajax({
                type: "POST",
                url: "descuento/cargarDatos",
                dataType: "json",
                data: {
                    'descuento_id': descuento_id
                },
                success: function (response) {
                    mApp.unblock('#descuento-form');
                    if (response.success) {
                        //Datos descuento    

                        var formTitle = "¿Deseas actualizar la descuento \"" + response.descuento.nombre + "\" ? Sigue los siguientes pasos:";
                        $('#form-descuento-title').html(formTitle);

                        $('#nombre').val(response.descuento.nombre);

                        $('#fechainicio').val(response.descuento.fechainicio);
                        $('#fechafin').val(response.descuento.fechafin);

                        if (!response.descuento.estado) {
                            $('#estadoactivo').prop('checked', false);
                            $('#estadoinactivo').prop('checked', true);
                        }

                        $('#porciento').val(response.descuento.porciento);
                        $('#porciento').trigger('change');

                        productos = response.descuento.productos;
                        actualizarTableListaProductos();

                        totalTabs = 3;
                        $('#li-tab-usos').removeClass('m--hide');
                        actualizarTableUsos();

                        event_change = false;

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#descuento-form');

                    toastr.error(response.error, "Error !!!");
                }
            });

        }
    };
    //Eliminar
    var initAccionEliminar = function () {
        $(document).off('click', "#descuento-table-editable a.delete");
        $(document).on('click', "#descuento-table-editable a.delete", function (e) {
            e.preventDefault();

            rowDelete = $(this).data('id');
            $('#modal-eliminar').modal({
                'show': true
            });
        });

        $(document).off('click', "#btn-eliminar-descuento");
        $(document).on('click', "#btn-eliminar-descuento", function (e) {
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
            var descuento_id = rowDelete;

            MyApp.block('#descuento-table-editable');

            $.ajax({
                type: "POST",
                url: "descuento/eliminarDescuento",
                dataType: "json",
                data: {
                    'descuento_id': descuento_id
                },
                success: function (response) {
                    mApp.unblock('#descuento-table-editable');
                    if (response.success) {
                        oTable.load();

                        toastr.success(response.message, "Exito !!!");

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#descuento-table-editable');

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

            MyApp.block('#descuento-table-editable');

            $.ajax({
                type: "POST",
                url: "descuento/eliminarDescuentos",
                dataType: "json",
                data: {
                    'ids': ids
                },
                success: function (response) {
                    mApp.unblock('#descuento-table-editable');
                    if (response.success) {
                        oTable.load();

                        toastr.success(response.message, "Exito !!!");

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#descuento-table-editable');
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
            //nombre = titulo por defecto
            if ($(this).attr('id') == 'nombre') {
                $('#titulo').val($(this).val());
            }
        });

        $(document).off('click', "#btn-save-changes");
        $(document).on('click', "#btn-save-changes", function (e) {
            cerrarFormsConfirmated();
        });
    };
    var cerrarFormsConfirmated = function () {
        resetForms();
        $('#form-descuento').addClass('m--hide');
        $('#lista-descuento').removeClass('m--hide');
    };

    //initPortlets
    var initPortlets = function () {
        var portlet = new mPortlet('lista-descuento');
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

    //Wizard
    var activeTab = 1;
    var totalTabs = 2;
    var initWizard = function () {
        $(document).off('click', "#form-descuento .wizard-tab");
        $(document).on('click', "#form-descuento .wizard-tab", function (e) {
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
                    actualizarTableListaProductos();
                    break;
                case 3:
                    $('#tab-usos').tab('show');
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
        $('#li-tab-usos').removeClass('m--hide').addClass('m--hide');
    }
    var validWizard = function () {
        var result = true;
        if (activeTab == 1) {

            var porciento_id = $('#porciento').val();

            if (!$('#descuento-form').valid() || porciento_id == "") {
                result = false;

                if (porciento_id == "") {
                    var $element = $('#select-porciento .select2');
                    $element.tooltip("dispose") // Destroy any pre-existing tooltip so we can repopulate with new tooltip content
                        .data("title", "Este campo es obligatorio")
                        .addClass("has-error")
                        .tooltip({
                            placement: 'bottom'
                        }); // Create a new tooltip based on the error messsage we just set in the title

                    $element.closest('.form-group')
                        .removeClass('has-success').addClass('has-error');

                }
            }

        }

        return result;
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
        $('#porciento').select2();
    };


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
								<div class="m-card-user__pic m-card-descuento__pic">\
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
                field: "views",
                title: "Visitas",
                width: 60,
                responsive: {visible: 'lg'},
                textAlign: 'center'
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
                        url: 'producto/listarParaPromocion',
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

        var generalSearch = $('#lista-relacionado .m_form_search').val();

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
            var estado = $(this).data('estado');
            var imagen = $(this).data('imagen');
            var precio = $(this).data('precio');
            var fecha = $(this).data('fecha');
            var views = $(this).data('views');

            var posicion = productos.length;

            var existeRelacionado = ExisteProducto(producto_id);
            if (!existeRelacionado) {

                productos.push({
                    producto_id: producto_id,
                    nombre: nombre,
                    categoria: categoria,
                    marca: marca,
                    estado: estado,
                    imagen: imagen,
                    precio: precio,
                    fecha: fecha,
                    views: views,
                    posicion: posicion
                });
                //actualizar lista
                actualizarTableListaProductos();

                btnClickFiltrarProductos();

                event_change = true;

            } else {
                toastr.error("Ya se ha agregado el producto seleccionado", "Error !!!");
            }
        });

        $(document).off('click', "#lista-productos-table-editable a.delete");
        $(document).on('click', "#lista-productos-table-editable a.delete", function (e) {

            e.preventDefault();
            var posicion = $(this).data('posicion');

            if (productos[posicion]) {

                var descuento_id = $('#descuento_id').val();

                if (descuento_id !== '') {

                    MyApp.block('#lista-productos-table-editable');

                    $.ajax({
                        type: "POST",
                        url: "descuento/eliminarProducto",
                        dataType: "json",
                        data: {
                            'producto_id': productos[posicion].producto_id
                        },
                        success: function (response) {
                            mApp.unblock('#lista-productos-table-editable');
                            if (response.success) {

                                toastr.success(response.message, "Exito !!!");
                                deleteProducto(posicion);
                            } else {
                                toastr.error(response.error, "Error !!!");
                            }
                        },
                        failure: function (response) {
                            mApp.unblock('#lista-productos-table-editable');
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
            actualizarTableListaProductos();
        }
    };

    var oTableListaProductos;
    var initTableListaProductos = function () {
        MyApp.block('#lista-productos-table-editable');

        var table = $('#lista-productos-table-editable');

        var aoColumns = [
            {
                field: "nombre",
                title: "Nombre",
                width: 200,
                template: function (row) {

                    var output = '<div class="m-card-user m-card-user--sm">\
								<div class="m-card-user__pic m-card-descuento__pic">\
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
                textAlign: 'center',
                template: function (row) {
                    return MyApp.formatearNumero(row.precio, 0, ',', '.')
                }
            },
            {
                field: "fecha",
                title: "Fecha de Publicación",
                responsive: {visible: 'lg'},
                width: 120,
                textAlign: 'center'
            },
            {
                field: "views",
                title: "Visitas",
                width: 60,
                responsive: {visible: 'lg'},
                textAlign: 'center'
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
                field: "posicion",
                width: 80,
                title: "Acciones",
                sortable: false,
                overflow: 'visible',
                textAlign: 'center',
                template: function (row) {
                    return '<a href="javascript:;" data-posicion="' + row.posicion + '" class="delete m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="Eliminar relacionado"><i class="la la-trash"></i></a>';
                }
            }
        ];
        oTableListaProductos = table.mDatatable({
            // datasource definition
            data: {
                type: 'local',
                source: productos,
                pageSize: 10,
                saveState: {
                    cookie: true,
                    webstorage: true
                }
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
        oTableListaProductos
            .on('m-datatable--on-ajax-done', function () {
                mApp.unblock('#lista-productos-table-editable');
            })
            .on('m-datatable--on-ajax-fail', function (e, jqXHR) {
                mApp.unblock('#lista-productos-table-editable');
            })
            .on('m-datatable--on-goto-page', function (e, args) {
                MyApp.block('#lista-productos-table-editable');
            })
            .on('m-datatable--on-reloaded', function (e) {
                MyApp.block('#lista-productos-table-editable');
            })
            .on('m-datatable--on-sort', function (e, args) {
                MyApp.block('#lista-productos-table-editable');
            })
            .on('m-datatable--on-check', function (e, args) {
                //eventsWriter('Checkbox active: ' + args.toString());
            })
            .on('m-datatable--on-uncheck', function (e, args) {
                //eventsWriter('Checkbox inactive: ' + args.toString());
            });

        //Busqueda
        var query = oTableListaProductos.getDataSourceQuery();
        $('#lista-productos .m_form_search').on('keyup', function (e) {
            // shortcode to datatable.getDataSourceParam('query');
            var query = oTableListaProductos.getDataSourceQuery();
            query.generalSearch = $(this).val().toLowerCase();

            // shortcode to datatable.setDataSourceParam('query', query);
            oTableListaProductos.setDataSourceQuery(query);

            oTableListaProductos.search(query.generalSearch);

        }).val(query.generalSearch);

    };
    var actualizarTableListaProductos = function () {
        oTableListaProductos.destroy();
        initTableListaProductos();
    }

    //Usos
    var oTableUsos;
    var initTableUsos = function () {
        MyApp.block('#usos-table-editable');

        var table = $('#usos-table-editable');

        var aoColumns = [
            {
                field: "rut",
                title: "Rut",
                width: 120,
            },
            {
                field: "email",
                title: "Email",
                responsive: {visible: 'lg'},
                template: function (row) {
                    return '<a class="m-link" href="mailto:' + row.email + '">' + row.email + '</a>';
                }
            },
            {
                field: "createdAt",
                title: "Fecha",
                responsive: {visible: 'lg'},
                width: 120,
                textAlign: 'center'
            }
        ];
        oTableUsos = table.mDatatable({
            // datasource definition
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: 'descuento/listarUsos',
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
        oTableUsos
            .on('m-datatable--on-ajax-done', function () {
                mApp.unblock('#usos-table-editable');
            })
            .on('m-datatable--on-ajax-fail', function (e, jqXHR) {
                mApp.unblock('#usos-table-editable');
            })
            .on('m-datatable--on-goto-page', function (e, args) {
                MyApp.block('#usos-table-editable');
            })
            .on('m-datatable--on-reloaded', function (e) {
                MyApp.block('#usos-table-editable');
            })
            .on('m-datatable--on-sort', function (e, args) {
                MyApp.block('#usos-table-editable');
            })
            .on('m-datatable--on-check', function (e, args) {
                //eventsWriter('Checkbox active: ' + args.toString());
            })
            .on('m-datatable--on-uncheck', function (e, args) {
                //eventsWriter('Checkbox inactive: ' + args.toString());
            });

        //Busqueda
        var query = oTableUsos.getDataSourceQuery();
        $('#lista-usos .m_form_search').on('keyup', function (e) {
            // shortcode to datatable.getDataSourceParam('query');
            var query = oTableUsos.getDataSourceQuery();
            query.generalSearch = $(this).val().toLowerCase();

            var descuento_id = $('#descuento_id').val();
            query.descuento_id = descuento_id;

            // shortcode to datatable.setDataSourceParam('query', query);
            oTableUsos.setDataSourceQuery(query);
            oTableUsos.load();
        }).val(query.generalSearch);

    };
    var actualizarTableUsos = function () {

        var generalSearch = $('#lista-usos .m_form_search').val();

        var query = oTableUsos.getDataSourceQuery();

        query.generalSearch = generalSearch;

        var descuento_id = $('#descuento_id').val();
        query.descuento_id = descuento_id;

        oTableUsos.setDataSourceQuery(query);
        oTableUsos.load();

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

            initWizard();

            //Productos
            initTableProductos();
            initTableListaProductos();
            initAccionesProductos();

            //Usos
            initTableUsos();
        }

    };

}();