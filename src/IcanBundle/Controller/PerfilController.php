<?php

namespace IcanBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use IcanBundle\Entity;

class PerfilController extends BaseController
{

    public function indexAction()
    {
        return $this->render('IcanBundle:Rol:index.html.twig', array());
    }

    /**
     * listarAction Acción que lista los perfiles
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
            $total = $this->TotalPerfiles($sSearch);
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

            $data = $this->ListarPerfiles($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0);

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
        $perfil_id = $request->get('perfil_id');
        $descripcion = $request->get('descripcion');

        if ($perfil_id == "") {
            $resultado = $this->SalvarPerfil($descripcion);
        } else {
            $resultado = $this->ActualizarPerfil($perfil_id, $descripcion);
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
     * eliminarAction Acción que elimina un perfil en la BD
     *
     */
    public function eliminarAction(Request $request)
    {
        $perfil_id = $request->get('perfil_id');

        $resultado = $this->EliminarPerfil($perfil_id);
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
     * eliminarPerfilesAction Acción que elimina los perfiles seleccionados en la BD
     *
     */
    public function eliminarPerfilesAction(Request $request)
    {
        $ids = $request->get('ids');

        $resultado = $this->EliminarPerfiles($ids);
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
     * cargarDatosAction Acción que carga los datos del perfil en la BD
     *
     */
    public function cargarDatosAction(Request $request)
    {
        $perfil_id = $request->get('perfil_id');

        $resultado = $this->CargarDatosPerfil($perfil_id);
        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['perfil'] = $resultado['perfil'];

            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];

            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * CargarDatosPerfil: Carga los datos de un perfil
     *
     * @param int $perfil_id Id
     *
     * @author Marcel
     */
    public function CargarDatosPerfil($perfil_id)
    {
        $resultado = array();
        $arreglo_resultado = array();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Rol')
            ->find($perfil_id);
        if ($entity != null) {

            $arreglo_resultado['descripcion'] = $entity->getNombre();

            $resultado['success'] = true;
            $resultado['perfil'] = $arreglo_resultado;
        }

        return $resultado;
    }

    /**
     * EliminarPerfil: Elimina un rol en la BD
     * @param int $rol_id Id
     * @author Marcel
     */
    public function EliminarPerfil($rol_id)
    {
        $em = $this->getDoctrine()->getManager();

        $rol = $this->getDoctrine()->getRepository('IcanBundle:Rol')
            ->find($rol_id);

        if ($rol != null) {
            $usuarios = $this->getDoctrine()->getRepository('IcanBundle:Usuario')
                ->ListarUsuariosRol($rol_id);
            if (count($usuarios) > 0) {
                $resultado['success'] = false;
                $resultado['error'] = "No se pudo eliminar el perfil, porque se encuentra relacionado con algun usuario";
                return $resultado;
            }


            $em->remove($rol);

            $em->flush();
            $resultado['success'] = true;
        } else {
            $resultado['success'] = false;
            $resultado['error'] = "No existe el registro solicitado";
        }

        return $resultado;
    }

    /**
     * EliminarPerfiles: Elimina los perfiles seleccionados en la BD
     * @param int $ids Ids
     * @author Marcel
     */
    public function EliminarPerfiles($ids)
    {
        $em = $this->getDoctrine()->getManager();

        if ($ids != "") {
            $ids = explode(',', $ids);
            $cant_eliminada = 0;
            $cant_total = 0;
            foreach ($ids as $perfil_id) {
                if ($perfil_id != "") {
                    $cant_total++;
                    $perfil = $this->getDoctrine()->getRepository('IcanBundle:Rol')
                        ->find($perfil_id);
                    if ($perfil != null) {
                        $usuarios = $this->getDoctrine()->getRepository('IcanBundle:Usuario')
                            ->ListarUsuariosRol($perfil_id);
                        if (count($usuarios) == 0) {

                            $em->remove($perfil);
                            $cant_eliminada++;
                        }
                    }
                }
            }
        }
        $em->flush();

        if ($cant_eliminada == 0) {
            $resultado['success'] = false;
            $resultado['error'] = "No se pudo eliminar los perfiles, porque están asociados a un usuario";
        } else {
            $resultado['success'] = true;

            $mensaje = ($cant_eliminada == $cant_total) ? "La operación se ha realizado correctamente" : "La operación se ha realizado correctamente. Pero atención no se pudo eliminar todas los perfiles seleccionados porque están asociados a un perfil";
            $resultado['message'] = $mensaje;
        }

        return $resultado;
    }

    /**
     * ActualizarPerfil: Actuializa los datos del rol en la BD
     * @param int $rol_id Id
     * @author Marcel
     */
    public function ActualizarPerfil($rol_id, $nombre)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Rol')
            ->find($rol_id);
        if ($entity != null) {
            //Verificar nombre
            $rol = $this->getDoctrine()->getRepository('IcanBundle:Rol')
                ->BuscarPorNombre($nombre);
            if ($rol != null) {
                if ($entity->getRolId() != $rol->getRolId()) {
                    $resultado['success'] = false;
                    $resultado['error'] = "El nombre del perfil está en uso, por favor intente ingrese otro.";
                    return $resultado;
                }
            }

            $entity->setNombre($nombre);
            $em->flush();

            $resultado['success'] = true;
            $resultado['message'] = "La operación se realizó correctamente";
            return $resultado;
        }
    }

    /**
     * SalvarPerfil: Guarda los datos del rol en la BD
     * @param string $nombre Nombre
     * @author Marcel
     */
    public function SalvarPerfil($nombre)
    {
        $em = $this->getDoctrine()->getManager();

        //Verificar nombre
        $rol = $this->getDoctrine()->getRepository('IcanBundle:Rol')
            ->BuscarPorNombre($nombre);
        if ($rol != null) {
            $resultado['success'] = false;
            $resultado['error'] = "El nombre del perfil está en uso, por favor intente ingrese otro.";
            return $resultado;
        }

        $entity = new Entity\Rol();

        $entity->setNombre($nombre);

        $em->persist($entity);
        $em->flush();

        $resultado['success'] = true;
        $resultado['message'] = "La operación se realizó correctamente";

        return $resultado;
    }

    /**
     * ListarPerfiles: Listar los perfiles
     *
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarPerfiles($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0)
    {
        $arreglo_resultado = array();
        $cont = 0;

        $lista = $this->getDoctrine()->getRepository('IcanBundle:Rol')
            ->ListarRoles($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0);

        foreach ($lista as $value) {
            $perfil_id = $value->getRolId();

            $acciones = $this->ListarAcciones($perfil_id);

            $arreglo_resultado[$cont] = array(
                "id" => $perfil_id,
                "nombre" => $value->getNombre(),
                "acciones" => $acciones
            );

            $cont++;
        }

        return $arreglo_resultado;
    }

    /**
     * TotalPerfiles: Total de perfiles
     * @param string $sSearch Para buscar
     * @author Marcel
     */
    public function TotalPerfiles($sSearch)
    {
        $total = $this->getDoctrine()->getRepository('IcanBundle:Rol')
            ->TotalRoles($sSearch);

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
