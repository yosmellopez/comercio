var Usuarios = function () {

    var oTable;
    var rowDelete = null;

    //Inicializa la tabla
    var initTable = function () {
        MyApp.block('#usuario-table-editable');

        var table = $('#usuario-table-editable');

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
                field: "nombre",
                title: "Nombre",
                responsive: {visible: 'lg'},
                width: 100,
            },
            {
                field: "apellidos",
                title: "Apellidos",
                responsive: {visible: 'lg'},
                width: 100,
            },
            {
                field: "perfil",
                title: "Perfil",
                responsive: {visible: 'lg'},
                width: 120,
            },
            {
                field: "habilitado",
                title: "Estado",
                responsive: {visible: 'lg'},
                width: 60,
                // callback function support for column rendering
                template: function (row) {
                    var status = {
                        1: {'title': 'Activo', 'class': ' m-badge--success'},
                        0: {'title': 'Inactivo', 'class': ' m-badge--danger'}
                    };
                    return '<span class="m-badge ' + status[row.habilitado].class + ' m-badge--wide">' + status[row.habilitado].title + '</span>';
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
                        url: 'usuario/listarUsuario',
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
                mApp.unblock('#usuario-table-editable');
            })
            .on('m-datatable--on-ajax-fail', function (e, jqXHR) {
                mApp.unblock('#usuario-table-editable');
            })
            .on('m-datatable--on-goto-page', function (e, args) {
                MyApp.block('#usuario-table-editable');
            })
            .on('m-datatable--on-reloaded', function (e) {
                MyApp.block('#usuario-table-editable');
            })
            .on('m-datatable--on-sort', function (e, args) {
                MyApp.block('#usuario-table-editable');
            })
            .on('m-datatable--on-check', function (e, args) {
                //eventsWriter('Checkbox active: ' + args.toString());
            })
            .on('m-datatable--on-uncheck', function (e, args) {
                //eventsWriter('Checkbox inactive: ' + args.toString());
            });

        //Busqueda
        var query = oTable.getDataSourceQuery();
        $('#lista-usuario .m_form_search').on('keyup', function (e) {
            // shortcode to datatable.getDataSourceParam('query');
            var query = oTable.getDataSourceQuery();
            query.generalSearch = $(this).val().toLowerCase();
            // shortcode to datatable.setDataSourceParam('query', query);
            oTable.setDataSourceQuery(query);
            oTable.load();
        }).val(query.generalSearch);
    };
    //Reset forms
    var resetForms = function () {
        $('#usuario-form input').each(function (e) {
            $element = $(this);
            $element.val('');

            $element.data("title", "").removeClass("has-error").tooltip("dispose");
            $element.closest('.form-group').removeClass('has-error').addClass('success');
        });

        $('#perfil').val('');
        $('#perfil').trigger('change');

        $('#estadoactivo').prop('checked', true);

        var $element = $('.select2');
        $element.removeClass('has-error').tooltip("dispose");

        $element.closest('.form-group')
            .removeClass('has-error');

        event_change = false;

        $('.info').removeClass('m--hide').addClass('m--hide');
    };

    //Validacion y Inicializacion de ajax form
    jQuery.validator.addMethod("rut", function (value, element) {
        return this.optional(element) || $.Rut.validar(value);
    }, "Este campo debe ser un rut valido.");
    $("#rut").Rut({
        format_on: 'keyup',
        format: true,
        validation: true
    });
    var initNuevoForm = function () {
        $("#usuario-form").validate({
            rules: {
                perfil: {
                    required: true
                },
                repetirpassword: {
                    //required: true,
                    equalTo: '#password'
                },
                nombre: {
                    required: true
                },
                apellidos: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                rut: {
                    rut: true
                }
            },
            messages: {
                //password: "Este campo es obligatorio",
                repetirpassword: {
                    //required: "Este campo es obligatorio",
                    equalTo: "Escribe el mismo valor de nuevo"
                },
                nombre: "Este campo es obligatorio",
                apellidos: "Este campo es obligatorio",
                email: {
                    required: "Este campo es obligatorio",
                    email: "El Email debe ser válido"
                },
                perfil: "Este campo es obligatorio",
                rut: {
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

        $("#password").rules("add", {
            required: true,
            messages: {
                required: "Este campo es obligatorio"
            }
        });
        $("#repetirpassword").rules("add", {
            required: true,
            messages: {
                required: "Este campo es obligatorio"
            }
        });
    };
    var initEditarForm = function () {
        $("#password").rules("remove");
        $("#repetirpassword").rules("remove", "required");
    };

    //Nuevo
    var initAccionNuevo = function () {
        $(document).off('click', "#btn-nuevo-usuario");
        $(document).on('click', "#btn-nuevo-usuario", function (e) {
            btnClickNuevo();
        });

        function btnClickNuevo() {
            resetForms();
            var formTitle = "¿Deseas crear un nuevo usuario? Sigue los siguientes pasos:";
            $('#form-usuario-title').html(formTitle);
            $('#form-usuario').removeClass('m--hide');
            $('#lista-usuario').addClass('m--hide');
        };
    };
    //Salvar
    var initAccionSalvar = function () {
        $(document).off('click', "#btn-salvar-usuario");
        $(document).on('click', "#btn-salvar-usuario", function (e) {
            btnClickSalvarForm();
        });

        function btnClickSalvarForm() {
            mUtil.scrollTo();
            event_change = false;

            //Validacion
            initNuevoForm();

            var usuario_id = $('#usuario_id').val();
            if (usuario_id != "") {
                initEditarForm();
            }

            var rol_id = $('#perfil').val();

            if ($('#usuario-form').valid() && rol_id != "") {

                var nombre = $('#nombre').val();
                var apellidos = $('#apellidos').val();
                var email = $('#email').val();

                var password = $('#password').val();
                password = (password != "") ? hex_sha1(password) : "";

                var estado = ($('#estadoactivo').prop('checked')) ? 1 : 0;

                var rut = $('#rut').val();
                var calle = $('#calle').val();
                var numero = $('#numero').val();
                var telefono = $('#telefono').val();

                MyApp.block('#form-usuario');

                $.ajax({
                    type: "POST",
                    url: "usuario/salvarUsuario",
                    dataType: "json",
                    data: {
                        'usuario_id': usuario_id,
                        'rol': rol_id,
                        'habilitado': estado,
                        'passwordcodificada': password,
                        'nombre': nombre,
                        'apellidos': apellidos,
                        'email': email,
                        'rut': rut,
                        'calle': calle,
                        'numero': numero,
                        'telefono': telefono
                    },
                    success: function (response) {
                        mApp.unblock('#form-usuario');
                        if (response.success) {

                            toastr.success(response.message, "Exito !!!");
                            cerrarForms();
                            oTable.load();
                        } else {
                            toastr.error(response.error, "Error !!!");
                        }
                    },
                    failure: function (response) {
                        mApp.unblock('#form-usuario');

                        toastr.error(response.error, "Error !!!");
                    }
                });

            } else {
                if (rol_id == "") {
                    var $element = $('#select-perfil .select2');
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
        };
    }
    //Cerrar form
    var initAccionCerrar = function () {
        $(document).off('click', ".cerrar-form-usuario");
        $(document).on('click', ".cerrar-form-usuario", function (e) {
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
        $(document).off('click', "#usuario-table-editable a.edit");
        $(document).on('click', "#usuario-table-editable a.edit", function (e) {
            e.preventDefault();
            resetForms();


            var usuario_id = $(this).data('id');
            $('#usuario_id').val(usuario_id);

            $('#form-usuario').removeClass('m--hide');
            $('#lista-usuario').addClass('m--hide');

            editRow(usuario_id);
        });

        function editRow(usuario_id) {

            MyApp.block('#usuario-form');

            $.ajax({
                type: "POST",
                url: "usuario/cargarDatos",
                dataType: "json",
                data: {
                    'usuario_id': usuario_id
                },
                success: function (response) {
                    mApp.unblock('#usuario-form');
                    if (response.success) {
                        //Datos usuario    
                        var rol_id = response.usuario.rol;
                        $('#perfil').val(rol_id);
                        $('#perfil').trigger('change');

                        var formTitle = "¿Deseas actualizar el usuario \"" + response.usuario.nombre + "\" ? Sigue los siguientes pasos:";
                        $('#form-usuario-title').html(formTitle);

                        $('#nombre').val(response.usuario.nombre);
                        $('#apellidos').val(response.usuario.apellidos);
                        $('#email').val(response.usuario.email);

                        if (!response.usuario.habilitado) {
                            $('#estadoactivo').prop('checked', false);
                            $('#estadoinactivo').prop('checked', true);
                        }

                        if (rol_id == 2) {
                            $('.info').removeClass('m--hide');
                            $('#rut').val(response.usuario.rut);
                            $('#calle').val(response.usuario.calle);
                            $('#numero').val(response.usuario.numero);
                            $('#telefono').val(response.usuario.telefono);
                        }


                        event_change = false;

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#usuario-form');

                    toastr.error(response.error, "Error !!!");
                }
            });

        }
    };
    //Activar
    var initAccionActivar = function () {
        //Activar usuario
        $(document).off('click', "#usuario-table-editable a.block");
        $(document).on('click', "#usuario-table-editable a.block", function (e) {
            e.preventDefault();
            /* Get the row as a parent of the link that was clicked on */
            var usuario_id = $(this).data('id');
            cambiarEstadoUsuario(usuario_id);
        });

        function cambiarEstadoUsuario(usuario_id) {

            MyApp.block('#usuario-table-editable');

            $.ajax({
                type: "POST",
                url: "usuario/activarUsuario",
                dataType: "json",
                data: {
                    'usuario_id': usuario_id
                },
                success: function (response) {
                    mApp.unblock('#usuario-table-editable');

                    if (response.success) {
                        toastr.success("La operación se realizó correctamente", "Exito !!!");
                        oTable.load();

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#usuario-table-editable');
                    toastr.error(response.error, "Error !!!");
                }
            });
        }
    };
    //Eliminar
    var initAccionEliminar = function () {
        $(document).off('click', "#usuario-table-editable a.delete");
        $(document).on('click', "#usuario-table-editable a.delete", function (e) {
            e.preventDefault();

            rowDelete = $(this).data('id');
            $('#modal-eliminar').modal({
                'show': true
            });
        });

        $(document).off('click', "#btn-eliminar-usuario");
        $(document).on('click', "#btn-eliminar-usuario", function (e) {
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
            var usuario_id = rowDelete;

            MyApp.block('#usuario-table-editable');

            $.ajax({
                type: "POST",
                url: "usuario/eliminarUsuario",
                dataType: "json",
                data: {
                    'usuario_id': usuario_id
                },
                success: function (response) {
                    mApp.unblock('#usuario-table-editable');
                    if (response.success) {
                        oTable.load();

                        toastr.success(response.message, "Exito !!!");

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#usuario-table-editable');

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

            MyApp.block('#usuario-table-editable');

            $.ajax({
                type: "POST",
                url: "usuario/eliminarUsuarios",
                dataType: "json",
                data: {
                    'ids': ids
                },
                success: function (response) {
                    mApp.unblock('#usuario-table-editable');
                    if (response.success) {
                        oTable.load();

                        toastr.success(response.message, "Exito !!!");

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#usuario-table-editable');
                    toastr.error(response.error, "Error !!!");
                }
            });
        };
    };

    //Init select
    var initSelects = function () {
        $('#perfil').select2();

        $('#perfil').change(function () {
            var rol_id = $(this).val();
            if (rol_id == 2) {
                $('.info').removeClass('m--hide');
            } else {
                $('.info').removeClass('m--hide').addClass('m--hide');
            }
        });
    }

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
        $('#form-usuario').addClass('m--hide');
        $('#lista-usuario').removeClass('m--hide');
    }

    //initPortlets
    var initPortlets = function () {
        var portlet = new mPortlet('lista-usuario');
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
            initSelects();

            initAccionNuevo();
            initAccionSalvar();
            initAccionCerrar();

            initAccionEditar();
            initAccionActivar();
            initAccionEliminar();

            initAccionChange();

            initPortlets();
        }

    };

}();