<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\PayumBundle\Payum\Be2bill\Action;

use Doctrine\Common\Persistence\ObjectManager;
use Payum\Be2Bill\Api;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Symfony\Reply\HttpResponse;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\Notify;
use SM\Factory\FactoryInterface;
use Sylius\Bundle\PayumBundle\Payum\Action\AbstractPaymentStateAwareAction;
use Sylius\Bundle\PayumBundle\Payum\Request\GetStatus;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class NotifyAction extends AbstractPaymentStateAwareAction implements ApiAwareInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $paymentRepository;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var Api
     */
    protected $api;

    public function __construct(
        RepositoryInterface $paymentRepository,
        ObjectManager $objectManager,
        FactoryInterface $factory,
        $identifier
    ) {
        parent::__construct($factory);

        $this->paymentRepository = $paymentRepository;
        $this->objectManager     = $objectManager;
        $this->identifier        = $identifier;
    }

    /**
     * {@inheritDoc}
     */
    public function setApi($api)
    {
        if (!$api instanceof Api) {
            throw new UnsupportedApiException('Not supported.');
        }

        $this->api = $api;
    }

    /**
     * {@inheritDoc}
     *
     * @param $request Notify
     */
    public function execute($request)
    {
        if (!$this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }

        $this->payment->execute($httpRequest = new GetHttpRequest());
        $details = $httpRequest->query;

        if (!$this->api->verifyHash($details)) {
            throw new BadRequestHttpException('Hash cannot be verified.');
        }

        if (empty($details['ORDERID'])) {
            throw new BadRequestHttpException('Order id cannot be guessed');
        }

        $payment = $this->paymentRepository->findOneBy(array($this->identifier => $details['ORDERID']));

        if (null === $payment) {
            throw new BadRequestHttpException('Paymenet cannot be retrieved.');
        }

        if ((int) $details['AMOUNT'] !== $payment->getAmount()) {
            throw new BadRequestHttpException('Request amount cannot be verified against payment amount.');
        }

        // Actually update payment details
        $details = array_merge($payment->getDetails(), $details);
        $payment->setDetails($details);

        $status = new GetStatus($payment);
        $this->payment->execute($status);

        $nextState = $status->getValue();

        $this->updatePaymentState($payment, $nextState);

        $this->objectManager->flush();

        throw new HttpResponse(new Response('OK', 200));
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return $request instanceof Notify;
    }
}
