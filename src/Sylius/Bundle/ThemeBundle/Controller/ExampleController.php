<?php

namespace Sylius\Bundle\ThemeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ExampleController extends Controller
{
    public function exampleAction()
    {
        return $this->render('SyliusThemeBundle:DELETE:example.html.twig');
    }
}