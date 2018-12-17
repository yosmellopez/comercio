<?php

namespace IcanBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use IcanBundle\Entity;

class NewsletterController extends BaseController
{

    public function indexAction()
    {
        return $this->render('IcanBundle:Newsletter:index.html.twig', array());
    }

    /**
     * listarAction Acción que lista los newsletters de contacto
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
            $total = $this->TotalNewsletters($sSearch, $fecha_inicial, $fecha_fin);
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

            $data = $this->ListarNewsletters($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $fecha_inicial, $fecha_fin);

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
        $newsletter_id = $request->get('newsletter_id');

        $email = $request->get('email');
        $estado = $request->get('estado');

        $resultadoJson = array();
        if ($newsletter_id == "") {
            $resultado = $this->SalvarNewsletter($email, $estado);
        } else {
            $resultado = $this->ActualizarNewsletter($newsletter_id, $email, $estado);
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
        $newsletter_id = $request->get('newsletter_id');

        $resultado = $this->EliminarNewsletter($newsletter_id);
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
     * eliminarNewslettersAction Acción que elimina varios newsletters en la BD
     *
     */
    public function eliminarNewslettersAction(Request $request)
    {
        $ids = $request->get('ids');

        $resultado = $this->EliminarNewsletters($ids);
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
        $newsletter_id = $request->get('newsletter_id');

        $resultado = $this->CargarDatosNewsletter($newsletter_id);
        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['newsletter'] = $resultado['newsletter'];
            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * CargarDatosNewsletter: Carga los datos de un usuario
     *
     * @param int $newsletter_id Id
     *
     * @author Marcel
     */
    public function CargarDatosNewsletter($newsletter_id)
    {
        $resultado = array();
        $arreglo_resultado = array();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Newsletter')
            ->find($newsletter_id);
        if ($entity != null) {
            $arreglo_resultado['email'] = $entity->getEmail();
            $arreglo_resultado['estado'] = ($entity->getEstado() == 1) ? true : false;

            $resultado['success'] = true;
            $resultado['newsletter'] = $arreglo_resultado;
        }
        return $resultado;
    }

    /**
     * EliminarNewsletter: Elimina un newsletter en la BD
     * @param int $newsletter_id Id
     * @author Marcel
     */
    public function EliminarNewsletter($newsletter_id)
    {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Newsletter')
            ->find($newsletter_id);

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
     * EliminarNewsletters: Elimina varios newsletters en la BD
     * @param array $$ids Ids
     * @author Marcel
     */
    public function EliminarNewsletters($ids)
    {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        if ($ids != "") {
            $ids = explode(',', $ids);
            foreach ($ids as $newsletter_id) {
                if ($newsletter_id != "") {
                    $entity = $this->getDoctrine()->getRepository('IcanBundle:Newsletter')
                        ->find($newsletter_id);

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
     * ActualizarNewsletter: Actualiza los datos del newsletter en la BD
     *
     * @param string $newsletter_id Id
     *
     * @author Marcel
     */
    public function ActualizarNewsletter($newsletter_id, $email, $estado)
    {
        $em = $this->getDoctrine()->getManager();

        $resultado = array();
        $entity = $this->getDoctrine()->getRepository('IcanBundle:Newsletter')
            ->find($newsletter_id);
        if ($entity != null) {

            $entity->setEmail($email);
            $entity->setEstado($estado);

            $em->flush();
            $resultado['success'] = true;
        } else {
            $resultado['success'] = false;
            $resultado['error'] = "No existe el registro solicitado";
        }
        return $resultado;
    }

    /**
     * SalvarNewsletter: Guarda los datos del usuario en la BD
     *
     * @author Marcel
     */
    public function SalvarNewsletter($email, $estado)
    {
        $em = $this->getDoctrine()->getManager();
        $resultado = array();

        $entity = new Entity\Newsletter();

        $entity->setEmail($email);
        $entity->setEstado($estado);

        $this->setTimeZone();
        $entity->setFecha(new \DateTime());

        $em->persist($entity);
        $em->flush();
        $resultado['success'] = true;

        return $resultado;
    }

    /**
     * ListarNewsletters: Listar las newsletters
     *
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarNewsletters($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $fecha_inicial, $fecha_fin)
    {
        $arreglo_resultado = array();
        $cont = 0;

        $lista = $this->getDoctrine()->getRepository('IcanBundle:Newsletter')
            ->ListarNewsletters($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $fecha_inicial, $fecha_fin);

        foreach ($lista as $value) {
            $newsletter_id = $value->getNewsletterId();

            $acciones = $this->ListarAcciones($newsletter_id);

            $arreglo_resultado[$cont] = array(
                "id" => $newsletter_id,
                "email" => $value->getEmail(),
                "estado" => ($value->getEstado()) ? 1 : 0,
                "fecha" => $value->getFecha()->format('d-m-Y H:i'),
                "acciones" => $acciones
            );

            $cont++;
        }

        return $arreglo_resultado;
    }

    /**
     * TotalNewsletters: Total de usuarios
     * @param string $sSearch Para buscar
     * @author Marcel
     */
    public function TotalNewsletters($sSearch, $fecha_inicial, $fecha_fin)
    {
        $total = $this->getDoctrine()->getRepository('IcanBundle:Newsletter')
            ->TotalNewsletters($sSearch, $fecha_inicial, $fecha_fin);

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
