<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PaymentBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Yaml\Parser;

/**
 * Payments extension.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusPaymentExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->configure(
            $config,
            new Configuration(),
            $container,
            self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS | self::CONFIGURE_VALIDATORS | self::CONFIGURE_FORMS
        );

        $container->setParameter('sylius.payment_gateways', $config['gateways']);

        $container
            ->getDefinition('sylius.form.type.payment_method')
            ->addArgument(new Reference('sylius.registry.payment.fee_calculator'))
        ;

        $container
            ->getDefinition('sylius.form.type.payment_method_choice')
            ->addArgument(new Reference('sylius.registry.payment.fee_calculator'))
            ->addArgument(new Reference('sylius.repository.payment'))
        ;
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
