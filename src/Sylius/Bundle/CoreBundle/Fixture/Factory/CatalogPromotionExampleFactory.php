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

namespace Sylius\Bundle\CoreBundle\Fixture\Factory;

use Faker\Factory;
use Faker\Generator;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CatalogPromotionExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    private Generator $faker;

    private OptionsResolver $optionsResolver;

    public function __construct(
        private FactoryInterface $catalogPromotionFactory,
        private RepositoryInterface $localeRepository,
        private ChannelRepositoryInterface $channelRepository,
        private ExampleFactoryInterface $catalogPromotionScopeExampleFactory,
        private ExampleFactoryInterface $catalogPromotionActionExampleFactory,
    ) {
        $this->faker = Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): CatalogPromotionInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var CatalogPromotionInterface $catalogPromotion */
        $catalogPromotion = $this->catalogPromotionFactory->createNew();
        $catalogPromotion->setCode($options['code']);
        $catalogPromotion->setName($options['name']);

        if (isset($options['start_date'])) {
            $catalogPromotion->setStartDate(new \DateTime($options['start_date']));
        }

        if (isset($options['end_date'])) {
            $catalogPromotion->setEndDate(new \DateTime($options['end_date']));
        }

        $catalogPromotion->setEnabled($options['enabled']);
        $catalogPromotion->setPriority($options['priority'] ?? 0);
        $catalogPromotion->setExclusive($options['exclusive'] ?? false);

        foreach ($this->getLocales() as $localeCode) {
            $catalogPromotion->setCurrentLocale($localeCode);
            $catalogPromotion->setFallbackLocale($localeCode);

            $catalogPromotion->setLabel($options['label']);
            $catalogPromotion->setDescription($options['description']);
        }

        foreach ($options['channels'] as $channel) {
            $catalogPromotion->addChannel($channel);
        }

        if (isset($options['scopes'])) {
            foreach ($options['scopes'] as $scope) {
                /** @var CatalogPromotionScopeInterface $catalogPromotionScope */
                $catalogPromotionScope = $this->catalogPromotionScopeExampleFactory->create($scope);
                $catalogPromotionScope->setCatalogPromotion($catalogPromotion);
                $catalogPromotion->addScope($catalogPromotionScope);
            }
        }

        if (isset($options['actions'])) {
            foreach ($options['actions'] as $action) {
                /** @var CatalogPromotionActionInterface $catalogPromotionAction */
                $catalogPromotionAction = $this->catalogPromotionActionExampleFactory->create($action);
                $catalogPromotionAction->setCatalogPromotion($catalogPromotion);
                $catalogPromotion->addAction($catalogPromotionAction);
            }
        }

        return $catalogPromotion;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('code', fn (Options $options): string => StringInflector::nameToCode($options['name']))
            ->setNormalizer('code', static function (Options $options, ?string $code): string {
                if ($code === null) {
                    return StringInflector::nameToCode($options['name']);
                }

                return $code;
            })
            ->setDefault('name', fn (Options $options): string => (string) $this->faker->words(3, true))
            ->setDefault('label', fn (Options $options): string => $options['name'])
            ->setDefault('description', fn (Options $options): string => $this->faker->sentence())
            ->setDefault('channels', LazyOption::all($this->channelRepository))
            ->setAllowedTypes('channels', 'array')
            ->setNormalizer('channels', LazyOption::findBy($this->channelRepository, 'code'))
            ->setDefined('scopes')
            ->setDefined('actions')
            ->setDefault('priority', 0)
            ->setAllowedTypes('priority', ['integer', 'null'])
            ->setDefault('exclusive', false)
            ->setAllowedTypes('exclusive', ['boolean', 'null'])
            ->setDefault('start_date', null)
            ->setAllowedTypes('start_date', ['string', 'null'])
            ->setDefault('end_date', null)
            ->setAllowedTypes('end_date', ['string', 'null'])
            ->setDefault('enabled', true)
            ->setAllowedTypes('enabled', 'boolean')
        ;
    }

    private function getLocales(): iterable
    {
        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findAll();
        foreach ($locales as $locale) {
            yield $locale->getCode();
        }
    }
}
