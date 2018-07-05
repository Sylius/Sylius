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

namespace Sylius\Bundle\CoreBundle\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

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
    public function load(array $options): void
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
    public function getName(): string
    {
        return 'currency';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNode
            ->children()
                ->arrayNode('currencies')
                    ->scalarPrototype()
        ;
    }
}
