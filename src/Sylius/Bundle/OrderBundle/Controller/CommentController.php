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

/**
 * Order comment controller.
 *
 * @author Myke Hines <myke@webhines.com>
 */
class CommentController extends ResourceController
{
    /**
     * Create new comment associated to order.
     */
    public function createAction(Request $request)
    {
        if (!$order = $this->get('sylius.repository.order')->findOneById($request->get('id'))) {
            throw new NotFoundHttpException('Requested order does not exist');
        }

        $resource = $this->createNew();

        $form = $this->getForm($resource);

        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            $resource->setOrder($order);
            $resource->setAuthor($this->getUser()->getEmail());

            $this->domainManager->create($resource);

            return $this->redirect($this->generateUrl('sylius_backend_order_show', array('id' => $order->getId())));
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
