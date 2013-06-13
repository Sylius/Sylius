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

use Sylius\Bundle\AddressingBundle\Model\Address;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
    
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAddressesAction()
    {
        $user = $this->get('sylius.repository.user')->find($this->getUser()->getId());
        
        return $this->render('SyliusWebBundle:Frontend/Account/Address:index.html.twig', array(
            'user' => $user,
        ));
    }
}
