<?php

namespace IcanBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RedsocialController extends BaseController
{

    public function indexAction()
    {

        $red_social = $this->getDoctrine()->getRepository('IcanBundle:Redsocial')
            ->DevolverRedSocial();

        return $this->render('IcanBundle:Redsocial:index.html.twig', array(
            'redsocial' => $red_social
        ));
    }

    /**
     * salvarAction Acción que inserta un usuario en la BD
     *
     */
    public function salvarAction(Request $request)
    {

        $redsocial_id = $request->get('redsocial_id');
        
        $facebook = $request->get('facebook');
        $activarfacebook = $request->get('activarfacebook');
        
        $youtube = $request->get('youtube');
        $activaryoutube = $request->get('activaryoutube');
        
        $twitter = $request->get('twitter');
        $activartwitter = $request->get('activartwitter');
        
        $flickr = $request->get('flickr');
        $activarflickr = $request->get('activarflickr');
        
        $instagram = $request->get('instagram');
        $activarinstagram = $request->get('activarinstagram');
        
        $linkedin = $request->get('linkedin');
        $activarlinkedin = $request->get('activarlinkedin');
        
        $soundcloud = $request->get('soundcloud');
        $activarsoundcloud = $request->get('activarsoundcloud');
        
        $tumblr = $request->get('tumblr');
        $activartumblr = $request->get('activartumblr');
        
        $vimeo = $request->get('vimeo');
        $activarvimeo = $request->get('activarvimeo');
        
        $google = $request->get('google');
        $activargoogle = $request->get('activargoogle');


        $resultadoJson = array();
        $resultado = $this->SalvarRedsocial($redsocial_id, $facebook, $activarfacebook, $youtube, $activaryoutube,
            $twitter, $activartwitter, $flickr, $activarflickr, $instagram, $activarinstagram, $linkedin, $activarlinkedin,
            $soundcloud, $activarsoundcloud, $tumblr, $activartumblr, $vimeo, $activarvimeo, $google, $activargoogle);

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
     * ActualizarRedsocial: Actualiza los datos del redsocial en la BD
     *
     * @param string $redsocial_id Id
     *
     * @author Marcel
     */
    public function SalvarRedsocial($redsocial_id, $facebook, $activarfacebook, $youtube, $activaryoutube, $twitter,
                                    $activartwitter, $flickr, $activarflickr, $instagram, $activarinstagram, $linkedin, 
                                    $activarlinkedin, $soundcloud, $activarsoundcloud, $tumblr, $activartumblr, $vimeo, 
                                    $activarvimeo, $google, $activargoogle)
    {
        $em = $this->getDoctrine()->getManager();

        $resultado = array();
        $entity = $this->getDoctrine()->getRepository('IcanBundle:Redsocial')->find($redsocial_id);
        if ($entity != null) {

            $entity->setFacebook($facebook);
            $entity->setActivarfacebook($activarfacebook);

            $entity->setYoutube($youtube);
            $entity->setActivaryoutube($activaryoutube);

            $entity->setTwitter($twitter);
            $entity->setActivartwitter($activartwitter);

            $entity->setFlickr($flickr);
            $entity->setActivarflickr($activarflickr);

            $entity->setInstagram($instagram);
            $entity->setActivarinstagram($activarinstagram);

            $entity->setLinkedin($linkedin);
            $entity->setActivarlinkedin($activarlinkedin);

            $entity->setSoundcloud($soundcloud);
            $entity->setActivarsoundcloud($activarsoundcloud);

            $entity->setTumblr($tumblr);
            $entity->setActivartumblr($activartumblr);

            $entity->setVimeo($vimeo);
            $entity->setActivarvimeo($activarvimeo);

            $entity->setGoogle($google);
            $entity->setActivargoogle($activargoogle);

            $em->flush();
            $resultado['success'] = true;
        } else {
            $resultado['success'] = false;
            $resultado['error'] = "No existe el registro solicitado";
        }
        return $resultado;
    }

}
