<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\ShippingBundle\Calculator\DefaultCalculators;
use Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface;

/**
 * Default shipping fixtures.
 * Creates sample shipping categories and methods.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class LoadShippingData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $regular = $this->createShippingCategory('Regular', 'Regular weight items');
        $heavy = $this->createShippingCategory('Heavy', 'Heavy items');

        $manager->persist($regular);
        $manager->persist($heavy);

        $config = array('first_item_cost' => 1000, 'additional_item_cost' => 500, 'additional_item_limit' => 0);
        $manager->persist($this->createShippingMethod('FedEx', 'USA', DefaultCalculators::FLEXIBLE_RATE, $config));

        $config = array('amount' => 2500);
        $manager->persist($this->createShippingMethod('UPS Ground', 'EU', DefaultCalculators::FLAT_RATE, $config));

        $config = array('amount' => 2350);
        $manager->persist($this->createShippingMethod('DHL', 'EU', DefaultCalculators::FLAT_RATE, $config));

        $config =  array('first_item_cost' => 4000, 'additional_item_cost' => 500, 'additional_item_limit' => 10);
        $manager->persist($this->createShippingMethod('FedEx World Shipping', 'Rest of World', DefaultCalculators::FLEXIBLE_RATE, $config));

        $manager->flush();
    }

    /**
     * Create new shipping category instance.
     *
     * @param string $name
     * @param string $description
     *
     * @return ShippingCategoryInterface
     */
    protected function createShippingCategory($name, $description)
    {
        $category = $this
            ->getShippingCategoryRepository()
            ->createNew()
        ;

        $category->setName($name);
        $category->setDescription($description);

        $this->setReference('Sylius.ShippingCategory.'.$name, $category);

        return $category;
    }

    /**
     * Create shipping method.
     *
     * @param string                    $name
     * @param string                    $zoneName
     * @param string                    $calculator
     * @param array                     $configuration
     * @param ShippingCategoryInterface $category
     *
     * @return ShippingMethodInterface
     */
    protected function createShippingMethod($name, $zoneName, $calculator = DefaultCalculators::PER_ITEM_RATE, array $configuration = array(), ShippingCategoryInterface $category = null)
    {
        $method = $this
            ->getShippingMethodRepository()
            ->createNew()
        ;

        $method->setName($name);
        $method->setZone($this->getZoneByName($zoneName));
        $method->setCalculator($calculator);
        $method->setConfiguration($configuration);
        $method->setCategory($category);

        $this->setReference('Sylius.ShippingMethod.'.$name, $method);

        return $method;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 4;
    }
}
