<?php
namespace Sylius\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author   Vidy Videni   <videni@foxmail.com>
 */
class PaymentMethodFormTypePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {

        if ($container->has('sylius.form.type.payment_method')) {
            $definition = $container->getDefinition('sylius.form.type.payment_method');

            $definition->addArgument(new Reference('payum'));
            $definition->addArgument(new Reference('payum.dynamic_gateways.config_storage'));
            $definition->addArgument(new Reference('translator'));
            $definition->addArgument($container->getParameter('sylius.default_gateways'));
        }
    }
}
