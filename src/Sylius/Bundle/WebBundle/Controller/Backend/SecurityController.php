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

use Sylius\Bundle\UserBundle\Controller\SecurityController as BaseSecurityController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Backend security controller.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SecurityController extends BaseSecurityController
{
    /**
     * Target action for _switch_user=_exit, redirects admin back to impersonated user
     *
     * @param string $username
     *
     * @return RedirectResponse
     *
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    public function exitUserSwitchAction($username)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
            throw new AccessDeniedException();
        }

        $user = $this->get('sylius.repository.user')->findOneBy(['usernameCanonical' => $username]);

        if (!$user) {
            throw new NotFoundHttpException(sprintf('User with username %s does not exist.', $username));
        }

        return $this->redirect($this->generateUrl('sylius_backend_customer_show', ['id' => $user->getCustomer()->getId()]));
    }
}
