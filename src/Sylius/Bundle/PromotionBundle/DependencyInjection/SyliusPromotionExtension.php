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

namespace Sylius\Bundle\PromotionBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Component\Promotion\Attribute\AsCatalogPromotionPriceCalculator;
use Sylius\Component\Promotion\Attribute\AsCatalogPromotionVariantChecker;
use Sylius\Component\Promotion\Attribute\AsPromotionAction;
use Sylius\Component\Promotion\Attribute\AsPromotionRuleChecker;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponEligibilityCheckerInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SyliusPromotionExtension extends AbstractResourceExtension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.xml');
        $loader->load(sprintf('services/integrations/%s.xml', $config['driver']));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $container->registerForAutoconfiguration(PromotionCouponEligibilityCheckerInterface::class)
            ->addTag('sylius.promotion_coupon_eligibility_checker')
        ;

        $container->registerForAutoconfiguration(PromotionEligibilityCheckerInterface::class)
            ->addTag('sylius.promotion_eligibility_checker')
        ;

        $container->registerAttributeForAutoconfiguration(
            AsPromotionAction::class,
            static function (ChildDefinition $definition, AsPromotionAction $attribute) {
                $definition->addTag('sylius.promotion_action', [
                    'type' => $attribute->type,
                    'label' => $attribute->label,
                    'formType' => $attribute->formType,
                ]);
            }
        );

        $container->registerAttributeForAutoconfiguration(
            AsPromotionRuleChecker::class,
            static function (ChildDefinition $definition, AsPromotionRuleChecker $attribute) {
                $definition->addTag('sylius.promotion_rule_checker', [
                    'type' => $attribute->type,
                    'label' => $attribute->label,
                    'formType' => $attribute->formType,
                ]);
            }
        );

        $container->registerAttributeForAutoconfiguration(
            AsCatalogPromotionPriceCalculator::class,
            static function (ChildDefinition $definition, AsCatalogPromotionPriceCalculator $attribute) {
                $definition->addTag('sylius.catalog_promotion.price_calculator', [
                    'type' => $attribute->type,
                ]);
            }
        );

        $container->registerAttributeForAutoconfiguration(
            AsCatalogPromotionVariantChecker::class,
            static function (ChildDefinition $definition, AsCatalogPromotionVariantChecker $attribute) {
                $definition->addTag('sylius.catalog_promotion.variant_checker', [
                    'type' => $attribute->type,
                ]);
            }
        );
    }
}
