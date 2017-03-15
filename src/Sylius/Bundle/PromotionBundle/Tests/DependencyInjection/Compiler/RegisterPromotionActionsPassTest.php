<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\PromotionBundle\DependencyInjection\Compiler\RegisterPromotionActionsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class RegisterPromotionActionsPassTest extends AbstractCompilerPassTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterPromotionActionsPass());
    }

    /**
     * @test
     */
    public function it_registers_collected_rule_commands_in_the_registry()
    {
        $this->setDefinition('sylius.registry_promotion_action', new Definition());
        $this->setDefinition(
            'custom_promotion_action_command',
            (new Definition())->addTag('sylius.promotion_action', ['type' => 'custom', 'label' => 'Label'])
        );

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.registry_promotion_action',
            'register',
            ['custom', new Reference('custom_promotion_action_command')]
        );
    }

    /**
     * @test
     */
    public function it_creates_parameter_which_maps_rule_type_to_label()
    {
        $this->setDefinition('sylius.registry_promotion_action', new Definition());
        $this->setDefinition(
            'custom_promotion_action_command',
            (new Definition())->addTag('sylius.promotion_action', ['type' => 'custom', 'label' => 'Label'])
        );

        $this->compile();

        $this->assertContainerBuilderHasParameter(
            'sylius.promotion_actions',
            ['custom' => 'Label']
        );
    }
}
