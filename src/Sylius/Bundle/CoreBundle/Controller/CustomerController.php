<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Controller;

use Sylius\Bundle\UserBundle\Controller\CustomerController as BaseCustomerController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends BaseCustomerController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function filterFormAction(Request $request)
    {
        return $this->container->get('templating')->renderResponse('SyliusWebBundle:Backend/Customer:filterForm.html.twig', [
            'form' => $this->container->get('form.factory')->createNamed('criteria', 'sylius_customer_filter', $request->query->get('criteria'))->createView(),
        ]);
    }
}
