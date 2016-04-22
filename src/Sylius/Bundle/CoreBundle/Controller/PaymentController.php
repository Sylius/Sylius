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

use FOS\RestBundle\View\View;
use Gedmo\Loggable\Entity\LogEntry;
use Sylius\Bundle\PaymentBundle\Controller\PaymentController as BasePaymentController;
use Sylius\Component\Core\Model\Payment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
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
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $payment = $this->findOr404($configuration);

        $logRepository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(LogEntry::class)
        ;

        $view = View::create();

        $view->setTemplate($configuration->getTemplate('history.html'));
        $view->setData([
            'payment' => $payment,
            'logs' => $logRepository->getLogEntries($payment),
        ]);

        return $this->viewHandler->handle($configuration, $view);
    }
}
