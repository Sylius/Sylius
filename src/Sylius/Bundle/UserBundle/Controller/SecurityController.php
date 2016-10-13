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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class SecurityController extends Controller
{
    /**
     * Login form action.
     */
    public function loginAction(Request $request)
    {
        $template = $request->attributes->get('_sylius[template]', true);
        if (null === $template) {
            throw new HttpException(
                Response::HTTP_NOT_ACCEPTABLE,
                'The routing attribute "_sylius[template]" needs to be configured.'
            );
        }

        $formType = $request->attributes->get('_sylius[form]', 'sylius_user_security_login');

        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

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
     * @param string $template
     * @param array $data
     *
     * @return Response
     */
    private function renderLogin($template, array $data)
    {
        return $this->render($template, $data);
    }
}
