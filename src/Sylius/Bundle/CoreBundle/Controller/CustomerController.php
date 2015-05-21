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

class CustomerController extends BaseCustomerController
{
    /**
     * Render user filter form.
     */
    public function filterFormAction(Request $request)
    {
        return $this->render('SyliusWebBundle:Backend/Customer:filterForm.html.twig', array(
            'form' => $this->get('form.factory')->createNamed('criteria', 'sylius_customer_filter', $request->query->get('criteria'))->createView()
        ));
    }
}
