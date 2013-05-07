<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DataFixtures\ORM;

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
        $email = $this->createShippingCategory('E-mail', 'Wysyłka produktów wirtualnych');

        $manager->persist($email);

        $config = array('amount' => 0);
        $manager->persist($this->createShippingMethod('Email', 'EU', DefaultCalculators::FLAT_RATE, $config));

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
    private function createShippingCategory($name, $description)
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
    private function createShippingMethod($name, $zoneName, $calculator = DefaultCalculators::PER_ITEM_RATE, array $configuration = array(), ShippingCategoryInterface $category = null)
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
