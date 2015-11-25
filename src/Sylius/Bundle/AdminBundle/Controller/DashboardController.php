<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DashboardController extends Controller
{
    /**
     * @param Request
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('SyliusAdminBundle:Dashboard:index.html.twig');
    }
}
