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

        $('.shopping-cart-product').click(function (e) {
            e.preventDefault();
            var id = this.id.substr(7);
            var nombre = $("#item-nombre-" + id).html();
            var obj = {nombre: nombre, id: id};
            var cont = JSON.parse(localStorage.getItem("shopping-cart") || 0);
            products = JSON.parse(localStorage.getItem("shopping-cart-products") || '[]');
            var productos = products.filter(function (product) {
                return product.id == id;
            });
            if (productos.length !== 0) {
                Swal({
                    type: 'error',
                    title: 'Ya existe este producto',
                    html: '<span style="font-size: 15px">El producto ' + nombre + ' ya se ha añadido el carro de compra!</span>',
                    confirmButtonText: 'Cerrar!',
                })

            } else {
                products.push(obj);
                cont++;
                $("#cont-shopping-cart").html(cont);
                localStorage.setItem("shopping-cart", "" + cont);
                localStorage.setItem("shopping-cart-products", JSON.stringify(products));
            }
        });
    });
})(jQuery);