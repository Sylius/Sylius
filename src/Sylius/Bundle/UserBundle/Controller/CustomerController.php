<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class CustomerController extends ResourceController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function updateProfileAction(Request $request)
    {
        $resource = $this->getCustomer();
        $form     = $this->getForm($resource);

        if (in_array($request->getMethod(), array('POST', 'PUT', 'PATCH')) && $form->submit($request, !$request->isMethod('PATCH'))->isValid()) {
            $this->domainManager->update($resource);

            if ($this->config->isApiRequest()) {
                return $this->handleView($this->view($resource, 204));
            }

            return $this->redirectHandler->redirectTo($resource);
        }

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($form, 400));
        }

        return $this->render(
            'SyliusWebBundle:Frontend/Account:Profile/edit.html.twig',
            array(
                $this->config->getResourceName() => $resource,
                'form'                           => $form->createView(),
            )
        );
    }

    /**
     * @return CustomerInterface|null
     */
    protected function getCustomer()
    {
        return $this->get('sylius.context.customer')->getCustomer();
    }
}
