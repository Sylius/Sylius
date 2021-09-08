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

namespace Sylius\Bundle\CoreBundle\Fixture\Factory;

use Sylius\Component\Core\Model\CatalogPromotionRuleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CatalogPromotionRuleExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    private FactoryInterface $catalogPromotionRuleFactory;

    private OptionsResolver $optionsResolver;

    public function __construct(FactoryInterface $catalogPromotionRuleFactory)
    {
        $this->catalogPromotionRuleFactory = $catalogPromotionRuleFactory;

        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): CatalogPromotionRuleInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var CatalogPromotionRuleInterface $catalogPromotionRule */
        $catalogPromotionRule = $this->catalogPromotionRuleFactory->createNew();
        $catalogPromotionRule->setType($options['type']);
        $catalogPromotionRule->setConfiguration($options['configuration']);

        return $catalogPromotionRule;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('type', CatalogPromotionRuleInterface::TYPE_FOR_VARIANTS)
            ->setAllowedTypes('type', 'string')
            ->setDefault('configuration', [])
            ->setAllowedTypes('configuration', 'array')
        ;
    }
}
