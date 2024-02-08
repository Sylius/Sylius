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
use Sylius\Bundle\PromotionBundle\DependencyInjection\Compiler\RegisterRuleCheckersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterRuleCheckersPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function it_registers_collected_rule_checkers_in_the_registry(): void
    {
        $this->setDefinition('sylius.registry_promotion_rule_checker', new Definition());
        $this->setDefinition('sylius.form_registry.promotion_rule_checker', new Definition());
        $this->setDefinition(
            'checker',
            (new Definition())
                ->addTag('sylius.promotion_rule_checker', ['type' => 'custom', 'label' => 'Label 1', 'form_type' => 'FQCN1'])
                ->addTag('sylius.promotion_rule_checker', ['type' => 'another_custom', 'label' => 'Label 2', 'form_type' => 'FQCN2']),
        );

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.registry_promotion_rule_checker',
            'register',
            ['custom', new Reference('checker')],
        );
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.registry_promotion_rule_checker',
            'register',
            ['another_custom', new Reference('checker')],
        );
    }

    /**
     * @test
     */
    public function it_creates_parameter_which_maps_rule_type_to_label(): void
    {
        $this->setDefinition('sylius.registry_promotion_rule_checker', new Definition());
        $this->setDefinition('sylius.form_registry.promotion_rule_checker', new Definition());
        $this->setDefinition(
            'checker',
            (new Definition())
                ->addTag('sylius.promotion_rule_checker', ['type' => 'custom', 'label' => 'Label 1', 'form_type' => 'FQCN1'])
                ->addTag('sylius.promotion_rule_checker', ['type' => 'another_custom', 'label' => 'Label 2', 'form_type' => 'FQCN2']),
        );

        $this->compile();

        $this->assertContainerBuilderHasParameter(
            'sylius.promotion_rules',
            ['custom' => 'Label 1', 'another_custom' => 'Label 2'],
        );
    }

    /**
     * @test
     */
    public function it_registers_collected_rule_checkers_form_types_in_the_registry(): void
    {
        $this->setDefinition('sylius.registry_promotion_rule_checker', new Definition());
        $this->setDefinition('sylius.form_registry.promotion_rule_checker', new Definition());
        $this->setDefinition(
            'checker',
            (new Definition())
                ->addTag('sylius.promotion_rule_checker', ['type' => 'custom', 'label' => 'Label 1', 'form_type' => 'FQCN1'])
                ->addTag('sylius.promotion_rule_checker', ['type' => 'another_custom', 'label' => 'Label 2', 'form_type' => 'FQCN2']),
        );

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.form_registry.promotion_rule_checker',
            'add',
            ['custom', 'default', 'FQCN1'],
        );
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.form_registry.promotion_rule_checker',
            'add',
            ['another_custom', 'default', 'FQCN2'],
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterRuleCheckersPass());
    }
}
