<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Yaml\Parser;

/**
 * Shipping extension.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusShippingExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $this->configure(
            $config,
            new Configuration(),
            $container,
            self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS | self::CONFIGURE_VALIDATORS | self::CONFIGURE_TRANSLATIONS | self::CONFIGURE_FORMS
        );

        $shippingMethod = $container->getDefinition('sylius.form.type.shipping_method');
        $shippingMethod->addArgument(new Reference('sylius.shipping_calculator_registry'));
        $shippingMethod->addArgument(new Reference('sylius.shipping_rule_checker_registry'));

        $shippingMethod = $container->getDefinition('sylius.form.type.shipping_method_rule');
        $shippingMethod->addArgument(new Reference('sylius.shipping_rule_checker_registry'));
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        if (!$container->hasExtension('winzou_state_machine')) {
            throw new \RuntimeException('winzouStateMachineBundle must be registered!');
        }
        $parser = new Parser();
        $config = $parser->parse(file_get_contents($this->getDefinitionPath($container).'/state-machine.yml'));
        $container->prependExtensionConfig('winzou_state_machine', $config['winzou_state_machine']);
    }
}
