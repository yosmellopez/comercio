<?php

namespace IcanBundle\Controller;

use Spipu\Html2Pdf\Html2Pdf;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use IcanBundle\Entity;

class TransaccionWebpayController extends BaseController
{

    public function indexAction()
    {
        return $this->render('IcanBundle:TransaccionWebpay:index.html.twig', array());
    }

    /**
     * listarAction Acción que lista los transacciones de contacto
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
        $iSortCol_0 = !empty($sort['field']) ? $sort['field'] : 'transactionDate';
        //$start and $limit
        $pagination = !empty($request->get('pagination')) ? $request->get('pagination') : array();
        $page = !empty($pagination['page']) ? (int)$pagination['page'] : 1;
        $limit = !empty($pagination['perpage']) ? (int)$pagination['perpage'] : -1;
        $start = 0;

        try {
            $pages = 1;
            $total = $this->TotalTransacciones($sSearch, $fecha_inicial, $fecha_fin);
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

            $data = $this->ListarTransacciones($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $fecha_inicial, $fecha_fin);

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
     * eliminarAction Acción que elimina un rol en la BD
     *
     */
    public function eliminarAction(Request $request)
    {
        $transaccion_id = $request->get('transaccion_id');

        $resultado = $this->EliminarTransaccion($transaccion_id);
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
     * eliminarTransaccionesAction Acción que elimina varios transacciones en la BD
     *
     */
    public function eliminarTransaccionesAction(Request $request)
    {
        $ids = $request->get('ids');

        $resultado = $this->EliminarTransacciones($ids);
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
        $transaccion_id = $request->get('transaccion_id');

        $resultado = $this->CargarDatosTransaccion($transaccion_id);
        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['transaccion'] = $resultado['transaccion'];
            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * CargarDatosTransaccion: Carga los datos de un usuario
     *
     * @param int $transaccion_id Id
     *
     * @author Marcel
     */
    public function CargarDatosTransaccion($transaccion_id)
    {
        $resultado = array();
        $arreglo_resultado = array();

        $transaccion_entity = $this->getDoctrine()->getRepository('IcanBundle:TransaccionWebpay')
            ->find($transaccion_id);
        if ($transaccion_entity != null) {
            $entity = $transaccion_entity->getCotizacion();
            $arreglo_resultado['cotizacion_id'] = $entity->getCotizacionId();

            $usuario_id = ($entity->getUsuario() != null) ? $entity->getUsuario()->getUsuarioId() : "";
            $arreglo_resultado['usuario_id'] = $usuario_id;

            $rut = $entity->getRut();
            $rut = $this->FormatearRut($rut);
            $arreglo_resultado['rut'] = $rut;

            $arreglo_resultado['nombre'] = $entity->getNombre();
            $arreglo_resultado['apellidos'] = $entity->getApellidos();
            $arreglo_resultado['nombreCompleto'] = $entity->getNombreCompleto();
            $arreglo_resultado['email'] = $entity->getEmail();
            $arreglo_resultado['telefono'] = $entity->getTelefono();
            $arreglo_resultado['calle'] = $entity->getCalle();
            $arreglo_resultado['numero'] = $entity->getNumero();
            $arreglo_resultado['comentario'] = $entity->getComentario();
            $arreglo_resultado['fecha'] = $entity->getFecha()->format('d-m-Y H:i');

            $ruta = $this->ObtenerURL();
            $dir = 'uploads/productos/';
            $ruta = $ruta . $dir;

            //Productos
            $total_cantidad = 0;
            $total_precio = 0;
            $total = 0;
            $productos = array();
            $cont_productos = 0;
            $descuento = 0;
            $transaccion_productos = $this->getDoctrine()->getRepository('IcanBundle:CotizacionProducto')
                ->ListarProductos($transaccion_id);
            foreach ($transaccion_productos as $key => $transaccion_producto) {
                $producto = $transaccion_producto->getProducto();

                $cotizacion_producto_id = $transaccion_producto->getCotizacionproductoId();
                $productos[$cont_productos]['cotizacionproducto_id'] = $cotizacion_producto_id;
                $productos[$cont_productos]['posicion'] = $key;
                //Datos del producto
                $productos[$cont_productos]['producto_id'] = $producto->getProductoId();

                $imagen = $producto->getImagen();
                $productos[$cont_productos]['imagen'] = $ruta . $imagen;

                $productos[$cont_productos]['nombre'] = $producto->getNombre();
                $productos[$cont_productos]['precio'] = $producto->getPrecio();
                $productos[$cont_productos]['precioEspecial'] = $producto->getPrecioEspecial();
                $productos[$cont_productos]['categoria'] = ($producto->getCategoria() != null) ? $producto->getCategoria()->getNombre() : "";
                $productos[$cont_productos]['marca'] = ($producto->getMarca() != null) ? $producto->getMarca()->getNombre() : "";

                $descuento += $transaccion_producto->getDescuento();

                $total_cantidad += $transaccion_producto->getCantidad();
                $total_precio += $producto->getPrecio();
                $total += $transaccion_producto->getCantidad() * $producto->getPrecio();


                $cantidad = $transaccion_producto->getCantidad();
                $productos[$cont_productos]['cantidad'] = $cantidad;

                $productos[$cont_productos]['total'] = $transaccion_producto->getCantidad() * $producto->getPrecio();


                $cont_productos++;
            }
            $arreglo_resultado['productos'] = $productos;
            $arreglo_resultado['total_cantidad'] = $total_cantidad;
            $arreglo_resultado['total_precio'] = $total_precio;
            $arreglo_resultado['descuento'] = $descuento;
            $arreglo_resultado['total'] = $total - $descuento;

            //Datos de la transaccion
            $transaccion['transaccion_id'] = $transaccion_entity->getTransaccionId();

            $transaction_date = $transaccion_entity->getTransactionDate();
            $transaction_date = ($transaction_date != "") ? $transaction_date->format("d/m/Y H:i:s") : "";
            $transaccion['transactionDate'] = $transaction_date;

            $transaccion['cardNumber'] = $transaccion_entity->getCardNumber();
            $transaccion['cardExpirationDate'] = $transaccion_entity->getCardExpirationDate();
            $transaccion['authorizationCode'] = $transaccion_entity->getAuthorizationCode();

            $payment_type_code = $transaccion_entity->getPaymentTypeCode();
            //$payment_type_descripcion = $this->DevolverDescripcionDeTransaccionPaymentTypeCode($payment_type_code);
            $transaccion['paymentTypeCode'] = $payment_type_code;

            $transaccion['responseCode'] = $transaccion_entity->getResponseCode();
            $transaccion['amount'] = $transaccion_entity->getAmount();
            $transaccion['sharesAmount'] = $transaccion_entity->getSharesAmount();
            $transaccion['sharesNumber'] = $transaccion_entity->getSharesNumber();
            $transaccion['commerceCode'] = $transaccion_entity->getCommerceCode();
            $transaccion['token'] = $transaccion_entity->getToken();

            $arreglo_resultado['transaccion'] = $transaccion;

            $resultado['success'] = true;
            $resultado['transaccion'] = $arreglo_resultado;
        }
        return $resultado;
    }

    /**
     * EliminarTransaccion: Elimina un transaccion en la BD
     * @param int $transaccion_id Id
     * @author Marcel
     */
    public function EliminarTransaccion($transaccion_id)
    {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:TransaccionWebpay')
            ->find($transaccion_id);

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
     * EliminarTransacciones: Elimina varios transacciones en la BD
     * @param array $$ids Ids
     * @author Marcel
     */
    public function EliminarTransacciones($ids)
    {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        if ($ids != "") {
            $ids = explode(',', $ids);
            foreach ($ids as $transaccion_id) {
                if ($transaccion_id != "") {
                    $entity = $this->getDoctrine()->getRepository('IcanBundle:TransaccionWebpay')
                        ->find($transaccion_id);

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
     * ListarTransacciones: Listar las transacciones
     *
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarTransacciones($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $fecha_inicial, $fecha_fin)
    {
        $arreglo_resultado = array();
        $cont = 0;

        $lista = $this->getDoctrine()->getRepository('IcanBundle:TransaccionWebpay')
            ->ListarTransacciones($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $fecha_inicial, $fecha_fin);

        foreach ($lista as $value) {
            $transaccion_id = $value->getTransaccionId();

            $acciones = $this->ListarAcciones($transaccion_id);

            $arreglo_resultado[$cont] = array(
                "id" => $transaccion_id,
                "cotizacionId" => $value->getCotizacion()->getCotizacionId(),
                "nombre" => $value->getCotizacion()->getNombre() . " " . $value->getCotizacion()->getApellidos(),
                "email" => $value->getCotizacion()->getEmail(),
                "telefono" => $value->getCotizacion()->getTelefono(),
                "calle" => $value->getCotizacion()->getCalle() . " " . $value->getCotizacion()->getNumero(),
                "cardNumber" => $value->getCardNumber(),
                "cardExpirationDate" => $value->getCardExpirationDate(),
                "authorizationCode" => $value->getAuthorizationCode(),
                "paymentTypeCode" => $value->getPaymentTypeCode(),
                "responseCode" => $value->getResponseCode(),
                "amount" => number_format($value->getAmount(), 0, ',', '.'),
                "sharesAmount" => $value->getSharesAmount(),
                "sharesNumber" => $value->getSharesNumber(),
                "transactionDate" => $value->getTransactionDate()->format('d-m-Y H:i'),
                "acciones" => $acciones
            );

            $cont++;
        }

        return $arreglo_resultado;
    }

    /**
     * TotalTransacciones: Total de usuarios
     * @param string $sSearch Para buscar
     * @author Marcel
     */
    public function TotalTransacciones($sSearch, $fecha_inicial, $fecha_fin)
    {
        $total = $this->getDoctrine()->getRepository('IcanBundle:TransaccionWebpay')
            ->TotalTransacciones($sSearch, $fecha_inicial, $fecha_fin);

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
