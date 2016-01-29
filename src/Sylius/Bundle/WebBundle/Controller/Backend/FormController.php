<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Backend forms controller.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class FormController extends Controller
{
    /**
     * @param string $type
     * @param string $template
     *
     * @return Response
     */
    public function showAction($type, $template)
    {
        return $this->render($template, [
            'form' => $this->createForm($type)->createView(),
        ]);
    }

    /**
     * Render filter form.
     *
     * @param string $type
     * @param string $template
     *
     * @return Response
     */
    public function filterAction($type, $template)
    {
        $request = $this->get('request_stack')->getMasterRequest();

        $form = $this->get('form.factory')->createNamed('criteria', $type, null, ['method' => 'GET']);

        $form->handleRequest($request);

        return $this->render($template, [
            'form' => $form->createView(),
        ]);
    }
}
