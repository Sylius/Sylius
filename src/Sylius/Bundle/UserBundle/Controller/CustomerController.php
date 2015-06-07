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
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
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
            $this->getTemplate('frontend_profile_edit'),
            array(
                $this->config->getResourceName() => $customer,
                'form'                           => $form->createView(),
            )
        );
    }

    /**
     * Returns a template name based on bundle's configuration.
     *
     * @param string $name Template name
     *
     * @param null|string $default Optional default value if no template found with that name.
     *
     * @return string Template path
     */
    protected function getTemplate($name, $default = null) {
        try {
            return $this->container->getParameter(sprintf('sylius.template.%s', $name));
        } catch (InvalidArgumentException $e) {
            if (null !== $default) {
                return $default;
            }

            throw $e;
        }
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
