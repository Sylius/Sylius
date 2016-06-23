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

use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class PaymentMethodExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $paymentMethodFactory;

    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param FactoryInterface $paymentMethodFactory
     * @param RepositoryInterface $localeRepository
     */
    public function __construct(FactoryInterface $paymentMethodFactory, RepositoryInterface $localeRepository)
    {
        $this->paymentMethodFactory = $paymentMethodFactory;
        $this->localeRepository = $localeRepository;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver =
            (new OptionsResolver())
                ->setDefault('name', function (Options $options) {
                    return $this->faker->words(3, true);
                })
                ->setDefault('code', function (Options $options) {
                    return StringInflector::nameToCode($options['name']);
                })
                ->setDefault('gateway', 'offline')
                ->setDefault('enabled', function (Options $options) {
                    return $this->faker->boolean(90);
                })
                ->setAllowedTypes('enabled', 'bool')
        ;
    }
    /**
     * {@inheritdoc}
     */
    public function create(array $options = [])
    {
        $options = $this->optionsResolver->resolve($options);
        
        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $this->paymentMethodFactory->createNew();

        $paymentMethod->setCode($options['code']);
        $paymentMethod->setGateway($options['gateway']);
        $paymentMethod->setEnabled($options['enabled']);

        foreach ($this->getLocales() as $localeCode) {
            $paymentMethod->setCurrentLocale($localeCode);
            $paymentMethod->setFallbackLocale($localeCode);

            $paymentMethod->setName(sprintf('[%s] %s', $localeCode, $options['name']));
        }

        return $paymentMethod;
    }

    /**
     * @return array
     */
    private function getLocales()
    {
        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findAll();
        foreach ($locales as $locale) {
            yield $locale->getCode();
        }
    }
}
