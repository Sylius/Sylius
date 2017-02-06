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

use Doctrine\ORM\OptimisticLockException;
use FOS\RestBundle\View\View;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class PaymentController extends ResourceController
{
    /**
     * {@inheritdoc}
     */
    public function applyStateMachineTransitionAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::UPDATE);
        $payment = $this->findOr404($configuration);

        $event = $this->eventDispatcher->dispatchPreEvent(ResourceActions::UPDATE, $configuration, $payment);

        if ($event->isStopped() && !$configuration->isHtmlRequest()) {
            throw new HttpException($event->getErrorCode(), $event->getMessage());
        }
        if ($event->isStopped()) {
            $this->flashHelper->addFlashFromEvent($configuration, $event);

            return $this->redirectHandler->redirectToResource($configuration, $payment);
        }

        if (!$this->stateMachine->can($configuration, $payment)) {
            throw new BadRequestHttpException();
        }

        try {
            $this->stateMachine->apply($configuration, $payment);
            $this->manager->flush();
        } catch (OptimisticLockException $exception) {
            $this->addFlash('error', 'sylius.payment.apply_state_machine_transition_error');

            return $this->redirectHandler->redirectToResource($configuration, $payment);
        }

        $this->eventDispatcher->dispatchPostEvent(ResourceActions::UPDATE, $configuration, $payment);

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create($payment, Response::HTTP_OK));
        }

        $this->flashHelper->addSuccessFlash($configuration, ResourceActions::UPDATE, $payment);

        return $this->redirectHandler->redirectToResource($configuration, $payment);
    }
}
