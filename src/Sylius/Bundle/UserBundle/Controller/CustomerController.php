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
use Sylius\Component\User\Model\CustomerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class CustomerController extends ResourceController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function updateProfileAction(Request $request)
    {
        $this->validateAccess();
        $customer = $this->getCustomer();
        $form     = $this->getForm($customer);

        if (in_array($request->getMethod(), array('POST', 'PUT', 'PATCH')) && $form->submit($request, !$request->isMethod('PATCH'))->isValid()) {
            $this->domainManager->update($customer);

            if ($this->config->isApiRequest()) {
                return $this->handleView($this->view($customer, 204));
            }

            return $this->redirectHandler->redirectTo($customer);
        }

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($form, 400));
        }

        return $this->render(
            'SyliusWebBundle:Frontend/Account:Profile/edit.html.twig',
            array(
                $this->config->getResourceName() => $customer,
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

    // TODO will be replaced by denyAccessUnlessGranted after bump to Symfony 2.7
    protected function validateAccess()
    {
        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            throw new AccessDeniedException('You have to be registered user to access this section.');
        }
    }
}
