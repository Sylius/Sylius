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
use Sylius\Bundle\PromotionBundle\DependencyInjection\Compiler\RegisterRuleCheckersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class RegisterRuleCheckersPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @test
     */
    public function it_registers_collected_rule_checkers_in_the_registry()
    {
        $this->setDefinition('sylius.registry_promotion_rule_checker', new Definition());
        $this->setDefinition('sylius.form_registry.promotion_rule_checker', new Definition());
        $this->setDefinition(
            'custom_promotion_rule_checker',
            (new Definition())->addTag('sylius.promotion_rule_checker', ['type' => 'custom', 'label' => 'Label', 'form-type' => 'FQCN'])
        );

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.registry_promotion_rule_checker',
            'register',
            ['custom', new Reference('custom_promotion_rule_checker')]
        );
    }

    /**
     * @test
     */
    public function it_creates_parameter_which_maps_rule_type_to_label()
    {
        $this->setDefinition('sylius.registry_promotion_rule_checker', new Definition());
        $this->setDefinition('sylius.form_registry.promotion_rule_checker', new Definition());
        $this->setDefinition(
            'custom_promotion_rule_checker',
            (new Definition())->addTag('sylius.promotion_rule_checker', ['type' => 'custom', 'label' => 'Label', 'form-type' => 'FQCN'])
        );

        $this->compile();

        $this->assertContainerBuilderHasParameter(
            'sylius.promotion_rules',
            ['custom' => 'Label']
        );
    }

    /**
     * @test
     */
    public function it_registers_collected_rule_checkers_form_types_in_the_registry()
    {
        $this->setDefinition('sylius.registry_promotion_rule_checker', new Definition());
        $this->setDefinition('sylius.form_registry.promotion_rule_checker', new Definition());
        $this->setDefinition(
            'custom_promotion_rule_checker',
            (new Definition())->addTag('sylius.promotion_rule_checker', ['type' => 'custom', 'label' => 'Label', 'form-type' => 'FQCN'])
        );

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'sylius.form_registry.promotion_rule_checker',
            'add',
            ['custom', 'default', 'FQCN']
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterRuleCheckersPass());
    }
}
