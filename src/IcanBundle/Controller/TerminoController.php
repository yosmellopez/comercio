<?php

namespace IcanBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use IcanBundle\Entity;

class TerminoController extends BaseController
{
    public function indexAction()
    {

        $pagina = $this->getDoctrine()->getRepository('IcanBundle:Termino')
            ->DevolverTerminos();

        return $this->render('IcanBundle:Termino:index.html.twig', array(
            'pagina' => $pagina,
        ));
    }

    /**
     * salvarAction Acción que salva los datos de la pagina quienes somos en la BD
     *
     */
    public function salvarAction(Request $request)
    {
        $termino_id = $request->get('termino_id');

        $titulo = $request->get('titulo');
        $descripcion = $request->get('descripcion');
        $tags = $request->get('tags');

        $resultadoJson = array();
        $resultado = $this->SalvarPagina($termino_id, $titulo, $descripcion, $tags);

        if ($resultado['success']) {

            $resultadoJson['success'] = $resultado['success'];
            $resultadoJson['termino_id'] = $resultado['termino_id'];
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
        $termino_id = $request->get('termino_id');

        $resultado = $this->CargarDatosPagina($termino_id);
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
     * CargarDatosPagina: Carga los datos de un usuario
     *
     * @param int $termino_id Id
     *
     * @author Marcel
     */
    public function CargarDatosPagina($termino_id)
    {
        $resultado = array();
        $arreglo_resultado = array();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Termino')
            ->find($termino_id);
        if ($entity != null) {
            $arreglo_resultado['titulo'] = $entity->getTitulo();
            $arreglo_resultado['descripcion'] = $entity->getDescripcion();
            $arreglo_resultado['tags'] = $entity->getTags();

            $resultado['success'] = true;
            $resultado['pagina'] = $arreglo_resultado;
        } else {
            $resultado['success'] = true;
            $resultado['pagina'] = false;
        }
        return $resultado;
    }


    /**
     * SalvarPagina: Guarda los datos del usuario en la BD
     *
     *
     * @author Marcel
     */
    public function SalvarPagina($termino_id, $titulo, $descripcion, $tags)
    {
        $em = $this->getDoctrine()->getManager();

        $resultado = array();

        $entity = $this->getDoctrine()->getRepository('IcanBundle:Termino')->find($termino_id);
        $is_new = false;
        if ($entity == null) {
            $entity = new Entity\Termino();
            $is_new = true;
        }

        $entity->setTitulo($titulo);
        $entity->setDescripcion($descripcion);
        $entity->setTags($tags);

        if ($is_new) {
            $em->persist($entity);
        }
        $em->flush();

        $resultado['success'] = true;
        $resultado['termino_id'] = $termino_id;

        return $resultado;
    }
}
