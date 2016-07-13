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
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CurrencyFixture extends AbstractFixture
{
    /**
     * @var FactoryInterface
     */
    private $currencyFactory;

    /**
     * @var ObjectManager
     */
    private $currencyManager;

    /**
     * @var string
     */
    private $baseCurrencyCode;

    /**
     * @param FactoryInterface $currencyFactory
     * @param ObjectManager $currencyManager
     * @param string $baseCurrencyCode
     */
    public function __construct(FactoryInterface $currencyFactory, ObjectManager $currencyManager, $baseCurrencyCode)
    {
        $this->currencyFactory = $currencyFactory;
        $this->currencyManager = $currencyManager;
        $this->baseCurrencyCode = $baseCurrencyCode;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $options)
    {
        $currenciesCodes = array_merge([$this->baseCurrencyCode => true], $options['currencies']);

        foreach ($currenciesCodes as $currencyCode => $enabled) {
            /** @var CurrencyInterface $currency */
            $currency = $this->currencyFactory->createNew();

            $currency->setCode($currencyCode);
            $currency->setEnabled($enabled);

            if ($currencyCode === $this->baseCurrencyCode) {
                $currency->setExchangeRate(1.00);
            } else {
                $currency->setExchangeRate(mt_rand(0, 200) / 100);
            }

            $this->currencyManager->persist($currency);
        }

        $this->currencyManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'currency';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode)
    {
        $optionsNode
            ->children()
                ->arrayNode('currencies')
                    ->useAttributeAsKey('code')
                    ->prototype('boolean')
                        ->defaultTrue()
        ;
    }
}
