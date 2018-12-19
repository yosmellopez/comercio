<?php

namespace FrontendBundle\Controller;

use MakerLabs\PagerBundle\Pager;
use MakerLabs\PagerBundle\Adapter\ArrayAdapter;
use IcanBundle\Entity;
use Symfony\Component\HttpFoundation\Request;

class ProductoController extends BaseController
{

    public function indexAction(Request $request) {
        //Metaetiquetas para la pagina principal
        $url = 'listadoproductos';
        $seo_on_page = $this->getDoctrine()->getRepository('IcanBundle:SeoOnPage')->findOneBy(array('url' => $url));

        //Redes Sociales
        $redsocial = $this->ListarRedesSociales();
        //Categorias
        $categorias = $this->ListarCategorias();

        $page = $request->get('page');
        $productos = $this->ListarProductos($page);

        return $this->render('FrontendBundle:Producto:index.html.twig', array(
            'seo_on_page' => $seo_on_page,
            'productos' => $productos,
            'ruta' => 'listadoproductos',
            'parametro_ruta' => null,
            'categorias' => $categorias,
            'categoria_activa' => 0,
            'redsocial' => $redsocial,
        ));
    }

    public function productoscategoriaAction(Request $request) {
        $categoria = $request->get('categoria');
        $page = $request->get('page');
        $sort = $request->get("sort");

        $ruta = ($categoria == "todos") ? "" : $categoria;
        $productos = $this->ListarProductosCategoria($ruta, $page, $sort);
        $categoria = $this->getDoctrine()->getRepository('IcanBundle:Categoria')->BuscarPorUrl($ruta);

        if ($categoria != null) {
            //Redes Sociales
            $redsocial = $this->ListarRedesSociales();
            //Categorias
            $categorias = $this->ListarCategorias();

            $categoria_activa = ($categoria->getCategoriaPadre() != null) ? $categoria->getCategoriaPadre()->getCategoriaId() : $categoria->getCategoriaId();

            return $this->render('FrontendBundle:Producto:productoscategoria.html.twig', array(
                'productos' => $productos,
                'ruta' => 'listadoproductoscategoria',
                'parametro_ruta' => $ruta,
                'categoria' => $categoria,
                'categorias' => $categorias,
                'categoria_activa' => $categoria_activa,
                'redsocial' => $redsocial,
            ));
        } else {
            return $this->redirect($this->generateUrl('error404'));
        }
    }

    public function productosmarcaAction(Request $request) {
        $marca = $request->get('marca');
        $page = $request->get('page');

        $ruta = ($marca == "todos") ? "" : $marca;
        $productos = $this->ListarProductosMarca($ruta, $page);

        $marca = $this->getDoctrine()->getRepository('IcanBundle:Marca')
            ->BuscarPorUrl($ruta);

        if ($marca != null) {

            //Redes Sociales
            $redsocial = $this->ListarRedesSociales();
            //Categorias
            $categorias = $this->ListarCategorias();

            return $this->render('FrontendBundle:Producto:productosmarca.html.twig', array(
                'productos' => $productos,
                'ruta' => 'listadoproductosmarca',
                'parametro_ruta' => $ruta,
                'marca' => $marca,
                'categorias' => $categorias,
                'categoria_activa' => 0,
                'redsocial' => $redsocial
            ));
        } else {
            return $this->redirect($this->generateUrl('error404'));
        }
    }

    public function detalleAction(Request $request) {
        $ruta = $this->ObtenerURL();

        $url = $request->get('url');
        $producto = $this->DetalleProducto($url);

        //Redes Sociales
        $redsocial = $this->ListarRedesSociales();
        //Categorias
        $categorias = $this->ListarCategorias();

        if (count($producto) > 0) {
            return $this->render('FrontendBundle:Producto:detalle.html.twig', array(
                'direccion_url' => $ruta,
                'producto' => $producto,
                'categorias' => $categorias,
                'categoria_activa' => 0,
                'redsocial' => $redsocial
            ));
        } else {
            return $this->redirect($this->generateUrl('error404'));
        }
    }

