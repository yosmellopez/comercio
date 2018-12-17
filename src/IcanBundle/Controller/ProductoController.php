<?php

namespace IcanBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use IcanBundle\Entity;
use IcanBundle\Util;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

class ProductoController extends BaseController
{

    public function indexAction()
    {
        $categorias = $this->ListarCategoriasArbol();

        $marcas = $this->getDoctrine()->getRepository('IcanBundle:Marca')
            ->ListarOrdenadas();

        $porcientos = $this->getDoctrine()->getRepository('IcanBundle:Porciento')
            ->ListarOrdenados();

        $ruta = $this->ObtenerURL();
        $dir = 'uploads/productos/';

        return $this->render('IcanBundle:Producto:index.html.twig', array(
            'categorias' => $categorias,
            'marcas' => $marcas,
            'porcientos' => $porcientos,
            'ruta' => $ruta . $dir
        ));
    }

    /**
     * listarAction Acción que lista los marcas
     *
     */
    public function listarAction(Request $request)
    {
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
            $total = $this->TotalProductos($sSearch, $categoria_id, $marca_id);
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

            $data = $this->ListarProductos($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $categoria_id, $marca_id);

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
    public function listarParaRelacionadosAction(Request $request)
    {
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
            $total = $this->TotalProductosParaRelacionados($sSearch, $categoria_id, $marca_id, $productos_id);
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

            $data = $this->ListarProductosParaRelacionados($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $categoria_id, $marca_id, $productos_id);

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
     * listarParaCotizacionAction Acción que lista los productos para cotizacion
     *
     */
    public function listarParaCotizacionAction(Request $request)
    {
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
            $total = $this->TotalProductosParaCotizacion($sSearch, $categoria_id, $marca_id, $productos_id);
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

            $data = $this->ListarProductosParaCotizacion($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $categoria_id, $marca_id, $productos_id);

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
     * listarParaPromocionAction Acción que lista los productos para promocion
     *
     */
    public function listarParaPromocionAction(Request $request)
    {
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
            $total = $this->TotalProductosParaPromocion($sSearch, $categoria_id, $marca_id, $productos_id);
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

            $data = $this->ListarProductosParaPromocion($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $categoria_id, $marca_id, $productos_id);

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
    public function salvarAction(Request $request)
    {
        $producto_id = $request->get('producto_id');

        $categoria_id = $request->get('categoria');
        $marca_id = $request->get('marca');
        $porciento = $request->get('porciento');

        $nombre = $request->get('nombre');
        $titulo = $request->get('titulo');
        $descripcion = $request->get('descripcion');
        $caracteristica = $request->get('caracteristica');
        $tags = $request->get('tags');
        $stock = $request->get('stock');
        $precio = $request->get('precio');
        $precioEspecial = $request->get('precioEspecial');
        $destacado = $request->get('destacado');
        $estado = $request->get('estado');
        $fecha = $request->get('fecha');
        $imagen = $request->get('imagen');
        $imagenes = $request->get('imagenes');

        $disponibilidadInmediata = $request->get('disponibilidadInmediata');
        $formaPago = $request->get('formaPago');
        $terminoDespacho = $request->get('terminoDespacho');

        $productos = $request->get('productos');
        $productos = json_decode($productos, true);

        $deleteficha = $request->get('deleteficha');

        $archivo = "";
        if (count($_FILES) > 0) {
            $imagen_nombre = $_FILES['file']['name'];
            if ($imagen_nombre != "") {
                if ($_FILES['file']['type'] == 'image/jpeg' || $_FILES['file']['type'] == 'image/x-jpeg' || $_FILES['file']['type'] == 'image/jpg' || $_FILES['file']['type'] == 'image/x-jpg' || $_FILES['file']['type'] == 'image/png' ||
                    $_FILES['file']['type'] == 'image/x-png' || $_FILES['file']['type'] == 'application/pdf' || $_FILES['file']['type'] == 'application/msword' || $_FILES['file']['type'] == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {

                    $array_extension = explode('/', $_FILES['file']['type']);
                    $extension = "";
                    switch ($array_extension[1]) {
                        case 'jpeg':
                            $extension = "jpeg";
                            break;
                        case 'x-jpeg':
                            $extension = "jpeg";
                            break;
                        case 'jpg':
                            $extension = "jpg";
                            break;
                        case 'x-jpg':
                            $extension = "jpg";
                            break;
                        case 'png':
                            $extension = "png";
                            break;
                        case 'x-png':
                            $extension = "png";
                            break;
                        case 'pdf':
                            $extension = "pdf";
                            break;
                        case 'msword':
                            $extension = "doc";
                            break;
                        default:
                            $extension = "docx";
                            break;
                    }

                    $archivo = $this->generarCadenaAleatoria() . '.' . $extension;

                    //Manejar la imagen
                    $dir = 'uploads/productos/';
                    $archivo_tmp = $_FILES['file']['tmp_name'];
                    move_uploaded_file($archivo_tmp, $dir . $archivo);
                } else {
                    $resultadoJson['success'] = false;
                    $resultadoJson['error'] = "Verifique que el archivo seleccionado sea un documento o una imagen";
                    return new Response(json_encode($resultadoJson));
                }
            }
        }

        $resultadoJson = array();
        if ($producto_id == "") {
            $resultado = $this->SalvarProducto($categoria_id, $marca_id, $nombre, $titulo, $descripcion, $tags, $stock, $precio,
                $destacado, $estado, $fecha, $imagen, $imagenes, $productos, $caracteristica, $porciento,
                $precioEspecial, $archivo, $disponibilidadInmediata, $formaPago, $terminoDespacho);
        } else {
            $resultado = $this->ActualizarProducto($producto_id, $categoria_id, $marca_id, $nombre, $titulo, $descripcion, $tags, $stock, $precio,
                $destacado, $estado, $fecha, $imagen, $imagenes, $productos, $caracteristica, $porciento,
                $precioEspecial, $archivo, $deleteficha, $disponibilidadInmediata, $formaPago, $terminoDespacho);
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

    public function salvarImagenAction()
    {
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

            $resultadoJson['success'] = true;

            $resultadoJson['name'] = $archivo;
            $resultadoJson['size'] = filesize($dir . $archivo);

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
    public function eliminarAction(Request $request)
    {
        $producto_id = $request->get('producto_id');

        $resultado = $this->EliminarProducto($producto_id);
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
     * eliminarProductosAction Acción que elimina varios productos en la BD
     *
     */
    public function eliminarProductosAction(Request $request)
    {
        $ids = $request->get('ids');

        $resultado = $this->EliminarProductos($ids);
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
    public function cargarDatosAction(Request $request)
    {
        $producto_id = $request->get('producto_id');

        $resultado = $this->CargarDatosProducto($producto_id);
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
    public function eliminarImagenAction(Request $request)
    {
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
    public function eliminarRelacionadoAction(Request $request)
    {
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
     * descargarFichaAction Acción que descarga la ficha del producto
     *
     */
    public function descargarFichaAction($producto_id)
    {
        $resultado = $this->DescargarFicha($producto_id);
        if ($resultado['success']) {
            $path = $this->get('kernel')->getRootDir() . "/../web/uploads/productos/";
            $content = file_get_contents($path . $resultado['archivo']);

            $response = new Response();
            //set headers
            $response->headers->set('Content-Type', 'mime/type');
            $response->headers->set('Content-Disposition', 'attachment;filename="' . $resultado['archivo']);

            $response->setContent($content);
            return $response;
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * recortarImagenAction Acción que recorta una imagen en la BD
     *
     */
    public function recortarImagenAction()
    {
        $archivo = $_FILES['foto']['name'];
        $array_nombre_archivo = explode('.', $archivo);
        $pos = count($array_nombre_archivo) - 1;
        $extension = $array_nombre_archivo[$pos];

        //Manejar la imagen
        $dir = 'uploads/productos/';
        $archivo_tmp = $_FILES['foto']['tmp_name'];
        move_uploaded_file($archivo_tmp, $dir . $archivo);

        $resultadoJson['success'] = true;

        $resultadoJson['name'] = $archivo;
        $resultadoJson['size'] = filesize($dir . $archivo);

        return new Response(json_encode($resultadoJson));
    }

    /**
     * DescargarFicha: Descarga la ficha del producto
     * @param int $producto_id
     * @author Marcel
     */
    public function DescargarFicha($producto_id)
    {
        $resultado = array();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Producto')
            ->find($producto_id);

        if (!is_null($entity)) {
            $archivo = $entity->getFicha();

            $resultado['success'] = true;
            $resultado['archivo'] = $archivo;
        } else {
            $resultado['success'] = false;
            $resultado['error'] = "No existe un producto que se corresponda con ese identificador";
        }
        return $resultado;
    }

    /**
     * EliminarRelacionado: Elimina un producto relacionado en la BD
     * @param int $productorelacion_id Id
     * @author Marcel
     */
    public function EliminarRelacionado($productorelacion_id)
    {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:ProductoRelacion')
            ->find($productorelacion_id);

        if ($entity != null) {

            $producto_id = $entity->getProducto()->getProductoId();
            $producto_relacionado_id = $entity->getProductoRelacion()->getProductoId();

            $em->remove($entity);

            //Eliminar la relacion inversa
            $relacion = $this->getDoctrine()->getRepository('IcanBundle:ProductoRelacion')
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
    public function EliminarImagen($imagen)
    {
        $resultado = array();
        //Eliminar foto       
        if ($imagen != "") {
            $dir = 'uploads/productos/';
            if (is_file($dir . $imagen)) {
                unlink($dir . $imagen);
                //unlink($dir . "portada-" . $imagen);
                //unlink($dir . "thumb-" . $imagen);
            }
        }

        $em = $this->getDoctrine()->getManager();
        $productoimagen = $this->getDoctrine()->getRepository('IcanBundle:ProductoImagen')
            ->findOneBy(
                array('imagen' => $imagen)
            );
        if ($productoimagen != null) {
            $em->remove($productoimagen);
        }

        $producto = $this->getDoctrine()->getRepository('IcanBundle:Producto')
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
     * CargarDatosProducto: Carga los datos de un usuario
     *
     * @param int $producto_id Id
     *
     * @author Marcel
     */
    public function CargarDatosProducto($producto_id)
    {
        $resultado = array();
        $arreglo_resultado = array();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Producto')
            ->find($producto_id);
        if ($entity != null) {

            $arreglo_resultado['nombre'] = $entity->getNombre();
            $arreglo_resultado['categoria'] = ($entity->getCategoria() != null) ? $entity->getCategoria()->getCategoriaId() : "";
            $arreglo_resultado['marca'] = ($entity->getMarca() != null) ? $entity->getMarca()->getMarcaId() : "";
            $arreglo_resultado['porciento'] = ($entity->getPorciento() != null) ? $entity->getPorciento()->getValor() : "";
            $arreglo_resultado['titulo'] = $entity->getTitulo();
            $arreglo_resultado['descripcion'] = $entity->getDescripcion();
            $arreglo_resultado['caracteristica'] = $entity->getCaracteristica();
            $arreglo_resultado['tags'] = $entity->getTags();
            $arreglo_resultado['stock'] = $entity->getStock();
            $arreglo_resultado['precio'] = $entity->getPrecio();
            $arreglo_resultado['precioEspecial'] = $entity->getPrecioEspecial();
            $arreglo_resultado['destacado'] = ($entity->getDestacado() == 1) ? true : false;
            $arreglo_resultado['estado'] = ($entity->getEstado() == 1) ? true : false;
            $arreglo_resultado['url'] = $entity->getUrl();
            $arreglo_resultado['ficha'] = $entity->getFicha();
            $arreglo_resultado['disponibilidadInmediata'] = ($entity->getDisponibilidadInmediata() == 1) ? true : false;
            $arreglo_resultado['formaPago'] = $entity->getFormaPago();
            $arreglo_resultado['terminoDespacho'] = $entity->getTerminoDespacho();

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
            $productoimagenes = $this->getDoctrine()->getRepository('IcanBundle:ProductoImagen')->ListarImagenes($producto_id);
            $imagenes = array();
            foreach ($productoimagenes as $productoimagen) {

                $imagen = $productoimagen->getImagen();
                $size = (is_file($dir . $imagen)) ? filesize($dir . $imagen) : 0;

                array_push($imagenes, array(
                    'imagen' => $imagen,
                    'size' => $size,
                    'ruta' => $ruta,
                    'valida' => true
                ));
            }
            $arreglo_resultado['imagenes'] = $imagenes;

            //Productos relacionados
            $relacionados = array();
            $productos = $this->getDoctrine()->getRepository('IcanBundle:ProductoRelacion')
                ->ListarRelacionados($producto_id);
            $posicion = 0;
            foreach ($productos as $key => $producto_relacion) {
                $producto = $producto_relacion->getProductoRelacion();
                if ($producto->getProductoId() != $producto_id) {
                    array_push($relacionados, array(
                        'productorelacion_id' => $producto_relacion->getProductorelacionId(),
                        'producto_id' => $producto->getProductoId(),
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
     * EliminarProducto: Elimina un producto en la BD
     * @param int $producto_id Id
     * @author Marcel
     */
    public function EliminarProducto($producto_id)
    {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Producto')
            ->find($producto_id);

        if ($entity != null) {

            //Cotizaciones
            $cotizaciones = $this->getDoctrine()->getRepository('IcanBundle:CotizacionProducto')
                ->ListarCotizaciones($producto_id);
            if (count($cotizaciones) > 0) {
                $resultado['success'] = false;
                $resultado['error'] = "No se pudo eliminar el producto, porque tiene cotizaciones asociadas";
                return $resultado;
            }

            //Eliminar foto
            $foto_eliminar = $entity->getImagen();
            if ($foto_eliminar != "") {
                $dir = 'uploads/productos/';
                if (is_file($dir . $foto_eliminar)) {
                    unlink($dir . $foto_eliminar);
                    //unlink($dir . "portada-" . $foto_eliminar);
                    //unlink($dir . "thumb-" . $foto_eliminar);
                }
            }
            //Eliminar ficha
            $ficha_eliminar = $entity->getFicha();
            if ($ficha_eliminar != "") {
                $dir = 'uploads/productos/';
                if (is_file($dir . $ficha_eliminar)) {
                    unlink($dir . $ficha_eliminar);
                }
            }

            //Eliminar las imagenes
            $productoimagenes = $this->getDoctrine()->getRepository('IcanBundle:ProductoImagen')
                ->ListarImagenes($producto_id);
            foreach ($productoimagenes as $productoimagen) {
                //Eliminar foto
                $foto_eliminar = $productoimagen->getImagen();
                if ($foto_eliminar != "") {
                    $dir = 'uploads/productos/';
                    if (is_file($dir . $foto_eliminar)) {
                        unlink($dir . $foto_eliminar);
                        //unlink($dir . "portada-" . $foto_eliminar);
                        //unlink($dir . "thumb-" . $foto_eliminar);
                    }
                }
                $em->remove($productoimagen);
            }

            //Eliminar los productos relacionados
            $relacionados = $this->getDoctrine()->getRepository('IcanBundle:ProductoRelacion')
                ->ListarRelacionados($producto_id);
            foreach ($relacionados as $relacionado) {
                $em->remove($relacionado);
            }

            //Eliminar los productos relacionados
            $productorelacionados = $this->getDoctrine()->getRepository('IcanBundle:ProductoRelacion')
                ->ListarProductosRelacionado($producto_id);
            foreach ($productorelacionados as $relacionado) {
                $em->remove($relacionado);
            }

            //Eliminar vistas
            $producto_views = $this->getDoctrine()->getRepository('IcanBundle:ProductoView')
                ->ListarViewsDeProducto($producto_id);
            foreach ($producto_views as $producto_view) {
                $em->remove($producto_view);
            }

            //Eliminar descuentos
            $descuentos = $this->getDoctrine()->getRepository('IcanBundle:DescuentoProducto')
                ->ListarDescuentos($producto_id);
            foreach ($descuentos as $descuento) {
                $em->remove($descuento);
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
     * EliminarProductos: Elimina varios productos en la BD
     * @param array $$ids Ids
     * @author Marcel
     */
    public function EliminarProductos($ids)
    {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        if ($ids != "") {
            $ids = explode(',', $ids);
            $cant_total = 0;
            $cant_eliminada = 0;
            foreach ($ids as $producto_id) {
                if ($producto_id != "") {
                    $cant_total++;
                    $entity = $this->getDoctrine()->getRepository('IcanBundle:Producto')
                        ->find($producto_id);
                    if ($entity != null) {

                        //Cotizaciones
                        $cotizaciones = $this->getDoctrine()->getRepository('IcanBundle:CotizacionProducto')
                            ->ListarCotizaciones($producto_id);
                        if (count($cotizaciones) == 0) {
                            //Eliminar foto
                            $foto_eliminar = $entity->getImagen();
                            if ($foto_eliminar != "") {
                                $dir = 'uploads/productos/';
                                if (is_file($dir . $foto_eliminar)) {
                                    unlink($dir . $foto_eliminar);
                                    //unlink($dir . "portada-" . $foto_eliminar);
                                    //unlink($dir . "thumb-" . $foto_eliminar);
                                }
                            }
                            //Eliminar ficha
                            $ficha_eliminar = $entity->getFicha();
                            if ($ficha_eliminar != "") {
                                $dir = 'uploads/productos/';
                                if (is_file($dir . $ficha_eliminar)) {
                                    unlink($dir . $ficha_eliminar);
                                }
                            }

                            //Eliminar las imagenes
                            $productoimagenes = $this->getDoctrine()->getRepository('IcanBundle:ProductoImagen')
                                ->ListarImagenes($producto_id);
                            foreach ($productoimagenes as $productoimagen) {
                                //Eliminar foto
                                $foto_eliminar = $productoimagen->getImagen();
                                if ($foto_eliminar != "") {
                                    $dir = 'uploads/productos/';
                                    if (is_file($dir . $foto_eliminar)) {
                                        unlink($dir . $foto_eliminar);
                                        //unlink($dir . "portada-" . $foto_eliminar);
                                        //unlink($dir . "thumb-" . $foto_eliminar);
                                    }
                                }
                                $em->remove($productoimagen);
                            }

                            //Eliminar los productos relacionados
                            $relacionados = $this->getDoctrine()->getRepository('IcanBundle:ProductoRelacion')
                                ->ListarRelacionados($producto_id);
                            foreach ($relacionados as $relacionado) {
                                $em->remove($relacionado);
                            }

                            //Eliminar los productos relacionados
                            $productorelacionados = $this->getDoctrine()->getRepository('IcanBundle:ProductoRelacion')
                                ->ListarProductosRelacionado($producto_id);
                            foreach ($productorelacionados as $relacionado) {
                                $em->remove($relacionado);
                            }

                            //Eliminar vistas
                            $producto_views = $this->getDoctrine()->getRepository('IcanBundle:ProductoView')
                                ->ListarViewsDeProducto($producto_id);
                            foreach ($producto_views as $producto_view) {
                                $em->remove($producto_view);
                            }

                            //Eliminar descuentos
                            $descuentos = $this->getDoctrine()->getRepository('IcanBundle:DescuentoProducto')
                                ->ListarDescuentos($producto_id);
                            foreach ($descuentos as $descuento) {
                                $em->remove($descuento);
                            }

                            $em->remove($entity);
                            $cant_eliminada++;
                        }

                    }
                }
            }
        }
        $em->flush();
        if ($cant_eliminada == 0) {
            $resultado['success'] = false;
            $resultado['error'] = "No se pudo eliminar los productos, porque tienen cotizaciones asociadas";
        } else {
            $resultado['success'] = true;

            $mensaje = ($cant_eliminada == $cant_total) ? "La operación se ha realizado correctamente" : "La operación se ha realizado correctamente. Pero atención no se pudo eliminar todos los productos asociados porque tienen cotizaciones asociadas";
            $resultado['message'] = $mensaje;
        }
        return $resultado;
    }

    /**
     * ActualizarProducto: Actualiza los datos del producto en la BD
     *
     * @param string $producto_id Id
     *
     * @author Marcel
     */
    public function ActualizarProducto($producto_id, $categoria_id, $marca_id, $nombre, $titulo, $descripcion, $tags, $stock, $precio,
                                       $destacado, $estado, $fecha, $imagen, $imagenes, $productos, $caracteristica, $porciento,
                                       $precioEspecial, $archivo, $deleteficha, $disponibilidadInmediata, $formaPago, $terminoDespacho)
    {
        $em = $this->getDoctrine()->getManager();

        $resultado = array();
        $entity = $this->getDoctrine()->getRepository('IcanBundle:Producto')->find($producto_id);

        if ($entity != null) {

            $entity->setNombre($nombre);
            $entity->setTitulo($titulo);
            $entity->setDescripcion($descripcion);
            $entity->setCaracteristica($caracteristica);
            $entity->setEstado($estado);
            $entity->setStock($stock);
            $entity->setPrecio($precio);
            $entity->setDestacado($destacado);
            $entity->setTags($tags);
            $entity->setDisponibilidadInmediata($disponibilidadInmediata);
            $entity->setFormaPago($formaPago);
            $entity->setTerminoDespacho($terminoDespacho);

            if ($precioEspecial != "") {
                $entity->setPrecioEspecial($precioEspecial);
            }

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

            if ($porciento != "") {
                $porciento = $this->getDoctrine()->getRepository('IcanBundle:Porciento')
                    ->findOneBy(array('valor' => $porciento));
                if ($porciento != null) {
                    $entity->setPorciento($porciento);
                }
            } else {
                $entity->setPorciento(NULL);
                $entity->setPromocion(NULL);
            }

            //Hacer Url
            $url = $this->HacerUrl($nombre);
            $i = 1;
            $paux = $this->getDoctrine()->getRepository('IcanBundle:Producto')
                ->BuscarPorUrl($url);
            while (!empty($paux) && $paux->getProductoId() != $producto_id) {
                $url = $this->HacerUrl($nombre) . "-" . $i;
                $paux = $this->getDoctrine()->getRepository('IcanBundle:Producto')
                    ->BuscarPorUrl($url);
                $i++;
            }

            $entity->setUrl($url);

            //stock 0 si estado es 0
            if ($estado != 1) {
                $entity->setStock(0);
            }

            if ($imagen != "") {
                $foto_eliminar = $entity->getImagen();
                if ($imagen != $foto_eliminar) {
                    //Eliminar foto                
                    if ($foto_eliminar != "") {
                        $dir = 'uploads/productos/';
                        if (is_file($dir . $foto_eliminar)) {
                            unlink($dir . $foto_eliminar);
                            //unlink($dir . "portada-" . $foto_eliminar);
                            //unlink($dir . "thumb-" . $foto_eliminar);
                        }
                    }
                    $imagen = $this->RenombrarImagen($url, $imagen);
                    $entity->setImagen($imagen);
                }
            }

            if ($archivo != "") {
                $ficha_eliminar = $entity->getFicha();
                if ($archivo != $ficha_eliminar) {
                    //Eliminar ficha
                    if ($ficha_eliminar != "") {
                        $dir = 'uploads/productos/';
                        if (is_file($dir . $ficha_eliminar)) {
                            unlink($dir . $ficha_eliminar);
                        }
                    }
                    $ficha = $this->RenombrarFicha($url, $archivo);
                    $entity->setFicha($ficha);
                }
            }

            //Eliminar la ficha del producto
            if ($deleteficha == 1) {
                $ficha_eliminar = $entity->getFicha();
                if ($ficha_eliminar != "") {
                    $dir = 'uploads/productos/';
                    if (is_file($dir . $ficha_eliminar)) {
                        unlink($dir . $ficha_eliminar);
                    }
                }
                $entity->setFicha("");
            }

            if ($imagenes != "") {
                $imagenes = explode(',', $imagenes);
                $cont = 1;
                foreach ($imagenes as $value) {
                    if ($value != "") {

                        $productoimagen = $this->getDoctrine()->getRepository('IcanBundle:ProductoImagen')
                            ->findOneBy(
                                array('imagen' => $value)
                            );
                        if ($productoimagen == null) {
                            $value = $this->RenombrarImagen($url, $value, $cont);

                            $productoimagen = new Entity\ProductoImagen();

                            $productoimagen->setProducto($entity);
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

                    $productoRelacion = $this->getDoctrine()->getRepository('IcanBundle:Producto')
                        ->find($relacionado_id);
                    if ($productoRelacion != null) {

                        $productorelacion = $this->getDoctrine()->getRepository('IcanBundle:ProductoRelacion')
                            ->find($productorelacion_id);
                        if ($productorelacion == null) {
                            $productorelacion = new Entity\ProductoRelacion();

                            $productorelacion->setProducto($entity);
                            $productorelacion->setProductoRelacion($productoRelacion);

                            $em->persist($productorelacion);

                            $productorelacion2 = new Entity\ProductoRelacion();

                            $productorelacion2->setProducto($productoRelacion);
                            $productorelacion2->setProductoRelacion($entity);

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
     * SalvarProducto: Guarda los datos del usuario en la BD
     *
     *
     * @author Marcel
     */
    public function SalvarProducto($categoria_id, $marca_id, $nombre, $titulo, $descripcion, $tags, $stock, $precio,
                                   $destacado, $estado, $fecha, $imagen, $imagenes, $productos, $caracteristica, $porciento,
                                   $precioEspecial, $archivo, $disponibilidadInmediata, $formaPago, $terminoDespacho)
    {
        $em = $this->getDoctrine()->getManager();
        $resultado = array();

        $entity = new Entity\Producto();

        $entity->setNombre($nombre);
        $entity->setTitulo($titulo);
        $entity->setDescripcion($descripcion);
        $entity->setCaracteristica($caracteristica);
        $entity->setEstado($estado);
        $entity->setStock($stock);
        $entity->setPrecio($precio);
        $entity->setDestacado($destacado);
        $entity->setTags($tags);
        $entity->setDisponibilidadInmediata($disponibilidadInmediata);
        $entity->setFormaPago($formaPago);
        $entity->setTerminoDespacho($terminoDespacho);

        if ($precioEspecial != "") {
            $entity->setPrecioEspecial($precioEspecial);
        }

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

        $porciento = $this->getDoctrine()->getRepository('IcanBundle:Porciento')
            ->findOneBy(array('valor' => $porciento));
        if ($porciento != null) {
            $entity->setPorciento($porciento);
        }

        //Hacer Url        
        $url = $this->HacerUrl($nombre);
        $i = 1;
        $paux = $this->getDoctrine()->getRepository('IcanBundle:Producto')
            ->BuscarPorUrl($url);
        while (!empty($paux)) {
            $url = $this->HacerUrl($nombre) . "-" . $i;
            $paux = $this->getDoctrine()->getRepository('IcanBundle:Producto')
                ->BuscarPorUrl($url);
            $i++;
        }

        $entity->setUrl($url);

        //stock 0 si estado es 0
        if ($estado != 1) {
            $entity->setStock(0);
        }


        $em->persist($entity);
        $em->flush();

        //Salvar imagen
        $imagen = $this->RenombrarImagen($url, $imagen);
        $entity->setImagen($imagen);

        //Salvar ficha
        $ficha = $this->RenombrarFicha($url, $archivo);
        $entity->setFicha($ficha);

        if ($imagenes != "") {
            $imagenes = explode(',', $imagenes);
            $cont = 1;
            foreach ($imagenes as $value) {
                $value = $this->RenombrarImagen($url, $value, $cont);
                if ($value != "") {
                    $productoimagen = new Entity\ProductoImagen();
                    $productoimagen->setProducto($entity);
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

                $productoRelacion = $this->getDoctrine()->getRepository('IcanBundle:Producto')
                    ->find($relacionado_id);
                if ($productoRelacion != null) {
                    $productorelacion = new Entity\ProductoRelacion();

                    $productorelacion->setProducto($entity);
                    $productorelacion->setProductoRelacion($productoRelacion);

                    $em->persist($productorelacion);

                    $productorelacion2 = new Entity\ProductoRelacion();

                    $productorelacion2->setProducto($productoRelacion);
                    $productorelacion2->setProductoRelacion($entity);

                    $em->persist($productorelacion2);
                }
            }
        }

        $em->flush();
        $resultado['success'] = true;

        return $resultado;
    }

    /**
     * RenombrarFicha: Renombra la ficha en la BD
     *
     * @author Marcel
     */
    public function RenombrarFicha($id, $ficha)
    {
        $dir = 'uploads/productos/';
        $ficha_new = "";

        if ($ficha != "") {
            $extension_array = explode('.', $ficha);
            $extension = $extension_array[1];

            //Ficha nueva
            $ficha_new = $id . '.' . $extension;
            if (is_file($dir . $ficha)) {
                //Renombrar ficha
                rename($dir . $ficha, $dir . $ficha_new);
            }
        }

        return $ficha_new;
    }

    /**
     * RenombrarImagen: Renombra la imagen en la BD
     *
     * @author Marcel
     */
    public function RenombrarImagen($id, $imagen, $cont = 0)
    {
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
                    //rename($dir . 'portada-' . $imagen, $dir . 'portada-' . $imagen_new);
                    //rename($dir . 'thumb-' . $imagen, $dir . 'thumb-' . $imagen_new);
                }
            } else {
                //Imagen nueva
                $imagen_new = $id . '-' . $cont . '.' . $extension;
                if (is_file($dir . $imagen)) {
                    //Renombrar imagen
                    rename($dir . $imagen, $dir . $imagen_new);
                    //Mover imagen small
                    //rename($dir . 'portada-' . $imagen, $dir . 'portada-' . $imagen_new);
                    //rename($dir . 'thumb-' . $imagen, $dir . 'thumb-' . $imagen_new);
                }
            }
        }

        return $imagen_new;
    }

    /**
     * ListarProductosParaPromocion: Listar los productos para generar promocion
     *
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarProductosParaPromocion($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $categoria_id, $marca_id, $productos_id)
    {
        $arreglo_resultado = array();
        $cont = 0;

        $lista = $this->getDoctrine()->getRepository('IcanBundle:Producto')
            ->ListarProductosParaPromocion($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $categoria_id, $marca_id, $productos_id);

        foreach ($lista as $value) {
            $producto_id = $value->getProductoId();

            $nombre = $value->getNombre();
            $categoria = ($value->getCategoria() != null) ? $value->getCategoria()->getNombre() : "";
            $marca = ($value->getMarca() != null) ? $value->getMarca()->getNombre() : "";
            $estado = ($value->getEstado()) ? 1 : 0;
            $precio = $value->getPrecio();
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
                "precio" => number_format($precio, 0, ',', '.'),
                "fechapublicacion" => $fecha,
                "views" => $views,
                "acciones" => $acciones
            );

            $cont++;
        }

        return $arreglo_resultado;
    }

    /**
     * TotalProductosParaPromocion: Total de productos para generar promocion
     * @param string $sSearch Para buscar
     * @author Marcel
     */
    public function TotalProductosParaPromocion($sSearch, $categoria_id, $marca_id, $productos_id)
    {
        $total = $this->getDoctrine()->getRepository('IcanBundle:Producto')
            ->TotalProductosParaPromocion($sSearch, $categoria_id, $marca_id, $productos_id);

        return $total;
    }

    /**
     * ListarProductosParaCotizacion: Listar los productos para generar cotizacion
     *
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarProductosParaCotizacion($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $categoria_id, $marca_id, $productos_id)
    {
        $arreglo_resultado = array();
        $cont = 0;

        $lista = $this->getDoctrine()->getRepository('IcanBundle:Producto')
            ->ListarProductosParaCotizacion($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $categoria_id, $marca_id, $productos_id);

        foreach ($lista as $value) {
            $producto_id = $value->getProductoId();

            $nombre = $value->getNombre();
            $categoria = ($value->getCategoria() != null) ? $value->getCategoria()->getNombre() : "";
            $marca = ($value->getMarca() != null) ? $value->getMarca()->getNombre() : "";
            $estado = ($value->getEstado()) ? 1 : 0;
            $precio = $value->getPrecio();
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
                "precio" => number_format($precio, 0, ',', '.'),
                "fechapublicacion" => $fecha,
                "views" => $views,
                "acciones" => $acciones
            );

            $cont++;
        }

        return $arreglo_resultado;
    }

    /**
     * TotalProductosParaCotizacion: Total de productos para generar cotizacion
     * @param string $sSearch Para buscar
     * @author Marcel
     */
    public function TotalProductosParaCotizacion($sSearch, $categoria_id, $marca_id, $productos_id)
    {
        $total = $this->getDoctrine()->getRepository('IcanBundle:Producto')
            ->TotalProductosParaCotizacion($sSearch, $categoria_id, $marca_id, $productos_id);

        return $total;
    }

    /**
     * ListarProductos: Listar los usuarios
     *
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarProductosParaRelacionados($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $categoria_id, $marca_id, $productos_id)
    {
        $arreglo_resultado = array();
        $cont = 0;

        $lista = $this->getDoctrine()->getRepository('IcanBundle:Producto')
            ->ListarProductosParaRelacionados($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $categoria_id, $marca_id, $productos_id);

        foreach ($lista as $value) {
            $producto_id = $value->getProductoId();

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
     * TotalProductos: Total de usuarios
     * @param string $sSearch Para buscar
     * @author Marcel
     */
    public function TotalProductosParaRelacionados($sSearch, $categoria_id, $marca_id, $productos_id)
    {
        $total = $this->getDoctrine()->getRepository('IcanBundle:Producto')
            ->TotalProductosParaRelacionados($sSearch, $categoria_id, $marca_id, $productos_id);

        return $total;
    }

    /**
     * ListarProductos: Listar las productos
     *
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarProductos($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $categoria_id, $marca_id)
    {
        $arreglo_resultado = array();
        $cont = 0;

        $lista = $this->getDoctrine()->getRepository('IcanBundle:Producto')
            ->ListarProductos($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $categoria_id, $marca_id);


        foreach ($lista as $value) {
            $producto_id = $value->getProductoId();

            $acciones = $this->ListarAcciones($producto_id);

            $ruta = $this->ObtenerURL();
            $dir = 'uploads/productos/';
            $imagen = $ruta . $dir . $value->getImagen();

            $acciones1 = '';
            $ficha = $value->getFicha();
            if ($ficha != "") {
                $acciones1 = ' <a href="' . $this->generateUrl('descargarFichaProducto', array('producto_id' => $producto_id)) . '" target="_blank" class="download m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Descargar ficha"><i class="la la-download"></i></a>';
            }

            $arreglo_resultado[$cont] = array(
                "id" => $producto_id,
                "nombre" => $value->getNombre(),
                "categoria" => ($value->getCategoria() != null) ? $value->getCategoria()->getNombre() : "",
                "marca" => ($value->getMarca() != null) ? $value->getMarca()->getNombre() : "",
                "estado" => ($value->getEstado()) ? 1 : 0,
                "imagen" => $imagen,
                "precio" => number_format($value->getPrecio(), 0, ',', '.'),
                "fechapublicacion" => $value->getFechapublicacion() != "" ? $value->getFechapublicacion()->format("d/m/Y H:i") : "",
                "views" => $value->getViews(),
                "acciones" => $acciones . $acciones1
            );

            $cont++;
        }

        return $arreglo_resultado;
    }

    /**
     * TotalProductos: Total de usuarios
     * @param string $sSearch Para buscar
     * @author Marcel
     */
    public function TotalProductos($sSearch, $categoria_id, $marca_id)
    {
        $total = $this->getDoctrine()->getRepository('IcanBundle:Producto')
            ->TotalProductos($sSearch, $categoria_id, $marca_id);

        return $total;
    }

    /**
     * ListarCategoriasArbol: Lista las categoria para el select en forma de arbol de la BD
     * @author Marcel
     */
    public function ListarCategoriasArbol($estado = "1")
    {
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

    public function getChilds($tree, $categoria_hijos, $master_id, $class_count = 0, $categoria_padre_id)
    {
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
    public function ListarAcciones($id)
    {

        $acciones = "";

        $acciones .= '<a href="javascript:;" class="edit m-portlet__nav-link btn m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill" title="Editar registro" data-id="' . $id . '"> <i class="la la-edit"></i> </a> ';
        $acciones .= ' <a href="javascript:;" class="delete m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" title="Eliminar registro" data-id="' . $id . '"><i class="la la-trash"></i></a>';

        return $acciones;
    }

}
