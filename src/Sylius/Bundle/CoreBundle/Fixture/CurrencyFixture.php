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
class CurrencyFixture extends AbstractFixture
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
     * @param FactoryInterface $currencyFactory
     * @param ObjectManager $currencyManager
     */
    public function __construct(FactoryInterface $currencyFactory, ObjectManager $currencyManager)
    {
        $this->currencyFactory = $currencyFactory;
        $this->currencyManager = $currencyManager;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $options)
    {
        foreach ($options['currencies'] as $currencyCode) {
            /** @var CurrencyInterface $currency */
            $currency = $this->currencyFactory->createNew();

            $currency->setCode($currencyCode);

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
                    ->prototype('scalar')
        ;
    }
}
