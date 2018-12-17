<?php

namespace IcanBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use IcanBundle\Entity;

class MensajeController extends BaseController
{

    public function indexAction()
    {
        return $this->render('IcanBundle:Mensaje:index.html.twig', array());
    }

    /**
     * listarAction Acción que lista los mensajes de contacto
     *
     */
    public function listarAction(Request $request)
    {
        // search filter by keywords
        $query = !empty($request->get('query')) ? $request->get('query') : array();
        $sSearch = isset($query['generalSearch']) && is_string($query['generalSearch']) ? $query['generalSearch'] : '';
        $fecha_inicial = isset($query['fechaInicial']) && is_string($query['fechaInicial']) ? $query['fechaInicial'] : '';
        $fecha_fin = isset($query['fechaFin']) && is_string($query['fechaFin']) ? $query['fechaFin'] : '';
        //Sort
        $sort = !empty($request->get('sort')) ? $request->get('sort') : array();
        $sSortDir_0 = !empty($sort['sort']) ? $sort['sort'] : 'desc';
        $iSortCol_0 = !empty($sort['field']) ? $sort['field'] : 'fecha';
        //$start and $limit
        $pagination = !empty($request->get('pagination')) ? $request->get('pagination') : array();
        $page = !empty($pagination['page']) ? (int)$pagination['page'] : 1;
        $limit = !empty($pagination['perpage']) ? (int)$pagination['perpage'] : -1;
        $start = 0;

        try {
            $pages = 1;
            $total = $this->TotalMensajes($sSearch, $fecha_inicial, $fecha_fin);
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

            $data = $this->ListarMensajes($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $fecha_inicial, $fecha_fin);

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
        $mensaje_id = $request->get('mensaje_id');

        $nombre = $request->get('nombre');
        $telefono = $request->get('telefono');
        $email = $request->get('email');
        $asunto = $request->get('asunto');
        $descripcion = $request->get('descripcion');

        $resultadoJson = array();
        if ($mensaje_id == "") {
            $resultado = $this->SalvarMensaje($nombre, $telefono, $email, $asunto, $descripcion);
        } else {
            $resultado = $this->ActualizarMensaje($mensaje_id, $nombre, $telefono, $email, $asunto, $descripcion);
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
     * eliminarAction Acción que elimina un rol en la BD
     *
     */
    public function eliminarAction(Request $request)
    {
        $mensaje_id = $request->get('mensaje_id');

        $resultado = $this->EliminarMensaje($mensaje_id);
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
     * eliminarMensajesAction Acción que elimina varios mensajes en la BD
     *
     */
    public function eliminarMensajesAction(Request $request)
    {
        $ids = $request->get('ids');

        $resultado = $this->EliminarMensajes($ids);
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
        $mensaje_id = $request->get('mensaje_id');

        $resultado = $this->CargarDatosMensaje($mensaje_id);
        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['mensaje'] = $resultado['mensaje'];
            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * CargarDatosMensaje: Carga los datos de un usuario
     *
     * @param int $mensaje_id Id
     *
     * @author Marcel
     */
    public function CargarDatosMensaje($mensaje_id)
    {
        $resultado = array();
        $arreglo_resultado = array();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Mensaje')
            ->find($mensaje_id);
        if ($entity != null) {
            $arreglo_resultado['nombre'] = $entity->getNombre();
            $arreglo_resultado['telefono'] = $entity->getTelefono();
            $arreglo_resultado['email'] = $entity->getEmail();
            $arreglo_resultado['asunto'] = $entity->getAsunto();
            $arreglo_resultado['descripcion'] = $entity->getDescripcion();


            $resultado['success'] = true;
            $resultado['mensaje'] = $arreglo_resultado;
        }
        return $resultado;
    }

    /**
     * EliminarMensaje: Elimina un mensaje en la BD
     * @param int $mensaje_id Id
     * @author Marcel
     */
    public function EliminarMensaje($mensaje_id)
    {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Mensaje')
            ->find($mensaje_id);

        if ($entity != null) {
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
     * EliminarMensajes: Elimina varios mensajes en la BD
     * @param array $$ids Ids
     * @author Marcel
     */
    public function EliminarMensajes($ids)
    {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        if ($ids != "") {
            $ids = explode(',', $ids);
            foreach ($ids as $mensaje_id) {
                if ($mensaje_id != "") {
                    $entity = $this->getDoctrine()->getRepository('IcanBundle:Mensaje')
                        ->find($mensaje_id);

                    if ($entity != null) {
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
     * ActualizarMensaje: Actualiza los datos del mensaje en la BD
     *
     * @param string $mensaje_id Id
     *
     * @author Marcel
     */
    public function ActualizarMensaje($mensaje_id, $nombre, $telefono, $email, $asunto, $descripcion)
    {
        $em = $this->getDoctrine()->getManager();

        $resultado = array();
        $entity = $this->getDoctrine()->getRepository('IcanBundle:Mensaje')->find($mensaje_id);
        if ($entity != null) {
            $entity->setNombre($nombre);
            $entity->setTelefono($telefono);
            $entity->setAsunto($asunto);
            $entity->setDescripcion($descripcion);
            $entity->setEmail($email);

            $em->flush();
            $resultado['success'] = true;
        } else {
            $resultado['success'] = false;
            $resultado['error'] = "No existe el registro solicitado";
        }
        return $resultado;
    }

    /**
     * SalvarMensaje: Guarda los datos del usuario en la BD
     *
     * @author Marcel
     */
    public function SalvarMensaje($nombre, $telefono, $email, $asunto, $descripcion)
    {
        $em = $this->getDoctrine()->getManager();
        $resultado = array();

        $entity = new Entity\Mensaje();

        $entity->setNombre($nombre);
        $entity->setTelefono($telefono);
        $entity->setAsunto($asunto);
        $entity->setDescripcion($descripcion);
        $entity->setEmail($email);

        $this->setTimeZone();
        $entity->setFecha(new \DateTime());

        $em->persist($entity);
        $em->flush();
        $resultado['success'] = true;

        return $resultado;
    }

    /**
     * ListarMensajes: Listar las mensajes
     *
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarMensajes($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $fecha_inicial, $fecha_fin)
    {
        $arreglo_resultado = array();
        $cont = 0;

        $lista = $this->getDoctrine()->getRepository('IcanBundle:Mensaje')
            ->ListarMensajes($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $fecha_inicial, $fecha_fin);

        foreach ($lista as $value) {
            $mensaje_id = $value->getMensajeId();

            $acciones = $this->ListarAcciones($mensaje_id);

            $arreglo_resultado[$cont] = array(
                "id" => $mensaje_id,
                "nombre" => $value->getNombre(),
                "email" => $value->getEmail(),
                "telefono" => $value->getTelefono(),
                "asunto" => $value->getAsunto(),
                "descripcion" => $value->getDescripcion(),
                "fecha" => $value->getFecha()->format('d-m-Y H:i'),
                "acciones" => $acciones
            );

            $cont++;
        }

        return $arreglo_resultado;
    }

    /**
     * TotalMensajes: Total de usuarios
     * @param string $sSearch Para buscar
     * @author Marcel
     */
    public function TotalMensajes($sSearch, $fecha_inicial, $fecha_fin)
    {
        $total = $this->getDoctrine()->getRepository('IcanBundle:Mensaje')
            ->TotalMensajes($sSearch, $fecha_inicial, $fecha_fin);

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
