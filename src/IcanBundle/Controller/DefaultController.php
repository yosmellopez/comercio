<?php

namespace IcanBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use IcanBundle\Entity;
use IcanBundle\Util;

class DefaultController extends BaseController
{

    public function indexAction() {
        $ultimos = $this->getDoctrine()->getRepository('IcanBundle:Producto')->ListarProductosOrdenados(0, 3);
        $masvisitados = $this->getDoctrine()->getRepository('IcanBundle:Producto')->ListarProductosMasVistas();

        return $this->render('IcanBundle:Default:index.html.twig', array(
            'ultimos' => $ultimos,
            'masvisitados' => $masvisitados,
        ));
    }

    public function renderHeaderAction() {
        $usuario = $this->getUser();
        $mensajes = $this->ListarMensajesUltimosDias();

        return $this->render('@Ican/Layout/header.html.twig', array(
            'usuario' => $usuario,
            'mensajes' => $mensajes
        ));
    }

    public function renderMenuAction() {
        $usuario = $this->getUser();

        return $this->render('@Ican/Layout/menu.html.twig', array(
            'usuario' => $usuario
        ));
    }

    public function testwebpayAction() {
        //Crear configuracion de webpay
        $webpay = $this->ConfigurarWebpay();

        //Urls propias
        $direccion_url = $this->ObtenerURL();
        $urlReturn = $direccion_url . "admin/test-webpay-result";
        $urlFinal = $direccion_url . "admin/test-webpay-final";

        $webpay_request = array(
            "amount" => 9990,      // monto a cobrar
            "buyOrder" => rand(),    // numero orden de compra
            "sessionId" => uniqid(), // idsession local
            "urlReturn" => $urlReturn,
            "urlFinal" => $urlFinal,
        );

        // Iniciamos Transaccion
        // Iniciamos Transaccion
        $this->writelog("WebPay Init Transaction Request: " . var_export($webpay_request, true));
        $result = $webpay->initTransaction($webpay_request['amount'], $webpay_request['buyOrder'], $webpay_request['sessionId'], $webpay_request['urlReturn'], $webpay_request['urlFinal']);
        $this->writelog("WebPay Init Transaction Result: " . var_export($result, true));
        $webpay_token = $result->token;

        // Verificamos respuesta de inicio en webpay
        if (strlen($webpay_token)) {
            $url = $result->url;
            return $this->render('IcanBundle:Default:webpay-transicion.html.twig', array(
                'token' => $webpay_token,
                'url' => $url
            ));
        } else {
            $message = "webpay no disponible";
        }
        return new Response($message);
    }

    public function testwebpayresultAction(Request $request) {
        $webpay_token = $request->get('token_ws');
        if (strlen($webpay_token)) {

            $webpay_request = array(
                "token" => $_POST["token_ws"]
            );

            //Crear configuracion de webpay
            $webpay = $this->ConfigurarWebpay();

            // Rescatamos resultado y datos de la transaccion
            $result = $webpay->getTransactionResult($webpay_request["token"]);
            $this->writelog("WebPay Transaction Result: " . var_export($result, true));

            // Verificamos resultado del pago
            $responseCode = $result->detailOutput->responseCode;
            $responseDescription = /*$result->detailOutput->responseDescription*/
                "";
            if ($responseCode === 0) {
                $url = $result->urlRedirection;
                //Tomar datos de la transaccion
                $cotizacion_id = $result->buyOrder;
                $transactionDate = $result->transactionDate;
                $cardNumber = $result->cardDetail->cardNumber;
                $cardExpirationDate = $result->cardDetail->cardExpirationDate;
                $authorizationCode = $result->detailOutput->authorizationCode;
                $paymentTypeCode = $result->detailOutput->paymentTypeCode;
                $amount = $result->detailOutput->amount;
                $sharesNumber = $result->detailOutput->sharesNumber;
                $sharesAmount = ($sharesNumber != 0) ? $result->detailOutput->sharesAmount : 0;
                $commerceCode = $result->detailOutput->commerceCode;

                $this->SalvarTransaccionWebpay($cotizacion_id, $transactionDate, $cardNumber, $cardExpirationDate,
                    $authorizationCode, $paymentTypeCode, $responseDescription, $amount, $sharesAmount, $sharesNumber, $commerceCode);

                return $this->render('@Ican/Default/webpay-transicion.html.twig', array(
                    'token' => $webpay_token,
                    'url' => $url
                ));
            } else {
                $error = "Pago RECHAZADO por webpay - " . $responseDescription;
                return new Response($error);
            }


        } else {
            return new Response("Error: Token de Webpay no enviado");
        }

    }

    public function testwebpayfinalAction() {
        $request = $this->getRequest();

        $webpay_token = $request->get('TBK_TOKEN');
        if (strlen($webpay_token)) {
            $error = "Transacción Webpay Anulada";

            return new Response("$error");
        } else {
            return new Response("Final OK");
        }
    }

    /**
     * SalvarTransaccionWebpay: Guarda los datos de la transaccion de webpay en la BD
     *
     * @author Marcel
     */
    public function SalvarTransaccionWebpay($cotizacion_id, $transactionDate, $cardNumber, $cardExpirationDate,
                                            $authorizationCode, $paymentTypeCode, $responseDescription, $amount, $sharesAmount, $sharesNumber, $commerceCode) {
        $em = $this->getDoctrine()->getManager();

        $entity = new Entity\TransaccionWebpay();

        if ($transactionDate != "") {
            $transactionDate = \DateTime::createFromFormat("Y-m-d\TH:i:s.uP", $transactionDate);
            $entity->setTransactionDate($transactionDate);
        }

        $entity->setCardNumber($cardNumber);
        $entity->setCardExpirationDate($cardExpirationDate);
        $entity->setAuthorizationCode($authorizationCode);
        $entity->setPaymentTypeCode($paymentTypeCode);
        $entity->setResponseCode($responseDescription);
        $entity->setAmount($amount);
        $entity->setSharesAmount($sharesAmount);
        $entity->setSharesNumber($sharesNumber);
        $entity->setCommerceCode($commerceCode);

        $cotizacion = $em->find('IcanBundle:Cotizacion', $cotizacion_id);
        if ($cotizacion != null) {
            $entity->setCotizacion($cotizacion);
        }

        $em->persist($entity);

        $em->flush();
    }
}
