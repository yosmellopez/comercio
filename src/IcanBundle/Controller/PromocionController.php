<?php

namespace IcanBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use IcanBundle\Entity;
use IcanBundle\Util;

class PromocionController extends BaseController
{

    public function indexAction()
    {
        $porcientos = $this->getDoctrine()->getRepository('IcanBundle:Porciento')
            ->ListarOrdenados();

        $categorias = $this->ListarCategoriasArbol();

        $marcas = $this->getDoctrine()->getRepository('IcanBundle:Marca')
            ->ListarOrdenadas();

        $ruta = $this->ObtenerURL();
        $dir = 'uploads/promociones/';

        return $this->render('IcanBundle:Promocion:index.html.twig', array(
            'porcientos' => $porcientos,
            'categorias' => $categorias,
            'marcas' => $marcas,
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

        //Sort
        $sort = !empty($request->get('sort')) ? $request->get('sort') : array();
        $sSortDir_0 = !empty($sort['sort']) ? $sort['sort'] : 'desc';
        $iSortCol_0 = !empty($sort['field']) ? $sort['field'] : 'fechainicio';
        //$start and $limit
        $pagination = !empty($request->get('pagination')) ? $request->get('pagination') : array();
        $page = !empty($pagination['page']) ? (int)$pagination['page'] : 1;
        $limit = !empty($pagination['perpage']) ? (int)$pagination['perpage'] : -1;
        $start = 0;

        try {
            $pages = 1;
            $total = $this->TotalPromociones($sSearch);
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

            $data = $this->ListarPromociones($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0);

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
        $promocion_id = $request->get('promocion_id');

        $porciento_id = $request->get('porciento');
        $nombre = $request->get('nombre');
        $titulo = $request->get('titulo');
        $descripcion = $request->get('descripcion');
        $tags = $request->get('tags');
        $estado = $request->get('estado');
        $fechainicio = $request->get('fechainicio');
        $fechafin = $request->get('fechafin');
        $imagen = $request->get('imagen');

        $productos = $request->get('productos');

        $resultadoJson = array();
        if ($promocion_id == "") {
            $resultado = $this->SalvarPromocion($porciento_id, $nombre, $titulo, $descripcion, $tags, $estado,
                $fechainicio, $fechafin, $imagen, $productos);
        } else {
            $resultado = $this->ActualizarPromocion($promocion_id, $porciento_id, $nombre, $titulo, $descripcion, $tags, $estado,
                $fechainicio, $fechafin, $imagen, $productos);
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
            $dir = 'uploads/promociones/';
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
        $promocion_id = $request->get('promocion_id');

        $resultado = $this->EliminarPromocion($promocion_id);
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
     * eliminarPromocionesAction Acción que elimina varios promociones en la BD
     *
     */
    public function eliminarPromocionesAction(Request $request)
    {
        $ids = $request->get('ids');

        $resultado = $this->EliminarPromociones($ids);
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
        $promocion_id = $request->get('promocion_id');

        $resultado = $this->CargarDatosPromocion($promocion_id);
        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['promocion'] = $resultado['promocion'];

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
     * eliminarProductoAction Acción que elimina una promocion de un producto en la BD
     *
     */
    public function eliminarProductoAction(Request $request)
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
        $dir = 'uploads/promociones/';
        $archivo_tmp = $_FILES['foto']['tmp_name'];
        move_uploaded_file($archivo_tmp, $dir . $archivo);

        $resultadoJson['success'] = true;

        $resultadoJson['name'] = $archivo;
        $resultadoJson['size'] = filesize($dir . $archivo);

        return new Response(json_encode($resultadoJson));
    }

    /**
     * EliminarProducto: Elimina un producto de la promocion en la BD
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

            $entity->setPromocion(NULL);
            $entity->setPorciento(NULL);
            $entity->setPrecioEspecial(NULL);

            $em->flush();
            $resultado['success'] = true;
        } else {
            $resultado['success'] = false;
            $resultado['error'] = "No existe el registro solicitado";
        }

        return $resultado;
    }

    /**
     * EliminarImagen: Elimina una imagen de un promocion en la BD
     * @param int $promocion_id Id
     * @author Marcel
     */
    public function EliminarImagen($imagen)
    {
        $resultado = array();
        //Eliminar foto       
        if ($imagen != "") {
            $dir = 'uploads/promociones/';
            if (is_file($dir . $imagen)) {
                unlink($dir . $imagen);
                //unlink($dir . "portada-" . $imagen);
                //unlink($dir . "thumb-" . $imagen);
            }
        }

        $em = $this->getDoctrine()->getManager();

        $promocion = $this->getDoctrine()->getRepository('IcanBundle:Promocion')
            ->findOneBy(
                array('imagen' => $imagen)
            );
        if ($promocion != null) {
            $promocion->setImagen("");
        }
        $em->flush();

        $resultado['success'] = true;
        return $resultado;
    }

    /**
     * CargarDatosPromocion: Carga los datos de un usuario
     *
     * @param int $promocion_id Id
     *
     * @author Marcel
     */
    public function CargarDatosPromocion($promocion_id)
    {
        $resultado = array();
        $arreglo_resultado = array();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Promocion')
            ->find($promocion_id);
        if ($entity != null) {

            $arreglo_resultado['codigo'] = $entity->getCodigo();
            $arreglo_resultado['nombre'] = $entity->getNombre();
            $arreglo_resultado['porciento'] = ($entity->getPorciento() != null) ? $entity->getPorciento()->getPorcientoId() : "";
            $arreglo_resultado['titulo'] = $entity->getTitulo();
            $arreglo_resultado['descripcion'] = $entity->getDescripcion();
            $arreglo_resultado['tags'] = $entity->getTags();
            $arreglo_resultado['estado'] = ($entity->getEstado() == 1) ? true : false;
            $arreglo_resultado['url'] = $entity->getUrl();

            $fechainicio = $entity->getFechainicio();
            if ($fechainicio != "") {
                $fechainicio = $fechainicio->format('d/m/Y H:i');
            }
            $arreglo_resultado['fechainicio'] = $fechainicio;

            $fechafin = $entity->getFechafin();
            if ($fechafin != "") {
                $fechafin = $fechafin->format('d/m/Y H:i');
            }
            $arreglo_resultado['fechafin'] = $fechafin;

            $imagen = $entity->getImagen();

            $ruta = $this->ObtenerURL();
            $dir = 'uploads/promociones/';
            $ruta = $ruta . $dir;

            $size = (is_file($dir . $imagen)) ? filesize($dir . $imagen) : 0;
            $arreglo_resultado['imagen'] = array($imagen, $size, $ruta);

            //Productos relacionados
            $ruta = $this->ObtenerURL();
            $dir = 'uploads/productos/';
            $ruta = $ruta . $dir;

            $relacionados = array();
            $productos = $this->getDoctrine()->getRepository('IcanBundle:Producto')
                ->ListarProductosDePromocion($promocion_id);
            $posicion = 0;
            foreach ($productos as $key => $producto) {

                array_push($relacionados, array(
                    'producto_id' => $producto->getProductoId(),
                    'nombre' => $producto->getNombre(),
                    "categoria" => ($producto->getCategoria() != null) ? $producto->getCategoria()->getNombre() : "",
                    "marca" => ($producto->getMarca() != null) ? $producto->getMarca()->getNombre() : "",
                    "estado" => ($producto->getEstado()) ? 1 : 0,
                    "imagen" => $ruta . $producto->getImagen(),
                    "precio" => $producto->getPrecio(),
                    "fecha" => $producto->getFechapublicacion() != "" ? $producto->getFechapublicacion()->format("d/m/Y H:i") : "",
                    "views" => $producto->getViews(),
                    'posicion' => $posicion
                ));
                $posicion++;

            }
            $arreglo_resultado['productos'] = $relacionados;

            $resultado['success'] = true;
            $resultado['promocion'] = $arreglo_resultado;
        }
        return $resultado;
    }

    /**
     * EliminarPromocion: Elimina un promocion en la BD
     * @param int $promocion_id Id
     * @author Marcel
     */
    public function EliminarPromocion($promocion_id)
    {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Promocion')
            ->find($promocion_id);

        if ($entity != null) {

            //Eliminar foto
            $foto_eliminar = $entity->getImagen();
            if ($foto_eliminar != "") {
                $dir = 'uploads/promociones/';
                if (is_file($dir . $foto_eliminar)) {
                    unlink($dir . $foto_eliminar);
                    //unlink($dir . "portada-" . $foto_eliminar);
                    //unlink($dir . "thumb-" . $foto_eliminar);
                }
            }

            // Quitar descuento a los productos relacionados
            $productos = $this->getDoctrine()->getRepository('IcanBundle:Producto')
                ->ListarProductosDePromocion($promocion_id);
            foreach ($productos as $producto) {
                $producto->setPrecioEspecial(null);
                $producto->setPromocion(null);
                $producto->setPorciento(NULL);
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
     * EliminarPromociones: Elimina varios promociones en la BD
     * @param array $$ids Ids
     * @author Marcel
     */
    public function EliminarPromociones($ids)
    {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        if ($ids != "") {
            $ids = explode(',', $ids);
            $cant_total = 0;
            $cant_eliminada = 0;
            foreach ($ids as $promocion_id) {
                if ($promocion_id != "") {
                    $cant_total++;
                    $entity = $this->getDoctrine()->getRepository('IcanBundle:Promocion')
                        ->find($promocion_id);
                    if ($entity != null) {

                        //Eliminar foto
                        $foto_eliminar = $entity->getImagen();
                        if ($foto_eliminar != "") {
                            $dir = 'uploads/promociones/';
                            if (is_file($dir . $foto_eliminar)) {
                                unlink($dir . $foto_eliminar);
                                //unlink($dir . "portada-" . $foto_eliminar);
                                //unlink($dir . "thumb-" . $foto_eliminar);
                            }
                        }

                        // Quitar descuento a los productos relacionados
                        $productos = $this->getDoctrine()->getRepository('IcanBundle:Producto')
                            ->ListarProductosDePromocion($promocion_id);
                        foreach ($productos as $producto) {
                            $producto->setPrecioEspecial(null);
                            $producto->setPromocion(null);
                            $producto->setPorciento(NULL);
                        }

                        $em->remove($entity);
                        $cant_eliminada++;

                    }
                }
            }
        }
        $em->flush();
        if ($cant_eliminada == 0) {
            $resultado['success'] = false;
            $resultado['error'] = "No se pudo eliminar los promociones, porque tienen cotizaciones asociadas";
        } else {
            $resultado['success'] = true;

            $mensaje = ($cant_eliminada == $cant_total) ? "La operación se ha realizado correctamente" : "La operación se ha realizado correctamente. Pero atención no se pudo eliminar todos los promociones asociados porque tienen cotizaciones asociadas";
            $resultado['message'] = $mensaje;
        }
        return $resultado;
    }

    /**
     * ActualizarPromocion: Actualiza los datos del promocion en la BD
     *
     * @param string $promocion_id Id
     *
     * @author Marcel
     */
    public function ActualizarPromocion($promocion_id, $porciento_id, $nombre, $titulo, $descripcion, $tags, $estado,
                                        $fechainicio, $fechafin, $imagen, $productos)
    {
        $em = $this->getDoctrine()->getManager();

        $resultado = array();
        $entity = $this->getDoctrine()->getRepository('IcanBundle:Promocion')->find($promocion_id);

        if ($entity != null) {

            $naux = $this->getDoctrine()->getRepository('IcanBundle:Promocion')
                ->findOneByNombre($nombre);
            if (!empty($naux)) {
                if ($promocion_id != $naux->getPromocionId()) {
                    $resultado['success'] = false;
                    $resultado['error'] = "El nombre ingresado ya está en uso por otra promoción.";
                    return $resultado;
                }
            }

            $entity->setNombre($nombre);
            $entity->setTitulo($titulo);
            $entity->setDescripcion($descripcion);
            $entity->setEstado($estado);
            $entity->setTags($tags);

            if ($fechainicio != "") {
                $fechainicio = \DateTime::createFromFormat('d/m/Y H:i', $fechainicio);
                $entity->setFechainicio($fechainicio);
            }
            if ($fechafin != "") {
                $fechafin = \DateTime::createFromFormat('d/m/Y H:i', $fechafin);
                $entity->setFechafin($fechafin);
            }

            $porciento = $em->find('IcanBundle:Porciento', $porciento_id);
            if ($porciento != null) {
                $entity->setPorciento($porciento);
            }

            //Hacer Url
            $url = $this->HacerUrl($nombre);
            $i = 1;
            $paux = $this->getDoctrine()->getRepository('IcanBundle:Promocion')
                ->findOneByUrl($url);
            while (!empty($paux) && $paux->getPromocionId() != $promocion_id) {
                $url = $this->HacerUrl($nombre) . "-" . $i;
                $paux = $this->getDoctrine()->getRepository('IcanBundle:Promocion')
                    ->findOneByUrl($url);
                $i++;
            }

            $entity->setUrl($url);

            if ($imagen != "") {
                $foto_eliminar = $entity->getImagen();
                if ($imagen != $foto_eliminar) {
                    //Eliminar foto                
                    if ($foto_eliminar != "") {
                        $dir = 'uploads/promociones/';
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

            //Productos
            if (count($productos) > 0) {
                foreach ($productos as $value) {
                    $producto_id = $value['producto_id'];

                    $producto = $this->getDoctrine()->getRepository('IcanBundle:Producto')
                        ->find($producto_id);
                    if ($producto != null) {
                        $producto->setPromocion($entity);
                        $producto->setPorciento($porciento);

                        $especial = $producto->getPrecio() * (100 - $entity->getPorciento()->getValor()) / 100;
                        $producto->setPrecioEspecial($especial);
                    }
                }
            }

            //Productos que ya tiene el descuento seleccionado
            $productos = $this->getDoctrine()->getRepository('IcanBundle:Producto')
                ->ListarProductosDePorciento($porciento_id);
            if (count($productos) > 0) {
                foreach ($productos as $producto) {
                    $producto->setPromocion($entity);
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
     * SalvarPromocion: Guarda los datos del usuario en la BD
     *
     *
     * @author Marcel
     */
    public function SalvarPromocion($porciento_id, $nombre, $titulo, $descripcion, $tags, $estado,
                                    $fechainicio, $fechafin, $imagen, $productos)
    {
        $em = $this->getDoctrine()->getManager();
        $resultado = array();

        $entity = new Entity\Promocion();

        //generar un codigo
        $codigo = $this->generarCadenaAleatoriaMix();
        $caux = $this->getDoctrine()->getRepository('IcanBundle:Promocion')
            ->findOneByCodigo($codigo);
        while (!empty($caux)) {
            $codigo = $this->generarCadenaAleatoriaMix();
            $caux = $this->getDoctrine()->getRepository('IcanBundle:Promocion')
                ->findOneByCodigo($codigo);
        }
        $entity->setCodigo($codigo);

        $naux = $this->getDoctrine()->getRepository('IcanBundle:Promocion')
            ->findOneByNombre($nombre);
        if (!empty($naux)) {
            $resultado['success'] = false;
            $resultado['error'] = "El nombre ingresado ya está en uso por otra promoción.";
            return $resultado;
        }

        $entity->setNombre($nombre);
        $entity->setTitulo($titulo);
        $entity->setDescripcion($descripcion);
        $entity->setEstado($estado);
        $entity->setTags($tags);

        if ($fechainicio != "") {
            $fechainicio = \DateTime::createFromFormat('d/m/Y H:i', $fechainicio);
            $entity->setFechainicio($fechainicio);
        }
        if ($fechafin != "") {
            $fechafin = \DateTime::createFromFormat('d/m/Y H:i', $fechafin);
            $entity->setFechafin($fechafin);
        }

        $porciento = $em->find('IcanBundle:Porciento', $porciento_id);
        if ($porciento != null) {
            $entity->setPorciento($porciento);
        }

        //Hacer Url        
        $url = $this->HacerUrl($nombre);
        $i = 1;
        $paux = $this->getDoctrine()->getRepository('IcanBundle:Promocion')
            ->findOneByUrl($url);
        while (!empty($paux)) {
            $url = $this->HacerUrl($nombre) . "-" . $i;
            $paux = $this->getDoctrine()->getRepository('IcanBundle:Promocion')
                ->findOneByUrl($url);
            $i++;
        }

        $entity->setUrl($url);

        $em->persist($entity);
        $em->flush();

        //Salvar imagen
        $imagen = $this->RenombrarImagen($url, $imagen);
        $entity->setImagen($imagen);

        //Productos
        if (count($productos) > 0) {
            foreach ($productos as $value) {
                $producto_id = $value['producto_id'];

                $producto = $this->getDoctrine()->getRepository('IcanBundle:Producto')
                    ->find($producto_id);
                if ($producto != null) {
                    $producto->setPromocion($entity);
                    $producto->setPorciento($porciento);

                    $especial = $producto->getPrecio() * (100 - $entity->getPorciento()->getValor()) / 100;
                    $producto->setPrecioEspecial($especial);
                }
            }
        }

        //Productos que ya tiene el descuento seleccionado
        $productos = $this->getDoctrine()->getRepository('IcanBundle:Producto')
            ->ListarProductosDePorciento($porciento_id);
        if (count($productos) > 0) {
            foreach ($productos as $producto) {
                $producto->setPromocion($entity);
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
    public function RenombrarImagen($id, $imagen, $cont = 0)
    {
        $dir = 'uploads/promociones/';
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
     * ListarPromociones: Listar las promociones
     *
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarPromociones($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0)
    {
        $arreglo_resultado = array();
        $cont = 0;

        $lista = $this->getDoctrine()->getRepository('IcanBundle:Promocion')
            ->ListarPromociones($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0);

        foreach ($lista as $value) {
            $promocion_id = $value->getPromocionId();

            $acciones = $this->ListarAcciones($promocion_id);

            $ruta = $this->ObtenerURL();
            $dir = 'uploads/promociones/';
            $imagen = $ruta . $dir . $value->getImagen();

            $arreglo_resultado[$cont] = array(
                "id" => $promocion_id,
                "nombre" => $value->getNombre(),
                "valor" => ($value->getPorciento() != null) ? $value->getPorciento()->getValor() : "",
                "estado" => ($value->getEstado()) ? 1 : 0,
                "imagen" => $imagen,
                "fechainicio" => $value->getFechainicio() != "" ? $value->getFechainicio()->format("d/m/Y H:i") : "",
                "fechafin" => $value->getFechafin() != "" ? $value->getFechafin()->format("d/m/Y H:i") : "",
                "acciones" => $acciones
            );

            $cont++;
        }

        return $arreglo_resultado;
    }

    /**
     * TotalPromociones: Total de usuarios
     * @param string $sSearch Para buscar
     * @author Marcel
     */
    public function TotalPromociones($sSearch)
    {
        $total = $this->getDoctrine()->getRepository('IcanBundle:Promocion')
            ->TotalPromociones($sSearch);

        return $total;
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

}
