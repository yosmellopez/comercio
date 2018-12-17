<?php

namespace IcanBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use IcanBundle\Entity;
use IcanBundle\Util;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

class CompraController extends BaseController
{

    public function indexAction() {
        return $this->render('IcanBundle:Compra:index.html.twig', array());
    }

    /**
     * listarAction Acción que lista los marcas
     *
     */
    public function listarAction(Request $request) {
        // search filter by keywords
        $query = !empty($request->get('query')) ? $request->get('query') : array();
        $sSearch = isset($query['generalSearch']) && is_string($query['generalSearch']) ? $query['generalSearch'] : '';
        $categoria_id = isset($query['categoria_id']) && is_string($query['categoria_id']) ? $query['categoria_id'] : '';
        $marca_id = isset($query['marca_id']) && is_string($query['marca_id']) ? $query['marca_id'] : '';

        //Sort
        $sort = !empty($request->get('sort')) ? $request->get('sort') : array();
        $sSortDir_0 = !empty($sort['sort']) ? $sort['sort'] : 'desc';
        $iSortCol_0 = !empty($sort['field']) ? $sort['field'] : 'fechapublicacion';
        //$start and $limit
        $pagination = !empty($request->get('pagination')) ? $request->get('pagination') : array();
        $page = !empty($pagination['page']) ? (int)$pagination['page'] : 1;
        $limit = !empty($pagination['perpage']) ? (int)$pagination['perpage'] : -1;
        $start = 0;

        try {
            $pages = 1;
            $total = $this->TotalCompras($sSearch, $categoria_id, $marca_id);
            if ($limit > 0) {
                $pages = ceil($total / $limit); // calculate total pages
                $page = max($page, 1); // get 1 page when $_REQUEST['page'] <= 0
                $page = min($page, $pages); // get last page when $_REQUEST['page'] > $totalPages
                $start = ($page - 1) * $limit;
                if ($start < 0) {
                    $start = 0;
                }
            }

            $meta = array(
                'page' => $page,
                'pages' => $pages,
                'perpage' => $limit,
                'total' => $total,
                'field' => $iSortCol_0,
                'sort' => $sSortDir_0
            );

            $data = $this->ListarCompras($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $categoria_id, $marca_id);

            $resultadoJson = array(
                'meta' => $meta,
                'data' => $data
            );

            return new Response(json_encode($resultadoJson));

        } catch (Exception $e) {
            $resultadoJson['success'] = false;
            $resultadoJson['error'] = $e->getMessage();

            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * listarParaRelacionadosAction Acción que lista los productos para relacionar
     *
     */
    public function listarParaRelacionadosAction(Request $request) {
        // search filter by keywords
        $query = !empty($request->get('query')) ? $request->get('query') : array();
        $sSearch = isset($query['generalSearch']) && is_string($query['generalSearch']) ? $query['generalSearch'] : '';
        $categoria_id = isset($query['categoria_id']) && is_string($query['categoria_id']) ? $query['categoria_id'] : '';
        $marca_id = isset($query['marca_id']) && is_string($query['marca_id']) ? $query['marca_id'] : '';
        $productos_id = isset($query['productos_id']) && is_array($query['productos_id']) ? $query['productos_id'] : array();

        //Sort
        $sort = !empty($request->get('sort')) ? $request->get('sort') : array();
        $sSortDir_0 = !empty($sort['sort']) ? $sort['sort'] : 'asc';
        $iSortCol_0 = !empty($sort['field']) ? $sort['field'] : 'nombre';
        //$start and $limit
        $pagination = !empty($request->get('pagination')) ? $request->get('pagination') : array();
        $page = !empty($pagination['page']) ? (int)$pagination['page'] : 1;
        $limit = !empty($pagination['perpage']) ? (int)$pagination['perpage'] : -1;
        $start = 0;

        try {
            $pages = 1;
            $total = $this->TotalComprasParaRelacionados($sSearch, $categoria_id, $marca_id, $productos_id);
            if ($limit > 0) {
                $pages = ceil($total / $limit); // calculate total pages
                $page = max($page, 1); // get 1 page when $_REQUEST['page'] <= 0
                $page = min($page, $pages); // get last page when $_REQUEST['page'] > $totalPages
                $start = ($page - 1) * $limit;
                if ($start < 0) {
                    $start = 0;
                }
            }
            $meta = array(
                'page' => $page,
                'pages' => $pages,
                'perpage' => $limit,
                'total' => $total,
                'field' => $iSortCol_0,
                'sort' => $sSortDir_0
            );
            $data = $this->ListarComprasParaRelacionados($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $categoria_id, $marca_id, $productos_id);
            $resultadoJson = array(
                'meta' => $meta,
                'data' => $data
            );
            return new Response(json_encode($resultadoJson));
        } catch (Exception $e) {
            $resultadoJson['success'] = false;
            $resultadoJson['error'] = $e->getMessage();
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * salvarAction Acción que inserta un usuario en la BD
     *
     */
    public function salvarAction(Request $request) {
        $producto_id = $request->get('producto_id');

        $categoria_id = $request->get('categoria');
        $marca_id = $request->get('marca');

        $nombre = $request->get('nombre');
        $titulo = $request->get('titulo');
        $descripcion = $request->get('descripcion');
        $tags = $request->get('tags');
        $stock = $request->get('stock');
        $precio = $request->get('precio');
        $precioOferta = $request->get('precioOferta');
        $mostrarPrecio = $request->get('mostrarPrecio');
        $estado = $request->get('estado');
        $fecha = $request->get('fecha');
        $imagen = $request->get('imagen');
        $imagenes = $request->get('imagenes');
        $productos = $request->get('productos');

        $resultadoJson = array();
        if ($producto_id == "") {
            $resultado = $this->SalvarCompra($categoria_id, $marca_id, $nombre, $titulo, $descripcion, $tags, $stock, $precio,
                $precioOferta, $mostrarPrecio, $estado, $fecha, $imagen, $imagenes, $productos);
        } else {
            $resultado = $this->ActualizarCompra($producto_id, $categoria_id, $marca_id, $nombre, $titulo, $descripcion, $tags, $stock, $precio,
                $precioOferta, $mostrarPrecio, $estado, $fecha, $imagen, $imagenes, $productos);
        }

        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['message'] = "La operación se realizó correctamente";

            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];
            return new Response(json_encode($resultadoJson));
        }
    }

    public function salvarImagenAction() {
        try {
            $nombre_archivo = $_FILES['foto']['name'];
            $array_nombre_archivo = explode('.', $nombre_archivo);
            $pos = count($array_nombre_archivo) - 1;
            $extension = $array_nombre_archivo[$pos];

            $archivo = $this->generarCadenaAleatoria() . '.' . $extension;

            //Manejar la imagen
            $dir = 'uploads/productos/';
            $archivo_tmp = $_FILES['foto']['tmp_name'];
            move_uploaded_file($archivo_tmp, $dir . $archivo);

            $archivo_thumb = "portada-" . $archivo;
            $imagine = new Imagine();
            $size = new Box(290, 220);
            $mode = ImageInterface::THUMBNAIL_OUTBOUND;
            $imagine->open($dir . $archivo)
                ->thumbnail($size, $mode)
                ->save($dir . $archivo_thumb);

            $archivo_thumb = "thumb-" . $archivo;
            $imagine = new Imagine();
            $size = new Box(96, 75);
            $mode = ImageInterface::THUMBNAIL_OUTBOUND;
            $imagine->open($dir . $archivo)
                ->thumbnail($size, $mode)
                ->save($dir . $archivo_thumb);

            $resultadoJson['success'] = true;
            $resultadoJson['name'] = $archivo;
            return new Response(json_encode($resultadoJson));
        } catch (Exception $e) {
            $resultadoJson['success'] = false;
            $resultadoJson['error'] = $e->getMessage();
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * eliminarAction Acción que elimina un rol en la BD
     *
     */
    public function eliminarAction(Request $request) {
        $producto_id = $request->get('producto_id');

        $resultado = $this->EliminarCompra($producto_id);
        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['message'] = "La operación se realizó correctamente";
            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * eliminarComprasAction Acción que elimina varios productos en la BD
     *
     */
    public function eliminarComprasAction(Request $request) {
        $ids = $request->get('ids');

        $resultado = $this->EliminarCompras($ids);
        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['message'] = "La operación se realizó correctamente";

            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];

            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * cargarDatosAction Acción que carga los datos del usuario en la BD
     *
     */
    public function cargarDatosAction(Request $request) {
        $producto_id = $request->get('producto_id');

        $resultado = $this->CargarDatosCompra($producto_id);
        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['producto'] = $resultado['producto'];

            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * eliminarImagenAction Acción que elimina una imagen en la BD
     *
     */
    public function eliminarImagenAction(Request $request) {
        $imagen = $request->get('imagen');

        $resultado = $this->EliminarImagen($imagen);
        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['message'] = "La operación se realizó correctamente";

            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];

            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * eliminarRelacionadoAction Acción que elimina un producto relacionado en la BD
     *
     */
    public function eliminarRelacionadoAction(Request $request) {
        $productorelacion_id = $request->get('productorelacion_id');

        $resultado = $this->EliminarRelacionado($productorelacion_id);
        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['message'] = "La operación se realizó correctamente";
            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * EliminarRelacionado: Elimina un producto relacionado en la BD
     * @param int $productorelacion_id Id
     * @author Marcel
     */
    public function EliminarRelacionado($productorelacion_id) {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:CompraRelacion')
            ->find($productorelacion_id);

        if ($entity != null) {

            $producto_id = $entity->getCompra()->getCompraId();
            $producto_relacionado_id = $entity->getCompraRelacion()->getCompraId();

            $em->remove($entity);

            //Eliminar la relacion inversa
            $relacion = $this->getDoctrine()->getRepository('IcanBundle:CompraRelacion')
                ->BuscarRelacion($producto_relacionado_id, $producto_id);
            if ($relacion != null) {
                $em->remove($relacion);
            }

            $em->flush();
            $resultado['success'] = true;
        } else {
            $resultado['success'] = false;
            $resultado['error'] = "No existe el registro solicitado";
        }

        return $resultado;
    }

    /**
     * EliminarImagen: Elimina una imagen de un producto en la BD
     * @param int $producto_id Id
     * @author Marcel
     */
    public function EliminarImagen($imagen) {
        $resultado = array();
        //Eliminar foto       
        if ($imagen != "") {
            $dir = 'uploads/productos/';
            if (is_file($dir . $imagen)) {
                unlink($dir . $imagen);
                unlink($dir . "portada-" . $imagen);
                unlink($dir . "thumb-" . $imagen);
            }
        }

        $em = $this->getDoctrine()->getManager();
        $productoimagen = $this->getDoctrine()->getRepository('IcanBundle:CompraImagen')
            ->findOneBy(
                array('imagen' => $imagen)
            );
        if ($productoimagen != null) {
            $em->remove($productoimagen);
        }

        $producto = $this->getDoctrine()->getRepository('IcanBundle:Compra')
            ->findOneBy(
                array('imagen' => $imagen)
            );
        if ($producto != null) {
            $producto->setImagen("");
        }
        $em->flush();

        $resultado['success'] = true;
        return $resultado;
    }

    /**
     * CargarDatosCompra: Carga los datos de un usuario
     *
     * @param int $producto_id Id
     *
     * @author Marcel
     */
    public function CargarDatosCompra($producto_id) {
        $resultado = array();
        $arreglo_resultado = array();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Compra')
            ->find($producto_id);
        if ($entity != null) {

            $arreglo_resultado['nombre'] = $entity->getNombre();
            $arreglo_resultado['categoria'] = ($entity->getCategoria() != null) ? $entity->getCategoria()->getCategoriaId() : "";
            $arreglo_resultado['marca'] = ($entity->getMarca() != null) ? $entity->getMarca()->getMarcaId() : "";
            $arreglo_resultado['titulo'] = $entity->getTitulo();
            $arreglo_resultado['descripcion'] = $entity->getDescripcion();
            $arreglo_resultado['tags'] = $entity->getTags();
            $arreglo_resultado['stock'] = $entity->getStock();
            $arreglo_resultado['precio'] = $entity->getPrecio();
            $arreglo_resultado['precioOferta'] = $entity->getPrecioOferta();
            $arreglo_resultado['mostrarPrecio'] = ($entity->getMostrarPrecio() == 1) ? true : false;
            $arreglo_resultado['estado'] = ($entity->getEstado() == 1) ? true : false;
            $arreglo_resultado['url'] = $entity->getUrl();

            $imagen = $entity->getImagen();

            $ruta = $this->ObtenerURL();
            $dir = 'uploads/productos/';
            $ruta = $ruta . $dir;

            $size = (is_file($dir . $imagen)) ? filesize($dir . $imagen) : 0;
            $arreglo_resultado['imagen'] = array($imagen, $size, $ruta);


            $fecha = $entity->getFechapublicacion();
            if ($fecha != "") {
                $fecha = $fecha->format('d/m/Y H:i');
            }
            $arreglo_resultado['fecha'] = $fecha;

            //Imagenes del producto
            $productoimagenes = $this->getDoctrine()->getRepository('IcanBundle:CompraImagen')
                ->ListarImagenes($producto_id);
            $imagenes = array();
            $cont = 0;
            foreach ($productoimagenes as $productoimagen) {

                $size = (is_file($dir . $productoimagen->getImagen())) ? filesize($dir . $productoimagen->getImagen()) : 0;
                $imagenes[$cont]['imagen'] = array($productoimagen->getImagen(), $size, $ruta);

                $cont++;
            }
            $arreglo_resultado['imagenes'] = $imagenes;

            //Compras relacionados
            $relacionados = array();
            $productos = $this->getDoctrine()->getRepository('IcanBundle:CompraRelacion')
                ->ListarRelacionados($producto_id);
            $posicion = 0;
            foreach ($productos as $key => $producto_relacion) {
                $producto = $producto_relacion->getCompraRelacion();
                if ($producto->getCompraId() != $producto_id) {
                    array_push($relacionados, array(
                        'productorelacion_id' => $producto_relacion->getComprarelacionId(),
                        'producto_id' => $producto->getCompraId(),
                        'nombre' => $producto->getNombre(),
                        "categoria" => ($producto->getCategoria() != null) ? $producto->getCategoria()->getNombre() : "",
                        "marca" => ($producto->getMarca() != null) ? $producto->getMarca()->getNombre() : "",
                        "estado" => ($producto->getEstado()) ? 1 : 0,
                        "imagen" => $ruta . $producto->getImagen(),
                        "precio" => number_format($producto->getPrecio(), 0, ',', '.'),
                        "fecha" => $producto->getFechapublicacion() != "" ? $producto->getFechapublicacion()->format("d/m/Y H:i") : "",
                        "views" => $producto->getViews(),
                        'posicion' => $posicion
                    ));
                    $posicion++;
                }
            }
            $arreglo_resultado['relacionados'] = $relacionados;

            $resultado['success'] = true;
            $resultado['producto'] = $arreglo_resultado;
        }
        return $resultado;
    }

    /**
     * EliminarCompra: Elimina un producto en la BD
     * @param int $producto_id Id
     * @author Marcel
     */
    public function EliminarCompra($producto_id) {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Compra')
            ->find($producto_id);

        if ($entity != null) {

            //Eliminar foto
            $foto_eliminar = $entity->getImagen();
            if ($foto_eliminar != "") {
                $dir = 'uploads/productos/';
                if (is_file($dir . $foto_eliminar)) {
                    unlink($dir . $foto_eliminar);
                    unlink($dir . "portada-" . $foto_eliminar);
                    unlink($dir . "thumb-" . $foto_eliminar);
                }
            }

            //Eliminar las imagenes
            $productoimagenes = $this->getDoctrine()->getRepository('IcanBundle:CompraImagen')
                ->ListarImagenes($producto_id);
            foreach ($productoimagenes as $productoimagen) {
                //Eliminar foto
                $foto_eliminar = $productoimagen->getImagen();
                if ($foto_eliminar != "") {
                    $dir = 'uploads/productos/';
                    if (is_file($dir . $foto_eliminar)) {
                        unlink($dir . $foto_eliminar);
                        unlink($dir . "portada-" . $foto_eliminar);
                        unlink($dir . "thumb-" . $foto_eliminar);
                    }
                }
                $em->remove($productoimagen);
            }

            //Eliminar los productos relacionados
            $relacionados = $this->getDoctrine()->getRepository('IcanBundle:CompraRelacion')
                ->ListarRelacionados($producto_id);
            foreach ($relacionados as $relacionado) {
                $em->remove($relacionado);
            }

            //Eliminar los productos relacionados
            $productorelacionados = $this->getDoctrine()->getRepository('IcanBundle:CompraRelacion')
                ->ListarComprasRelacionado($producto_id);
            foreach ($productorelacionados as $relacionado) {
                $em->remove($relacionado);
            }

            //Eliminar vistas
            $producto_views = $this->getDoctrine()->getRepository('IcanBundle:CompraView')
                ->ListarViewsDeCompra($producto_id);
            foreach ($producto_views as $producto_view) {
                $em->remove($producto_view);
            }

            $em->remove($entity);

            $em->flush();
            $resultado['success'] = true;
        } else {
            $resultado['success'] = false;
            $resultado['error'] = "No existe el registro solicitado";
        }

        return $resultado;
    }

    /**
     * EliminarCompras: Elimina varios productos en la BD
     * @param array $$ids Ids
     * @author Marcel
     */
    public function EliminarCompras($ids) {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        if ($ids != "") {
            $ids = explode(',', $ids);
            foreach ($ids as $producto_id) {
                if ($producto_id != "") {

                    $entity = $this->getDoctrine()->getRepository('IcanBundle:Compra')
                        ->find($producto_id);
                    if ($entity != null) {

                        //Eliminar foto
                        $foto_eliminar = $entity->getImagen();
                        if ($foto_eliminar != "") {
                            $dir = 'uploads/productos/';
                            if (is_file($dir . $foto_eliminar)) {
                                unlink($dir . $foto_eliminar);
                                unlink($dir . "portada-" . $foto_eliminar);
                                unlink($dir . "thumb-" . $foto_eliminar);
                            }
                        }

                        //Eliminar las imagenes
                        $productoimagenes = $this->getDoctrine()->getRepository('IcanBundle:CompraImagen')
                            ->ListarImagenes($producto_id);
                        foreach ($productoimagenes as $productoimagen) {
                            //Eliminar foto
                            $foto_eliminar = $productoimagen->getImagen();
                            if ($foto_eliminar != "") {
                                $dir = 'uploads/productos/';
                                if (is_file($dir . $foto_eliminar)) {
                                    unlink($dir . $foto_eliminar);
                                    unlink($dir . "portada-" . $foto_eliminar);
                                    unlink($dir . "thumb-" . $foto_eliminar);
                                }
                            }
                            $em->remove($productoimagen);
                        }

                        //Eliminar los productos relacionados
                        $relacionados = $this->getDoctrine()->getRepository('IcanBundle:CompraRelacion')
                            ->ListarRelacionados($producto_id);
                        foreach ($relacionados as $relacionado) {
                            $em->remove($relacionado);
                        }

                        //Eliminar los productos relacionados
                        $productorelacionados = $this->getDoctrine()->getRepository('IcanBundle:CompraRelacion')
                            ->ListarComprasRelacionado($producto_id);
                        foreach ($productorelacionados as $relacionado) {
                            $em->remove($relacionado);
                        }

                        //Eliminar vistas
                        $producto_views = $this->getDoctrine()->getRepository('IcanBundle:CompraView')
                            ->ListarViewsDeCompra($producto_id);
                        foreach ($producto_views as $producto_view) {
                            $em->remove($producto_view);
                        }

                        $em->remove($entity);
                    }
                }
            }
        }
        $em->flush();
        $resultado['success'] = true;
        return $resultado;
    }

    /**
     * ActualizarCompra: Actualiza los datos del producto en la BD
     *
     * @param string $producto_id Id
     *
     * @author Marcel
     */
    public function ActualizarCompra($producto_id, $categoria_id, $marca_id, $nombre, $titulo, $descripcion, $tags, $stock, $precio,
                                     $precioOferta, $mostrarPrecio, $estado, $fecha, $imagen, $imagenes, $productos) {
        $em = $this->getDoctrine()->getManager();

        $resultado = array();
        $entity = $this->getDoctrine()->getRepository('IcanBundle:Compra')->find($producto_id);

        if ($entity != null) {

            $entity->setNombre($nombre);
            $entity->setTitulo($titulo);
            $entity->setDescripcion($descripcion);
            $entity->setEstado($estado);
            $entity->setStock($stock);
            $entity->setPrecio($precio);
            $entity->setPrecioOferta($precioOferta);
            $entity->setMostrarPrecio($mostrarPrecio);
            $entity->setTags($tags);

            if ($fecha != "") {
                $fecha = \DateTime::createFromFormat('d/m/Y H:i', $fecha);
                $entity->setFechapublicacion($fecha);
            }

            $categoria = $em->find('IcanBundle:Categoria', $categoria_id);
            if ($categoria != null) {
                $entity->setCategoria($categoria);
            }

            $marca = $em->find('IcanBundle:Marca', $marca_id);
            if ($marca != null) {
                $entity->setMarca($marca);
            }

            //Hacer Url
            $url = $this->HacerUrl($nombre);
            $i = 1;
            $paux = $this->getDoctrine()->getRepository('IcanBundle:Compra')
                ->BuscarPorUrl($url);
            while (!empty($paux) && $paux->getCompraId() != $producto_id) {
                $url = $this->HacerUrl($nombre) . "-" . $i;
                $paux = $this->getDoctrine()->getRepository('IcanBundle:Compra')
                    ->BuscarPorUrl($url);
                $i++;
            }

            $entity->setUrl($url);

            if ($imagen != "") {
                $foto_eliminar = $entity->getImagen();
                if ($imagen != $foto_eliminar) {
                    //Eliminar foto                
                    if ($foto_eliminar != "") {
                        $dir = 'uploads/productos/';
                        if (is_file($dir . $foto_eliminar)) {
                            unlink($dir . $foto_eliminar);
                            unlink($dir . "portada-" . $foto_eliminar);
                            unlink($dir . "thumb-" . $foto_eliminar);
                        }
                    }
                    $imagen = $this->RenombrarImagen($url, $imagen);
                    $entity->setImagen($imagen);
                }
            }

            if ($imagenes != "") {
                $imagenes = explode(',', $imagenes);
                $cont = 1;
                foreach ($imagenes as $value) {
                    if ($value != "") {

                        $productoimagen = $this->getDoctrine()->getRepository('IcanBundle:CompraImagen')
                            ->findOneBy(
                                array('imagen' => $value)
                            );
                        if ($productoimagen == null) {
                            $value = $this->RenombrarImagen($url, $value, $cont);

                            $productoimagen = new Entity\CompraImagen();

                            $productoimagen->setCompra($entity);
                            $productoimagen->setImagen($value);

                            $em->persist($productoimagen);
                        }
                        $cont++;
                    }
                }
            }

            //Relacionados
            if (count($productos) > 0) {
                foreach ($productos as $value) {
                    $relacionado_id = $value['producto_id'];
                    $productorelacion_id = $value['productorelacion_id'];

                    $productoRelacion = $this->getDoctrine()->getRepository('IcanBundle:Compra')
                        ->find($relacionado_id);
                    if ($productoRelacion != null) {

                        $productorelacion = $this->getDoctrine()->getRepository('IcanBundle:CompraRelacion')
                            ->find($productorelacion_id);
                        if ($productorelacion == null) {
                            $productorelacion = new Entity\CompraRelacion();

                            $productorelacion->setCompra($entity);
                            $productorelacion->setCompraRelacion($productoRelacion);

                            $em->persist($productorelacion);

                            $productorelacion2 = new Entity\CompraRelacion();

                            $productorelacion2->setCompra($productoRelacion);
                            $productorelacion2->setCompraRelacion($entity);

                            $em->persist($productorelacion2);
                        }
                    }
                }
            }


            $em->flush();
            $resultado['success'] = true;
        } else {
            $resultado['success'] = false;
            $resultado['error'] = "No existe el registro solicitado";
        }
        return $resultado;
    }

    /**
     * SalvarCompra: Guarda los datos del usuario en la BD
     *
     *
     * @author Marcel
     */
    public function SalvarCompra($categoria_id, $marca_id, $nombre, $titulo, $descripcion, $tags, $stock, $precio,
                                 $precioOferta, $mostrarPrecio, $estado, $fecha, $imagen, $imagenes, $productos) {
        $em = $this->getDoctrine()->getManager();
        $resultado = array();

        $entity = new Entity\Compra();

        $entity->setMonto($nombre);
        $entity->setTitulo($titulo);
        $entity->setDescripcion($descripcion);
        $entity->setEstado($estado);
        $entity->setStock($stock);
        $entity->setPrecio($precio);
        $entity->setPrecioOferta($precioOferta);
        $entity->setMostrarPrecio($mostrarPrecio);
        $entity->setTags($tags);

        if ($fecha != "") {
            $fecha = \DateTime::createFromFormat('d/m/Y H:i', $fecha);
            $entity->setFechaCompra($fecha);
        }

        $categoria = $em->find('IcanBundle:Categoria', $categoria_id);
        if ($categoria != null) {
            $entity->setCategoria($categoria);
        }

        $marca = $em->find('IcanBundle:Marca', $marca_id);
        if ($marca != null) {
            $entity->setMarca($marca);
        }

        //Hacer Url        
        $url = $this->HacerUrl($nombre);
        $i = 1;
        $paux = $this->getDoctrine()->getRepository('IcanBundle:Compra')
            ->BuscarPorUrl($url);
        while (!empty($paux)) {
            $url = $this->HacerUrl($nombre) . "-" . $i;
            $paux = $this->getDoctrine()->getRepository('IcanBundle:Compra')
                ->BuscarPorUrl($url);
            $i++;
        }

        $entity->setUrl($url);

        $em->persist($entity);
        $em->flush();

        //Salvar imagen
        $imagen = $this->RenombrarImagen($url, $imagen);
        $entity->setImagen($imagen);

        if ($imagenes != "") {
            $imagenes = explode(',', $imagenes);
            $cont = 1;
            foreach ($imagenes as $value) {
                $value = $this->RenombrarImagen($url, $value, $cont);
                if ($value != "") {
                    $productoimagen = new Entity\CompraImagen();
                    $productoimagen->setCompra($entity);
                    $productoimagen->setImagen($value);
                    $em->persist($productoimagen);

                    $cont++;
                }
            }
        }

        //Relacionados
        if (count($productos) > 0) {
            foreach ($productos as $value) {
                $relacionado_id = $value['producto_id'];

                $productoRelacion = $this->getDoctrine()->getRepository('IcanBundle:Compra')
                    ->find($relacionado_id);
                if ($productoRelacion != null) {
                    $productorelacion = new Entity\CompraRelacion();

                    $productorelacion->setCompra($entity);
                    $productorelacion->setCompraRelacion($productoRelacion);

                    $em->persist($productorelacion);

                    $productorelacion2 = new Entity\CompraRelacion();

                    $productorelacion2->setCompra($productoRelacion);
                    $productorelacion2->setCompraRelacion($entity);

                    $em->persist($productorelacion2);
                }
            }
        }

        $em->flush();
        $resultado['success'] = true;

        return $resultado;
    }

    /**
     * RenombrarImagen: Renombra la imagen en la BD
     *
     * @author Marcel
     */
    public function RenombrarImagen($id, $imagen, $cont = 0) {
        $dir = 'uploads/productos/';
        $imagen_new = "";

        if ($imagen != "") {
            $extension_array = explode('.', $imagen);
            $extension = $extension_array[1];


            if ($cont == 0) {
                //Imagen nueva
                $imagen_new = $id . '.' . $extension;
                if (is_file($dir . $imagen)) {
                    //Renombrar imagen
                    rename($dir . $imagen, $dir . $imagen_new);
                    //Mover imagen small
                    rename($dir . 'portada-' . $imagen, $dir . 'portada-' . $imagen_new);
                    rename($dir . 'thumb-' . $imagen, $dir . 'thumb-' . $imagen_new);
                }
            } else {
                //Imagen nueva
                $imagen_new = '-' . $id . '-' . $cont . '.' . $extension;
                if (is_file($dir . $imagen)) {
                    //Renombrar imagen
                    rename($dir . $imagen, $dir . $imagen_new);
                    //Mover imagen small
                    rename($dir . 'portada-' . $imagen, $dir . 'portada-' . $imagen_new);
                    rename($dir . 'thumb-' . $imagen, $dir . 'thumb-' . $imagen_new);
                }
            }
        }

        return $imagen_new;
    }

    /**
     * ListarCompras: Listar los usuarios
     *
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarComprasParaRelacionados($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $categoria_id, $marca_id, $productos_id) {
        $arreglo_resultado = array();
        $cont = 0;

        $lista = $this->getDoctrine()->getRepository('IcanBundle:Compra')
            ->ListarComprasParaRelacionados($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $categoria_id, $marca_id, $productos_id);

        foreach ($lista as $value) {
            $producto_id = $value->getCompraId();

            $nombre = $value->getNombre();
            $categoria = ($value->getCategoria() != null) ? $value->getCategoria()->getNombre() : "";
            $marca = ($value->getMarca() != null) ? $value->getMarca()->getNombre() : "";
            $estado = ($value->getEstado()) ? 1 : 0;
            $precio = number_format($value->getPrecio(), 0, ',', '.');
            $fecha = $value->getFechapublicacion() != "" ? $value->getFechapublicacion()->format("d/m/Y H:i") : "";
            $views = $value->getViews();

            $ruta = $this->ObtenerURL();
            $dir = 'uploads/productos/';
            $imagen = $ruta . $dir . $value->getImagen();


            $acciones = '<a href="javascript:;" class="add importar-producto m-portlet__nav-link btn m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill" title="Agregar producto relacionado"
                         data-id="' . $producto_id . '" data-nombre="' . $nombre . '" data-categoria="' . $categoria . '"
                         data-marca="' . $marca . '" data-estado="' . $estado . '" data-imagen="' . $imagen . '"
                         data-precio="' . $precio . '" data-fecha="' . $fecha . '" data-views="' . $views . '"> <i class="la la-plus"></i> </a> ';

            $arreglo_resultado[$cont] = array(
                "id" => $producto_id,
                "nombre" => $nombre,
                "categoria" => $categoria,
                "marca" => $marca,
                "estado" => $estado,
                "imagen" => $imagen,
                "precio" => $precio,
                "fechapublicacion" => $fecha,
                "views" => $views,
                "acciones" => $acciones
            );

            $cont++;
        }

        return $arreglo_resultado;
    }

    /**
     * TotalCompras: Total de usuarios
     * @param string $sSearch Para buscar
     * @author Marcel
     */
    public function TotalComprasParaRelacionados($sSearch, $categoria_id, $marca_id, $productos_id) {
        $total = $this->getDoctrine()->getRepository('IcanBundle:Compra')
            ->TotalComprasParaRelacionados($sSearch, $categoria_id, $marca_id, $productos_id);

        return $total;
    }

    /**
     * ListarCompras: Listar las productos
     *
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarCompras($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $categoria_id, $marca_id) {
        $arreglo_resultado = array();
        $cont = 0;
        $lista = $this->getDoctrine()->getRepository('IcanBundle:Compra')->ListarCompras($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $categoria_id, $marca_id);

        foreach ($lista as $value) {
            $producto_id = $value->getCompraId();
            $acciones = $this->ListarAcciones($producto_id);
            $arreglo_resultado[$cont] = array(
                "id" => $producto_id,
                "estado" => ($value->getEstado()) ? 1 : 0,
                "precio" => number_format($value->getPrecio(), 0, ',', '.'),
                "fechaCompra" => $value->getFechaCompra() != "" ? $value->getFechaCompra()->format("d/m/Y H:i") : "",
                "acciones" => $acciones
            );
            $cont++;
        }
        return $arreglo_resultado;
    }

    /**
     * TotalCompras: Total de usuarios
     * @param string $sSearch Para buscar
     * @author Marcel
     */
    public function TotalCompras($sSearch, $categoria_id, $marca_id) {
        $total = $this->getDoctrine()->getRepository('IcanBundle:Compra')
            ->TotalCompras($sSearch, $categoria_id, $marca_id);

        return $total;
    }

    /**
     * ListarCategoriasArbol: Lista las categoria para el select en forma de arbol de la BD
     * @author Marcel
     */
    public function ListarCategoriasArbol($estado = "1") {
        $tree = array();
        //Categoria Padres
        $categoria_padres = $this->getDoctrine()->getRepository('IcanBundle:Categoria')
            ->ListarPadres($estado);
        //Resto de categoria
        $categoria_hijos = $this->getDoctrine()->getRepository('IcanBundle:Categoria')
            ->ListarHijos($estado);

        for ($i = 0; $i < count($categoria_padres); $i++) {

            $value = $categoria_padres[$i];
            $categoria_id = $value->getCategoriaId();

            $subcategoria = $this->getDoctrine()->getRepository('IcanBundle:Categoria')
                ->ListarCategoriasDelPadre($categoria_id, $estado);
            $class = (count($subcategoria) > 0) ? "optionGroup" : "";

            $element = array(
                'categoria_id' => $categoria_id,
                'categoria_padre_id' => "",
                'descripcion' => $value->getNombre(),
                'class' => $class
            );

            array_push($tree, $element);

            $tree = $this->getChilds($tree, $categoria_hijos, $categoria_id, 0, $categoria_id);

        }

        return $tree;
    }

    public function getChilds($tree, $categoria_hijos, $master_id, $class_count = 0, $categoria_padre_id) {
        $class_count = $class_count + 1;

        for ($i = 0; $i < count($categoria_hijos); $i++) {

            $value = $categoria_hijos[$i];

            if ($value->getCategoriaPadre()->getCategoriaId() == $master_id) {

                $hijos = $this->getDoctrine()->getRepository('IcanBundle:Categoria')
                    ->ListarCategoriasDelPadre($value->getCategoriaId(), "");
                $class_padre_hijo = (count($hijos) > 0) ? "optionGroup" : "";

                $element = array(
                    'categoria_id' => $value->getCategoriaId(),
                    'categoria_padre_id' => $categoria_padre_id,
                    'descripcion' => $value->getNombre(),
                    'class' => "optionChild$class_count $class_padre_hijo"
                );

                array_push($tree, $element);

                $tree = $this->getChilds($tree, $categoria_hijos, $value->getCategoriaId(), $class_count, $categoria_padre_id);
            }
        }

        return $tree;
    }

    /**
     * ListarAcciones: Lista los permisos de un usuario de la BD
     *
     * @author Marcel
     */
    public function ListarAcciones($id) {

        $acciones = "";

        $acciones .= '<a href="javascript:;" class="edit m-portlet__nav-link btn m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill" title="Editar registro" data-id="' . $id . '"> <i class="la la-edit"></i> </a> ';
        $acciones .= ' <a href="javascript:;" class="delete m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="Eliminar registro" data-id="' . $id . '"><i class="la la-trash"></i></a>';

        return $acciones;
    }

}
