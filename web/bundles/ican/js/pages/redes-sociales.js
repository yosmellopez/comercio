var RedesSociales = function () {

    //Validacion
    var initForm = function () {
        $("#redsocial-form").validate({
            rules: {
                facebook: {
                    url: true
                },
                youtube: {
                    url: true
                },
                twitter: {
                    url: true
                },
                flickr: {
                    url: true
                },
                instagram: {
                    url: true
                },
                linkedin: {
                    url: true
                },
                soundcloud: {
                    url: true
                },
                tumblr: {
                    url: true
                },
                vimeo: {
                    url: true
                },
                google: {
                    url: true
                }
            },
            messages: {
                facebook: "Por favor, introduzca una url válida",
                youtube: "Por favor, introduzca una url válida",
                twitter: "Por favor, introduzca una url válida",
                flickr: "Por favor, introduzca una url válida",
                instagram: "Por favor, introduzca una url válida",
                linkedin: "Por favor, introduzca una url válida",
                soundcloud: "Por favor, introduzca una url válida",
                tumblr: "Por favor, introduzca una url válida",
                vimeo: "Por favor, introduzca una url válida",
                google: "Por favor, introduzca una url válida"
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

    //Salvar
    var initAccionSalvar = function () {
        $(document).off('click', "#btn-salvar-redsocial");
        $(document).on('click', "#btn-salvar-redsocial", function (e) {
            btnClickSalvarForm();
        });

        function btnClickSalvarForm() {
            mUtil.scrollTo();

            if ($('#redsocial-form').valid()) {

                var redsocial_id = $('#redsocial_id').val();

                var facebook = $('#facebook').val();
                var activarfacebook = ($('#activarfacebook').prop('checked')) ? 1 : 0;

                var youtube = $('#youtube').val();
                var activaryoutube = ($('#activaryoutube').prop('checked')) ? 1 : 0;

                var twitter = $('#twitter').val();
                var activartwitter = ($('#activartwitter').prop('checked')) ? 1 : 0;

                var flickr = $('#flickr').val();
                var activarflickr = ($('#activarflickr').prop('checked')) ? 1 : 0;

                var instagram = $('#instagram').val();
                var activarinstagram = ($('#activarinstagram').prop('checked')) ? 1 : 0;

                var linkedin = $('#linkedin').val();
                var activarlinkedin = ($('#activarlinkedin').prop('checked')) ? 1 : 0;

                var soundcloud = $('#soundcloud').val();
                var activarsoundcloud = ($('#activarsoundcloud').prop('checked')) ? 1 : 0;

                var tumblr = $('#tumblr').val();
                var activartumblr = ($('#activartumblr').prop('checked')) ? 1 : 0;

                var vimeo = $('#vimeo').val();
                var activarvimeo = ($('#activarvimeo').prop('checked')) ? 1 : 0;

                var google = $('#google').val();
                var activargoogle = ($('#activargoogle').prop('checked')) ? 1 : 0;

                MyApp.block('#form-redsocial');

                $.ajax({
                    type: "POST",
                    url: "redsocial/salvarRedsocial",
                    dataType: "json",
                    data: {
                        'redsocial_id': redsocial_id,
                        'facebook': facebook,
                        'activarfacebook': activarfacebook,
                        'youtube': youtube,
                        'activaryoutube': activaryoutube,
                        'twitter': twitter,
                        'activartwitter': activartwitter,
                        'flickr': flickr,
                        'activarflickr': activarflickr,
                        'instagram': instagram,
                        'activarinstagram': activarinstagram,
                        'linkedin': linkedin,
                        'activarlinkedin': activarlinkedin,
                        'soundcloud': soundcloud,
                        'activarsoundcloud': activarsoundcloud,
                        'tumblr': tumblr,
                        'activartumblr': activartumblr,
                        'vimeo': vimeo,
                        'activarvimeo': activarvimeo,
                        'google': google,
                        'activargoogle': activargoogle
                    },
                    success: function (response) {
                        mApp.unblock('#form-redsocial');
                        if (response.success) {

                            toastr.success(response.message, "Exito !!!");

                        } else {
                            toastr.error(response.error, "Error !!!");
                        }
                    },
                    failure: function (response) {
                        mApp.unblock('#form-redsocial');

                        toastr.error(response.error, "Error !!!");
                    }
                });

            }
        };
    }

    return {
        //main function to initiate the module
        init: function () {

            initForm();
            initAccionSalvar();
        }

    };

}();