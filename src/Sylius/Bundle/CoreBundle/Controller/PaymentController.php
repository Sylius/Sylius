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

use Gedmo\Loggable\Entity\LogEntry;
use Sylius\Bundle\PaymentBundle\Controller\PaymentController as BasePaymentController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Payment controller.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class PaymentController extends BasePaymentController
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
            ->getRepository(LogEntry::class)
        ;

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('history.html'))
            ->setData([
                $this->config->getResourceName() => $payment,
                'logs' => $logRepository->getLogEntries($payment),
            ])
        ;

        return $this->handleView($view);
    }
}
