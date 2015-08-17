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
use Symfony\Component\Security\Core\SecurityContext;

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
        if (!$this->get('security.context')->isGranted('ROLE_SYLIUS_ADMIN')) {
            throw new AccessDeniedException();
        }

        $user = $this->get('sylius.repository.user')->findOneBy(array('usernameCanonical' => $username));

        if (!$user) {
            throw new NotFoundHttpException(sprintf('User with username %s does not exist.', $username));
        }

        return $this->redirect($this->generateUrl('sylius_backend_customer_show', array('id' => $user->getCustomer()->getId())));
    }
}