    /**
     * DetalleProducto: Devuleve el producto para el detalle
     *
     * @param string $url Url del producto
     * @return array $orden
     *
     * @author Marcel
     */
    public function DetalleProducto($url) {
        $arreglo_resultado = array();
        $value = $this->getDoctrine()->getRepository('IcanBundle:Producto')
            ->BuscarPorUrl($url);
        if ($value != null) {
            $producto_id = $value->getProductoId();

            $arreglo_resultado['producto_id'] = $producto_id;
            $arreglo_resultado['nombre'] = $value->getNombre();
            $arreglo_resultado['titulo'] = $value->getTitulo();
            $arreglo_resultado['descripcion'] = $value->getDescripcion();
            $arreglo_resultado['tags'] = $value->getTags();
            $arreglo_resultado['stock'] = $value->getStock();
            $arreglo_resultado['mostrarPrecio'] = true;
            $arreglo_resultado['precioOferta'] = number_format(100, 0, ',', '.');
            $arreglo_resultado['precio'] = number_format($value->getPrecio(), 0, ',', '.');
            $arreglo_resultado['imagen'] = $value->getImagen();
            $arreglo_resultado['url'] = $value->getUrl();
            $arreglo_resultado['categoria'] = $value->getCategoria();
            $arreglo_resultado['marca'] = $value->getMarca();

            //Imagenes del producto
            $productoimagenes = $this->getDoctrine()->getRepository('IcanBundle:ProductoImagen')->ListarImagenes($producto_id);
            $imagenes = array();
            $cont_imagenes = 0;
            foreach ($productoimagenes as $productoimagen) {
                $imagenes[$cont_imagenes]['imagen'] = $productoimagen->getImagen();
                $cont_imagenes++;
            }

            $arreglo_resultado['imagenes'] = $imagenes;

            //Productos relacionados
            $relacionados = array();
            $cont_relacionados = 0;

            $fecha_actual = date('Y-m-d H:i', strtotime("+1 day"));
            $productos = $this->getDoctrine()->getRepository('IcanBundle:ProductoRelacion')
                ->ListarRelacionadosPortada($producto_id, $fecha_actual);
            foreach ($productos as $producto) {
                if ($producto->getProductoRelacion()->getProductoId() != $producto_id) {
                    $relacionados[$cont_relacionados]['producto_id'] = $producto->getProductoRelacion()->getProductoId();
                    $relacionados[$cont_relacionados]['nombre'] = $producto->getProductoRelacion()->getNombre();
                    $relacionados[$cont_relacionados]['descripcion'] = $producto->getProductoRelacion()->getDescripcion();
                    $relacionados[$cont_relacionados]['stock'] = $producto->getProductoRelacion()->getStock();
                    $relacionados[$cont_relacionados]['mostrarPrecio'] = true;
                    $relacionados[$cont_relacionados]['precio'] = number_format($producto->getProductoRelacion()->getPrecio(), 0, ',', '.');
                    $relacionados[$cont_relacionados]['precioOferta'] = number_format(200, 0, ',', '.');
                    $relacionados[$cont_relacionados]['imagen'] = $producto->getProductoRelacion()->getImagen();
                    $relacionados[$cont_relacionados]['url'] = $producto->getProductoRelacion()->getUrl();
                    $relacionados[$cont_relacionados]['categoria'] = $producto->getProductoRelacion()->getCategoria();
                    $relacionados[$cont_relacionados]['marca'] = $producto->getProductoRelacion()->getMarca();

                    $cont_relacionados++;
                }
            }
            $arreglo_resultado['relacionados'] = $relacionados;

            //Contar la visita
            $fecha_visita = date('Y-m-d');
            $ip = $_SERVER['REMOTE_ADDR'];
            $visita = $this->getDoctrine()->getRepository('IcanBundle:ProductoView')
                ->BuscarProductoView($fecha_visita, $ip, $producto_id);
            if ($visita == null) {
                $visita = new Entity\ProductoView();
                $fecha_visita = new \DateTime($fecha_visita);
                $visita->setFecha($fecha_visita);
                $visita->setIp($ip);
                $visita->setProducto($value);

                $em = $this->getDoctrine()->getManager();
                $em->persist($visita);

                $value->setViews($value->getViews() + 1);
                $em->flush();
            }
        }
        return $arreglo_resultado;
    }

