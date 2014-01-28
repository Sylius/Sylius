<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sylius\Bundle\OrderBundle\EventDispatcher\Event\OrderUpdateEvent;

/**
 * Order controller.
 *
 * @author Myke Hines <myke@webhines.com>
 */
class HistoryController extends ResourceController
{
    /**
     * Create new history item associated to orders
     */
    public function createAction(Request $request)
    {
        $resource = $this->createNew();

        if (!$order = $this->get('sylius.repository.order')->findOneById($this->getRequest()->get('id')))
            throw new NotFoundHttpException('Requested order does not exist');

        $form = $this->getForm($resource);

        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            $resource->setOrder($order);

            $event = $this->create($resource);
            if (!$event->isStopped()) {
                $this->setFlash('success', 'create');

                // Trigger notification
                if (true === $resource->getNotifyCustomer()) {
                    $event = new OrderUpdateEvent($order, $resource);
                    $this->get('event_dispatcher')->dispatch ('sylius.order.update', $event);
                }

                return $this->redirect($this->generateUrl('sylius_backend_order_show', array('id' => $order->getId() )));

            }

            $this->setFlash($event->getMessageType(), $event->getMessage(), $event->getMessageParams());

        }

        $config = $this->getConfiguration();
        if ($config->isApiRequest()) {
            return $this->handleView($this->view($form));
        }

        $view = $this
            ->view()
            ->setTemplate($config->getTemplate('create.html'))
            ->setData(array(
                $config->getResourceName() => $resource,
                'form'                     => $form->createView()
            ))
        ;

        return $this->handleView($view);
    }

}
