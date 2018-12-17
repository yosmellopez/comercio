<?php

namespace IcanBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use IcanBundle\Entity;

class SeoOnPageController extends BaseController
{

    public function indexAction()
    {
        $secciones = $this->ListarSecciones();

        return $this->render('IcanBundle:SeoOnPage:index.html.twig', array(
            'secciones' => $secciones
        ));
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
        //Sort
        $sort = !empty($request->get('sort')) ? $request->get('sort') : array();
        $sSortDir_0 = !empty($sort['sort']) ? $sort['sort'] : 'desc';
        $iSortCol_0 = !empty($sort['field']) ? $sort['field'] : 'titulo';
        //$start and $limit
        $pagination = !empty($request->get('pagination')) ? $request->get('pagination') : array();
        $page = !empty($pagination['page']) ? (int)$pagination['page'] : 1;
        $limit = !empty($pagination['perpage']) ? (int)$pagination['perpage'] : -1;
        $start = 0;

        try {
            $pages = 1;
            $total = $this->TotalPaginas($sSearch);
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

            $data = $this->ListarPaginas($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0);

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
        $pagina_id = $request->get('pagina_id');
        $url = $request->get('url');
        $titulo = $request->get('titulo');
        $descripcion = $request->get('descripcion');
        $tags = $request->get('tags');

        $resultadoJson = array();
        if ($pagina_id == "")
            $resultado = $this->SalvarSeoOnPage($url, $titulo, $descripcion, $tags);
        else
            $resultado = $this->ActualizarSeoOnPage($pagina_id, $url, $titulo, $descripcion, $tags);

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
        $pagina_id = $request->get('pagina_id');

        $resultado = $this->EliminarSeoOnPage($pagina_id);
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
     * eliminarPaginasAction Acción que elimina varias paginas en la BD
     *
     */
    public function eliminarPaginasAction(Request $request)
    {
        $ids = $request->get('ids');

        $resultado = $this->EliminarPaginas($ids);
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
        $pagina_id = $request->get('pagina_id');

        $resultado = $this->CargarDatosSeoOnPage($pagina_id);
        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['pagina'] = $resultado['pagina'];
            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * CargarDatosSeoOnPage: Carga los datos de un usuario
     *
     * @param int $pagina_id Id
     *
     * @author Marcel
     */
    public function CargarDatosSeoOnPage($pagina_id)
    {
        $resultado = array();
        $arreglo_resultado = array();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:SeoOnPage')
            ->find($pagina_id);
        if ($entity != null) {
            $arreglo_resultado['url'] = $entity->getUrl();
            $arreglo_resultado['titulo'] = $entity->getTitulo();
            $arreglo_resultado['descripcion'] = $entity->getDescripcion();
            $arreglo_resultado['tags'] = $entity->getTags();

            $resultado['success'] = true;
            $resultado['pagina'] = $arreglo_resultado;
        }
        return $resultado;
    }

    /**
     * EliminarSeoOnPage: Elimina un pagina en la BD
     * @param int $pagina_id Id
     * @author Marcel
     */
    public function EliminarSeoOnPage($pagina_id)
    {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:SeoOnPage')
            ->find($pagina_id);

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
     * EliminarPaginas: Elimina varias paginas en la BD
     * @param array $$ids Ids
     * @author Marcel
     */
    public function EliminarPaginas($ids)
    {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        if ($ids != "") {
            $ids = explode(',', $ids);
            foreach ($ids as $pagina_id) {
                if ($pagina_id != "") {
                    $entity = $this->getDoctrine()->getRepository('IcanBundle:SeoOnPage')
                        ->find($pagina_id);
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
     * ActualizarSeoOnPage: Actualiza los datos de la pagina en la BD
     *
     * @param string $pagina_id Id
     *
     * @author Marcel
     */
    public function ActualizarSeoOnPage($pagina_id, $url, $titulo, $descripcion, $tags)
    {
        $em = $this->getDoctrine()->getManager();

        $resultado = array();
        $entity = $this->getDoctrine()->getRepository('IcanBundle:SeoOnPage')->find($pagina_id);
        if ($entity != null) {
            $pagina = $this->getDoctrine()->getRepository('IcanBundle:SeoOnPage')
                ->findOneBy(array('url' => $url));
            if ($pagina != null) {
                if ($entity->getPaginaId() != $pagina->getPaginaId()) {
                    $resultado['success'] = false;
                    $resultado['error'] = "Ya se ha definido el Seo On Page para la página seleccionada";
                    return $resultado;
                }
            }

            $entity->setUrl($url);
            $entity->setTitulo($titulo);
            $entity->setDescripcion($descripcion);
            $entity->setTags($tags);

            $em->flush();
            $resultado['success'] = true;
        } else {
            $resultado['success'] = false;
            $resultado['error'] = "No existe el registro solicitado";
        }
        return $resultado;
    }

    /**
     * SalvarSeoOnPage: Guarda los datos del usuario en la BD
     *
     *
     * @author Marcel
     */
    public function SalvarSeoOnPage($url, $titulo, $descripcion, $tags)
    {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        $pagina = $this->getDoctrine()->getRepository('IcanBundle:SeoOnPage')
            ->findOneBy(array('url' => $url));
        if ($pagina != null) {
            $resultado['success'] = false;
            $resultado['error'] = "Ya se ha definido el Seo On Page para la página seleccionada";
            return $resultado;
        }

        $entity = new Entity\SeoOnPage();

        $entity->setUrl($url);
        $entity->setTitulo($titulo);
        $entity->setDescripcion($descripcion);
        $entity->setTags($tags);

        $em->persist($entity);

        $em->flush();
        $resultado['success'] = true;

        return $resultado;
    }

    /**
     * ListarPaginas: Listar los usuarios
     *
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarPaginas($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0)
    {
        $arreglo_resultado = array();
        $cont = 0;

        $lista = $this->getDoctrine()->getRepository('IcanBundle:SeoOnPage')
            ->ListarPaginas($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0);

        foreach ($lista as $value) {
            $pagina_id = $value->getPaginaId();


            $acciones = $this->ListarAcciones($pagina_id);

            $url = $value->getUrl();
            $seccion = $this->BuscarSeccion($url);

            $arreglo_resultado[$cont] = array(
                "id" => $pagina_id,
                "seccion" => $seccion,
                "titulo" => $value->getTitulo(),
                "descripcion" => $value->getDescripcion(),
                "tags" => $value->getTags(),
                "acciones" => $acciones
            );

            $cont++;
        }

        return $arreglo_resultado;
    }

    /**
     * TotalPaginas: Total de usuarios
     * @param string $sSearch Para buscar
     * @author Marcel
     */
    public function TotalPaginas($sSearch)
    {
        $total = $this->getDoctrine()->getRepository('IcanBundle:SeoOnPage')
            ->TotalPaginas($sSearch);

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


    private function ListarSecciones()
    {
        $arreglo_resultado = array();

        //homefrontend
        array_push($arreglo_resultado, array('url' => 'homefrontend', 'seccion' => "Página Principal"));
        //error404
        array_push($arreglo_resultado, array('url' => 'error404', 'seccion' => "Página no Encontrada"));
        //contacto
        array_push($arreglo_resultado, array('url' => 'contacto', 'seccion' => "Contacto"));
        //contactoenviado
        array_push($arreglo_resultado, array('url' => 'listadoproductos', 'seccion' => "Nuestros Productos"));

        return $arreglo_resultado;
    }

    private function BuscarSeccion($url)
    {
        $seccion = "";

        $secciones = $this->ListarSecciones();
        foreach ($secciones as $value) {
            if ($value['url'] == $url) {
                $seccion = $value['seccion'];
                break;
            }
        }

        return $seccion;
    }

}
