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

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\SyliusPaymentEvents;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Payment controller.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class PaymentController extends ResourceController
{
    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function historyAction(Request $request)
    {
        $payment = $this->findOr404($request);

        $logRepository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('Gedmo\Loggable\Entity\LogEntry')
        ;

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('history.html'))
            ->setData(array(
                $this->config->getResourceName() => $payment,
                'logs'                           => $logRepository->getLogEntries($payment)
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * {@inheritdoc}
     */
    public function updateAction(Request $request)
    {
        /** @var PaymentInterface $payment */
        $payment = $this->findOr404($request);
        $form = $this->getForm($payment);

        $previousState = $payment->getState();

        if (($request->isMethod('PUT') || $request->isMethod('POST')) && $form->submit($request)->isValid()) {
            if ($payment->getState() !== $previousState) {
                $this->get('event_dispatcher')->dispatch(SyliusPaymentEvents::PRE_STATE_CHANGE, new GenericEvent($payment));
            }

            $this->domainManager->update($payment);

            if ($payment->getState() !== $previousState) {
                $this->get('event_dispatcher')->dispatch(SyliusPaymentEvents::POST_STATE_CHANGE, new GenericEvent($payment));
            }

            return $this->redirectHandler->redirectTo($payment);
        }

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($form));
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('update.html'))
            ->setData(array(
                'payment' => $payment,
                'form'    => $form->createView()
            ))
        ;

        return $this->handleView($view);
    }
}
