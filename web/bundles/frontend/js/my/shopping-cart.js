(function ($) {
    //Contacto y Registro
    $(function () {
        //Contacto
        $('#shopping-cart').click(function (e) {
            e.preventDefault();
            products = JSON.parse(localStorage.getItem("shopping-cart-products") || '[]');
            $('#modal-login-registro').modal({
                show: true
            });
        });

        $('.shopping-cart-product').click(function (e) {
            e.preventDefault();
            var id = this.id.substr(7);
            var nombre = $("#item-nombre-" + id).html();
            var urlImagen = $("#item-imagen-" + id).attr("src");
            var obj = {nombre: nombre, id: id, imagen: urlImagen};
            var cont = JSON.parse(localStorage.getItem("shopping-cart") || 0);
            products = JSON.parse(localStorage.getItem("shopping-cart-products") || '[]');
            var productos = products.filter(function (product) {
                return product.id == id;
            });
            if (productos.length !== 0) {
                Swal({
                    type: 'error',
                    title: 'Ya existe este producto!',
                    html: '<span style="font-size: 16px">El producto ' + nombre + ' ya se ha añadido al carro de compra!</span>',
                    confirmButtonText: 'Aceptar',
                })

            } else {
                Swal({
                    position: 'top-center',
                    type: 'success',
                    title: 'Producto ' + nombre + ' añadido exitosamente',
                    showConfirmButton: false,
                    timer: 1500
                })
                products.push(obj);
                cont++;
                $("#cont-shopping-cart").html(cont);
                localStorage.setItem("shopping-cart", "" + cont);
                localStorage.setItem("shopping-cart-products", JSON.stringify(products));
            }
        });
    });
})(jQuery);

var ShoppingCart = function () {
    var oTable;
    var rowDelete = null;

    function initShopping() {
        var products = JSON.parse(localStorage.getItem("shopping-cart-products") || '[]');
        for (var i in products) {
            $('#my-container').append(
                `<div class="col-md-3">
                     <div class="thumbnail">
                          <img src="${products[i].imagen}" class="img-rounded img-responsive" width="100%"/>
                          <div class="caption">
                               <p>${products[i].nombre}</p>
                          </div>
                     </div>
                 </div>`
            );
        }
    }

    return {
        init: function () {
            initShopping();
        }
    };

}();
