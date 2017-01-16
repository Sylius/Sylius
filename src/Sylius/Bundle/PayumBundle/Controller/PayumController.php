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
use Sylius\Bundle\PayumBundle\Request\GetStatus;
use Sylius\Bundle\PayumBundle\Request\ResolveNextRoute;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfigurationFactoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\ViewHandlerInterface;
use Sylius\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

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
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var FormTypeRegistryInterface
     */
    private $gatewayConfigurationTypeRegistry;

    /**
     * @var MetadataInterface
     */
    private $orderMetadata;

    /**
     * @var RequestConfigurationFactoryInterface
     */
    private $requestConfigurationFactory;

    /**
     * @var ViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param Payum $payum
     * @param OrderRepositoryInterface $orderRepository
     * @param FormTypeRegistryInterface $gatewayConfigurationTypeRegistry
     * @param MetadataInterface $orderMetadata
     * @param RequestConfigurationFactoryInterface $requestConfigurationFactory
     * @param ViewHandlerInterface $viewHandler
     * @param RouterInterface $router
     */
    public function __construct(
        Payum $payum,
        OrderRepositoryInterface $orderRepository,
        FormTypeRegistryInterface $gatewayConfigurationTypeRegistry,
        MetadataInterface $orderMetadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        ViewHandlerInterface $viewHandler,
        RouterInterface $router
    ) {
        $this->payum = $payum;
        $this->orderRepository = $orderRepository;
        $this->gatewayConfigurationTypeRegistry = $gatewayConfigurationTypeRegistry;
        $this->orderMetadata = $orderMetadata;
        $this->requestConfigurationFactory = $requestConfigurationFactory;
        $this->viewHandler = $viewHandler;
        $this->router = $router;
    }

    /**
     * @param Request $request
     * @param mixed $tokenValue
     *
     * @return Response
     */
    public function prepareCaptureAction(Request $request, $tokenValue)
    {
        $configuration = $this->requestConfigurationFactory->create($this->orderMetadata, $request);

        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByTokenValue($tokenValue);

        if (null === $order) {
            throw new NotFoundHttpException(sprintf('Order with token "%s" does not exist.', $tokenValue));
        }

        $request->getSession()->set('sylius_order_id', $order->getId());
        $options = $configuration->getParameters()->get('redirect');

        $payment = $order->getLastPayment(PaymentInterface::STATE_NEW);

        if (null === $payment) {
            $url = $this->router->generate('sylius_shop_order_thank_you');
            return new RedirectResponse($url);
        }

        $captureToken = $this->getTokenFactory()->createCaptureToken(
            $payment->getMethod()->getGatewayConfig()->getGatewayName(),
            $payment,
            isset($options['route']) ? $options['route'] : null,
            isset($options['parameters']) ? $options['parameters'] : []
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
        $configuration = $this->requestConfigurationFactory->create($this->orderMetadata, $request);

        $token = $this->getHttpRequestVerifier()->verify($request);

        $status = new GetStatus($token);
        $this->payum->getGateway($token->getGatewayName())->execute($status);
        $resolveNextRoute = new ResolveNextRoute($status->getFirstModel());
        $this->payum->getGateway($token->getGatewayName())->execute($resolveNextRoute);

        $this->getHttpRequestVerifier()->invalidate($token);

        if (PaymentInterface::STATE_NEW !== $status->getValue()) {
            $request->getSession()->getBag('flashes')->add('info', sprintf('sylius.payment.%s', $status->getValue()));
        }

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
