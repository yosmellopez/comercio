<?php

namespace IcanBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use IcanBundle\Entity;

class SliderController extends BaseController
{

    public function indexAction()
    {
        $ruta = $this->ObtenerURL();
        $dir = 'uploads/sliders/';
        return $this->render('IcanBundle:Slider:index.html.twig', array('ruta' => $ruta . $dir));
    }

    /**
     * listarAction Acción que lista los sliders
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
        $iSortCol_0 = !empty($sort['field']) ? $sort['field'] : 'posicion';
        //$start and $limit
        $pagination = !empty($request->get('pagination')) ? $request->get('pagination') : array();
        $page = !empty($pagination['page']) ? (int)$pagination['page'] : 1;
        $limit = !empty($pagination['perpage']) ? (int)$pagination['perpage'] : -1;
        $start = 0;

        try {
            $pages = 1;
            $total = $this->TotalSliders($sSearch);
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

            $data = $this->ListarSliders($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0);

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
        $slider_id = $request->get('slider_id');

        $nombre = $request->get('nombre');
        $estado = $request->get('estado');
        $titulo = $request->get('titulo');
        $descripcion = $request->get('descripcion');
        $url = $request->get('url');
        $formadeabrir = $request->get('formadeabrir');
        $fechapublicacion = $request->get('fecha');

        $imagen = $request->get('imagen');

        if ($slider_id == "") {
            $resultado = $this->SalvarSlider($nombre, $url, $formadeabrir, $estado, $fechapublicacion, $titulo, $descripcion, $imagen);
        } else {
            $resultado = $this->ActualizarSlider($slider_id, $nombre, $url, $formadeabrir, $estado, $fechapublicacion, $titulo, $descripcion, $imagen);
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
     * eliminarAction Acción que elimina un slider en la BD
     *
     */
    public function eliminarAction(Request $request)
    {
        $slider_id = $request->get('slider_id');

        $resultado = $this->EliminarSlider($slider_id);
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
     * eliminarSlidersAction Acción que elimina los sliders seleccionados en la BD
     *
     */
    public function eliminarSlidersAction(Request $request)
    {
        $ids = $request->get('ids');

        $resultado = $this->EliminarSliders($ids);
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
     * cargarDatosAction Acción que carga los datos del slider en la BD
     *
     */
    public function cargarDatosAction(Request $request)
    {
        $slider_id = $request->get('slider_id');

        $resultado = $this->CargarDatosSlider($slider_id);
        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['slider'] = $resultado['slider'];

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
            $dir = 'uploads/sliders/';
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
     * cambiarPosicionAction Acción que sube o baja un slider
     *
     */
    public function cambiarPosicionAction(Request $request)
    {
        $slider_id = $request->get('slider_id');
        $direccion = $request->get('direccion');

        $resultado = $this->CambiarPosicionSlider($slider_id, $direccion);
        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
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
        $dir = 'uploads/sliders/';
        $archivo_tmp = $_FILES['foto']['tmp_name'];
        move_uploaded_file($archivo_tmp, $dir . $archivo);

        $resultadoJson['success'] = true;

        $resultadoJson['name'] = $archivo;
        $resultadoJson['size'] = filesize($dir . $archivo);

        return new Response(json_encode($resultadoJson));
    }

    /**
     * CambiarPosicionSlider: Sube/baja un slider
     * @param int $usuario_id Id
     * @author Marcel
     */
    public function CambiarPosicionSlider($slider_id, $direccion)
    {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Slider')
            ->find($slider_id);

        if (!is_null($entity)) {
            $lista = $this->getDoctrine()->getRepository('IcanBundle:Slider')
                ->ListarSliderOrdenados('ASC');
            $pos = -1;
            foreach ($lista as $i => $value) {
                if ($direccion == 'subir') {
                    if ($value->getSliderId() == $entity->getSliderId()) {
                        if ($value->getPosicion() > 1) {
                            $pos = $i - 1;
                            $posicion = $value->getPosicion();
                            $value->setPosicion($lista[$pos]->getPosicion());
                            $lista[$pos]->setPosicion($posicion);
                        }
                    }
                } else {
                    if ($value->getSliderId() == $entity->getSliderId()) {
                        if ($value->getPosicion() < count($lista)) {
                            $pos = $i + 1;
                            $posicion = $value->getPosicion();
                            $value->setPosicion($lista[$pos]->getPosicion());
                            $lista[$pos]->setPosicion($posicion);
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
     * EliminarImagen: Elimina una imagen en la BD
     *
     * @author Marcel
     */
    public function EliminarImagen($imagen)
    {
        $resultado = array();
        //Eliminar foto
        if ($imagen != "") {
            $dir = 'uploads/sliders/';
            if (is_file($dir . $imagen)) {
                unlink($dir . $imagen);
            }
        }

        $em = $this->getDoctrine()->getManager();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Slider')
            ->findOneBy(array('imagen' => $imagen));
        if ($entity != null) {
            $entity->setImagen("");
        }

        $em->flush();

        $resultado['success'] = true;
        return $resultado;
    }

    /**
     * CargarDatosSlider: Carga los datos de un slider
     *
     * @param int $slider_id Id
     *
     * @author Marcel
     */
    public function CargarDatosSlider($slider_id)
    {
        $resultado = array();
        $arreglo_resultado = array();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Slider')
            ->find($slider_id);
        if ($entity != null) {

            $arreglo_resultado['nombre'] = $entity->getNombre();
            $arreglo_resultado['titulo'] = $entity->getTitulo();
            $arreglo_resultado['url'] = $entity->getUrl();
            $arreglo_resultado['descripcion'] = $entity->getDescripcion();
            $arreglo_resultado['formadeabrir'] = $entity->getFormadeabrir();
            $arreglo_resultado['estado'] = ($entity->getEstado() == 1) ? true : false;
            $arreglo_resultado['fecha'] = ($entity->getFechapublicacion() != "") ? $entity->getFechapublicacion()->format('d/m/Y H:i') : "";

            $ruta = $this->ObtenerURL();
            $dir = 'uploads/sliders/';
            $ruta = $ruta . $dir;
            $imagen = $entity->getImagen();
            $size = (is_file($dir . $imagen)) ? filesize($dir . $imagen) : 0;
            $arreglo_resultado['imagen'] = array($imagen, $size, $ruta);

            $resultado['success'] = true;
            $resultado['slider'] = $arreglo_resultado;
        }

        return $resultado;
    }

    /**
     * EliminarSlider: Elimina un rol en la BD
     * @param int $slider_id Id
     * @author Marcel
     */
    public function EliminarSlider($slider_id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Slider')
            ->find($slider_id);

        if ($entity != null) {

            $pos_elimnar = $entity->getPosicion();

            //Eliminar foto
            $foto_eliminar = $entity->getImagen();
            if ($foto_eliminar != "") {
                $dir = 'uploads/sliders/';
                if (is_file($dir . $foto_eliminar)) {
                    unlink($dir . $foto_eliminar);
                }
            }
            $em->remove($entity);

            //Actualizar posiciones
            $sliders = $this->getDoctrine()->getRepository('IcanBundle:Slider')
                ->ListarSliderMayorPosision($pos_elimnar);
            foreach ($sliders as $slider) {
                $slider->setPosicion($slider->getPosicion() - 1);
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
     * EliminarSliders: Elimina los sliders seleccionados en la BD
     * @param int $ids Ids
     * @author Marcel
     */
    public function EliminarSliders($ids)
    {
        $em = $this->getDoctrine()->getManager();

        if ($ids != "") {
            $ids = explode(',', $ids);
            $cant_eliminada = 0;
            foreach ($ids as $slider_id) {
                if ($slider_id != "") {
                    $entity = $this->getDoctrine()->getRepository('IcanBundle:Slider')
                        ->find($slider_id);
                    if ($entity != null) {

                        $pos_elimnar = $entity->getPosicion();

                        //Eliminar foto
                        $foto_eliminar = $entity->getImagen();
                        if ($foto_eliminar != "") {
                            $dir = 'uploads/sliders/';
                            if (is_file($dir . $foto_eliminar)) {
                                unlink($dir . $foto_eliminar);
                            }
                        }
                        $em->remove($entity);

                        //Actualizar posiciones
                        $sliders = $this->getDoctrine()->getRepository('IcanBundle:Slider')
                            ->ListarSliderMayorPosision($pos_elimnar);
                        foreach ($sliders as $slider) {
                            $slider->setPosicion($slider->getPosicion() - 1);
                        }
                    }
                }
            }
        }
        $em->flush();

        $resultado['success'] = true;
        return $resultado;
    }

    /**
     * ActualizarSlider: Actualiza los datos del slider en la BD
     *
     * @param string $slider_id Id
     *
     * @author Marcel
     */
    public function ActualizarSlider($slider_id, $nombre, $url, $formadeabrir, $estado, $fecha, $titulo, $descripcion, $imagen)
    {
        $em = $this->getDoctrine()->getManager();

        $resultado = array();
        $entity = $this->getDoctrine()->getRepository('IcanBundle:Slider')->find($slider_id);
        if ($entity != null) {
            //Verificar nombre
            $slider = $this->getDoctrine()->getRepository('IcanBundle:Slider')
                ->findOneBy(array('nombre' => $nombre));
            if ($slider != null) {
                if ($entity->getSliderId() != $slider->getSliderId()) {
                    $resultado['success'] = false;
                    $resultado['error'] = "El nombre del slider está en uso, por favor intente ingrese otro.";
                    return $resultado;
                }
            }

            $entity->setNombre($nombre);
            $entity->setUrl($url);
            $entity->setTitulo($titulo);
            $entity->setFormadeabrir($formadeabrir);
            $entity->setDescripcion($descripcion);
            $entity->setEstado($estado);

            if ($fecha != "") {
                $fecha = \DateTime::createFromFormat('d/m/Y H:i', $fecha);
                $entity->setFechapublicacion($fecha);
            }

            if ($imagen != "") {
                $foto_eliminar = $entity->getImagen();
                if ($imagen != $foto_eliminar) {
                    //Eliminar foto
                    if ($foto_eliminar != "") {
                        $dir = 'uploads/sliders/';
                        if (is_file($dir . $foto_eliminar)) {
                            unlink($dir . $foto_eliminar);
                        }
                    }
                    $imagen = $this->RenombrarImagen($slider_id, $imagen);
                    $entity->setImagen($imagen);
                }
            }

            $em->flush();
            $resultado['success'] = true;
        } else {
            $resultado['success'] = false;
            $resultado['error'] = "No existe un slider que se corresponda con ese identificador";
        }
        return $resultado;
    }

    /**
     * SalvarSlider: Guarda los datos del usuario en la BD
     *
     *
     * @author Marcel
     */
    public function SalvarSlider($nombre, $url, $formadeabrir, $estado, $fecha, $titulo, $descripcion, $imagen)
    {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        //Verificar nombre
        $slider = $this->getDoctrine()->getRepository('IcanBundle:Slider')
            ->findOneBy(array('nombre' => $nombre));
        if ($slider != null) {
            $resultado['success'] = false;
            $resultado['error'] = "El nombre del slider está en uso, por favor intente ingrese otro.";
            return $resultado;
        }

        $entity = new Entity\Slider();

        $entity->setNombre($nombre);
        $entity->setUrl($url);
        $entity->setTitulo($titulo);
        $entity->setFormadeabrir($formadeabrir);
        $entity->setDescripcion($descripcion);
        $entity->setEstado($estado);
        $entity->setImagen($imagen);

        if ($fecha != "") {
            $fecha = \DateTime::createFromFormat('d/m/Y H:i', $fecha);
            $entity->setFechapublicacion($fecha);
        }

        $lista = $this->getDoctrine()->getRepository('IcanBundle:Slider')
            ->ListarSliderOrdenados('DESC');

        if (count($lista) > 0)
            $posicion = $lista[0]->getPosicion() + 1;
        else
            $posicion = 1;

        if ($posicion != 0)
            $entity->setPosicion($posicion);

        $em->persist($entity);

        $em->flush();

        //Salvar imagen
        $slider_id = $entity->getSliderId();
        $imagen = $this->RenombrarImagen($slider_id, $imagen);
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
        $dir = 'uploads/sliders/';
        $empresa = "ican";
        $tipo = "slider";
        $imagen_new = "";

        if ($imagen != "") {
            $extension_array = explode('.', $imagen);
            $extension = $extension_array[1];

            //Imagen nueva
            $imagen_new = $empresa . '-' . $tipo . '-' . $id . '.' . $extension;
            if (is_file($dir . $imagen)) {
                //Renombrar imagen
                rename($dir . $imagen, $dir . $imagen_new);
            }
        }

        return $imagen_new;
    }

    /**
     * ListarSliders: Listar los sliders
     *
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarSliders($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0)
    {
        $arreglo_resultado = array();
        $cont = 0;

        $lista = $this->getDoctrine()->getRepository('IcanBundle:Slider')
            ->ListarSliders($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0);

        foreach ($lista as $value) {
            $slider_id = $value->getSliderId();

            $fecha = $value->getFechapublicacion();
            if ($fecha != "")
                $fecha = $fecha->format('d/m/Y H:i');

            $acciones = $this->ListarAcciones($slider_id);

            $iconos = '<a href="javascript:;" class="subir m-portlet__nav-link btn m-btn m-btn--hover-success m-btn--icon m-btn--icon-only m-btn--pill" data-id="' . $slider_id . '"><i title="Subir un nivel" class="fa fa-chevron-up ic-color-ok"></i></a>  <a href="javascript:;" class="bajar m-portlet__nav-link btn m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill" data-id="' . $slider_id . '"><i title="Bajar un nivel" class="fa fa-chevron-down ic-color-ok"></i></a>';

            $arreglo_resultado[$cont] = array(
                "id" => $slider_id,
                "nombre" => $value->getNombre(),
                "fechapublicacion" => $fecha,
                "estado" => ($value->getEstado()) ? 1 : 0,
                "posicion" => ($value->getPosicion() == "") ? '0' . $iconos : $value->getPosicion() . $iconos,
                "acciones" => $acciones
            );

            $cont++;
        }

        return $arreglo_resultado;
    }

    /**
     * TotalSliders: Total de sliders
     * @param string $sSearch Para buscar
     * @author Marcel
     */
    public function TotalSliders($sSearch)
    {
        $total = $this->getDoctrine()->getRepository('IcanBundle:Slider')
            ->TotalSliders($sSearch);

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
