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

use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sylius\Component\User\Model\UserInterface;

/**
 * User account address controller.
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class AccountController extends Controller
{
    public function indexAction(Request $request)
    {
        $user = $this->getUser();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new UnexpectedTypeException(
                $user,
                'Sylius\Component\User\Model\UserInterface'
            );
        }

        return $this->render('SyliusWebBundle:Frontend/Account:show.html.twig', array(
            'user' => $user
        ));
    }
}
