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
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class PaymentMethodFixture extends AbstractResourceFixture
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
     * @param FactoryInterface $paymentMethodFactory
     * @param ObjectManager $paymentMethodManager
     * @param RepositoryInterface $localeRepository
     */
    public function __construct(
        FactoryInterface $paymentMethodFactory,
        ObjectManager $paymentMethodManager,
        RepositoryInterface $localeRepository
    ) {
        parent::__construct($paymentMethodManager, 'payment_methods', 'name');

        $this->paymentMethodFactory = $paymentMethodFactory;
        $this->localeRepository = $localeRepository;

        $this->faker = \Faker\Factory::create();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'payment_method';
    }

    /**
     * {@inheritdoc}
     */
    protected function loadResource(array $options)
    {
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
     * {@inheritdoc}
     */
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode)
    {
        $optionsNode->children()->scalarNode('gateway')->defaultValue('offline');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode)
    {
        $resourceNode
            ->children()
                ->scalarNode('code')->cannotBeEmpty()->end()
                ->scalarNode('gateway')->cannotBeEmpty()->end()
                ->booleanNode('enabled')->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceOptionsResolver(array $options, OptionsResolver $optionsResolver)
    {
        $optionsResolver
            ->setRequired('name')
            ->setDefault('code', function (Options $options) {
                return StringInflector::nameToCode($options['name']);
            })
            ->setDefault('gateway', $options['gateway'])
            ->setDefault('enabled', true)
            ->setAllowedTypes('enabled', 'bool')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function generateResourcesOptions($amount)
    {
        $resourcesOptions = [];
        for ($i = 0; $i < $amount; ++$i) {
            $resourcesOptions[] = ['name' => $this->faker->words(3, true)];
        }

        return $resourcesOptions;
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
