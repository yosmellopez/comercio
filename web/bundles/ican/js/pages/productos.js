var Productos = function () {

    var oTable;
    var rowDelete = null;

    //Inicializa la tabla
    var initTable = function () {
        MyApp.block('#producto-table-editable');

        var table = $('#producto-table-editable');

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
                        url: 'producto/listarProducto',
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
                mApp.unblock('#producto-table-editable');
            })
            .on('m-datatable--on-ajax-fail', function (e, jqXHR) {
                mApp.unblock('#producto-table-editable');
            })
            .on('m-datatable--on-goto-page', function (e, args) {
                MyApp.block('#producto-table-editable');
            })
            .on('m-datatable--on-reloaded', function (e) {
                MyApp.block('#producto-table-editable');
            })
            .on('m-datatable--on-sort', function (e, args) {
                MyApp.block('#producto-table-editable');
            })
            .on('m-datatable--on-check', function (e, args) {
                //eventsWriter('Checkbox active: ' + args.toString());
            })
            .on('m-datatable--on-uncheck', function (e, args) {
                //eventsWriter('Checkbox inactive: ' + args.toString());
            });

        //Busqueda
        var query = oTable.getDataSourceQuery();
        $('#lista-producto .m_form_search').on('keyup', function (e) {

            var query = oTable.getDataSourceQuery();

            query.generalSearch = $(this).val().toLowerCase();

            var categoria_id = $('#filtro-categoria').val();
            query.categoria_id = categoria_id;

            var marca_id = $('#filtro-marca').val();
            query.marca_id = marca_id;

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

            var generalSearch = $('#lista-producto .m_form_search').val();
            query.generalSearch = generalSearch;

            var categoria_id = $('#filtro-categoria').val();
            query.categoria_id = categoria_id;

            var marca_id = $('#filtro-marca').val();
            query.marca_id = marca_id;

            oTable.setDataSourceQuery(query);
            oTable.load();
        }

    };

    //Reset forms
    var resetForms = function () {
        $('#producto-form input').each(function (e) {
            $element = $(this);
            $element.val('');

            $element.data("title", "").removeClass("has-error").tooltip("dispose");
            $element.closest('.form-group').removeClass('has-error').addClass('success');
        });
        $('#seo-form input, #seo-form textarea').each(function (e) {
            $element = $(this);
            $element.val('');

            $element.data("title", "").removeClass("has-error").tooltip("dispose");
            $element.closest('.form-group').removeClass('has-error').addClass('success');
        });

        var fecha_actual = new Date();
        $('#fecha').val(fecha_actual.format("d/m/Y H:i"));

        $('#descripcion').summernote('code', '');
        $('#caracteristica').summernote('code', '');
        $('#formaPago').summernote('code', '');
        $('#terminoDespacho').summernote('code', '');

        $('#categoria').val('');
        $('#categoria').trigger('change');

        $('#marca').val('');
        $('#marca').trigger('change');

        $('#porciento').val('');
        $('#porciento').trigger('change');

        $('#estadoactivo').prop('checked', true);
        $('#destacadoinactivo').prop('checked', true);
        $('#disponibilidadInmediatainactivo').prop('checked', true);

        $('#fileinput-archivo').fileinput('clear');

        //Dropzone
        if ($('#my-dropzone').hasClass('dz-started')) {
            $('#my-dropzone').removeClass('dz-started');
        }
        if ($('#my-dropzone-galeria').hasClass('dz-started')) {
            $('#my-dropzone-galeria').removeClass('dz-started');
        }
        $('.dz-preview').remove();
        realFiles = 0;
        realFilesGaleria = 0;

        //Limpiar tags
        $('#tags_tagsinput span').each(function (e) {
            $(this).remove();
        });

        var $element = $('.tagsinput');
        $element.tooltip("dispose");
        $element.removeClass('has-error');
        $element.closest('.form-group')
            .removeClass('has-error');

        var $element = $('#processing-dropzone');
        $element.tooltip("dispose")
            .removeClass('has-error')
            .closest('.form-group')
            .removeClass('has-error');

        var $element = $('.select2');
        $element.removeClass('has-error').tooltip("dispose");

        $element.closest('.form-group')
            .removeClass('has-error');

        event_change = false;

        //Reset relacionados
        relacionados = [];
        actualizarTableListaRelacionados();

        //Mostrar el primer tab
        resetWizard();
    };

    //Validacion
    var initForm = function () {
        $("#producto-form").validate({
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
        $("#seo-form").validate({
            rules: {
                titulo: {
                    required: true
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
                titulo: "Este campo es obligatorio",
                descripcion: {
                    required: "Este campo es obligatorio",
                    maxlength: "Por favor, no escribas más de {0} caracteres"
                },
                tags: "Este campo es obligatorio",
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

        //Ajax form
        var opciones_form = {
            beforeSubmit: function () {
                MyApp.block('#form-producto');
            }, //funcion que se ejecuta antes de enviar el form
            success: function (response) {
                mApp.unblock('#form-producto');
                if (response.success) {

                    toastr.success(response.message, "Exito !!!");
                    cerrarForms();
                    oTable.load();

                } else {
                    toastr.error(response.error, "Error !!!");
                }
            },
            dataType: 'json',
            resetForm: false
        };
        $('#producto-form').ajaxForm(opciones_form);
    };

    //Nuevo
    var initAccionNuevo = function () {
        $(document).off('click', "#btn-nuevo-producto");
        $(document).on('click', "#btn-nuevo-producto", function (e) {
            btnClickNuevo();
        });

        function btnClickNuevo() {
            resetForms();
            var formTitle = "¿Deseas crear una nueva producto? Sigue los siguientes pasos:";
            $('#form-producto-title').html(formTitle);
            $('#form-producto').removeClass('m--hide');
            $('#lista-producto').addClass('m--hide');
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

            var imagen = "";
            $('#my-dropzone .dz-preview').each(function (e) {
                imagen = $(this).attr('data-value-imagen');
            });

            var tags = $('#tags').val();

            var descripcion = $('#descripcion').summernote('code');
            $('#descripcionhidden').val(descripcion);

            var caracteristica = $('#caracteristica').summernote('code');
            $('#caracteristicahidden').val(caracteristica);

            var formaPago = $('#formaPago').summernote('code');
            $('#formaPagohidden').val(formaPago);

            var terminoDespacho = $('#terminoDespacho').summernote('code');
            $('#terminoDespachohidden').val(terminoDespacho);

            var categoria_id = $('#categoria').val();
            var marca_id = $('#marca').val();

            if ($('#producto-form').valid() && $('#seo-form').valid() && descripcion !== ""
                && imagen != "" && tags != "" && categoria_id != "" && marca_id != "") {

                $('#titulohidden').val($('#titulo').val());
                $('#tagshidden').val(tags);

                var estado = ($('#estadoactivo').prop('checked')) ? 1 : 0;
                $('#estado').val(estado);

                var destacado = ($('#destacadoactivo').prop('checked')) ? 1 : 0;
                $('#destacado').val(destacado);

                var disponibilidadInmediata = ($('#disponibilidadInmediataactivo').prop('checked')) ? 1 : 0;
                $('#disponibilidadInmediata').val(disponibilidadInmediata);

                var precio = $('#precio').val();
                $('#preciohidden').val(precio);
                $('#precioespecialhidden').val($('#precioEspecial').val());

                var imagenes = "";
                $('#my-dropzone-galeria .dz-preview').each(function (e) {
                    imagenes += $(this).attr('data-value-imagen') + ',';
                });
                $('#imagenes').val(imagenes);
                $('#imagen').val(imagen);

                $('#productos').val(JSON.stringify(relacionados));

                if ($('#fileinput-archivo').hasClass('fileinput-new')) {
                    $('#deleteficha').val(1);
                } else {
                    $('#deleteficha').val(0);
                }


                $('#producto-form').submit();

            } else {
                if (imagen == "") {
                    var $element = $('#processing-dropzone');
                    $element.tooltip("dispose") // Destroy any pre-existing tooltip so we can repopulate with new tooltip content
                        .data("title", "Es obligatorio seleccionar una imagen")
                        .addClass("has-error")
                        .tooltip({
                            placement: 'top'
                        }); // Create a new tooltip based on the error messsage we just set in the title

                    $element.closest('.form-group')
                        .removeClass('has-success').addClass('has-error');
                }
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
                if (categoria_id == "") {

                    var $element = $('#select-categoria .select2');
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
                if (marca_id == "") {

                    var $element = $('#select-marca .select2');
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
                if (descripcion == "") {
                    var $element = $('.note-editor');
                    $element.tooltip("dispose") // Destroy any pre-existing tooltip so we can repopulate with new tooltip content
                        .data("title", "Este campo es obligatorio")
                        .addClass("has-error")
                        .tooltip({
                            placement: 'top'
                        }); // Create a new tooltip based on the error messsage we just set in the title

                    $element.closest('.form-group')
                        .removeClass('has-success').addClass('has-error');
                }
            }
        };
    }
    //Cerrar form
    var initAccionCerrar = function () {
        $(document).off('click', ".cerrar-form-producto");
        $(document).on('click', ".cerrar-form-producto", function (e) {
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
        $(document).off('click', "#producto-table-editable a.edit");
        $(document).on('click', "#producto-table-editable a.edit", function (e) {
            e.preventDefault();
            resetForms();


            var producto_id = $(this).data('id');
            $('#producto_id').val(producto_id);

            $('#form-producto').removeClass('m--hide');
            $('#lista-producto').addClass('m--hide');

            editRow(producto_id);
        });

        function editRow(producto_id) {

            MyApp.block('#producto-form');

            $.ajax({
                type: "POST",
                url: "producto/cargarDatos",
                dataType: "json",
                data: {
                    'producto_id': producto_id
                },
                success: function (response) {
                    mApp.unblock('#producto-form');
                    if (response.success) {
                        //Datos producto    

                        var formTitle = "¿Deseas actualizar la producto \"" + response.producto.nombre + "\" ? Sigue los siguientes pasos:";
                        $('#form-producto-title').html(formTitle);

                        $('#nombre').val(response.producto.nombre);
                        $('#titulo').val(response.producto.titulo);
                        $('#descripcion').summernote('code', response.producto.descripcion);
                        $('#caracteristica').summernote('code', response.producto.caracteristica);
                        $('#formaPago').summernote('code', response.producto.formaPago);
                        $('#terminoDespacho').summernote('code', response.producto.terminoDespacho);

                        $('#stock').val(response.producto.stock);
                        $('#precio').val(response.producto.precio);
                        $('#precioEspecial').val(response.producto.precioEspecial);

                        $('#fecha').val(response.producto.fecha);

                        if (!response.producto.estado) {
                            $('#estadoactivo').prop('checked', false);
                            $('#estadoinactivo').prop('checked', true);
                        }
                        if (response.producto.destacado) {
                            $('#destacadoactivo').prop('checked', true);
                            $('#destacadoinactivo').prop('checked', false);
                        }
                        if (response.producto.disponibilidadInmediata) {
                            $('#disponibilidadInmediataactivo').prop('checked', true);
                            $('#disponibilidadInmediatainactivo').prop('checked', false);
                        }

                        var tags = response.producto.tags;
                        if (tags != "" && tags != null) {
                            tags = tags.split(',');
                            for (var i = 0; i < tags.length; i++)
                                $('#tags').addTag(tags[i]);
                        }

                        $('#categoria').val(response.producto.categoria);
                        $('#categoria').trigger('change');

                        $('#marca').val(response.producto.marca);
                        $('#marca').trigger('change');

                        $('#porciento').val(response.producto.porciento);
                        $('#porciento').trigger('change');

                        mostrarImagen(response.producto.imagen[0], response.producto.imagen[1], response.producto.imagen[2]);

                        var images = response.producto.imagenes;
                        for (var i = 0; i < images.length; i++) {
                            mostrarImagenGaleria(images[i].imagen[0], images[i].imagen[1], images[i].imagen[2]);
                        }

                        if (response.producto.ficha != "" && response.producto.ficha != null) {
                            $('#fileinput-archivo .fileinput-filename').html(response.producto.ficha);
                            $('#fileinput-archivo').fileinput().removeClass('fileinput-new').addClass('fileinput-exists');
                        }

                        relacionados = response.producto.relacionados;
                        actualizarTableListaRelacionados();

                        event_change = false;

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#producto-form');

                    toastr.error(response.error, "Error !!!");
                }
            });

        }
    };
    //Eliminar
    var initAccionEliminar = function () {
        $(document).off('click', "#producto-table-editable a.delete");
        $(document).on('click', "#producto-table-editable a.delete", function (e) {
            e.preventDefault();

            rowDelete = $(this).data('id');
            $('#modal-eliminar').modal({
                'show': true
            });
        });

        $(document).off('click', "#btn-eliminar-producto");
        $(document).on('click', "#btn-eliminar-producto", function (e) {
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
            var producto_id = rowDelete;

            MyApp.block('#producto-table-editable');

            $.ajax({
                type: "POST",
                url: "producto/eliminarProducto",
                dataType: "json",
                data: {
                    'producto_id': producto_id
                },
                success: function (response) {
                    mApp.unblock('#producto-table-editable');
                    if (response.success) {
                        oTable.load();

                        toastr.success(response.message, "Exito !!!");

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#producto-table-editable');

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

            MyApp.block('#producto-table-editable');

            $.ajax({
                type: "POST",
                url: "producto/eliminarProductos",
                dataType: "json",
                data: {
                    'ids': ids
                },
                success: function (response) {
                    mApp.unblock('#producto-table-editable');
                    if (response.success) {
                        oTable.load();

                        toastr.success(response.message, "Exito !!!");

                    } else {
                        toastr.error(response.error, "Error !!!");
                    }
                },
                failure: function (response) {
                    mApp.unblock('#producto-table-editable');
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
        $('#form-producto').addClass('m--hide');
        $('#lista-producto').removeClass('m--hide');
    };

    //Init dropzone
    var myDropZone = null;
    var realFiles = 0;
    var maxFiles = 1;
    var initDropZone = function () {
        // Prevent Dropzone from auto discovering this element:
        Dropzone.options.myDropzone = false;
        Dropzone.autoDiscover = false;

        myDropZone = new Dropzone("div#my-dropzone", {
            thumbnailWidth: 1131,
            thumbnailHeight: 780,
            maxFilesize: 10, // MB
            paramName: "foto", // The name that will be used to transfer the file
            dictCancelUploadConfirmation: "¿Estas seguro que quieres cancelar la subida del archivo?",
            dictRemoveFileConfirmation: "¿Estas seguro que quieres eliminar el archivo?",
            dictDefaultMessage: "Arrastra tu imagen o haz click aquí",
            addRemoveLinks: true,
            dictRemoveFile: "Eliminar",
            dictCancelUpload: "Cancelar",
            url: "producto/salvarImagen",
            accept: function (file, done) {
                realFiles++;
                if (!file.type.match(/image.*/)) {
                    toastr.error('Por favor, verifique que el archivo seleccionado sea una imagen', "Error !!!");
                    //Eliminar
                    eliminarImagen(file);

                } else {
                    if (maxFiles < realFiles) {
                        toastr.error('Solo se permite una imagen principal por producto', "Error !!!");
                        //Eliminar
                        eliminarImagen(file);

                    }
                    else {
                        done();
                    }
                }
            },
            success: function (file, response) {

                if (file.width != '591' || file.height != '447') {
                    toastr.error('Las dimensiones de la imagen deben ser 591 x 447', "Error !!!");
                    myDropZone.removeFile(file);
                } else {

                    $(file.previewElement).attr('data-value-imagen', response.name);
                    if (file.previewElement) {
                        return file.previewElement.classList.add("dz-success");
                    }

                    event_change = true;
                }

            },
            removedfile: function (file) {
                var imagen = $(file.previewElement).attr('data-value-imagen');

                MyApp.block('#processing-dropzone');

                $.ajax({
                    type: "POST",
                    url: "producto/eliminarImagen",
                    dataType: "json",
                    data: {
                        'imagen': imagen
                    },
                    success: function (response) {
                        mApp.unblock('#processing-dropzone');
                        if (response.success) {
                            eliminarImagen(file);
                            return;

                        } else {
                            toastr.error(response.error, "Error !!!");
                            return;
                        }
                    },
                    failure: function (response) {
                        mApp.unblock('#processing-dropzone');

                        toastr.error(response.error, "Error !!!");
                        return;
                    }
                });
            },
            thumbnail: function (file, dataUrl) {
                var thumbnailElement, _i, _len, _ref, _results;
                if (file.previewElement) {
                    file.previewElement.classList.remove("dz-file-preview");
                    file.previewElement.classList.add("dz-image-preview");
                    _ref = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
                    _results = [];
                    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                        thumbnailElement = _ref[_i];
                        thumbnailElement.alt = file.name;
                        _results.push(thumbnailElement.src = dataUrl);
                    }
                    $(file.previewElement).attr('data-value-imagen', file.name);
                    return _results;
                }
            },
        });
        Dropzone.confirm = function (question, accepted, rejected) {
            // Ask the question, and call accepted() or rejected() accordingly.
            // CAREFUL: rejected might not be defined. Do nothing in that case.
            $('#confirm-question').html(question);

            $('#modal-confirm').modal({
                show: true
            });


            $(document).off('click', "#btn-confirm-aceptar");
            $(document).on('click', '#btn-confirm-aceptar', function () {
                accepted();
            });
            $(document).off('click', "#btn-confirm-cancelar");
            $(document).on('click', '#btn-confirm-cancelar', function () {
                //rejected();
            });
        }

        function eliminarImagen(file) {
            var _ref;
            if (file.previewElement) {
                if ((_ref = file.previewElement) != null) {
                    _ref.parentNode.removeChild(file.previewElement);
                }
            }
            realFiles--;
            event_change = true;
            if (realFiles < 1) {
                if ($('#my-dropzone').hasClass('dz-started'))
                    $('#my-dropzone').removeClass('dz-started');
            }

        }
    };
    var mostrarImagen = function (imagen, size, src) {
        if (imagen != "" && imagen != null) {
            var mockFile = {name: imagen, size: size};

            myDropZone.emit("addedfile", mockFile);
            myDropZone.emit("thumbnail", mockFile, src + imagen);
            myDropZone.emit("complete", mockFile);

            realFiles++;
            $('my-dropzone .dz-preview').addClass('dz-success');
        }
    };

    var myDropZoneGaleria = null;
    var realFilesGaleria = 0;
    var initDropZoneGaleria = function () {
        // Prevent Dropzone from auto discovering this element:
        Dropzone.options.myDropzoneGaleria = false;
        Dropzone.autoDiscover = false;

        myDropZoneGaleria = new Dropzone("div#my-dropzone-galeria", {
            thumbnailWidth: 1131,
            thumbnailHeight: 780,
            parallelUploads: 1,
            maxFilesize: 10, // MB
            paramName: "foto", // The name that will be used to transfer the file
            dictCancelUploadConfirmation: "¿Estas seguro que quieres cancelar la subida del archivo?",
            dictRemoveFileConfirmation: "¿Estas seguro que quieres eliminar el archivo?",
            dictDefaultMessage: "Arrastra tu imagen o haz click aquí",
            addRemoveLinks: true,
            dictRemoveFile: "Eliminar",
            dictCancelUpload: "Cancelar",
            url: "producto/salvarImagen",
            accept: function (file, done) {
                realFilesGaleria++;
                if (!file.type.match(/image.*/)) {
                    toastr.error('Por favor, verifique que el archivo seleccionado sea una imagen', "Error !!!");
                    //Eliminar
                    eliminarImagen(file);

                } else {
                    done();
                }
            },
            success: function (file, response) {

                if (file.width != '591' || file.height != '447') {
                    toastr.error('Las dimensiones de la imagen deben ser 591 x 447', "Error !!!");
                    myDropZone.removeFile(file);
                } else {

                    $(file.previewElement).attr('data-value-imagen', response.name);
                    if (file.previewElement) {
                        return file.previewElement.classList.add("dz-success");
                    }

                    event_change = true;
                }

            },
            removedfile: function (file) {
                var imagen = $(file.previewElement).attr('data-value-imagen');

                MyApp.block('#processing-dropzone-galeria');

                $.ajax({
                    type: "POST",
                    url: "producto/eliminarImagen",
                    dataType: "json",
                    data: {
                        'imagen': imagen
                    },
                    success: function (response) {
                        mApp.unblock('#processing-dropzone-galeria');
                        if (response.success) {
                            eliminarImagen(file);
                            return;

                        } else {
                            toastr.error(response.error, "Error !!!");
                            return;
                        }
                    },
                    failure: function (response) {
                        mApp.unblock('#processing-dropzone-galeria');

                        toastr.error(response.error, "Error !!!");
                        return;
                    }
                });
            },
            thumbnail: function (file, dataUrl) {
                var thumbnailElement, _i, _len, _ref, _results;
                if (file.previewElement) {
                    file.previewElement.classList.remove("dz-file-preview");
                    file.previewElement.classList.add("dz-image-preview");
                    _ref = file.previewElement.querySelectorAll("[data-dz-thumbnail]");
                    _results = [];
                    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                        thumbnailElement = _ref[_i];
                        thumbnailElement.alt = file.name;
                        _results.push(thumbnailElement.src = dataUrl);
                    }
                    $(file.previewElement).attr('data-value-imagen', file.name);
                    return _results;
                }
            },
        });
        Dropzone.confirm = function (question, accepted, rejected) {
            // Ask the question, and call accepted() or rejected() accordingly.
            // CAREFUL: rejected might not be defined. Do nothing in that case.
            $('#confirm-question').html(question);

            $('#modal-confirm').modal({
                show: true
            });


            $(document).off('click', "#btn-confirm-aceptar");
            $(document).on('click', '#btn-confirm-aceptar', function () {
                accepted();
            });
            $(document).off('click', "#btn-confirm-cancelar");
            $(document).on('click', '#btn-confirm-cancelar', function () {
                //rejected();
            });
        }

        function eliminarImagen(file) {
            var _ref;
            if (file.previewElement) {
                if ((_ref = file.previewElement) != null) {
                    _ref.parentNode.removeChild(file.previewElement);
                }
            }
            realFilesGaleria--;
            event_change = true;
            if (realFilesGaleria < 1) {
                if ($('#my-dropzone-galeria').hasClass('dz-started'))
                    $('#my-dropzone-galeria').removeClass('dz-started');
            }

        }
    };
    var mostrarImagenGaleria = function (imagen, size, src) {
        if (imagen != "" && imagen != null) {
            var mockFile = {name: imagen, size: size};

            myDropZoneGaleria.emit("addedfile", mockFile);
            myDropZoneGaleria.emit("thumbnail", mockFile, src + imagen);
            myDropZoneGaleria.emit("complete", mockFile);

            realFilesGaleria++;
            $('#my-dropzone-galeria .dz-preview').addClass('dz-success');
        }
    };

    //initPortlets
    var initPortlets = function () {
        var portlet = new mPortlet('lista-producto');
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

                //bug visual de la tabla que muestra las cols corridas
                actualizarTableListaRelacionados();
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
                    $('#tab-seo').tab('show');
                    break;
                case 3:
                    $('#tab-relacionados').tab('show');
                    //bug visual de la tabla que muestra las cols corridas
                    actualizarTableListaRelacionados();
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
    var validWizard = function () {
        var result = true;
        if (activeTab == 1) {

            var imagen = "";
            $('#my-dropzone .dz-preview').each(function (e) {
                imagen = $(this).attr('data-value-imagen');
            });

            if (!$('#producto-form').valid() || imagen == "") {
                result = false;

                if (imagen == "") {
                    var $element = $('#processing-dropzone');
                    $element.tooltip("dispose") // Destroy any pre-existing tooltip so we can repopulate with new tooltip content
                        .data("title", "Es obligatorio seleccionar una imagen")
                        .addClass("has-error")
                        .tooltip({
                            placement: 'top'
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
        $('#categoria').select2({
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
        $('#filtro-categoria-relacionado').select2({
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

        $('#marca').select2();
        $('#filtro-marca').select2();
        $('#filtro-marca-relacionado').select2();

        $('#porciento').select2();

    };

    var initChanges = function () {
        $('#porciento').change(function () {
            var porciento = $(this).val();
            if (porciento != "") {
                calcularPrecioEspecial();
            } else {
                $('#precioEspecial').val('');
            }
        });

        $(document).off('keyup', "#precio");
        $(document).on('keyup', "#precio", calcularPrecioEspecial);

        function calcularPrecioEspecial() {
            var precio = parseInt($('#precio').val());
            var porciento = parseInt($('#porciento').val());

            if (precio > 0 && porciento > 0) {
                var precio_especial = precio / 100 * (100 - porciento);
                precio_especial = Math.ceil(precio_especial);

                $('#precioEspecial').val(precio_especial);
            } else {
                $('#precioEspecial').val('');
            }
        }
    }


    //Relacionados
    var oTableRelacionados;
    var relacionados = [];

    var initTableRelacionados = function () {
        MyApp.block('#relacionados-table-editable');

        var table = $('#relacionados-table-editable');

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
        oTableRelacionados = table.mDatatable({
            // datasource definition
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: 'producto/listarParaRelacionados',
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
        oTableRelacionados
            .on('m-datatable--on-ajax-done', function () {
                mApp.unblock('#relacionados-table-editable');
            })
            .on('m-datatable--on-ajax-fail', function (e, jqXHR) {
                mApp.unblock('#relacionados-table-editable');
            })
            .on('m-datatable--on-goto-page', function (e, args) {
                MyApp.block('#relacionados-table-editable');
            })
            .on('m-datatable--on-reloaded', function (e) {
                MyApp.block('#relacionados-table-editable');
            })
            .on('m-datatable--on-sort', function (e, args) {
                MyApp.block('#relacionados-table-editable');
            })
            .on('m-datatable--on-check', function (e, args) {
                //eventsWriter('Checkbox active: ' + args.toString());
            })
            .on('m-datatable--on-uncheck', function (e, args) {
                //eventsWriter('Checkbox inactive: ' + args.toString());
            });

        //Busqueda
        var query = oTableRelacionados.getDataSourceQuery();
        $('#lista-relacionado .m_form_search').on('keyup', function (e) {
            // shortcode to datatable.getDataSourceParam('query');
            var query = oTableRelacionados.getDataSourceQuery();
            query.generalSearch = $(this).val().toLowerCase();

            var categoria_id = $('#filtro-categoria-relacionado').val();
            query.categoria_id = categoria_id;

            var marca_id = $('#filtro-marca-relacionado').val();
            query.marca_id = marca_id;

            var productos_id = devolverRelacionadosId();
            query.productos_id = productos_id;

            // shortcode to datatable.setDataSourceParam('query', query);
            oTableRelacionados.setDataSourceQuery(query);
            oTableRelacionados.load();
        }).val(query.generalSearch);

    };
    var btnClickFiltrarRelacionados = function () {

        var generalSearch = $('#lista-relacionado .m_form_search').val();

        var query = oTableRelacionados.getDataSourceQuery();

        query.generalSearch = generalSearch;

        var categoria_id = $('#filtro-categoria-relacionado').val();
        query.categoria_id = categoria_id;

        var marca_id = $('#filtro-marca-relacionado').val();
        query.marca_id = marca_id;

        var productos_id = devolverRelacionadosId();
        query.productos_id = productos_id;

        oTableRelacionados.setDataSourceQuery(query);
        oTableRelacionados.load();

    };
    var devolverRelacionadosId = function () {
        var lista = [];
        for (var i = 0; i < relacionados.length; i++) {
            lista.push(relacionados[i].producto_id);
        }

        //agregar el producto
        var producto_id = $('#producto_id').val();
        if (producto_id != "") {
            lista.push(producto_id);
        }

        return lista;
    }

    var ExisteRelacionado = function (id) {
        var result = false;
        for (var i = 0; i < relacionados.length; i++) {
            if (relacionados[i].producto_id == id) {
                result = true;
                break;
            }
        }
        return result;
    };
    var initAccionesRelacionados = function () {

        $(document).off('click', "#btn-agregar-relacionados");
        $(document).on('click', "#btn-agregar-relacionados", function (e) {
            $('#modal-relacionados').modal({
                'show': true
            });
            btnClickFiltrarRelacionados();
        });
        $(document).off('click', "#btn-filtrar-relacionados");
        $(document).on('click', "#btn-filtrar-relacionados", function (e) {
            btnClickFiltrarRelacionados();
        });

        $(document).off('click', "#relacionados-table-editable a.add");
        $(document).on('click', "#relacionados-table-editable a.add", function (e) {
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

            var posicion = relacionados.length;

            var existeRelacionado = ExisteRelacionado(producto_id);
            if (!existeRelacionado) {

                relacionados.push({
                    productorelacion_id: '',
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
                actualizarTableListaRelacionados();

                btnClickFiltrarRelacionados();

                event_change = true;

            } else {
                toastr.error("Ya se ha agregado el producto seleccionado", "Error !!!");
            }
        });

        $(document).off('click', "#lista-relacionados-table-editable a.delete");
        $(document).on('click', "#lista-relacionados-table-editable a.delete", function (e) {

            e.preventDefault();
            var posicion = $(this).data('posicion');

            if (relacionados[posicion]) {

                var productorelacion_id = relacionados[posicion].productorelacion_id;

                if (productorelacion_id !== '') {

                    MyApp.block('#lista-relacionados-table-editable');

                    $.ajax({
                        type: "POST",
                        url: "producto/eliminarRelacionado",
                        dataType: "json",
                        data: {
                            'productorelacion_id': productorelacion_id
                        },
                        success: function (response) {
                            mApp.unblock('#lista-relacionados-table-editable');
                            if (response.success) {

                                toastr.success(response.message, "Exito !!!");
                                deleteRelacionado(posicion);
                            } else {
                                toastr.error(response.error, "Error !!!");
                            }
                        },
                        failure: function (response) {
                            mApp.unblock('#lista-relacionados-table-editable');
                            toastr.error(response.error, "Error !!!");
                        }
                    });
                } else {
                    deleteRelacionado(posicion);
                }
            }
        });

        function deleteRelacionado(posicion) {
            //Eliminar
            relacionados.splice(posicion, 1);
            //actualizar posiciones
            for (var i = 0; i < relacionados.length; i++) {
                relacionados[i].posicion = i;
            }
            //actualizar lista
            actualizarTableListaRelacionados();
        }
    };

    var oTableListaRelacionados;
    var initTableListaRelacionados = function () {
        MyApp.block('#lista-relacionados-table-editable');

        var table = $('#lista-relacionados-table-editable');

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
        oTableListaRelacionados = table.mDatatable({
            // datasource definition
            data: {
                type: 'local',
                source: relacionados,
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
        oTableListaRelacionados
            .on('m-datatable--on-ajax-done', function () {
                mApp.unblock('#lista-relacionados-table-editable');
            })
            .on('m-datatable--on-ajax-fail', function (e, jqXHR) {
                mApp.unblock('#lista-relacionados-table-editable');
            })
            .on('m-datatable--on-goto-page', function (e, args) {
                MyApp.block('#lista-relacionados-table-editable');
            })
            .on('m-datatable--on-reloaded', function (e) {
                MyApp.block('#lista-relacionados-table-editable');
            })
            .on('m-datatable--on-sort', function (e, args) {
                MyApp.block('#lista-relacionados-table-editable');
            })
            .on('m-datatable--on-check', function (e, args) {
                //eventsWriter('Checkbox active: ' + args.toString());
            })
            .on('m-datatable--on-uncheck', function (e, args) {
                //eventsWriter('Checkbox inactive: ' + args.toString());
            });

        //Busqueda
        var query = oTableListaRelacionados.getDataSourceQuery();
        $('#lista-relacionados .m_form_search').on('keyup', function (e) {
            // shortcode to datatable.getDataSourceParam('query');
            var query = oTableListaRelacionados.getDataSourceQuery();
            query.generalSearch = $(this).val().toLowerCase();

            // shortcode to datatable.setDataSourceParam('query', query);
            oTableListaRelacionados.setDataSourceQuery(query);

            oTableListaRelacionados.search(query.generalSearch);

        }).val(query.generalSearch);

    };
    var actualizarTableListaRelacionados = function () {
        oTableListaRelacionados.destroy();
        initTableListaRelacionados();
    }

    return {
        //main function to initiate the module
        init: function () {

            initTable();
            initForm();

            initSelects();
            initDropZone();
            initDropZoneGaleria();
            initTagsInput();

            initChanges();

            initAccionFiltrar();

            initAccionNuevo();
            initAccionSalvar();
            initAccionCerrar();

            initAccionEditar();
            initAccionEliminar();

            initAccionChange();

            initPortlets();

            initWizard();

            //Relacionados
            initTableRelacionados();
            initTableListaRelacionados();
            initAccionesRelacionados();
        }

    };

}();