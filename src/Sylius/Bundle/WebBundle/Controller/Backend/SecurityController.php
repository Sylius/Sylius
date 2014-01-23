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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Backend security controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SecurityController extends Controller
{
    /**
     * Target action for _switch_user=_exit, redirects admin back to impersonated user
     *
     * @param string $username
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     * @return RedirectResponse
     */
    public function exitUserSwitchAction($username)
    {
        if (!$this->get('security.context')->isGranted('ROLE_SYLIUS_ADMIN')) {
            throw new AccessDeniedException();
        }

        $user = $this->get('sylius.repository.user')->findOneBy(array('usernameCanonical' => $username));

        if (!$user) {
            throw new NotFoundHttpException(sprintf('User with username %s does not exist.', $username));
        }

        return $this->redirect($this->generateUrl('sylius_backend_user_show', array('id' => $user->getId())));
    }

    public function loginAction(Request $request)
    {
        $session = $request->getSession();
        $error = null;

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        if ($error) {
            $error = $error->getMessage();
        }

        $lastUsername = (null === $session) ? '' : $session->get(SecurityContext::LAST_USERNAME);

        $csrfToken = $this
            ->getFormCsrfProvider()
            ->generateCsrfToken('authenticate')
        ;

        return $this->render('SyliusWebBundle:Backend/Security:login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
            'token'         => $csrfToken,
        ));
    }

    public function checkAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall.');
    }

    public function logoutAction()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration');
    }

    private function getFormCsrfProvider()
    {
        return $this->get('form.csrf_provider');
    }
}
