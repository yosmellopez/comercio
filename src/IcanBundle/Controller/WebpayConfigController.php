<?php

namespace IcanBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use IcanBundle\Entity;

class WebpayConfigController extends BaseController
{
    public function indexAction()
    {

        $webpayconfig = $this->getDoctrine()->getRepository('IcanBundle:WebpayConfig')
            ->DevolverConfig();

        return $this->render('IcanBundle:WebpayConfig:index.html.twig', array(
            'webpay' => $webpayconfig,
        ));
    }

    /**
     * salvarAction Acción que salva los datos de la pagina quienes somos en la BD
     *
     */
    public function salvarAction(Request $request)
    {
        $webpay_id = $request->get('webpay_id');

        $comercioCode = $request->get('comercioCode');
        $privateKey = $request->get('privateKey');
        $privateCert = $request->get('privateCert');
        $publicCert = $request->get('publicCert');

        $resultadoJson = array();

        $resultado = $this->SalvarConfiguracion($webpay_id, $comercioCode, $privateKey, $privateCert, $publicCert);

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
     * SalvarConfiguracion: Salva los datos de la configuración de webpay
     *
     *
     * @author Marcel
     */
    public function SalvarConfiguracion($webpay_id, $comercioCode, $privateKey, $privateCert, $publicCert)
    {
        $em = $this->getDoctrine()->getManager();

        $resultado = array();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:WebpayConfig')->find($webpay_id);
        $is_new = false;
        if ($entity == null) {
            $entity = new WebpayConfig();
            $is_new = true;
        }

        $entity->setComercioCode($comercioCode);
        $entity->setPrivateKey($privateKey);
        $entity->setPrivateCert($privateCert);
        $entity->setPublicCert($publicCert);

        if ($is_new) {
            $em->persist($entity);
        }

        $em->flush();
        $resultado['success'] = true;

        return $resultado;
    }
}
