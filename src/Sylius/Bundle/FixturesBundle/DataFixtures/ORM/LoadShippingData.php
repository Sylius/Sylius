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
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Calculator\DefaultCalculators;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;

/**
 * Default shipping fixtures.
 * Creates sample shipping categories and methods.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class LoadShippingData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $regular = $this->createShippingCategory('Regular', 'Regular weight items', 'SC1');
        $heavy = $this->createShippingCategory('Heavy', 'Heavy items', 'SC2');

        $manager->persist($regular);
        $manager->persist($heavy);

        $config = ['first_unit_cost' => 1000, 'additional_unit_cost' => 500, 'additional_unit_limit' => 0];
        $manager->persist($this->createShippingMethod([$this->defaultLocale => 'FedEx'], 'SM1', 'USA', DefaultCalculators::FLEXIBLE_RATE, $config));

        $config = ['amount' => 2500];
        $manager->persist($this->createShippingMethod([$this->defaultLocale => 'UPS Ground', 'es_ES' => 'UPS terrestre'], 'SM2', 'EU', DefaultCalculators::FLAT_RATE, $config));

        $config = ['amount' => 2350];
        $manager->persist($this->createShippingMethod([$this->defaultLocale => 'DHL'], 'SM3', 'EU', DefaultCalculators::FLAT_RATE, $config));

        $config = ['first_unit_cost' => 4000, 'additional_unit_cost' => 500, 'additional_unit_limit' => 10];
        $manager->persist($this->createShippingMethod([$this->defaultLocale => 'FedEx World Shipping', 'es_ES' => 'FedEx internacional'], 'SM4', 'RoW', DefaultCalculators::FLEXIBLE_RATE, $config));

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 30;
    }

    /**
     * Create new shipping category instance.
     *
     * @param string $name
     * @param string $description
     * @param string $code
     *
     * @return ShippingCategoryInterface
     */
    protected function createShippingCategory($name, $description, $code)
    {
        /* @var $category ShippingCategoryInterface */
        $category = $this->getShippingCategoryFactory()->createNew();
        $category->setName($name);
        $category->setDescription($description);
        $category->setCode($code);

        $this->setReference('Sylius.ShippingCategory.'.$name, $category);

        return $category;
    }

    /**
     * Create shipping method.
     *
     * @param array                     $translatedNames
     * @param string                    $code
     * @param string                    $zoneCode
     * @param string                    $calculator
     * @param array                     $configuration
     * @param ShippingCategoryInterface $category
     *
     * @return ShippingMethodInterface
     */
    protected function createShippingMethod(array $translatedNames, $code, $zoneCode, $calculator = DefaultCalculators::PER_UNIT_RATE, array $configuration = [], ShippingCategoryInterface $category = null)
    {
        /* @var $method ShippingMethodInterface */
        $method = $this->getShippingMethodFactory()->createNew();

        foreach ($translatedNames as $locale => $name) {
            $method->setCurrentLocale($locale);
            $method->setFallbackLocale($locale);
            $method->setName($name);

            if ($this->defaultLocale == $locale) {
                $this->setReference('Sylius.ShippingMethod.'.$name, $method);
            }
        }

        $method->setZone($this->getZoneByCode($zoneCode));
        $method->setCode($code);
        $method->setCalculator($calculator);
        $method->setConfiguration($configuration);
        $method->setCategory($category);

        return $method;
    }
}
