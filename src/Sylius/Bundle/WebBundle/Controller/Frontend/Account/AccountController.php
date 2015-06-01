<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Controller\Frontend\Account;

use Sylius\Bundle\WebBundle\Controller\WebController;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * User account address controller.
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class AccountController extends WebController
{
    /**
     * @return Response
     */
    public function indexAction()
    {
        $user = $this->getUser();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new UnexpectedTypeException(
                $user,
                'Sylius\Component\User\Model\UserInterface'
            );
        }

        return $this->render($this->getTemplate('frontend_account'), array(
            'user' => $user
        ));
    }
}
