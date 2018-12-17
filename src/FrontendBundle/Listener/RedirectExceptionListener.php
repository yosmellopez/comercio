<?php

namespace FrontendBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RedirectExceptionListener {

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $serviceContainer;

    function __construct($serviceContainer) {
        $this->serviceContainer = $serviceContainer;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event
     */
    public function checkRedirect(GetResponseForExceptionEvent $event) {
        $exception = $event->getException();
        if ($exception instanceof NotFoundHttpException) {            
            $response = new RedirectResponse($this->serviceContainer->get('router')->generate('error404'));
            $event->setResponse($response);
        }
    }

}
