<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PayumBundle\Controller;

use FOS\RestBundle\View\View;
use Payum\Core\Payum;
use Payum\Core\Security\GenericTokenFactoryInterface;
use Payum\Core\Security\HttpRequestVerifierInterface;
use Sylius\Bundle\PayumBundle\Request\AfterCapture;
use Sylius\Bundle\PayumBundle\Request\GetStatus;
use Sylius\Bundle\PayumBundle\Request\ResolveNextRoute;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfigurationFactoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\ViewHandlerInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class PayumController
{
    /**
     * @var Payum
     */
    private $payum;

    /**
     * @var PaymentRepositoryInterface
     */
    private $paymentRepository;

    /**
     * @var MetadataInterface
     */
    private $paymentMetadata;

    /**
     * @var RequestConfigurationFactoryInterface
     */
    private $requestConfigurationFactory;

    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @param Payum $payum
     * @param PaymentRepositoryInterface $paymentRepository
     * @param MetadataInterface $paymentMetadata
     * @param RequestConfigurationFactoryInterface $requestConfigurationFactory
     * @param ViewHandlerInterface $viewHandler
     */
    public function __construct(
        Payum $payum,
        PaymentRepositoryInterface $paymentRepository,
        MetadataInterface $paymentMetadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        ViewHandlerInterface $viewHandler
    ) {
        $this->payum = $payum;
        $this->paymentRepository = $paymentRepository;
        $this->paymentMetadata = $paymentMetadata;
        $this->requestConfigurationFactory = $requestConfigurationFactory;
        $this->viewHandler = $viewHandler;
    }

    /**
     * @param Request $request
     * @param $lastNewPaymentId
     *
     * @return Response
     */
    public function prepareCaptureAction(Request $request, $lastNewPaymentId)
    {
        $configuration = $this->requestConfigurationFactory->create($this->paymentMetadata, $request);

        $payment = $this->paymentRepository->find($lastNewPaymentId);
        Assert::notNull($payment);
        $request->getSession()->set('sylius_order_id', $payment->getOrder()->getId());

        $captureToken = $this->getTokenFactory()->createCaptureToken(
            $payment->getMethod()->getGateway(),
            $payment,
            $configuration->getParameters()->get('redirect[route]', null, true),
            $configuration->getParameters()->get('redirect[parameters]', [], true)
        );

        $view = View::createRedirect($captureToken->getTargetUrl());

        return $this->viewHandler->handle($configuration, $view);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function afterCaptureAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->paymentMetadata, $request);

        $token = $this->getHttpRequestVerifier()->verify($request);
        $this->getHttpRequestVerifier()->invalidate($token);

        $status = new GetStatus($token);
        $this->payum->getGateway($token->getGatewayName())->execute($status);
        $resolveNextRoute = new ResolveNextRoute($status->getFirstModel());
        $this->payum->getGateway($token->getGatewayName())->execute($resolveNextRoute);

        return $this->viewHandler->handle(
            $configuration,
            View::createRouteRedirect($resolveNextRoute->getRouteName(), $resolveNextRoute->getRouteParameters())
        );
    }

    /**
     * @return GenericTokenFactoryInterface
     */
    private function getTokenFactory()
    {
        return $this->payum->getTokenFactory();
    }

    /**
     * @return HttpRequestVerifierInterface
     */
    private function getHttpRequestVerifier()
    {
        return $this->payum->getHttpRequestVerifier();
    }
}
