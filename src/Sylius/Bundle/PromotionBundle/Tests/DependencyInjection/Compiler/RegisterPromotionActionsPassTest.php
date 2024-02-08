<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\PromotionBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sylius\Bundle\PromotionBundle\DependencyInjection\Compiler\RegisterPromotionActionsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterPromotionActionsPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function it_registers_collected_promotion_actions_in_the_registry(): void
    {
        $this->setDefinition('sylius.registry_promotion_action', new Definition());
        $this->setDefinition('sylius.form_registry.promotion_action', new Definition());
        $this->setDefinition(
            'action',
            (new Definition())
                ->addTag('sylius.promotion_action', ['type' => 'custom', 'label' => 'Label 1', 'form_type' => 'FQCN1'])
                ->addTag('sylius.promotion_action', ['type' => 'another_custom', 'label' => 'Label 2', 'form_type' => 'FQCN2']),
        );

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.registry_promotion_action',
            'register',
            ['custom', new Reference('action')],
        );
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.registry_promotion_action',
            'register',
            ['another_custom', new Reference('action')],
        );
    }

    /**
     * @test
     */
    public function it_creates_parameter_which_maps_promotion_action_type_to_label(): void
    {
        $this->setDefinition('sylius.registry_promotion_action', new Definition());
        $this->setDefinition('sylius.form_registry.promotion_action', new Definition());
        $this->setDefinition(
            'action',
            (new Definition())
                ->addTag('sylius.promotion_action', ['type' => 'custom', 'label' => 'Label 1', 'form_type' => 'FQCN1'])
                ->addTag('sylius.promotion_action', ['type' => 'another_custom', 'label' => 'Label 2', 'form_type' => 'FQCN2']),
        );

        $this->compile();

        $this->assertContainerBuilderHasParameter(
            'sylius.promotion_actions',
            ['custom' => 'Label 1', 'another_custom' => 'Label 2'],
        );
    }

    /**
     * @test
     */
    public function it_registers_collected_promotion_actions_form_types_in_the_registry(): void
    {
        $this->setDefinition('sylius.registry_promotion_action', new Definition());
        $this->setDefinition('sylius.form_registry.promotion_action', new Definition());
        $this->setDefinition(
            'action',
            (new Definition())
                ->addTag('sylius.promotion_action', ['type' => 'custom', 'label' => 'Label 1', 'form_type' => 'FQCN1'])
                ->addTag('sylius.promotion_action', ['type' => 'another_custom', 'label' => 'Label 2', 'form_type' => 'FQCN2']),
        );

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.form_registry.promotion_action',
            'add',
            ['custom', 'default', 'FQCN1'],
        );
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.form_registry.promotion_action',
            'add',
            ['another_custom', 'default', 'FQCN2'],
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterPromotionActionsPass());
    }
}
