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
    public function showProfileAction(Request $request)
    {
        $customer = $this->getCustomer();

        return $this->render(
            $this->config->getTemplate('showProfile.html'),
            array(
                $this->config->getResourceName() => $customer,
            )
        );
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function updateProfileAction(Request $request)
    {
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
            $this->config->getTemplate('updateProfile.html'),
            array(
                $this->config->getResourceName() => $customer,
                'form'                           => $form->createView(),
            )
        );
    }

    /**
     * @return CustomerInterface
     * @throws AccessDeniedException - When user is not logged in.
     */
    protected function getCustomer()
    {
        $customer = $this->get('sylius.context.customer')->getCustomer();

        if (null === $customer) {
            throw new AccessDeniedException('You have to be logged in user to access this section.');
        }

        return $customer;
    }
}
