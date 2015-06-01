<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * An abstract controller that WebBundle controllers may extend.
 *
 * @author Aram Alipoor <aram.alipoor@gmail.com>
 */
class WebController extends FOSRestController {

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
     * @return CustomerInterface
     */
    protected function getCustomer()
    {
        return $this->get('sylius.context.customer')->getCustomer();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function redirectToIndex()
    {
        return $this->redirect($this->generateUrl('sylius_account_address_index'));
    }

    /**
     * @param string $type
     * @param string $message
     */
    protected function addFlash($type, $message)
    {
        $translator = $this->get('translator');
        $this->get('session')->getFlashBag()->add($type, $translator->trans($message, array(), 'flashes'));
    }
}