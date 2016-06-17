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
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class PaymentMethodFixture extends AbstractFixture
{
    /**
     * @var FactoryInterface
     */
    private $paymentMethodFactory;

    /**
     * @var ObjectManager
     */
    private $paymentMethodManager;

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

        $this->paymentMethodFactory = $paymentMethodFactory;
        $this->paymentMethodManager = $paymentMethodManager;
        $this->localeRepository = $localeRepository;

        $this->faker = \Faker\Factory::create();
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $options)
    {
        foreach ($options['payment_methods'] as $name) {
            $paymentMethod = $this->createPaymentMethod($name, $options['gateway']);

            $this->paymentMethodManager->persist($paymentMethod);
        }

        $this->paymentMethodManager->flush();
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
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode)
    {
        $optionsNodeBuilder = $optionsNode->children();

        $optionsNodeBuilder->scalarNode('gateway')->defaultValue('offline');

        /** @var ArrayNodeDefinition $paymentMethodsNode */
        $paymentMethodsNode = $optionsNodeBuilder->arrayNode('payment_methods');
        $paymentMethodsNode
            ->isRequired()
            ->requiresAtLeastOneElement()
            ->beforeNormalization()
                ->ifTrue(function ($value) {
                    return is_numeric($value) && 0 !== (int) $value;
                })
                ->then(function ($amount) {
                    $names = [];
                    for ($i = 0; $i < (int) $amount; ++$i) {
                        $names[] = $this->faker->words(3, true);
                    }

                    return $names;
                })
        ;
        $paymentMethodsNode->prototype('scalar');
    }

    /**
     * @param string $name
     * @param string $gateway
     *
     * @return PaymentMethodInterface
     */
    private function createPaymentMethod($name, $gateway)
    {
        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $this->paymentMethodFactory->createNew();

        $paymentMethod->setCode(StringInflector::nameToCode($name));
        $paymentMethod->setGateway($gateway);
        $paymentMethod->setEnabled(true);

        foreach ($this->getLocales() as $localeCode) {
            $paymentMethod->setCurrentLocale($localeCode);
            $paymentMethod->setFallbackLocale($localeCode);

            $paymentMethod->setName(sprintf('[%s] %s', $localeCode, $name));
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
