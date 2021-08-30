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

use Faker\Generator;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CatalogPromotionExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    private FactoryInterface $catalogPromotionFactory;

    private RepositoryInterface $localeRepository;

    private ChannelRepositoryInterface $channelRepository;

    private Generator $faker;

    private OptionsResolver $optionsResolver;

    public function __construct(
        FactoryInterface $catalogPromotionFactory,
        RepositoryInterface $localeRepository,
        ChannelRepositoryInterface $channelRepository
    ) {
        $this->catalogPromotionFactory = $catalogPromotionFactory;
        $this->localeRepository = $localeRepository;
        $this->channelRepository = $channelRepository;

        $this->faker = \Faker\Factory::create();
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

        foreach ($this->getLocales() as $localeCode) {
            $catalogPromotion->setCurrentLocale($localeCode);
            $catalogPromotion->setFallbackLocale($localeCode);

            $catalogPromotion->setLabel($options['label']);
            $catalogPromotion->setDescription($options['description']);
        }

        foreach ($options['channels'] as $channel) {
            $catalogPromotion->addChannel($channel);
        }

        return $catalogPromotion;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('code', function (Options $options): string {
                return StringInflector::nameToCode($options['name']);
            })
            ->setNormalizer('code', static function (Options $options, ?string $code): string {
                if ($code === null) {
                    return StringInflector::nameToCode($options['name']);
                }

                return $code;
            })
            ->setDefault('name', function (Options $options): string {
                /** @var string $words */
                $words = $this->faker->words(3, true);

                return $words;
            })
            ->setDefault('label', function (Options $options): string {
                return $options['name'];
            })
            ->setDefault('description', function (Options $options): string {
                return $this->faker->sentence();
            })
            ->setDefault('channels', LazyOption::all($this->channelRepository))
            ->setAllowedTypes('channels', 'array')
            ->setNormalizer('channels', LazyOption::findBy($this->channelRepository, 'code'))
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
