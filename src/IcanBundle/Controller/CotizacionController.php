<?php

namespace IcanBundle\Controller;

use Spipu\Html2Pdf\Html2Pdf;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use IcanBundle\Entity;

class CotizacionController extends BaseController
{

    public function indexAction()
    {
        $categorias = $this->ListarCategoriasArbol();

        $marcas = $this->getDoctrine()->getRepository('IcanBundle:Marca')
            ->ListarOrdenadas();

        $usuarios = $this->ListarUsuariosClientes();

        return $this->render('IcanBundle:Cotizacion:index.html.twig', array(
            'categorias' => $categorias,
            'marcas' => $marcas,
            'usuarios' => $usuarios
        ));
    }

    /**
     * listarAction Acción que lista los cotizaciones de contacto
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
            $total = $this->TotalCotizaciones($sSearch, $fecha_inicial, $fecha_fin);
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

            $data = $this->ListarCotizaciones($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $fecha_inicial, $fecha_fin);

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
        $cotizacion_id = $request->get('cotizacion_id');

        $usuario_id = $request->get('usuario');

        $rut = $request->get('rut');
        $nombre = $request->get('nombre');
        $apellidos = $request->get('apellidos');
        $telefono = $request->get('telefono');
        $email = $request->get('email');
        $calle = $request->get('calle');
        $numero = $request->get('numero');

        $comentario = $request->get('comentario');

        $productos = $request->get('productos');

        $resultadoJson = array();
        if ($cotizacion_id == "") {
            $resultado = $this->SalvarCotizacion($usuario_id, $rut, $nombre, $apellidos, $email, $telefono, $calle,
                $numero, $comentario, $productos);
        } else {
            $resultado = $this->ActualizarCotizacion($cotizacion_id, $usuario_id, $rut, $nombre, $apellidos, $email, $telefono, $calle,
                $numero, $comentario, $productos);
        }

        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['ruta'] = $resultado['ruta'];
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
        $cotizacion_id = $request->get('cotizacion_id');

        $resultado = $this->EliminarCotizacion($cotizacion_id);
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
     * eliminarCotizacionesAction Acción que elimina varios cotizaciones en la BD
     *
     */
    public function eliminarCotizacionesAction(Request $request)
    {
        $ids = $request->get('ids');

        $resultado = $this->EliminarCotizaciones($ids);
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
        $cotizacion_id = $request->get('cotizacion_id');

        $resultado = $this->CargarDatosCotizacion($cotizacion_id);
        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['cotizacion'] = $resultado['cotizacion'];
            return new Response(json_encode($resultadoJson));
        } else {
            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['error'] = $resultado['error'];
            return new Response(json_encode($resultadoJson));
        }
    }

    /**
     * descargarOrdenAction Acción que descarga la orden
     *
     */
    public function descargarOrdenAction($cotizacion_id)
    {

        $this->HacerPDF($cotizacion_id);

        $path = $this->get('kernel')->getRootDir() . "/../web/uploads/cotizacion/";
        $archivo = "orden-" . $cotizacion_id . ".pdf";
        $content = file_get_contents($path . $archivo);

        $response = new Response();
        //set headers
        $response->headers->set('Content-Type', 'mime/type');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $archivo);

        $response->setContent($content);
        return $response;
    }

    /**
     * eliminarProductoAction Acción que elimina un producto en la BD
     *
     */
    public function eliminarProductoAction(Request $request)
    {
        $cotizacionproducto_id = $request->get('cotizacionproducto_id');

        $resultado = $this->EliminarProducto($cotizacionproducto_id);
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
     * EliminarProducto: Elimina un producto de la cotizacion en la BD
     * @param int $cotizacionproducto_id Id
     * @author Marcel
     */
    public function EliminarProducto($cotizacionproducto_id)
    {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:CotizacionProducto')
            ->find($cotizacionproducto_id);

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
     * CargarDatosCotizacion: Carga los datos de un usuario
     *
     * @param int $cotizacion_id Id
     *
     * @author Marcel
     */
    public function CargarDatosCotizacion($cotizacion_id)
    {
        $resultado = array();
        $arreglo_resultado = array();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Cotizacion')
            ->find($cotizacion_id);
        if ($entity != null) {
            $arreglo_resultado['cotizacion_id'] = $cotizacion_id;

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
            $cotizacion_productos = $this->getDoctrine()->getRepository('IcanBundle:CotizacionProducto')
                ->ListarProductos($cotizacion_id);
            foreach ($cotizacion_productos as $key => $cotizacion_producto) {
                $producto = $cotizacion_producto->getProducto();

                $cotizacion_producto_id = $cotizacion_producto->getCotizacionproductoId();
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

                $descuento += $cotizacion_producto->getDescuento();

                $total_cantidad += $cotizacion_producto->getCantidad();
                $total_precio += $producto->getPrecio();
                $total += $cotizacion_producto->getCantidad() * $producto->getPrecio();


                $cantidad = $cotizacion_producto->getCantidad();
                $productos[$cont_productos]['cantidad'] = $cantidad;

                $productos[$cont_productos]['total'] = $cotizacion_producto->getCantidad() * $producto->getPrecio();


                $cont_productos++;
            }
            $arreglo_resultado['productos'] = $productos;
            $arreglo_resultado['total_cantidad'] = $total_cantidad;
            $arreglo_resultado['total_precio'] = $total_precio;
            $arreglo_resultado['descuento'] = $descuento;
            $arreglo_resultado['total'] = $total - $descuento;

            //Datos de la transaccion
            $transaccion_entity = $this->getDoctrine()->getRepository('IcanBundle:TransaccionWebpay')
                ->BuscarTransaccion($cotizacion_id);
            if ($transaccion_entity != null) {
                $transaccion = array();

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
            } else {
                $transaccion = false;
            }
            $arreglo_resultado['transaccion'] = $transaccion;

            $resultado['success'] = true;
            $resultado['cotizacion'] = $arreglo_resultado;
        }
        return $resultado;
    }

    /**
     * EliminarCotizacion: Elimina un cotizacion en la BD
     * @param int $cotizacion_id Id
     * @author Marcel
     */
    public function EliminarCotizacion($cotizacion_id)
    {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Cotizacion')
            ->find($cotizacion_id);

        if ($entity != null) {

            //Productos
            $productos = $this->getDoctrine()->getRepository('IcanBundle:CotizacionProducto')
                ->ListarProductos($cotizacion_id);
            foreach ($productos as $producto) {
                $em->remove($producto);
            }

            //Eliminar orden pdf
            $ficha_eliminar = "orden-" . $cotizacion_id . ".pdf";
            $ficha_eliminar_cliente = "orden-cliente-" . $cotizacion_id . ".pdf";
            if ($ficha_eliminar != "") {
                $dir = 'uploads/cotizacion/';
                if (is_file($dir . $ficha_eliminar)) {
                    unlink($dir . $ficha_eliminar);
                }
                if (is_file($dir . $ficha_eliminar_cliente)) {
                    unlink($dir . $ficha_eliminar_cliente);
                }
            }

            //Eliminar transaccion
            $transaccion_webpay = $this->getDoctrine()->getRepository('IcanBundle:TransaccionWebpay')
                ->BuscarTransaccion($cotizacion_id);
            if ($transaccion_webpay != null) {
                $em->remove($transaccion_webpay);
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
     * EliminarCotizaciones: Elimina varios cotizaciones en la BD
     * @param array $$ids Ids
     * @author Marcel
     */
    public function EliminarCotizaciones($ids)
    {
        $resultado = array();
        $em = $this->getDoctrine()->getManager();

        if ($ids != "") {
            $ids = explode(',', $ids);
            foreach ($ids as $cotizacion_id) {
                if ($cotizacion_id != "") {
                    $entity = $this->getDoctrine()->getRepository('IcanBundle:Cotizacion')
                        ->find($cotizacion_id);

                    if ($entity != null) {

                        //Productos
                        $productos = $this->getDoctrine()->getRepository('IcanBundle:CotizacionProducto')
                            ->ListarProductos($cotizacion_id);
                        foreach ($productos as $producto) {
                            $em->remove($producto);
                        }

                        //Eliminar orden pdf
                        $ficha_eliminar = "orden-" . $cotizacion_id . ".pdf";
                        $ficha_eliminar_cliente = "orden-cliente-" . $cotizacion_id . ".pdf";
                        if ($ficha_eliminar != "") {
                            $dir = 'uploads/cotizacion/';
                            if (is_file($dir . $ficha_eliminar)) {
                                unlink($dir . $ficha_eliminar);
                            }
                            if (is_file($dir . $ficha_eliminar_cliente)) {
                                unlink($dir . $ficha_eliminar_cliente);
                            }
                        }

                        //Eliminar transaccion
                        $transaccion_webpay = $this->getDoctrine()->getRepository('IcanBundle:TransaccionWebpay')
                            ->BuscarTransaccion($cotizacion_id);
                        if ($transaccion_webpay != null) {
                            $em->remove($transaccion_webpay);
                        }

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
     * ActualizarCotizacion: Actualiza los datos del cotizacion en la BD
     *
     * @param string $cotizacion_id Id
     *
     * @author Marcel
     */
    public function ActualizarCotizacion($cotizacion_id, $usuario_id, $rut, $nombre, $apellidos, $email, $telefono, $calle,
                                         $numero, $comentario, $productos)
    {
        $em = $this->getDoctrine()->getManager();

        $resultado = array();
        $entity = $this->getDoctrine()->getRepository('IcanBundle:Cotizacion')->find($cotizacion_id);
        if ($entity != null) {

            $entity->setRut($rut);
            $entity->setNombre($nombre);
            $entity->setApellidos($apellidos);
            $entity->setEmail($email);
            $entity->setTelefono($telefono);
            $entity->setCalle($calle);
            $entity->setNumero($numero);
            $entity->setComentario($comentario);

            if ($usuario_id != "") {
                $usuario = $this->getDoctrine()->getRepository("IcanBundle:Usuario")
                    ->find($usuario_id);
                if ($usuario != null) {
                    $entity->setUsuario($usuario);
                }
            }

            if (count($productos) > 0) {
                foreach ($productos as $producto) {
                    $producto_id = $producto['producto_id'];
                    $cantidad = $producto['cantidad'];
                    $cotizacionproducto_id = $producto['cotizacionproducto_id'];

                    $producto = $this->getDoctrine()->getRepository('IcanBundle:Producto')
                        ->find($producto_id);
                    if ($producto != null) {
                        $cotizacion_producto = $this->getDoctrine()->getRepository('IcanBundle:CotizacionProducto')
                            ->find($cotizacionproducto_id);
                        $is_new = false;
                        if ($cotizacion_producto == null) {
                            $cotizacion_producto = new Entity\CotizacionProducto();
                            $is_new = true;
                        }

                        $cantidad_old = $cotizacion_producto->getCantidad();
                        $cotizacion_producto->setCantidad($cantidad);

                        $descuento = 0;
                        if ($producto != null) {
                            $cotizacion_producto->setProducto($producto);
                            if ($producto->getPrecioEspecial() > 0) {
                                $descuento = $producto->getPrecio() - $producto->getPrecioEspecial();
                            }
                        }
                        $cotizacion_producto->setDescuento($descuento);

                        $cotizacion_producto->setProducto($producto);

                        if ($is_new) {
                            $cotizacion_producto->setCotizacion($entity);
                            $em->persist($cotizacion_producto);

                            //Disminuir el stock
                            $stock = $producto->getStock() - $cantidad;
                            $producto->setStock($stock);
                        } else {
                            if ($cantidad_old != $cantidad) {
                                if ($cantidad > $cantidad_old) {
                                    $resto = $cantidad - $cantidad_old;
                                    $stock = $producto->getStock() - $resto;
                                    $producto->setStock($stock);
                                } else {
                                    $suma = $cantidad_old - $cantidad;
                                    $stock = $producto->getStock() + $suma;
                                    $producto->setStock($stock);
                                }
                            }
                        }
                    }
                }
            }

            $em->flush();

            $this->HacerPDF($cotizacion_id);

            $resultado['success'] = true;
            $resultado['ruta'] = $this->generateUrl('descargarOrdenCotizacion', array('cotizacion_id' => $entity->getCotizacionId()));

            return $resultado;
        } else {
            $resultado['success'] = false;
            $resultado['error'] = "No existe el registro solicitado";
        }
        return $resultado;
    }

    /**
     * SalvarCotizacion: Guarda los datos del usuario en la BD
     *
     * @author Marcel
     */
    public function SalvarCotizacion($usuario_id, $rut, $nombre, $apellidos, $email, $telefono, $calle,
                                     $numero, $comentario, $productos)
    {
        $em = $this->getDoctrine()->getManager();
        $resultado = array();

        $entity = new Entity\Cotizacion();

        $entity->setRut($rut);
        $entity->setNombre($nombre);
        $entity->setApellidos($apellidos);
        $entity->setEmail($email);
        $entity->setTelefono($telefono);
        $entity->setCalle($calle);
        $entity->setNumero($numero);
        $entity->setComentario($comentario);

        $entity->setEstado("Exitosa");

        if ($usuario_id != "") {
            $usuario = $this->getDoctrine()->getRepository("IcanBundle:Usuario")
                ->find($usuario_id);
            if ($usuario != null) {
                $entity->setUsuario($usuario);
            }
        }

        $this->setTimeZone();
        $entity->setFecha(new \DateTime());

        $em->persist($entity);

        if (count($productos) > 0) {
            foreach ($productos as $producto) {
                $producto_id = $producto['producto_id'];
                $cantidad = $producto['cantidad'];

                $producto = $this->getDoctrine()->getRepository('IcanBundle:Producto')
                    ->find($producto_id);
                if ($producto != null) {
                    $cotizacion_producto = new Entity\CotizacionProducto();

                    $cotizacion_producto->setCantidad($cantidad);

                    $descuento = 0;
                    if ($producto != null) {
                        $cotizacion_producto->setProducto($producto);
                        if ($producto->getPrecioEspecial() > 0) {
                            $descuento = $producto->getPrecio() - $producto->getPrecioEspecial();
                        }
                    }
                    $cotizacion_producto->setDescuento($descuento);

                    $cotizacion_producto->setProducto($producto);

                    $cotizacion_producto->setCotizacion($entity);

                    $em->persist($cotizacion_producto);

                    //Disminuir el stock
                    $stock = $producto->getStock() - $cantidad;
                    $producto->setStock($stock);
                }
            }
        }

        $em->flush();

        $this->HacerPDF($entity->getCotizacionId());

        $resultado['success'] = true;
        $resultado['ruta'] = $this->generateUrl('descargarOrdenCotizacion', array('cotizacion_id' => $entity->getCotizacionId()));

        return $resultado;
    }

    public function HacerPDF($cotizacion_id)
    {
        $archivo = "orden-$cotizacion_id.pdf";
        $attach = $this->get('kernel')->getRootDir() . "/../web/uploads/cotizacion/$archivo";

        $archivo2 = "orden-cliente-$cotizacion_id.pdf";
        $attach2 = $this->get('kernel')->getRootDir() . "/../web/uploads/cotizacion/$archivo2";

        $arreglo_resultado = $this->CargarDatosCotizacion($cotizacion_id);
        $cotizacion = $arreglo_resultado['cotizacion'];

        $ruta = $this->ObtenerURL();

        $html = $this->renderView('IcanBundle:Cotizacion:pdf.html.twig', array(
            'direccion_url' => $ruta,
            'cotizacion' => $cotizacion
        ));

        $html2pdf = new Html2Pdf('P', 'A4', 'fr', true, 'UTF-8', array(5, 5, 5, 2));
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);
        $html2pdf->Output($attach, 'F');

        //Pdf cliente
        $html_cliente = $this->renderView('IcanBundle:Cotizacion:pdf-cliente.html.twig', array(
            'direccion_url' => $ruta,
            'cotizacion' => $cotizacion
        ));

        $html2pdf_cliente = new Html2Pdf('P', 'A4', 'fr', true, 'UTF-8', array(5, 5, 5, 2));
        $html2pdf_cliente->pdf->SetDisplayMode('real');
        $html2pdf_cliente->writeHTML($html_cliente);
        $html2pdf_cliente->Output($attach2, 'F');

    }

    /**
     * ListarCotizaciones: Listar las cotizaciones
     *
     * @param int $start Inicio
     * @param int $limit Limite
     * @param string $sSearch Para buscar
     *
     * @author Marcel
     */
    public function ListarCotizaciones($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $fecha_inicial, $fecha_fin)
    {
        $arreglo_resultado = array();
        $cont = 0;

        $lista = $this->getDoctrine()->getRepository('IcanBundle:Cotizacion')
            ->ListarCotizaciones($start, $limit, $sSearch, $iSortCol_0, $sSortDir_0, $fecha_inicial, $fecha_fin);

        foreach ($lista as $value) {
            $cotizacion_id = $value->getCotizacionId();

            $acciones = $this->ListarAcciones($cotizacion_id);

            $rut = $value->getRut();
            $rut = $this->FormatearRut($rut);

            $arreglo_resultado[$cont] = array(
                "id" => $cotizacion_id,
                "cotizacionId" => $cotizacion_id,
                "rut" => $rut,
                "nombre" => $value->getNombre() . " " . $value->getApellidos(),
                "email" => $value->getEmail(),
                "telefono" => $value->getTelefono(),
                "calle" => $value->getCalle() . " " . $value->getNumero(),
                "comentario" => $value->getComentario(),
                "estado" => $value->getEstado(),
                "fecha" => $value->getFecha()->format('d-m-Y H:i'),
                "acciones" => $acciones
            );

            $cont++;
        }

        return $arreglo_resultado;
    }

    /**
     * TotalCotizaciones: Total de usuarios
     * @param string $sSearch Para buscar
     * @author Marcel
     */
    public function TotalCotizaciones($sSearch, $fecha_inicial, $fecha_fin)
    {
        $total = $this->getDoctrine()->getRepository('IcanBundle:Cotizacion')
            ->TotalCotizaciones($sSearch, $fecha_inicial, $fecha_fin);

        return $total;
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
        $acciones .= ' <a href="' . $this->generateUrl('descargarOrdenCotizacion', array('cotizacion_id' => $id)) . '" target="_blank" class="download m-portlet__nav-link btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" title="Descargar PDF"><i class="la la-download"></i></a>';

        return $acciones;
    }

    //Listar usuarios clientes
    public function ListarUsuariosClientes()
    {
        $arreglo_resultado = array();
        $cont = 0;

        $lista = $this->getDoctrine()->getRepository('IcanBundle:Usuario')
            ->ListarUsuariosRol(2);

        foreach ($lista as $value) {
            $usuario_id = $value->getUsuarioId();

            $arreglo_resultado[$cont]['usuario_id'] = $usuario_id;
            $arreglo_resultado[$cont]['nombre'] = $value->getNombre();
            $arreglo_resultado[$cont]['apellidos'] = $value->getApellidos();
            $arreglo_resultado[$cont]['email'] = $value->getEmail();

            $rut = "";
            $calle = "";
            $numero = "";
            $telefono = "";

            $usuario_info = $this->getDoctrine()->getRepository('IcanBundle:UsuarioInfo')->BuscarInfoDeUsuario($usuario_id);
            if ($usuario_info != null) {

                $rut = $usuario_info->getRut();
                $rut = $this->FormatearRut($rut);

                $calle = $usuario_info->getCalle();
                $numero = $usuario_info->getNumero();
                $telefono = $usuario_info->getTelefono();
            }

            $arreglo_resultado[$cont]['rut'] = $rut;
            $arreglo_resultado[$cont]['calle'] = $calle;
            $arreglo_resultado[$cont]['numero'] = $numero;
            $arreglo_resultado[$cont]['telefono'] = $telefono;

            $cont++;
        }

        return $arreglo_resultado;
    }
}
