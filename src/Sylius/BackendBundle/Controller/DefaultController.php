<?php

namespace Sylius\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SyliusBackendBundle:Default:index.html.twig');
    }
}
