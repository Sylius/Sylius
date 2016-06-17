<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Channel\Factory\ChannelFactoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ChannelFixture extends AbstractResourceFixture
{
    /**
     * @var ChannelFactoryInterface
     */
    private $channelFactory;

    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * @var RepositoryInterface
     */
    private $currencyRepository;

    /**
     * @var RepositoryInterface
     */
    private $paymentMethodRepository;

    /**
     * @var RepositoryInterface
     */
    private $shippingMethodRepository;

    /**
     * @var \Faker\Generator
     */
    private $defaultFaker;

    /**
     * @param ChannelFactoryInterface $channelFactory
     * @param ObjectManager $channelManager
     * @param RepositoryInterface $localeRepository
     * @param RepositoryInterface $currencyRepository
     * @param RepositoryInterface $paymentMethodRepository
     * @param RepositoryInterface $shippingMethodRepository
     */
    public function __construct(
        ChannelFactoryInterface $channelFactory,
        ObjectManager $channelManager,
        RepositoryInterface $localeRepository,
        RepositoryInterface $currencyRepository,
        RepositoryInterface $paymentMethodRepository,
        RepositoryInterface $shippingMethodRepository
    ) {
        parent::__construct($channelManager, 'channels', 'name');

        $this->channelFactory = $channelFactory;
        $this->localeRepository = $localeRepository;
        $this->currencyRepository = $currencyRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->shippingMethodRepository = $shippingMethodRepository;

        $this->defaultFaker = \Faker\Factory::create();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'channel';
    }

    /**
     * {@inheritdoc}
     */
    protected function loadResource(array $options)
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelFactory->createNamed($options['name']);
        $channel->setCode($options['code']);
        $channel->setHostname($options['hostname']);
        $channel->setEnabled($options['enabled']);
        $channel->setColor($options['color']);

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

    /**
     * {@inheritdoc}
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode)
    {
        $resourceNode
            ->children()
                ->scalarNode('code')->cannotBeEmpty()->end()
                ->scalarNode('hostname')->cannotBeEmpty()->end()
                ->scalarNode('color')->cannotBeEmpty()->end()
                ->booleanNode('enabled')->end()
                ->arrayNode('locales')->prototype('scalar')->end()->end()
                ->arrayNode('currencies')->prototype('scalar')->end()->end()
                ->arrayNode('payment_methods')->prototype('scalar')->end()->end()
                ->arrayNode('shipping_methods')->prototype('scalar')->end()->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsResolver(OptionsResolver $optionsResolver)
    {
        $optionsResolver
            ->setRequired(['name'])
            ->setDefault('code', function (Options $options) {
                return StringInflector::nameToCode($options['name']);
            })
            ->setDefault('hostname', function (Options $options) {
                return $options['code'] . '.localhost';
            })
            ->setDefault('color', 'black')
            ->setDefault('enabled', true)
            ->setAllowedTypes('enabled', 'bool')
            ->setDefault('locales', [])
            ->setAllowedTypes('locales', 'array')
            ->setNormalizer('locales', static::createResourcesNormalizer($this->localeRepository))
            ->setDefault('currencies', [])
            ->setAllowedTypes('currencies', 'array')
            ->setNormalizer('currencies', static::createResourcesNormalizer($this->currencyRepository))
            ->setDefault('payment_methods', [])
            ->setAllowedTypes('payment_methods', 'array')
            ->setNormalizer('payment_methods', static::createResourcesNormalizer($this->paymentMethodRepository))
            ->setDefault('shipping_methods', [])
            ->setAllowedTypes('shipping_methods', 'array')
            ->setNormalizer('shipping_methods', static::createResourcesNormalizer($this->shippingMethodRepository))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function generateResourcesConfigurations($amount)
    {
        $names = [];
        for ($i = 0; $i < (int) $amount; ++$i) {
            $names[] = $this->defaultFaker->words(3, true);
        }

        return $names;
    }
}
