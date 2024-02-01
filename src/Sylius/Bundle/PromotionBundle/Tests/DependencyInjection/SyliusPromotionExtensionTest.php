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

namespace Sylius\Bundle\PromotionBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\PromotionBundle\Attribute\AsPromotionAction;
use Sylius\Bundle\PromotionBundle\Attribute\AsPromotionCouponEligibilityChecker;
use Sylius\Bundle\PromotionBundle\Attribute\AsPromotionEligibilityChecker;
use Sylius\Bundle\PromotionBundle\Attribute\AsPromotionRuleChecker;
use Sylius\Bundle\PromotionBundle\DependencyInjection\SyliusPromotionExtension;
use Sylius\Bundle\PromotionBundle\Tests\Stub\PromotionActionStub;
use Sylius\Bundle\PromotionBundle\Tests\Stub\PromotionCouponEligibilityCheckerStub;
use Sylius\Bundle\PromotionBundle\Tests\Stub\PromotionEligibilityCheckerStub;
use Sylius\Bundle\PromotionBundle\Tests\Stub\PromotionRuleCheckerStub;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Symfony\Component\DependencyInjection\Definition;

final class SyliusPromotionExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_autoconfigures_promotion_action_with_attribute(): void
    {
        $this->container->setDefinition(
            'acme.promotion_action_autoconfigured',
            (new Definition())
                ->setClass(PromotionActionStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.promotion_action_autoconfigured',
            AsPromotionAction::SERVICE_TAG,
            [
                'type' => 'test',
                'label' => 'Test',
                'priority' => 10,
            ],
        );
    }

    /** @test */
    public function it_autoconfigures_promotion_coupon_eligibility_checker_with_attribute(): void
    {
        $this->container->setDefinition(
            'acme.promotion_coupon_eligibility_checker_autoconfigured',
            (new Definition())
                ->setClass(PromotionCouponEligibilityCheckerStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.promotion_coupon_eligibility_checker_autoconfigured',
            AsPromotionCouponEligibilityChecker::SERVICE_TAG,
            ['priority' => 20],
        );
    }

    /** @test */
    public function it_autoconfigures_promotion_eligibility_checker_with_attribute(): void
    {
        $this->container->setDefinition(
            'acme.promotion_eligibility_checker_autoconfigured',
            (new Definition())
                ->setClass(PromotionEligibilityCheckerStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.promotion_eligibility_checker_autoconfigured',
            AsPromotionEligibilityChecker::SERVICE_TAG,
            ['priority' => 30],
        );
    }

    /** @test */
    public function it_autoconfigures_promotion_rule_checker_with_attribute(): void
    {
        $this->container->setDefinition(
            'acme.promotion_rule_checker_autoconfigured',
            (new Definition())
                ->setClass(PromotionRuleCheckerStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.promotion_rule_checker_autoconfigured',
            AsPromotionRuleChecker::SERVICE_TAG,
            [
                'type' => 'test',
                'label' => 'Test',
                'form-type' => 'SomeFormType',
                'priority' => 40,
            ],
        );
    }

    /** @test */
    public function it_loads_promotion_action_validation_groups_parameter_value_properly(): void
    {
        $this->load([
            'promotion_action' => [
                'validation_groups' => [
                    'order_percentage_discount' => ['sylius', 'order_percentage_discount'],
                    'order_fixed_discount' => ['sylius', 'order_fixed_discount'],
                ],
            ],
        ]);

        $this->assertContainerBuilderHasParameter(
            'sylius.promotion.promotion_action.validation_groups',
            ['order_percentage_discount' => ['sylius', 'order_percentage_discount'], 'order_fixed_discount' => ['sylius', 'order_fixed_discount']],
        );
    }

    /** @test */
    public function it_loads_empty_promotion_action_validation_groups_parameter_value(): void
    {
        $this->load();

        $this->assertContainerBuilderHasParameter(
            'sylius.promotion.promotion_action.validation_groups',
            [],
        );
    }

    /** @test */
    public function it_loads_promotion_rule_validation_groups_parameter_value_properly(): void
    {
        $this->load([
            'promotion_rule' => [
                'validation_groups' => [
                    'cart_quantity' => ['sylius', 'cart_quantity'],
                    'nth_order' => ['sylius', 'nth_order'],
                ],
            ],
        ]);

        $this->assertContainerBuilderHasParameter(
            'sylius.promotion.promotion_rule.validation_groups',
            ['cart_quantity' => ['sylius', 'cart_quantity'], 'nth_order' => ['sylius', 'nth_order']],
        );
    }

    /** @test */
    public function it_loads_empty_promotion_rule_validation_groups_parameter_value(): void
    {
        $this->load();

        $this->assertContainerBuilderHasParameter(
            'sylius.promotion.promotion_rule.validation_groups',
            [],
        );
    }

    /** @test */
    public function it_loads_catalog_promotion_action_validation_groups_parameter_value_properly(): void
    {
        $this->load([
            'catalog_promotion_action' => [
                'validation_groups' => [
                    'something' => ['sylius', 'something'],
                    'test' => ['sylius', 'test'],
                ],
            ],
        ]);

        $this->assertContainerBuilderHasParameter(
            'sylius.promotion.catalog_promotion_action.validation_groups',
            ['something' => ['sylius', 'something'], 'test' => ['sylius', 'test']],
        );
    }

    /** @test */
    public function it_loads_empty_catalog_promotion_action_validation_groups_parameter_value(): void
    {
        $this->load();

        $this->assertContainerBuilderHasParameter(
            'sylius.promotion.catalog_promotion_action.validation_groups',
            [],
        );
    }

    /** @test */
    public function it_loads_catalog_promotion_scope_validation_groups_parameter_value_properly(): void
    {
        $this->load([
            'catalog_promotion_scope' => [
                'validation_groups' => [
                    'something' => ['sylius', 'something'],
                    'test' => ['sylius', 'test'],
                ],
            ],
        ]);

        $this->assertContainerBuilderHasParameter(
            'sylius.promotion.catalog_promotion_scope.validation_groups',
            ['something' => ['sylius', 'something'], 'test' => ['sylius', 'test']],
        );
    }

    /** @test */
    public function it_loads_empty_catalog_promotion_scope_validation_groups_parameter_value(): void
    {
        $this->load();

        $this->assertContainerBuilderHasParameter(
            'sylius.promotion.catalog_promotion_scope.validation_groups',
            [],
        );
    }

    protected function getContainerExtensions(): array
    {
        return [new SyliusPromotionExtension()];
    }

    protected function getMinimalConfiguration(): array
    {
        return [
            'resources' => [
                'promotion_subject' => [
                    'classes' => [
                        'model' => PromotionSubjectInterface::class,
                    ],
                ],
            ],
        ];
    }
}
