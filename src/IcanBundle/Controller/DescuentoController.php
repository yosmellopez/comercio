<?php

namespace IcanBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use IcanBundle\Entity;
use IcanBundle\Util;

class DescuentoController extends BaseController
{

    public function indexAction()
    {
        $porcientos = $this->getDoctrine()->getRepository('IcanBundle:Porciento')
            ->ListarOrdenados();

        $categorias = $this->ListarCategoriasArbol();

        $marcas = $this->getDoctrine()->getRepository('IcanBundle:Marca')
            ->ListarOrdenadas();

        return $this->render('IcanBundle:Descuento:index.html.twig', array(
            'porcientos' => $porcientos,
            'categorias' => $categorias,
            'marcas' => $marcas
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
            $total = $this->TotalDescuentos($sSearch);
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

            $data = $this->ListarDescuentos($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0);

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
        $descuento_id = $request->get('descuento_id');

        $porciento_id = $request->get('porciento');
        $nombre = $request->get('nombre');
        $estado = $request->get('estado');
        $fechainicio = $request->get('fechainicio');
        $fechafin = $request->get('fechafin');

        $productos = $request->get('productos');

        $resultadoJson = array();
        if ($descuento_id == "") {
            $resultado = $this->SalvarDescuento($porciento_id, $nombre, $estado,
                $fechainicio, $fechafin, $productos);
        } else {
            $resultado = $this->ActualizarDescuento($descuento_id, $porciento_id, $nombre, $estado,
                $fechainicio, $fechafin, $productos);
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
        $descuento_id = $request->get('descuento_id');

        $resultado = $this->EliminarDescuento($descuento_id);
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
     * eliminarDescuentosAction Acción que elimina varios descuentos en la BD
     *
     */
    public function eliminarDescuentosAction(Request $request)
    {
        $ids = $request->get('ids');

        $resultado = $this->EliminarDescuentos($ids);
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
        $descuento_id = $request->get('descuento_id');

        $resultado = $this->CargarDatosDescuento($descuento_id);
        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['descuento'] = $resultado['descuento'];

            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * eliminarProductoAction Acción que elimina una descuento de un producto en la BD
     *
     */
    public function eliminarProductoAction(Request $request)
    {
        $id = $request->get('id');

        $resultado = $this->EliminarProducto($id);
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
     * listarUsosAction Acción que lista los usos
     *
     */
    public function listarUsosAction(Request $request)
    {
        // search filter by keywords
        $query = !empty($request->get('query')) ? $request->get('query') : array();
        $sSearch = isset($query['generalSearch']) && is_string($query['generalSearch']) ? $query['generalSearch'] : '';
        $descuento_id = isset($query['descuento_id']) && is_string($query['descuento_id']) ? $query['descuento_id'] : '';
        //Sort
        $sort = !empty($request->get('sort')) ? $request->get('sort') : array();
        $sSortDir_0 = !empty($sort['sort']) ? $sort['sort'] : 'desc';
        $iSortCol_0 = !empty($sort['field']) ? $sort['field'] : 'createdAt';
        //$start and $limit
        $pagination = !empty($request->get('pagination')) ? $request->get('pagination') : array();
        $page = !empty($pagination['page']) ? (int)$pagination['page'] : 1;
        $limit = !empty($pagination['perpage']) ? (int)$pagination['perpage'] : -1;
        $start = 0;

        try {
            $pages = 1;
            $total = ($descuento_id != "") ? $this->TotalUsos($sSearch, $descuento_id) : 0;
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

            $data = ($descuento_id != "") ? $this->ListarUsos($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $descuento_id) : array();

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
     * ListarUsos: Listar los usos
     *
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarUsos($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $descuento_id)
    {
        $arreglo_resultado = array();
        $cont = 0;

        $lista = $this->getDoctrine()->getRepository('IcanBundle:DescuentoUso')
            ->ListarUsos($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $descuento_id);

        foreach ($lista as $value) {

            $rut = $value->getRut();
            $rut = $this->FormatearRut($rut);

            $arreglo_resultado[$cont] = array(
                "id" => $value->getUsoId(),
                "rut" => $rut,
                "email" => $value->getEmail(),
                "createdAt" => $value->getCreatedAt()->format('d/m/Y H:i')
            );
            $cont++;
        }

        return $arreglo_resultado;
    }

    /**
     * TotalUsos: Total de usos
     * @param string $sSearch Para buscar
     * @author Marcel
     */
    public function TotalUsos($sSearch, $descuento_id)
    {
        $total = $this->getDoctrine()->getRepository('IcanBundle:DescuentoUso')
            ->TotalUsos($sSearch, $descuento_id);

        return $total;
    }


    /**
     * EliminarProducto: Elimina un producto de la descuento en la BD
     * @param int $producto_id Id
     * @author Marcel
     */
    public function EliminarProducto($id)
    {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:DescuentoProducto')
            ->find($id);

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
     * CargarDatosDescuento: Carga los datos de un usuario
     *
     * @param int $descuento_id Id
     *
     * @author Marcel
     */
    public function CargarDatosDescuento($descuento_id)
    {
        $resultado = array();
        $arreglo_resultado = array();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Descuento')
            ->find($descuento_id);
        if ($entity != null) {

            $arreglo_resultado['codigo'] = $entity->getCodigo();
            $arreglo_resultado['nombre'] = $entity->getNombre();
            $arreglo_resultado['porciento'] = ($entity->getPorciento() != null) ? $entity->getPorciento()->getPorcientoId() : "";
            $arreglo_resultado['estado'] = ($entity->getEstado() == 1) ? true : false;

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

            //Productos relacionados
            $ruta = $this->ObtenerURL();
            $dir = 'uploads/productos/';
            $ruta = $ruta . $dir;

            $relacionados = array();
            $descuento_productos = $this->getDoctrine()->getRepository('IcanBundle:DescuentoProducto')
                ->ListarProductos($descuento_id);
            $posicion = 0;
            foreach ($descuento_productos as $key => $descuento_producto) {
                $producto = $descuento_producto->getProducto();
                array_push($relacionados, array(
                    'id' => $descuento_producto->getId(),
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
            $resultado['descuento'] = $arreglo_resultado;
        }
        return $resultado;
    }

    /**
     * EliminarDescuento: Elimina un descuento en la BD
     * @param int $descuento_id Id
     * @author Marcel
     */
    public function EliminarDescuento($descuento_id)
    {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Descuento')
            ->find($descuento_id);

        if ($entity != null) {

            // Quitar descuento a los productos relacionados
            $descuento_productos = $this->getDoctrine()->getRepository('IcanBundle:DescuentoProducto')
                ->ListarProductos($descuento_id);
            foreach ($descuento_productos as $descuento_producto) {
                $em->remove($descuento_producto);
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
     * EliminarDescuentos: Elimina varios descuentos en la BD
     * @param array $$ids Ids
     * @author Marcel
     */
    public function EliminarDescuentos($ids)
    {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        if ($ids != "") {
            $ids = explode(',', $ids);
            $cant_total = 0;
            $cant_eliminada = 0;
            foreach ($ids as $descuento_id) {
                if ($descuento_id != "") {
                    $cant_total++;
                    $entity = $this->getDoctrine()->getRepository('IcanBundle:Descuento')
                        ->find($descuento_id);
                    if ($entity != null) {

                        // Quitar descuento a los productos relacionados
                        $descuento_productos = $this->getDoctrine()->getRepository('IcanBundle:DescuentoProducto')
                            ->ListarProductos($descuento_id);
                        foreach ($descuento_productos as $descuento_producto) {
                            $em->remove($descuento_producto);
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
            $resultado['error'] = "No se pudo eliminar los descuentos, porque tienen cotizaciones asociadas";
        } else {
            $resultado['success'] = true;

            $mensaje = ($cant_eliminada == $cant_total) ? "La operación se ha realizado correctamente" : "La operación se ha realizado correctamente. Pero atención no se pudo eliminar todos los descuentos asociados porque tienen cotizaciones asociadas";
            $resultado['message'] = $mensaje;
        }
        return $resultado;
    }

    /**
     * ActualizarDescuento: Actualiza los datos del descuento en la BD
     *
     * @param string $descuento_id Id
     *
     * @author Marcel
     */
    public function ActualizarDescuento($descuento_id, $porciento_id, $nombre, $estado, $fechainicio, $fechafin, $productos)
    {
        $em = $this->getDoctrine()->getManager();

        $resultado = array();
        $entity = $this->getDoctrine()->getRepository('IcanBundle:Descuento')->find($descuento_id);

        if ($entity != null) {

            $naux = $this->getDoctrine()->getRepository('IcanBundle:Descuento')
                ->findOneByNombre($nombre);
            if (!empty($naux)) {
                if ($descuento_id != $naux->getDescuentoId()) {
                    $resultado['success'] = false;
                    $resultado['error'] = "El nombre ingresado ya está en uso por otro descuento especial.";
                    return $resultado;
                }
            }

            $entity->setNombre($nombre);
            $entity->setEstado($estado);

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

            //Productos
            if (count($productos) > 0) {
                foreach ($productos as $value) {
                    $id = $value['id'];
                    $producto_id = $value['producto_id'];

                    $producto = $this->getDoctrine()->getRepository('IcanBundle:Producto')
                        ->find($producto_id);
                    if ($producto != null) {

                        $descuento_producto = $this->getDoctrine()->getRepository('IcanBundle:DescuentoProducto')
                            ->find($id);
                        $is_new = false;
                        if ($descuento_producto == null) {
                            $descuento_producto = new Entity\DescuentoProducto();
                            $is_new = true;
                        }

                        $descuento_producto->setProducto($producto);

                        if ($is_new) {
                            $descuento_producto->setDescuento($entity);
                            $em->persist($descuento_producto);
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
     * SalvarDescuento: Guarda los datos del usuario en la BD
     *
     *
     * @author Marcel
     */
    public function SalvarDescuento($porciento_id, $nombre, $estado, $fechainicio, $fechafin, $productos)
    {
        $em = $this->getDoctrine()->getManager();
        $resultado = array();

        $entity = new Entity\Descuento();

        //generar un codigo
        $codigo = $this->generarCadenaAleatoriaMix();
        $caux = $this->getDoctrine()->getRepository('IcanBundle:Descuento')
            ->findOneByCodigo($codigo);
        while (!empty($caux)) {
            $codigo = $this->generarCadenaAleatoriaMix();
            $caux = $this->getDoctrine()->getRepository('IcanBundle:Descuento')
                ->findOneByCodigo($codigo);
        }
        $entity->setCodigo($codigo);

        $naux = $this->getDoctrine()->getRepository('IcanBundle:Descuento')
            ->findOneByNombre($nombre);
        if (!empty($naux)) {
            $resultado['success'] = false;
            $resultado['error'] = "El nombre ingresado ya está en uso por otro descuento especial.";
            return $resultado;
        }

        $entity->setNombre($nombre);
        $entity->setEstado($estado);

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

        $em->persist($entity);
        $em->flush();


        //Productos
        if (count($productos) > 0) {
            foreach ($productos as $value) {
                $producto_id = $value['producto_id'];

                $producto = $this->getDoctrine()->getRepository('IcanBundle:Producto')
                    ->find($producto_id);
                if ($producto != null) {
                    $descuento_producto = new Entity\DescuentoProducto();
                    $descuento_producto->setDescuento($entity);
                    $descuento_producto->setProducto($producto);
                    $em->persist($descuento_producto);
                }
            }
        }

        $em->flush();

        $resultado['success'] = true;

        return $resultado;
    }

    /**
     * ListarDescuentos: Listar las descuentos
     *
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarDescuentos($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0)
    {
        $arreglo_resultado = array();
        $cont = 0;

        $lista = $this->getDoctrine()->getRepository('IcanBundle:Descuento')
            ->ListarDescuentos($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0);

        foreach ($lista as $value) {
            $descuento_id = $value->getDescuentoId();

            $acciones = $this->ListarAcciones($descuento_id);


            $arreglo_resultado[$cont] = array(
                "id" => $descuento_id,
                "nombre" => $value->getNombre(),
                "valor" => ($value->getPorciento() != null) ? $value->getPorciento()->getValor() : "",
                "estado" => ($value->getEstado()) ? 1 : 0,
                "fechainicio" => $value->getFechainicio() != "" ? $value->getFechainicio()->format("d/m/Y H:i") : "",
                "fechafin" => $value->getFechafin() != "" ? $value->getFechafin()->format("d/m/Y H:i") : "",
                "acciones" => $acciones
            );

            $cont++;
        }

        return $arreglo_resultado;
    }

    /**
     * TotalDescuentos: Total de usuarios
     * @param string $sSearch Para buscar
     * @author Marcel
     */
    public function TotalDescuentos($sSearch)
    {
        $total = $this->getDoctrine()->getRepository('IcanBundle:Descuento')
            ->TotalDescuentos($sSearch);

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
