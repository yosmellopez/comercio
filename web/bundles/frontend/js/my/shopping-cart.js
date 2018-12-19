(function ($) {
    //Contacto y Registro
    $(function () {
        //Contacto
        $('#shopping-cart').click(function (e) {
            e.preventDefault();
            var cont = JSON.parse(localStorage.getItem("shopping-cart") || 0);
            cont++;
            $("#cont-shopping-cart").html(cont);
            localStorage.setItem("shopping-cart", "" + cont);
        });
    });
})(jQuery);