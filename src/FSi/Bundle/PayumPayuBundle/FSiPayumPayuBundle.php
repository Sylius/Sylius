<?php

namespace FSi\Bundle\PayumPayuBundle;

use FSi\Bundle\PayumPayuBundle\DependencyInjection\Factory\Payment\PayuPaymentFactory;
use Payum\Bundle\PayumBundle\DependencyInjection\PayumExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FSiPayumPayuBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        /** @var  PayumExtension $payumExtension */
        $payumExtension = $container->getExtension('payum');

        $payumExtension->addPaymentFactory(new PayuPaymentFactory());
    }
}