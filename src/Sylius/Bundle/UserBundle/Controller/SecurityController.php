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

use Sylius\Bundle\WebBundle\Controller\WebController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class SecurityController extends WebController
{
    /**
     * Login form action.
     */
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        $form = $this->get('form.factory')->createNamed('', 'sylius_user_security_login');

        return $this->renderLogin(array(
            'form'          => $form->createView(),
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    /**
     * Login check action. This action should never be called.
     */
    public function checkAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall.');
    }

    /**
     * Logout action. This action should never be called.
     */
    public function logoutAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall.');
    }

    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderLogin(array $data)
    {
        return $this->render($this->getTemplate('frontend_user_login'), $data);
    }
}
