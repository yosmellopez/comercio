<?php

namespace IcanBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use IcanBundle\Entity;

class PorcientoController extends BaseController
{

    public function indexAction()
    {
        return $this->render('IcanBundle:Porciento:index.html.twig', array());
    }

    /**
     * listarAction Acción que lista los porcientos
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
        $iSortCol_0 = !empty($sort['field']) ? $sort['field'] : 'valor';
        //$start and $limit
        $pagination = !empty($request->get('pagination')) ? $request->get('pagination') : array();
        $page = !empty($pagination['page']) ? (int)$pagination['page'] : 1;
        $limit = !empty($pagination['perpage']) ? (int)$pagination['perpage'] : -1;
        $start = 0;

        try {
            $pages = 1;
            $total = $this->TotalPorcientos($sSearch);
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

            $data = $this->ListarPorcientos($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0);

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
        $porciento_id = $request->get('porciento_id');

        $valor = $request->get('valor');

        if ($porciento_id == "") {
            $resultado = $this->SalvarPorciento($valor);
        } else {
            $resultado = $this->ActualizarPorciento($porciento_id, $valor);
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
     * eliminarAction Acción que elimina un porciento en la BD
     *
     */
    public function eliminarAction(Request $request)
    {
        $porciento_id = $request->get('porciento_id');

        $resultado = $this->EliminarPorciento($porciento_id);
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
     * eliminarPorcientosAction Acción que elimina los porcientos seleccionados en la BD
     *
     */
    public function eliminarPorcientosAction(Request $request)
    {
        $ids = $request->get('ids');

        $resultado = $this->EliminarPorcientos($ids);
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
     * cargarDatosAction Acción que carga los datos del porciento en la BD
     *
     */
    public function cargarDatosAction(Request $request)
    {
        $porciento_id = $request->get('porciento_id');

        $resultado = $this->CargarDatosPorciento($porciento_id);
        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['porciento'] = $resultado['porciento'];

            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];

            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * CargarDatosPorciento: Carga los datos de un porciento
     *
     * @param int $porciento_id Id
     *
     * @author Marcel
     */
    public function CargarDatosPorciento($porciento_id)
    {
        $resultado = array();
        $arreglo_resultado = array();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Porciento')
            ->find($porciento_id);
        if ($entity != null) {

            $arreglo_resultado['valor'] = $entity->getValor();

            $resultado['success'] = true;
            $resultado['porciento'] = $arreglo_resultado;
        }

        return $resultado;
    }

    /**
     * EliminarPorciento: Elimina un rol en la BD
     * @param int $porciento_id Id
     * @author Marcel
     */
    public function EliminarPorciento($porciento_id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Porciento')
            ->find($porciento_id);

        if ($entity != null) {

            //Productos
            $productos = $this->getDoctrine()->getRepository('IcanBundle:Producto')
                ->ListarProductosDePorciento($porciento_id);
            if (count($productos) > 0) {
                $resultado['success'] = false;
                $resultado['error'] = "No se pudo eliminar el porciento de descuento, porque tiene productos asociados";
                return $resultado;
            }

            //Promociones
            $promociones = $this->getDoctrine()->getRepository('IcanBundle:Promocion')
                ->ListarPromocionesDePorciento($porciento_id);
            if (count($promociones) > 0) {
                $resultado['success'] = false;
                $resultado['error'] = "No se pudo eliminar el porciento de descuento, porque tiene promociones asociadas";
                return $resultado;
            }

            //Descuentos
            $descuentos = $this->getDoctrine()->getRepository('IcanBundle:Descuento')
                ->ListarDescuentosDePorciento($porciento_id);
            if (count($descuentos) > 0) {
                $resultado['success'] = false;
                $resultado['error'] = "No se pudo eliminar el porciento de descuento, porque tiene descuentos asociados";
                return $resultado;
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
     * EliminarPorcientos: Elimina los porcientos seleccionados en la BD
     * @param int $ids Ids
     * @author Marcel
     */
    public function EliminarPorcientos($ids)
    {
        $em = $this->getDoctrine()->getManager();

        if ($ids != "") {
            $ids = explode(',', $ids);
            $cant_eliminada = 0;
            $cant_total = 0;
            foreach ($ids as $porciento_id) {
                if ($porciento_id != "") {
                    $cant_total++;
                    $entity = $this->getDoctrine()->getRepository('IcanBundle:Porciento')
                        ->find($porciento_id);
                    if ($entity != null) {

                        //Productos
                        $productos = $this->getDoctrine()->getRepository('IcanBundle:Producto')
                            ->ListarProductosDePorciento($porciento_id);
                        //Promociones
                        $promociones = $this->getDoctrine()->getRepository('IcanBundle:Promocion')
                            ->ListarPromocionesDePorciento($porciento_id);
                        //Descuentos
                        $descuentos = $this->getDoctrine()->getRepository('IcanBundle:Descuento')
                            ->ListarDescuentosDePorciento($porciento_id);

                        if (count($productos) == 0 && count($promociones) == 0 && count($descuentos) == 0) {

                            //Eliminar foto
                            $foto_eliminar = $entity->getImagen();
                            if ($foto_eliminar != "") {
                                $dir = 'uploads/porcientos/';
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
            $resultado['error'] = "No se pudo eliminar las porcientos, porque están asociados a un producto, promociones o descuentos";
        } else {
            $resultado['success'] = true;

            $mensaje = ($cant_eliminada == $cant_total) ? "La operación se ha realizado correctamente" : "La operación se ha realizado correctamente. Pero atención no se pudo eliminar todas las porcientos seleccionadas porque están asociadas a un producto, promociones o descuentos";
            $resultado['message'] = $mensaje;
        }

        return $resultado;
    }

    /**
     * ActualizarPorciento: Actualiza los datos del porciento en la BD
     *
     * @param string $porciento_id Id
     *
     * @author Marcel
     */
    public function ActualizarPorciento($porciento_id, $valor)
    {
        $em = $this->getDoctrine()->getManager();

        $resultado = array();
        $entity = $this->getDoctrine()->getRepository('IcanBundle:Porciento')->find($porciento_id);
        if ($entity != null) {
            //Verificar valor
            $porciento = $this->getDoctrine()->getRepository('IcanBundle:Porciento')
                ->findOneBy(array('valor' => $valor));
            if ($porciento != null) {
                if ($porciento_id != $porciento->getPorcientoId()) {
                    $resultado['success'] = false;
                    $resultado['error'] = "El valor del porciento está en uso, por favor intente ingrese otro.";
                    return $resultado;
                }
            }

            $entity->setValor($valor);

            $em->flush();
            $resultado['success'] = true;
        } else {
            $resultado['success'] = false;
            $resultado['error'] = "No existe un porciento que se corresponda con ese identificador";
        }
        return $resultado;
    }

    /**
     * SalvarPorciento: Guarda los datos del usuario en la BD
     *
     *
     * @author Marcel
     */
    public function SalvarPorciento($valor)
    {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        //Verificar valor
        $porciento = $this->getDoctrine()->getRepository('IcanBundle:Porciento')
            ->findOneBy(array('valor' => $valor));
        if ($porciento != null) {
            $resultado['success'] = false;
            $resultado['error'] = "El valor del porciento está en uso, por favor intente ingrese otro.";
            return $resultado;
        }

        $entity = new Entity\Porciento();

        $entity->setValor($valor);

        $em->persist($entity);
        $em->flush();

        $resultado['success'] = true;

        return $resultado;
    }

    /**
     * ListarPorcientos: Listar los porcientos
     *
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarPorcientos($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0)
    {
        $arreglo_resultado = array();
        $cont = 0;

        $lista = $this->getDoctrine()->getRepository('IcanBundle:Porciento')
            ->ListarPorcientos($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0);

        foreach ($lista as $value) {
            $porciento_id = $value->getPorcientoId();

            $acciones = $this->ListarAcciones($porciento_id);

            $arreglo_resultado[$cont] = array(
                "id" => $porciento_id,
                "valor" => $value->getValor(),
                "acciones" => $acciones
            );

            $cont++;
        }

        return $arreglo_resultado;
    }

    /**
     * TotalPorcientos: Total de porcientos
     * @param string $sSearch Para buscar
     * @author Marcel
     */
    public function TotalPorcientos($sSearch)
    {
        $total = $this->getDoctrine()->getRepository('IcanBundle:Porciento')
            ->TotalPorcientos($sSearch);

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
