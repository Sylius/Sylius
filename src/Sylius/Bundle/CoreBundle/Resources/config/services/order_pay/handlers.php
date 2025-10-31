<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.handler.order_pay.payment_state_flash', 'Sylius\Bundle\CoreBundle\OrderPay\Handler\PaymentStateFlashHandler')
        ->abstract();

    $services->alias('Sylius\Bundle\CoreBundle\OrderPay\Handler\PaymentStateFlashHandlerInterface', 'sylius.handler.order_pay.payment_state_flash');
};
