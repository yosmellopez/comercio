<?php

namespace IcanBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use IcanBundle\Entity;

class MarcaController extends BaseController
{

    public function indexAction()
    {
        $ruta = $this->ObtenerURL();
        $dir = 'uploads/marcas/';
        return $this->render('IcanBundle:Marca:index.html.twig', array('ruta' => $ruta . $dir));
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
        $sSortDir_0 = !empty($sort['sort']) ? $sort['sort'] : 'asc';
        $iSortCol_0 = !empty($sort['field']) ? $sort['field'] : 'nombre';
        //$start and $limit
        $pagination = !empty($request->get('pagination')) ? $request->get('pagination') : array();
        $page = !empty($pagination['page']) ? (int)$pagination['page'] : 1;
        $limit = !empty($pagination['perpage']) ? (int)$pagination['perpage'] : -1;
        $start = 0;

        try {
            $pages = 1;
            $total = $this->TotalMarcas($sSearch);
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

            $data = $this->ListarMarcas($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0);

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
     * salvarAction Acción que inserta un menu en la BD
     *
     */
    public function salvarAction(Request $request)
    {
        $marca_id = $request->get('marca_id');

        $nombre = $request->get('nombre');
        $titulo = $request->get('titulo');
        $descripcion = $request->get('descripcion');
        $tags = $request->get('tags');
        $estado = $request->get('estado');

        $imagen = $request->get('imagen');

        if ($marca_id == "") {
            $resultado = $this->SalvarMarca($nombre, $titulo, $descripcion, $tags, $estado, $imagen);
        } else {
            $resultado = $this->ActualizarMarca($marca_id, $nombre, $titulo, $descripcion, $tags, $estado, $imagen);
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

    /**
     * eliminarAction Acción que elimina un marca en la BD
     *
     */
    public function eliminarAction(Request $request)
    {
        $marca_id = $request->get('marca_id');

        $resultado = $this->EliminarMarca($marca_id);
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
     * eliminarMarcasAction Acción que elimina los marcas seleccionados en la BD
     *
     */
    public function eliminarMarcasAction(Request $request)
    {
        $ids = $request->get('ids');

        $resultado = $this->EliminarMarcas($ids);
        if ($resultado['success']) {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['message'] = $resultado['message'];
            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * cargarDatosAction Acción que carga los datos del marca en la BD
     *
     */
    public function cargarDatosAction(Request $request)
    {
        $marca_id = $request->get('marca_id');

        $resultado = $this->CargarDatosMarca($marca_id);
        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['marca'] = $resultado['marca'];

            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];

            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * salvarImagenAction Acción para subir una imagen al servidor
     *
     */
    public function salvarImagenAction()
    {
        try {
            $nombre_archivo = $_FILES['foto']['name'];
            $array_nombre_archivo = explode('.', $nombre_archivo);
            $pos = count($array_nombre_archivo) - 1;
            $extension = $array_nombre_archivo[$pos];

            $archivo = $this->generarCadenaAleatoria() . '.' . $extension;

            //Manejar la imagen
            $dir = 'uploads/marcas/';
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
        $dir = 'uploads/marcas/';
        $archivo_tmp = $_FILES['foto']['tmp_name'];
        move_uploaded_file($archivo_tmp, $dir . $archivo);

        $resultadoJson['success'] = true;

        $resultadoJson['name'] = $archivo;
        $resultadoJson['size'] = filesize($dir . $archivo);

        return new Response(json_encode($resultadoJson));
    }

    /**
     * EliminarImagen: Elimina una imagen en la BD
     *
     * @author Marcel
     */
    public function EliminarImagen($imagen)
    {
        $resultado = array();
        //Eliminar foto
        if ($imagen != "") {
            $dir = 'uploads/marcas/';
            if (is_file($dir . $imagen)) {
                unlink($dir . $imagen);
            }
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Marca')
            ->findOneBy(array('imagen' => $imagen));
        if ($entity != null) {
            $entity->setImagen("");
        }

        $em->flush();

        $resultado['success'] = true;
        return $resultado;
    }

    /**
     * CargarDatosMarca: Carga los datos de un marca
     *
     * @param int $marca_id Id
     *
     * @author Marcel
     */
    public function CargarDatosMarca($marca_id)
    {
        $resultado = array();
        $arreglo_resultado = array();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Marca')
            ->find($marca_id);
        if ($entity != null) {

            $arreglo_resultado['nombre'] = $entity->getNombre();
            $arreglo_resultado['titulo'] = $entity->getTitulo();
            $arreglo_resultado['descripcion'] = $entity->getDescripcion();
            $arreglo_resultado['estado'] = ($entity->getEstado() == 1) ? true : false;
            $arreglo_resultado['tags'] = $entity->getTags();

            $ruta = $this->ObtenerURL();
            $dir = 'uploads/marcas/';
            $ruta = $ruta . $dir;
            $imagen = $entity->getImagen();
            $size = (is_file($dir . $imagen)) ? filesize($dir . $imagen) : 0;
            $arreglo_resultado['imagen'] = array($imagen, $size, $ruta);

            $resultado['success'] = true;
            $resultado['marca'] = $arreglo_resultado;
        }

        return $resultado;
    }

    /**
     * EliminarMarca: Elimina un rol en la BD
     * @param int $marca_id Id
     * @author Marcel
     */
    public function EliminarMarca($marca_id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Marca')
            ->find($marca_id);

        if ($entity != null) {

            //Productos
            $productos = $this->getDoctrine()->getRepository('IcanBundle:Producto')
                ->ListarProductosDeMarca($marca_id);
            if (count($productos) > 0) {
                $resultado['success'] = false;
                $resultado['error'] = "No se pudo eliminar la marca, porque tiene productos asociados";
                return $resultado;
            }

            //Eliminar foto
            $foto_eliminar = $entity->getImagen();
            if ($foto_eliminar != "") {
                $dir = 'uploads/marcas/';
                if (is_file($dir . $foto_eliminar)) {
                    unlink($dir . $foto_eliminar);
                }
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
     * EliminarMarcas: Elimina los marcas seleccionados en la BD
     * @param int $ids Ids
     * @author Marcel
     */
    public function EliminarMarcas($ids)
    {
        $em = $this->getDoctrine()->getManager();

        if ($ids != "") {
            $ids = explode(',', $ids);
            $cant_eliminada = 0;
            $cant_total = 0;
            foreach ($ids as $marca_id) {
                if ($marca_id != "") {
                    $cant_total++;
                    $entity = $this->getDoctrine()->getRepository('IcanBundle:Marca')
                        ->find($marca_id);
                    if ($entity != null) {

                        //Productos
                        $productos = $this->getDoctrine()->getRepository('IcanBundle:Producto')
                            ->ListarProductosDeMarca($marca_id);
                        if (count($productos) == 0) {

                            //Eliminar foto
                            $foto_eliminar = $entity->getImagen();
                            if ($foto_eliminar != "") {
                                $dir = 'uploads/marcas/';
                                if (is_file($dir . $foto_eliminar)) {
                                    unlink($dir . $foto_eliminar);
                                }
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
            $resultado['error'] = "No se pudo eliminar las marcas, porque están asociados a un producto";
        } else {
            $resultado['success'] = true;

            $mensaje = ($cant_eliminada == $cant_total) ? "La operación se ha realizado correctamente" : "La operación se ha realizado correctamente. Pero atención no se pudo eliminar todas las marcas seleccionadas porque están asociadas a un producto";
            $resultado['message'] = $mensaje;
        }

        return $resultado;
    }

    /**
     * ActualizarMarca: Actualiza los datos del marca en la BD
     *
     * @param string $marca_id Id
     *
     * @author Marcel
     */
    public function ActualizarMarca($marca_id, $nombre, $titulo, $descripcion, $tags, $estado, $imagen)
    {
        $em = $this->getDoctrine()->getManager();

        $resultado = array();
        $entity = $this->getDoctrine()->getRepository('IcanBundle:Marca')->find($marca_id);
        if ($entity != null) {
            //Verificar nombre
            $marca = $this->getDoctrine()->getRepository('IcanBundle:Marca')
                ->findOneBy(array('nombre' => $nombre));
            if ($marca != null) {
                if ($entity->getMarcaId() != $marca->getMarcaId()) {
                    $resultado['success'] = false;
                    $resultado['error'] = "El nombre del marca está en uso, por favor intente ingrese otro.";
                    return $resultado;
                }
            }

            $entity->setNombre($nombre);
            $entity->setTitulo($titulo);
            $entity->setDescripcion($descripcion);
            $entity->setEstado($estado);
            $entity->setTags($tags);

            //Hacer Url
            $url = $this->HacerUrl($nombre);
            $i = 1;
            $paux = $this->getDoctrine()->getRepository('IcanBundle:Marca')
                ->BuscarPorUrl($url);
            while (!empty($paux) && $paux->getMarcaId() != $marca_id) {
                $url = $this->HacerUrl($nombre) . "-" . $i;
                $paux = $this->getDoctrine()->getRepository('IcanBundle:Marca')
                    ->BuscarPorUrl($url);
                $i++;
            }

            $entity->setUrl($url);

            if ($imagen != "") {
                $foto_eliminar = $entity->getImagen();
                if ($imagen != $foto_eliminar) {
                    //Eliminar foto
                    if ($foto_eliminar != "") {
                        $dir = 'uploads/marcas/';
                        if (is_file($dir . $foto_eliminar)) {
                            unlink($dir . $foto_eliminar);
                        }
                    }
                    $imagen = $this->RenombrarImagen($url, $imagen);
                    $entity->setImagen($imagen);
                }
            }

            $em->flush();
            $resultado['success'] = true;
        } else {
            $resultado['success'] = false;
            $resultado['error'] = "No existe un marca que se corresponda con ese identificador";
        }
        return $resultado;
    }

    /**
     * SalvarMarca: Guarda los datos del usuario en la BD
     *
     *
     * @author Marcel
     */
    public function SalvarMarca($nombre, $titulo, $descripcion, $tags, $estado, $imagen)
    {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        //Verificar nombre
        $marca = $this->getDoctrine()->getRepository('IcanBundle:Marca')
            ->findOneBy(array('nombre' => $nombre));
        if ($marca != null) {
            $resultado['success'] = false;
            $resultado['error'] = "El nombre del marca está en uso, por favor intente ingrese otro.";
            return $resultado;
        }

        $entity = new Entity\Marca();

        $entity->setNombre($nombre);
        $entity->setTitulo($titulo);
        $entity->setDescripcion($descripcion);
        $entity->setEstado($estado);
        $entity->setTags($tags);

        //Hacer Url
        $url = $this->HacerUrl($nombre);
        $i = 1;
        $paux = $this->getDoctrine()->getRepository('IcanBundle:Marca')
            ->BuscarPorUrl($url);
        while (!empty($paux)) {
            $url = $this->HacerUrl($nombre) . "-" . $i;
            $paux = $this->getDoctrine()->getRepository('IcanBundle:Marca')
                ->BuscarPorUrl($url);
            $i++;
        }

        $entity->setUrl($url);

        $em->persist($entity);
        $em->flush();

        //Salvar imagen
        $imagen = $this->RenombrarImagen($url, $imagen);
        $entity->setImagen($imagen);

        $em->flush();

        $resultado['success'] = true;

        return $resultado;
    }

    /**
     * RenombrarImagen: Renombra la imagen en la BD
     *
     * @author Marcel
     */
    public function RenombrarImagen($id, $imagen)
    {
        $dir = 'uploads/marcas/';
        $imagen_new = "";

        if ($imagen != "") {
            $extension_array = explode('.', $imagen);
            $extension = $extension_array[1];

            //Imagen nueva
            $imagen_new = $id . '.' . $extension;
            if (is_file($dir . $imagen)) {
                //Renombrar imagen
                rename($dir . $imagen, $dir . $imagen_new);
            }
        }

        return $imagen_new;
    }

    /**
     * ListarMarcas: Listar los marcas
     *
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarMarcas($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0)
    {
        $arreglo_resultado = array();
        $cont = 0;

        $lista = $this->getDoctrine()->getRepository('IcanBundle:Marca')
            ->ListarMarcas($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0);

        foreach ($lista as $value) {
            $marca_id = $value->getMarcaId();

            $acciones = $this->ListarAcciones($marca_id);

            $ruta = $this->ObtenerURL();
            $dir = 'uploads/marcas/';
            $imagen = $ruta . $dir . $value->getImagen();

            $arreglo_resultado[$cont] = array(
                "id" => $marca_id,
                "nombre" => $value->getNombre(),
                "descripcion" => $value->getDescripcion(),
                "estado" => ($value->getEstado()) ? 1 : 0,
                "imagen" => $imagen,
                "acciones" => $acciones
            );

            $cont++;
        }

        return $arreglo_resultado;
    }

    /**
     * TotalMarcas: Total de marcas
     * @param string $sSearch Para buscar
     * @author Marcel
     */
    public function TotalMarcas($sSearch)
    {
        $total = $this->getDoctrine()->getRepository('IcanBundle:Marca')
            ->TotalMarcas($sSearch);

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

}
