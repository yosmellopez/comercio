/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var App = function() {
    var handleScrollers = function() {
        if (!jQuery().slimScroll) {
            return;
        }

        $('.scroller').each(function() {
            $(this).slimScroll({
                //start: $('.blah:eq(1)'),
                height: $(this).attr("data-height"),
                alwaysVisible: ($(this).attr("data-always-visible") == "1" ? true : false),
                railVisible: ($(this).attr("data-rail-visible") == "1" ? true : false),
                disableFadeOut: true
            });
        });
    };
    var handleGoTop = function() {
        /* set variables locally for increased performance */
        jQuery('#footer .go-top').click(function() {
            App.scrollTo();
        });

    };

    var handleSummernote = function() {
        /* set variables locally for increased performance */
        if (!jQuery().summernote) {
            return;
        }
        $('.summernote').summernote({
            lang: 'es-ES',
            height: 200
        });

    };

    var handleSelect = function() {
        /* set variables locally for increased performance */
        if (!jQuery().selectpicker) {
            return;
        }
        $('.selectpicker').selectpicker();

    };

    var handleToolTips = function() {
        $(".remove").data("title", 'Cerrar').tooltip();
        $(".tools .collapse").data("title", 'Colapsar').tooltip();
        $(".tools .expand").data("title", 'Expandir').tooltip();
    };

    var handleAlerts = function() {

        $('body').on('click', '[data-close="alert"]', function(e) {
            $(this).parent('.alert').hide();
            e.preventDefault();
        });
    };

    var handleTools = function() {
        $('body').on('click', '.panel > .panel-heading > .tools > .collapse, .panel .panel-heading > .tools > .expand', function(e) {
            e.preventDefault();
            var el = $(this).closest(".panel").children(".panel-body");
            if ($(this).hasClass("collapse")) {
                $(".collapse").data("title", "").tooltip("destroy");
                $(this).removeClass("collapse").addClass("expand");
                $(".expand").data("title", 'Expandir').tooltip();

                el.slideUp(200);
            } else {
                $(".expand").data("title", "").tooltip("destroy");
                $(this).removeClass("expand").addClass("collapse");
                $(".collapse").data("title", 'Colapsar').tooltip();

                el.slideDown(200);
            }
        });

    };

    var handleDateTimePickers = function() {
        if (!jQuery().datepicker) {
            return;
        }

        $('.date-picker').datepicker({
            format: 'dd/mm/yyyy',
            language: 'es',
            autoclose: true
        });

    };
    
    var handleDropdownMenuHover = function() {
        /* set variables locally for increased performance */
        if (!jQuery().dropdownHover) {
            return;
        }
        //$('.dropdown-toggle').dropdownHover();

    };

    return {
        //main function to initiate the module
        init: function() {
            handleScrollers(); // handles slim scrolling contents        
            handleGoTop();
            handleSummernote();
            handleSelect();
            handleToolTips();
            handleAlerts();
            handleTools();
            handleDateTimePickers();
            handleDropdownMenuHover();
        },
        scrollTo: function(el) {
            pos = el ? el.offset().top : 0;
            jQuery('html,body').animate({
                scrollTop: pos
            }, 'slow');
        },
        alertSucces: function(message, scroll) {
            $("#message-success p").html(message);
            $("#message-success").css('display', 'block');
            $('#message-success').fadeOut(4500);
            App.scrollTo($(".page-header"));
        },
        alertError: function(message) {
            $("#message-error p").html(message);
            $("#message-error").css('display', 'block');
            $('#message-error').fadeOut(4500);
            App.scrollTo($(".page-header"));
        }
    };

}();


