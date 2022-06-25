<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\PromotionBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\CatalogPromotionPriceCalculatorInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\VariantInScopeCheckerInterface;
use Sylius\Bundle\PromotionBundle\DependencyInjection\SyliusPromotionExtension;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Promotion\Action\PromotionActionCommandInterface;
use Sylius\Component\Promotion\Attribute\AsCatalogPromotionPriceCalculator;
use Sylius\Component\Promotion\Attribute\AsCatalogPromotionVariantChecker;
use Sylius\Component\Promotion\Attribute\AsPromotionAction;
use Sylius\Component\Promotion\Attribute\AsPromotionRuleChecker;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponEligibilityCheckerInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Symfony\Component\DependencyInjection\Definition;

final class SyliusPromotionExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_autoconfigures_promotion_action_with_attribute(): void
    {
        $this->container->register(
            'acme.promotion_action',
            DummyPromotionAction::class
        )->setAutoconfigured(true);

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.promotion_action',
            'sylius.promotion_action',
            [
                'type' => 'dummy',
                'label' => 'dummy',
                'formType' => 'DummyPromotionActionConfigurationType'
            ]
        );
    }

    /** @test */
    public function it_autoconfigures_promotion_rule_checker_with_attribute(): void
    {
        $this->container->register(
            'acme.promotion_rule_checker',
            DummyPromotionRuleChecker::class
        )->setAutoconfigured(true);

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.promotion_rule_checker',
            'sylius.promotion_rule_checker',
            [
                'type' => 'dummy',
                'label' => 'dummy',
                'formType' => 'DummyPromotionRuleCheckerConfigurationType'
            ]
        );
    }

    /** @test */
    public function it_autoconfigures_catalog_promotion_price_calculator_with_attribute(): void
    {
        $this->container->register(
            'acme.catalog_promotion_price_calculator',
            DummyCatalogPromotionPriceCalculator::class
        )->setAutoconfigured(true);

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.catalog_promotion_price_calculator',
            'sylius.catalog_promotion.price_calculator',
            ['type' => 'dummy']
        );
    }

    /** @test */
    public function it_autoconfigures_catalog_promotion_variant_checker_with_attribute(): void
    {
        $this->container->register(
            'acme.catalog_promotion_variant_checker',
            DummyCatalogPromotionVariantChecker::class
        )->setAutoconfigured(true);

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'acme.catalog_promotion_variant_checker',
            'sylius.catalog_promotion.variant_checker',
            ['type' => 'dummy']
        );
    }

    /**
     * @dataProvider provideAutoconfigurableServices
     * @test
     */
    public function it_autoconfigures_services(string $serviceId, string $class, string $expectedTag): void
    {
        $this->container->setDefinition(
            $serviceId,
            (new Definition())
                ->setClass(self::getMockClass($class))
                ->setAutoconfigured(true)
        );

        $this->load();
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            $serviceId,
            $expectedTag
        );
    }

    public function provideAutoconfigurableServices()
    {
        yield [
            'acme.promotion_coupon_eligibility_checker_autoconfigured',
            PromotionCouponEligibilityCheckerInterface::class,
            'sylius.promotion_coupon_eligibility_checker'
        ];

        yield [
            'acme.promotion_eligibility_checker_autoconfigured',
            PromotionEligibilityCheckerInterface::class,
            'sylius.promotion_eligibility_checker'
        ];
    }

    /**
     * {@inheritdoc}
     */
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
                        'model' => 'acme_promotion',
                    ],
                ],
            ],
        ];
    }
}

#[AsCatalogPromotionPriceCalculator(type: 'dummy')]
class DummyCatalogPromotionPriceCalculator implements CatalogPromotionPriceCalculatorInterface
{
    public function calculate(ChannelPricingInterface $channelPricing, CatalogPromotionActionInterface $action): int
    {
        return 16;
    }
}

#[AsCatalogPromotionVariantChecker(type: 'dummy')]
class DummyCatalogPromotionVariantChecker implements VariantInScopeCheckerInterface
{
    public function inScope(CatalogPromotionScopeInterface $scope, ProductVariantInterface $productVariant): bool
    {
        return true;
    }
}

#[AsPromotionAction(type: 'dummy', label: 'dummy', formType: 'DummyPromotionActionConfigurationType')]
class DummyPromotionAction implements PromotionActionCommandInterface
{
    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion): bool
    {
        return true;
    }

    public function revert(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion): void
    {
    }
}

#[AsPromotionRuleChecker(type: 'dummy', label: 'dummy', formType: 'DummyPromotionRuleCheckerConfigurationType')]
class DummyPromotionRuleChecker implements RuleCheckerInterface
{
    public function isEligible(PromotionSubjectInterface $subject, array $configuration): bool
    {
        return true;
    }
}
