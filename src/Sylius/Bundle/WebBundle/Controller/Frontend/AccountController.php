<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Frontend user account controller.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class AccountController extends Controller
{
    /**
     * User account home page.
     *
     * @return Response
     */
    public function homepageAction()
    {
        return $this->render('SyliusWebBundle:Frontend/Account:homepage.html.twig');
    }
}
