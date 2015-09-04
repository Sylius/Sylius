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

use FOS\RestBundle\View\View;
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
        $configuration = $this->configurationFactory->create($this->metadata, $request);

        if (!$order = $this->get('sylius.repository.order')->find($request->get('id'))) {
            throw new NotFoundHttpException('Requested order does not exist');
        }

        $resource = $this->createNew($configuration);

        $form = $this->createResourceForm($configuration, $resource);

        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            $resource->setOrder($order);
            $resource->setAuthor($this->getUser()->getEmail());

            $this->manager->persist($resource);
            $this->manager->flush();


            return $this->redirect($this->generateUrl('sylius_backend_order_show', array('id' => $order->getId())));
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->handleView($configuration, View::create($form));
        }

        $view = View::create()
            ->setTemplate($configuration->getTemplate('create.html'))
            ->setData(array(
                $this->metadata->getResourceName() => $resource,
                'form'                             => $form->createView()
            ))
        ;

        return $this->handleView($configuration, $view);
    }
}
