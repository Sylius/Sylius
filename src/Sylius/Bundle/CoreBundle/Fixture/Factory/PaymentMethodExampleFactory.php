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
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Factory\PaymentMethodFactoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentMethodExampleFactory extends AbstractExampleFactory
{
    public const DEFAULT_LOCALE = 'en_US';

    private Generator $faker;

    private OptionsResolver $optionsResolver;

    /** @param RepositoryInterface<LocaleInterface> $localeRepository */
    public function __construct(
        private PaymentMethodFactoryInterface $paymentMethodFactory,
        private RepositoryInterface $localeRepository,
        private ChannelRepositoryInterface $channelRepository,
    ) {
        $this->faker = Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    public function create(array $options = []): PaymentMethodInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $this->paymentMethodFactory->createWithGateway($options['gatewayFactory']);
        /** @var GatewayConfigInterface $gatewayConfig */
        $gatewayConfig = $paymentMethod->getGatewayConfig();
        $gatewayConfig->setGatewayName($options['gatewayName']);
        $gatewayConfig->setConfig($options['gatewayConfig']);
        $gatewayConfig->setUsePayum($options['usePayum']);

        $paymentMethod->setCode($options['code']);
        $paymentMethod->setEnabled($options['enabled']);

        foreach ($this->getLocales() as $localeCode) {
            $paymentMethod->setCurrentLocale($localeCode);
            $paymentMethod->setFallbackLocale($localeCode);

            $paymentMethod->setName($options['name']);
            $paymentMethod->setDescription($options['description']);
            $paymentMethod->setInstructions($options['instructions']);
        }

        foreach ($options['channels'] as $channel) {
            $paymentMethod->addChannel($channel);
        }

        return $paymentMethod;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('name', function (Options $options): string {
                /** @var string $words */
                $words = $this->faker->words(3, true);

                return $words;
            })
            ->setDefault('code', fn (Options $options): string => StringInflector::nameToCode($options['name']))
            ->setDefault('description', fn (Options $options): string => $this->faker->sentence())
            ->setDefault('instructions', null)
            ->setAllowedTypes('instructions', ['null', 'string'])
            ->setDefault('gatewayName', 'Offline')
            ->setDefault('gatewayFactory', 'offline')
            ->setDefault('gatewayConfig', [])
            ->setDefault('usePayum', true)
            ->setAllowedTypes('usePayum', 'bool')
            ->setDefault('enabled', fn (Options $options): bool => $this->faker->boolean(90))
            ->setDefault('channels', LazyOption::all($this->channelRepository))
            ->setAllowedTypes('channels', 'array')
            ->setNormalizer('channels', LazyOption::findBy($this->channelRepository, 'code'))
            ->setAllowedTypes('enabled', 'bool')
        ;
    }

    private function getLocales(): iterable
    {
        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findAll();
        if (empty($locales)) {
            yield self::DEFAULT_LOCALE;
        }

        foreach ($locales as $locale) {
            yield $locale->getCode();
        }
    }
}
