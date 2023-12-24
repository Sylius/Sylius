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

namespace Sylius\Bundle\PromotionBundle\DependencyInjection;

use Sylius\Bundle\PromotionBundle\Attribute\AsPromotionAction;
use Sylius\Bundle\PromotionBundle\Attribute\AsPromotionCouponEligibilityChecker;
use Sylius\Bundle\PromotionBundle\Attribute\AsPromotionEligibilityChecker;
use Sylius\Bundle\PromotionBundle\Attribute\AsPromotionRuleChecker;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
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

        $container->setParameter('sylius.promotion.promotion_action.validation_groups', $config['promotion_action']['validation_groups']);
        $container->setParameter('sylius.promotion.promotion_rule.validation_groups', $config['promotion_rule']['validation_groups']);

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);
        $this->registerAutoconfiguration($container);
    }

    private function registerAutoconfiguration(ContainerBuilder $container): void
    {
        $container->registerAttributeForAutoconfiguration(
            AsPromotionAction::class,
            static function (ChildDefinition $definition, AsPromotionAction $attribute): void {
                $definition->addTag(AsPromotionAction::SERVICE_TAG, [
                    'type' => $attribute->getType(),
                    'label' => $attribute->getLabel(),
                    'priority' => $attribute->getPriority(),
                ]);
            },
        );

        $container->registerAttributeForAutoconfiguration(
            AsPromotionCouponEligibilityChecker::class,
            static function (ChildDefinition $definition, AsPromotionCouponEligibilityChecker $attribute): void {
                $definition->addTag(AsPromotionCouponEligibilityChecker::SERVICE_TAG, ['priority' => $attribute->getPriority()]);
            },
        );

        $container->registerAttributeForAutoconfiguration(
            AsPromotionEligibilityChecker::class,
            static function (ChildDefinition $definition, AsPromotionEligibilityChecker $attribute): void {
                $definition->addTag(AsPromotionEligibilityChecker::SERVICE_TAG, ['priority' => $attribute->getPriority()]);
            },
        );

        $container->registerAttributeForAutoconfiguration(
            AsPromotionRuleChecker::class,
            static function (ChildDefinition $definition, AsPromotionRuleChecker $attribute): void {
                $definition->addTag(AsPromotionRuleChecker::SERVICE_TAG, [
                    'type' => $attribute->getType(),
                    'label' => $attribute->getLabel(),
                    'form-type' => $attribute->getFormType(),
                    'priority' => $attribute->getPriority(),
                ]);
            },
        );
    }
}
