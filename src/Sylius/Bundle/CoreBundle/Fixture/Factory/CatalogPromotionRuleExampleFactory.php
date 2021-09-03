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

use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionRuleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CatalogPromotionRuleExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    private FactoryInterface $catalogPromotionRuleFactory;

    private RepositoryInterface $catalogPromotionRepository;

    private OptionsResolver $optionsResolver;

    public function __construct(
        FactoryInterface $catalogPromotionRuleFactory,
        RepositoryInterface $catalogPromotionRepository
    ) {
        $this->catalogPromotionRuleFactory = $catalogPromotionRuleFactory;
        $this->catalogPromotionRepository = $catalogPromotionRepository;

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
        $catalogPromotionRule->setCatalogPromotion($options['catalogPromotion']);

        return $catalogPromotionRule;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('type', CatalogPromotionRuleInterface::TYPE_FOR_VARIANTS)
            ->setAllowedTypes('type', 'string')
            ->setDefault('configuration', [])
            ->setAllowedTypes('configuration', 'array')
            ->setDefault('catalogPromotion', LazyOption::randomOne($this->catalogPromotionRepository))
            ->setAllowedTypes('catalogPromotion', ['null', 'string', CatalogPromotionInterface::class])
            ->setNormalizer('catalogPromotion', LazyOption::getOneBy($this->catalogPromotionRepository, 'code'))
        ;
    }
}
