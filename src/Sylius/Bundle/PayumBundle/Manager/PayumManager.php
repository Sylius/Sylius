<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PayumBundle\Manager;

use Payum\Bundle\PayumBundle\Security\TokenFactory;
use Payum\Core\Registry\RegistryInterface;
use Payum\Core\Security\HttpRequestVerifierInterface;
use Sylius\Bundle\PayumBundle\Payum\Request\StatusRequest;
use Sylius\Component\Payment\Manager\PaymentManagerInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentsSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\Request;

class PayumManager implements PaymentManagerInterface
{
    /**
     * @var RegistryInterface
     */
    private $payum;

    /**
     * @var HttpRequestVerifierInterface
     */
    private $requestVerifier;

    /**
     * @var TokenFactory
     */
    private $tokenFactory;

    /**
     * @var PaymentsSubjectInterface
     */
    private $subject;

    public function __construct(RegistryInterface $payum, TokenFactory $tokenFactory, HttpRequestVerifierInterface $requestVerifier)
    {
        $this->payum           = $payum;
        $this->tokenFactory    = $tokenFactory;
        $this->requestVerifier = $requestVerifier;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize($object, array $callbackDetails)
    {
        $this->supports($object);

        if (!isset($callbackDetails['route'])) {
            throw new \InvalidArgumentException('You must specific "route" variable for callback URL generation.');
        }

        if (!isset($callbackDetails['routeParameters'])) {
            $callbackDetails['routeParameters'] = array();
        }

        /** @var $payment PaymentInterface */
        $payment = $object->getPayments()->last();

        $captureToken = $this->tokenFactory->createCaptureToken(
            $payment->getMethod()->getGateway(),
            $payment,
            $callbackDetails['route'],
            $callbackDetails['routeParameters']
        );

        return $captureToken->getTargetUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function handle($data)
    {
        if ($data instanceof Request) {
            throw new \InvalidArgumentException();
        }

        $token = $this->requestVerifier->verify($data);

        $this->requestVerifier->invalidate($token);

        $status = new StatusRequest($token);

        $this->payum->getPayment($token->getPaymentName())->execute($status);

        $this->supports($status->getModel());

        return $status->getStatus();
    }

    /**
     * {@inheritdoc}
     */
    public function supports($object)
    {
        if (!$object instanceof PaymentsSubjectInterface) {
            throw new UnexpectedTypeException($object, 'Sylius\Component\Payment\Model\PaymentsSubjectInterface');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
        return $this->subject;
    }
}
