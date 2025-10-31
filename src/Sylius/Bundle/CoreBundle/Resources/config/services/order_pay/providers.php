<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.provider.order_pay.payment_request_pay_url', 'Sylius\Bundle\CoreBundle\OrderPay\Provider\UrlProvider')
        ->abstract()
        ->args([service('sylius.processor.order_pay.route_parameters')]);

    $services->set('sylius.provider.order_pay.after_pay_url', 'Sylius\Bundle\CoreBundle\OrderPay\Provider\UrlProvider')
        ->abstract()
        ->args([service('sylius.processor.order_pay.route_parameters')]);

    $services->set('sylius.provider.order_pay.final_url', 'Sylius\Bundle\CoreBundle\OrderPay\Provider\FinalUrlProvider')
        ->abstract()
        ->args([service('sylius.processor.order_pay.route_parameters')]);

    $services->set('sylius.provider.order_pay.pay_response.payment_request', 'Sylius\Bundle\CoreBundle\OrderPay\Provider\PaymentRequestPayResponseProvider')
        ->abstract()
        ->args([
            service('sylius.factory.payment_request'),
            service('sylius.repository.payment_request'),
            service('sylius.provider.payment_request.default_action'),
            service('sylius.provider.payment_request.default_payload'),
            service('sylius.checker.finalized_payment_request'),
        ]);

    $services->set('sylius.provider.order_pay.pay_response.no_payment', 'Sylius\Bundle\CoreBundle\OrderPay\Provider\NoPaymentPayResponseProvider')
        ->abstract();

    $services->set('sylius.provider.order_pay.after_pay_response.payment_request', 'Sylius\Bundle\CoreBundle\OrderPay\Provider\PaymentRequestAfterPayResponseProvider')
        ->abstract()
        ->args([
            service('sylius.factory.payment_request'),
            service('sylius.processor.payment_request.http_response'),
            service('sylius.repository.payment_request'),
        ]);
};
