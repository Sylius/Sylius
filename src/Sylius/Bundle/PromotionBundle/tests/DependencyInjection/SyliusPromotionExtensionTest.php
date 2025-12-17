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

namespace Tests\Sylius\Bundle\PromotionBundle\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Bundle\PromotionBundle\Attribute\AsCatalogPromotionVariantChecker;
use Sylius\Bundle\PromotionBundle\Attribute\AsPromotionAction;
use Sylius\Bundle\PromotionBundle\Attribute\AsPromotionCouponEligibilityChecker;
use Sylius\Bundle\PromotionBundle\Attribute\AsPromotionEligibilityChecker;
use Sylius\Bundle\PromotionBundle\Attribute\AsPromotionRuleChecker;
use Sylius\Bundle\PromotionBundle\DependencyInjection\SyliusPromotionExtension;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Symfony\Component\DependencyInjection\Definition;
use Tests\Sylius\Bundle\PromotionBundle\Stub\CatalogPromotionVariantCheckerStub;
use Tests\Sylius\Bundle\PromotionBundle\Stub\PromotionActionStub;
use Tests\Sylius\Bundle\PromotionBundle\Stub\PromotionCouponEligibilityCheckerStub;
use Tests\Sylius\Bundle\PromotionBundle\Stub\PromotionEligibilityCheckerStub;
use Tests\Sylius\Bundle\PromotionBundle\Stub\PromotionRuleCheckerStub;

final class SyliusPromotionExtensionTest extends AbstractExtensionTestCase
{
    #[Test]
    public function it_autoconfigures_catalog_promotion_variant_checker_with_attribute(): void
    {
        $this->container->setParameter('kernel.environment', 'prod');
        $this->container->setDefinition(
            'acme.catalog_promotion_variant_checker_with_attribute',
            (new Definition())
                ->setClass(CatalogPromotionVariantCheckerStub::class)
                ->setAutoconfigured(true),
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.catalog_promotion_variant_checker_with_attribute',
            AsCatalogPromotionVariantChecker::SERVICE_TAG,
            ['type' => 'custom', 'priority' => 9],
        );
    }

    #[Test]
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
                'form_type' => 'SomeFormType',
                'priority' => 10,
            ],
        );
    }

    #[Test]
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

    #[Test]
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

    #[Test]
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
                'form_type' => 'SomeFormType',
                'priority' => 40,
            ],
        );
    }

    #[Test]
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

    #[Test]
    public function it_loads_empty_promotion_action_validation_groups_parameter_value(): void
    {
        $this->load();

        $this->assertContainerBuilderHasParameter(
            'sylius.promotion.promotion_action.validation_groups',
            [],
        );
    }

    #[Test]
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

    #[Test]
    public function it_loads_empty_promotion_rule_validation_groups_parameter_value(): void
    {
        $this->load();

        $this->assertContainerBuilderHasParameter(
            'sylius.promotion.promotion_rule.validation_groups',
            [],
        );
    }

    #[Test]
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

    #[Test]
    public function it_loads_empty_catalog_promotion_action_validation_groups_parameter_value(): void
    {
        $this->load();

        $this->assertContainerBuilderHasParameter(
            'sylius.promotion.catalog_promotion_action.validation_groups',
            [],
        );
    }

    #[Test]
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

    #[Test]
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
