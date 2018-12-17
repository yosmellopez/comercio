/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var MyApp = function () {

    var initDatePickers = function () {
        if (!jQuery().datepicker) {
            return;
        }

        $.fn.datepicker.dates.es = {
            days: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"],
            daysShort: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb", "Dom"],
            daysMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa", "Do"],
            months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
            monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
            today: "Hoy",
            clear: "Borrar",
            weekStart: 1,
            format: "dd/mm/yyyy"
        }

        if (!jQuery().datetimepicker) {
            return;
        }

        $.fn.datetimepicker.dates['es'] = {
            days: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo"],
            daysShort: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb", "Dom"],
            daysMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa", "Do"],
            months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
            monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
            today: "Hoy",
            suffix: [],
            meridiem: []
        };

        // input group layout
        $('.date-picker').datepicker({
            format: 'dd/mm/yyyy',
            language: 'es',
            autoclose: true,
            todayHighlight: true,
            orientation: "bottom left",
            templates: {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            }
        });

        $('.date-time-picker').datetimepicker({
            format: 'dd/mm/yyyy hh:ii',
            language: 'es',
            todayHighlight: true,
            autoclose: true,
            pickerPosition: 'bottom-left',
        });
    }

    var initInputMasks = function () {
        $('.input-mask-date').inputmask("dd/mm/yyyy", {
            "placeholder": "dd/mm/yyyy",
            autoUnmask: true
        });
    }

    //Sumar días a una fecha
    var sumarDiasAFecha = function (fecha, days) {

        fechaVencimiento = "";
        if (fecha != "") {
            var fecha_registro = fecha;
            var fecha_registro_array = fecha_registro.split("/");
            var year = fecha_registro_array[2];
            var mouth = fecha_registro_array[1] - 1;
            var day = fecha_registro_array[0];

            var fechaVencimiento = new Date(year, mouth, day);

            //Obtenemos los milisegundos desde media noche del 1/1/1970
            var tiempo = fechaVencimiento.getTime();
            //Calculamos los milisegundos sobre la fecha que hay que sumar o restar...
            var milisegundos = parseInt(days * 24 * 60 * 60 * 1000);
            //Modificamos la fecha actual
            fechaVencimiento.setTime(tiempo + milisegundos);
        }


        return fechaVencimiento;
    };
    //Restar días a una fecha
    var restarDiasAFecha = function (fecha, days) {

        fechaVencimiento = "";
        if (fecha != "") {
            var fecha_registro = fecha;
            var fecha_registro_array = fecha_registro.split("/");
            var year = fecha_registro_array[2];
            var mouth = fecha_registro_array[1] - 1;
            var day = fecha_registro_array[0];

            var fechaVencimiento = new Date(year, mouth, day);

            //Obtenemos los milisegundos desde media noche del 1/1/1970
            var tiempo = fechaVencimiento.getTime();
            //Calculamos los milisegundos sobre la fecha que hay que sumar o restar...
            var milisegundos = parseInt(days * 24 * 60 * 60 * 1000);
            //Modificamos la fecha actual
            fechaVencimiento.setTime(tiempo - milisegundos);
        }


        return fechaVencimiento;
    };
    //Sumar meses a una fecha
    var sumarMesesAFecha = function (fecha, meses) {
        fechaVencimiento = "";
        if (fecha != "") {
            var fecha_registro = fecha;
            var fecha_registro_array = fecha_registro.split("/");
            var year = fecha_registro_array[2];
            var mouth = fecha_registro_array[1] - 1;
            var day = fecha_registro_array[0];

            var fechaVencimiento = new Date(year, mouth, day);

            var mouths = parseInt(mouth) + parseInt(meses);
            fechaVencimiento.setMonth(mouths);
        }

        return fechaVencimiento;
    };

    var toastrConfig = function () {
        toastr.options.timeOut = 4000;
        toastr.options.positionClass = 'toast-top-center';
    }
    var showAlert = function (msg) {
        toastr.error(msg, "Error !!!");
    };
    var showMessage = function (msg) {
        toastr.success(msg, "Exito !!!");
    };

    var block = function (target) {
        mApp.block(target,
            {
                overlayColor: '#000000',
                state: 'success',
                type: 'loader',
                //message: 'Por favor espere...'
            }
        );
    }

    var handlerNewValidateType = function () {
        jQuery.validator.addMethod("rut", function (value, element) {
            return this.optional(element) || $.Rut.validar(value);
        }, "Este campo debe ser un rut valido.");

        jQuery.validator.addMethod("date60",
            function (value, element) {
                //La fecha inicial no puede ser anterior a 60 dias
                var result = false;
                if (value == "") {
                    result = true;
                    return result;
                }

                var value_array = value.split('/');
                var value_dia = value_array[0];
                var value_mes = value_array[1];
                var value_year = value_array[2];

                var value_fecha = new Date();
                value_fecha.setDate(value_dia);
                value_fecha.setMonth(parseInt(value_mes) - 1);
                value_fecha.setYear(value_year);

                value = value_fecha.format('Y/m/d');

                //Anterior 60 dias
                var fecha_actual_menos_60 = new Date();
                var mouths_menos_60 = fecha_actual_menos_60.getMonth() - parseInt(2);
                fecha_actual_menos_60.setMonth(mouths_menos_60);
                fecha_actual_menos_60 = fecha_actual_menos_60.format('Y/m/d');
                //Posterior 60 dias
                var fecha_actual_mas_60 = new Date();
                var mouths_mas_60 = fecha_actual_mas_60.getMonth() + parseInt(2);
                fecha_actual_mas_60.setMonth(mouths_mas_60);
                fecha_actual_mas_60 = fecha_actual_mas_60.format('Y/m/d');

                if ((value >= fecha_actual_menos_60) && (value <= fecha_actual_mas_60)) {
                    result = true;
                }

                return result;
            },
            "La fecha inicial no puede ser anterior ni posterior a 60 días"
        );

        $(document).on('keypress', ".just-number", function (e) {
            var keynum = window.event ? window.event.keyCode : e.which;

            if ((keynum == 8) || (keynum == 0))
                return true;

            return /\d/.test(String.fromCharCode(keynum));
        });

        $(document).on('keypress', ".just-letters", function (e) {
            var keynum = window.event ? window.event.keyCode : e.which;

            if ((keynum == 8) || (keynum == 0))
                return true;

            return /^[a-zA-ZñÑáúíóéÁÚÍÓÉ\s]*$/.test(String.fromCharCode(keynum));
        });

        $(document).on('keypress', ".just-rut", function (e) {
            var keynum = window.event ? window.event.keyCode : e.which;

            if ((keynum == 8) || (keynum == 0))
                return true;

            return /^[0-9k\-]*$/.test(String.fromCharCode(keynum));
        });

    };

    var handlerSummernote = function () {
        if (!jQuery().summernote) {
            return;
        }
        $.extend($.summernote.lang, {
            'es-ES': {
                font: {
                    name: 'Fuente',
                    bold: 'Negrita',
                    italic: 'Cursiva',
                    underline: 'Subrayado',
                    superscript: 'Superíndice',
                    subscript: 'Subíndice',
                    strikethrough: 'Tachado',
                    clear: 'Quitar estilo de fuente',
                    height: 'Altura de línea',
                    size: 'Tamaño de la fuente'
                },
                image: {
                    image: 'Imagen',
                    insert: 'Insertar imagen',
                    resizeFull: 'Redimensionar a tamaño completo',
                    resizeHalf: 'Redimensionar a la mitad',
                    resizeQuarter: 'Redimensionar a un cuarto',
                    floatLeft: 'Flotar a la izquierda',
                    floatRight: 'Flotar a la derecha',
                    floatNone: 'No flotar',
                    dragImageHere: 'Arrastrar una imagen aquí',
                    selectFromFiles: 'Seleccionar desde los archivos',
                    url: 'URL de la imagen'
                },
                link: {
                    link: 'Link',
                    insert: 'Insertar link',
                    unlink: 'Quitar link',
                    edit: 'Editar',
                    textToDisplay: 'Texto para mostrar',
                    url: '¿Hacia que URL lleva el link?',
                    openInNewWindow: 'Abrir en una nueva ventana'
                },
                video: {
                    video: 'Video',
                    videoLink: 'Link del video',
                    insert: 'Insertar video',
                    url: '¿URL del video?',
                    providers: '(YouTube, Vimeo, Vine, Instagram, DailyMotion, o Youku)'
                },
                table: {
                    table: 'Tabla'
                },
                hr: {
                    insert: 'Insertar línea horizontal'
                },
                style: {
                    style: 'Estilo',
                    normal: 'Normal',
                    blockquote: 'Cita',
                    pre: 'Código',
                    h1: 'Título 1',
                    h2: 'Título 2',
                    h3: 'Título 3',
                    h4: 'Título 4',
                    h5: 'Título 5',
                    h6: 'Título 6'
                },
                lists: {
                    unordered: 'Lista desordenada',
                    ordered: 'Lista ordenada'
                },
                options: {
                    help: 'Ayuda',
                    fullscreen: 'Pantalla completa',
                    codeview: 'Ver código fuente'
                },
                paragraph: {
                    paragraph: 'Párrafo',
                    outdent: 'Menos tabulación',
                    indent: 'Más tabulación',
                    left: 'Alinear a la izquierda',
                    center: 'Alinear al centro',
                    right: 'Alinear a la derecha',
                    justify: 'Justificar'
                },
                color: {
                    recent: 'Último color',
                    more: 'Más colores',
                    background: 'Color de fondo',
                    foreground: 'Color de fuente',
                    transparent: 'Transparente',
                    setTransparent: 'Establecer transparente',
                    reset: 'Restaurar',
                    resetToDefault: 'Restaurar por defecto'
                },
                shortcut: {
                    shortcuts: 'Atajos de teclado',
                    close: 'Cerrar',
                    textFormatting: 'Formato de texto',
                    action: 'Acción',
                    paragraphFormatting: 'Formato de párrafo',
                    documentStyle: 'Estilo de documento'
                },
                history: {
                    undo: 'Deshacer',
                    redo: 'Rehacer'
                }
            }
        });
        $('.summernote').summernote({
            height: 200,
            lang: 'es-ES'
        });
    }

    var formatearNumero = function (number, decimals, dec_point, thousands_sep) {
        // Set the default values here, instead so we can use them in the replace below.
        thousands_sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep;
        dec_point = (typeof dec_point === 'undefined') ? '.' : dec_point;
        decimals = !isFinite(+decimals) ? 0 : Math.abs(decimals);

        // Work out the unicode representation for the decimal place and thousand sep.
        var u_dec = ('\\u' + ('0000' + (dec_point.charCodeAt(0).toString(16))).slice(-4));
        var u_sep = ('\\u' + ('0000' + (thousands_sep.charCodeAt(0).toString(16))).slice(-4));

        // Fix the number, so that it's an actual number.
        number = (number + '')
            .replace('\.', dec_point) // because the number if passed in as a float (having . as decimal point per definition) we need to replace this with the passed in decimal point character
            .replace(new RegExp(u_sep, 'g'), '')
            .replace(new RegExp(u_dec, 'g'), '.')
            .replace(new RegExp('[^0-9+\-Ee.]', 'g'), '');

        var n = !isFinite(+number) ? 0 : +number,
            s = '',
            toFixedFix = function (n, decimals) {
                var k = Math.pow(10, decimals);
                return '' + Math.round(n * k) / k;
            };

        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (decimals ? toFixedFix(n, decimals) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, thousands_sep);
        }
        if ((s[1] || '').length < decimals) {
            s[1] = s[1] || '';
            s[1] += new Array(decimals - s[1].length + 1).join('0');
        }
        return s.join(dec_point);
    }

    var handlerFormatNumber = function () {
        if (!jQuery().number) {
            return;
        }
        $('.form-control-number').number(true, 0, ',', '.');
    }

    var formatearFechaCalendario = function (fecha) {

        var result = "";
        if (fecha != "") {

            var array = fecha.split(" ");
            fecha = array[0];
            var hora_min = array[1];

            var fecha_array = fecha.split("/");
            var year = fecha_array[2];
            var mes = fecha_array[1];
            var day = fecha_array[0];

            result = year + "-" + mes + "-" + day + " " + hora_min;
        }


        return result;
    };

    var formatearFecha = function (fecha, format) {
        var result = fecha.format(format);
        return result;
    };

    return {
        //main function to initiate the module
        init: function () {
            initDatePickers();
            initInputMasks();
            toastrConfig();
            handlerNewValidateType();
            handlerSummernote();
            handlerFormatNumber();
        },
        showAlert: showAlert,
        showMessage: showMessage,
        block: block,
        sumarDiasAFecha: sumarDiasAFecha,
        restarDiasAFecha: restarDiasAFecha,
        sumarMesesAFecha: sumarMesesAFecha,
        formatearNumero: formatearNumero,
        handlerFormatNumber: handlerFormatNumber,
        formatearFechaCalendario: formatearFechaCalendario,
        formatearFecha: formatearFecha,
        scrollTo: function(el, offset) {
            var pos = (el && el.length > 0) ? el.offset().top : 0;
            pos = pos + (offset ? offset : 0);

            jQuery('html,body').animate({
                scrollTop: pos
            }, 'slow');
        },
    };

}();
MyApp.init();


