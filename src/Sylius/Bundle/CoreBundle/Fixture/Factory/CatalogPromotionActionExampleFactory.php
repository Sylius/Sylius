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

use Sylius\Bundle\CoreBundle\Calculator\PercentageDiscountPriceCalculator;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CatalogPromotionActionExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    private FactoryInterface $catalogPromotionActionFactory;

    private OptionsResolver $optionsResolver;

    public function __construct(FactoryInterface $catalogPromotionActionFactory)
    {
        $this->catalogPromotionActionFactory = $catalogPromotionActionFactory;

        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): CatalogPromotionActionInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var CatalogPromotionActionInterface $catalogPromotionAction */
        $catalogPromotionAction = $this->catalogPromotionActionFactory->createNew();
        $catalogPromotionAction->setType($options['type']);
        $catalogPromotionAction->setConfiguration($options['configuration']);

        return $catalogPromotionAction;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('type', PercentageDiscountPriceCalculator::TYPE)
            ->setAllowedTypes('type', 'string')
            ->setDefault('configuration', [])
            ->setAllowedTypes('configuration', 'array')
        ;
    }
}
