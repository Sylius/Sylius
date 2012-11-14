<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;

/**
 * Order controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderController extends ResourceController
{
    /**
     * Place order action.
     */
    public function placeAction(Request $request)
    {
        $fetcher = $this->getRequestFetcher();

        $order = $this->create();
        $form = $this->createForm($fetcher->getFormType('sylius_sales_place_order'));

        if ($form->isMethod('POST') && $form->bind($request)->isValid()) {
            $this->getManager()->persist($order);

            return $this->renderResponse('placed.html', array(
                'order' => $order
            ));
        }

        return $this->renderResponse('place.html', array(
            'form'  => $form->createView(),
            'order' => $order
        ));
    }

    /**
     * Confirms order.
     */
    public function confirmAction()
    {
        $criteria = $this
            ->getRequestFetcher()
            ->getIdentifierCriteria()
        ;

        $order = $this->findOr404($criteria);

        $order->setConfirmed(true);
        $this->getManager()->persist($order);

        return $this->renderResponse('confirmed.html.', array(
            'order' => $order
        ));
    }

    /**
     * Closes order.
     */
    public function closeAction()
    {
        $criteria = $this
            ->getRequestFetcher()
            ->getIdentifierCriteria()
        ;

        $order = $this->findOr404($criteria);

        $order->setClosed(true);
        $this->getManager()->persist($order);

        return $this->redirectTo($order);
    }

    /**
     * Opens order.
     */
    public function openAction($id)
    {
        $criteria = $this
            ->getRequestFetcher()
            ->getIdentifierCriteria()
        ;

        $order = $this->findOr404($criteria);

        $order->setClosed(false);
        $this->getManager()->persist($order);

        return $this->redirectTo($order);
    }
}
