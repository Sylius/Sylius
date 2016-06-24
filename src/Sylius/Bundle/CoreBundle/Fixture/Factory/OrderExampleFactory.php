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
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class OrderExampleFactory implements ExampleFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $orderFactory;

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param FactoryInterface $orderFactory
     * @param RepositoryInterface $channelRepository
     */
    public function __construct(
        FactoryInterface $orderFactory,
        RepositoryInterface $channelRepository
    ) {
        $this->orderFactory = $orderFactory;

        $this->faker = \Faker\Factory::create();
        $this->optionsResolver =
            (new OptionsResolver())
                ->setDefault('cu', LazyOption::all($shippingMethodRepository))
                ->setAllowedTypes('shipping_methods', 'array')
                ->setNormalizer('shipping_methods', LazyOption::findBy($shippingMethodRepository, 'code'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = [])
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var OrderInterface $channel */
        $channel = $this->orderFactory->createNamed($options['name']);
        $channel->setCode($options['code']);
        $channel->setHostname($options['hostname']);
        $channel->setEnabled($options['enabled']);
        $channel->setColor($options['color']);
        $channel->setTaxCalculationStrategy($options['tax_calculation_strategy']);

        foreach ($options['locales'] as $locale) {
            $channel->addLocale($locale);
        }

        foreach ($options['currencies'] as $currency) {
            $channel->addCurrency($currency);
        }

        foreach ($options['payment_methods'] as $paymentMethod) {
            $channel->addPaymentMethod($paymentMethod);
        }

        foreach ($options['shipping_methods'] as $shippingMethod) {
            $channel->addShippingMethod($shippingMethod);
        }

        return $channel;
    }
}
