<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Fixture\Factory;

use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Factory\PaymentMethodFactoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class PaymentMethodExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    const DEFAULT_LOCALE = 'en_US';

    /**
     * @var PaymentMethodFactoryInterface
     */
    private $paymentMethodFactory;

    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param PaymentMethodFactoryInterface $paymentMethodFactory
     * @param RepositoryInterface $localeRepository
     * @param ChannelRepositoryInterface $channelRepository
     */
    public function __construct(
        PaymentMethodFactoryInterface $paymentMethodFactory,
        RepositoryInterface $localeRepository,
        ChannelRepositoryInterface $channelRepository
    ) {
        $this->paymentMethodFactory = $paymentMethodFactory;
        $this->localeRepository = $localeRepository;
        $this->channelRepository = $channelRepository;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }
    /**
     * {@inheritdoc}
     */
    public function create(array $options = [])
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $this->paymentMethodFactory->createWithGateway($options['gatewayFactory']);
        $paymentMethod->getGatewayConfig()->setGatewayName($options['gatewayName']);
        $paymentMethod->getGatewayConfig()->setConfig($options['gatewayConfig']);

        $paymentMethod->setCode($options['code']);
        $paymentMethod->setEnabled($options['enabled']);

        foreach ($this->getLocales() as $localeCode) {
            $paymentMethod->setCurrentLocale($localeCode);
            $paymentMethod->setFallbackLocale($localeCode);

            $paymentMethod->setName($options['name']);
            $paymentMethod->setDescription($options['description']);
        }

        foreach ($options['channels'] as $channel) {
            $paymentMethod->addChannel($channel);
        }

        return $paymentMethod;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('name', function (Options $options) {
                return $this->faker->words(3, true);
            })
            ->setDefault('code', function (Options $options) {
                return StringInflector::nameToCode($options['name']);
            })
            ->setDefault('description', function (Options $options) {
                return $this->faker->sentence();
            })
            ->setDefault('gatewayName', 'Offline')
            ->setDefault('gatewayFactory', 'offline')
            ->setDefault('gatewayConfig', [])
            ->setDefault('enabled', function (Options $options) {
                return $this->faker->boolean(90);
            })
            ->setDefault('channels', LazyOption::all($this->channelRepository))
            ->setAllowedTypes('channels', 'array')
            ->setNormalizer('channels', LazyOption::findBy($this->channelRepository, 'code'))
            ->setAllowedTypes('enabled', 'bool')
        ;
    }

    /**
     * @return array
     */
    private function getLocales()
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
