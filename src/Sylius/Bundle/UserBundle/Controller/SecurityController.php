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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class SecurityController extends Controller
{
    /**
     * Login form action.
     */
    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $template = $request->attributes->get('_sylius[template]', 'SyliusUserBundle:Security:login.html.twig', true);
        $formType = $request->attributes->get('_sylius[form]', 'sylius_user_security_login', true);
        $form = $this->get('form.factory')->createNamed('', $formType);

        return $this->renderLogin($template, [
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Login check action. This action should never be called.
     */
    public function checkAction(Request $request)
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall.');
    }

    /**
     * Logout action. This action should never be called.
     */
    public function logoutAction(Request $request)
    {
        throw new \RuntimeException('You must configure the logout path to be handled by the firewall.');
    }

    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param string $template The view template name
     * @param array  $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderLogin($template, array $data)
    {
        return $this->render($template, $data);
    }
}