    /**
     * ListarProductosMarca: Lista todas las productos de una marca
     *
     * @return array $productos
     * @author Marcel
     */
    public function ListarProductosMarca($marca, $page) {
        $arreglo_resultado = array();
        $cont = 0;
        $fecha_actual = date('Y-m-d H:i', strtotime("+1 day"));
        $lista = $this->getDoctrine()->getRepository('IcanBundle:Producto')->ListarProductosMarcaPortada($marca, $fecha_actual);
        foreach ($lista as $value) {
            $producto_id = $value->getProductoId();
            $arreglo_resultado[$cont]['producto_id'] = $producto_id;
            $arreglo_resultado[$cont]['nombre'] = $value->getNombre();
            $arreglo_resultado[$cont]['descripcion'] = $value->getDescripcion();
            $arreglo_resultado[$cont]['stock'] = $value->getStock();
            $arreglo_resultado[$cont]['mostrarPrecio'] = ($value->getMostrarPrecio() == 1) ? true : false;
            $arreglo_resultado[$cont]['precioOferta'] = number_format($value->getPrecioOferta(), 0, ',', '.');
            $arreglo_resultado[$cont]['precio'] = number_format($value->getPrecio(), 0, ',', '.');
            $arreglo_resultado[$cont]['imagen'] = $value->getImagen();
            $arreglo_resultado[$cont]['url'] = $value->getUrl();
            $arreglo_resultado[$cont]['categoria'] = $value->getCategoria();
            $arreglo_resultado[$cont]['marca'] = $value->getMarca();

            $cont++;
        }


        $adapter = new ArrayAdapter($arreglo_resultado);
        $pager = new Pager($adapter, array('page' => $page, 'limit' => 12));

        return $pager;
    }

    /**
     * ListarProductosCategoria: Lista todos los productos de una categoria
     *
     * @return array $productos
     * @author Marcel
     */
    public function ListarProductosCategoria($categoria, $page, $sort) {
        $arreglo_resultado = array();
        $cont = 0;
        $fecha_actual = date('Y-m-d H:i', strtotime("+1 day"));
        $lista = $this->getDoctrine()->getRepository('IcanBundle:Producto')->ListarProductosCategoriaPortada($categoria, $fecha_actual, $sort);
        foreach ($lista as $value) {
            $producto_id = $value->getProductoId();
            $arreglo_resultado[$cont]['producto_id'] = $producto_id;
            $arreglo_resultado[$cont]['nombre'] = $value->getNombre();
            $arreglo_resultado[$cont]['descripcion'] = $value->getDescripcion();
            $arreglo_resultado[$cont]['stock'] = $value->getStock();
            $arreglo_resultado[$cont]['mostrarPrecio'] = true;
            $arreglo_resultado[$cont]['precioOferta'] = number_format(100, 0, ',', '.');
            $arreglo_resultado[$cont]['precio'] = number_format($value->getPrecio(), 0, ',', '.');
            $arreglo_resultado[$cont]['imagen'] = $value->getImagen();
            $arreglo_resultado[$cont]['url'] = $value->getUrl();
            $arreglo_resultado[$cont]['categoria'] = $value->getCategoria();
            $arreglo_resultado[$cont]['marca'] = $value->getMarca();
            $cont++;
        }
        $adapter = new ArrayAdapter($arreglo_resultado);
        $pager = new Pager($adapter, array('page' => $page, 'limit' => 12));
        return $pager;
    }

    /**
     * ListarProductos: Lista todas las productos
     *
     * @return array $productos
     * @author Marcel
     */
    public
    function ListarProductos($page) {
        $arreglo_resultado = array();
        $cont = 0;

        $fecha_actual = date('Y-m-d H:i', strtotime("+1 day"));
        $lista = $this->getDoctrine()->getRepository('IcanBundle:Producto')
            ->ListarProductosPortada($fecha_actual);

        foreach ($lista as $value) {
            $producto_id = $value->getProductoId();

            $arreglo_resultado[$cont]['producto_id'] = $producto_id;

            $arreglo_resultado[$cont]['nombre'] = $value->getNombre();
            $arreglo_resultado[$cont]['descripcion'] = $value->getDescripcion();
            $arreglo_resultado[$cont]['stock'] = $value->getStock();
            $arreglo_resultado[$cont]['mostrarPrecio'] = true;
            $arreglo_resultado[$cont]['precioOferta'] = 200;
            $arreglo_resultado[$cont]['precio'] = number_format($value->getPrecio(), 0, ',', '.');
            $arreglo_resultado[$cont]['imagen'] = $value->getImagen();
            $arreglo_resultado[$cont]['url'] = $value->getUrl();

            $arreglo_resultado[$cont]['categoria'] = $value->getCategoria();
            $arreglo_resultado[$cont]['marca'] = $value->getMarca();

            $cont++;
        }


        $adapter = new ArrayAdapter($arreglo_resultado);
        $pager = new Pager($adapter, array('page' => $page, 'limit' => 12));

        return $pager;
    }

}
