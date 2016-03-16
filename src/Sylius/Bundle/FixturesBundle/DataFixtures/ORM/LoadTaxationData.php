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
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

/**
 * Basic taxation fixtures.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class LoadTaxationData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $taxableGoods = $this->createTaxCategory('TC1', 'Taxable goods', 'Default taxation category');

        $manager->persist($taxableGoods);
        $manager->flush();

        $taxRate = $this->createTaxRate('TR1', 'EU VAT', 'EU', 0.23);
        $taxRate->setCategory($taxableGoods);

        $manager->persist($taxRate);
        $manager->flush();

        $taxableGoods->addRate($this->createTaxRate('TR2', 'US Sales Tax', 'USA', 0.08));
        $taxRate->setCategory($taxableGoods);

        $manager->persist($taxRate);
        $manager->flush();

        $taxableGoods->addRate($this->createTaxRate('TR3', 'No tax', 'RoW', 0.00));
        $taxRate->setCategory($taxableGoods);

        $manager->persist($taxRate);
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
     * @param string $code
     * @param string $name
     * @param string $description
     *
     * @return TaxCategoryInterface
     */
    protected function createTaxCategory($code, $name, $description)
    {
        /* @var $category TaxCategoryInterface */
        $category = $this->getTaxCategoryFactory()->createNew();
        $category->setName($name);
        $category->setCode($code);
        $category->setDescription($description);

        $this->setReference('Sylius.TaxCategory.'.$name, $category);

        return $category;
    }

    /**
     * Create tax rate.
     *
     * @param string  $code
     * @param string  $name
     * @param string  $zoneCode
     * @param float   $amount
     * @param bool $includedInPrice
     * @param string  $calculator
     *
     * @return TaxRateInterface
     */
    protected function createTaxRate($code, $name, $zoneCode, $amount, $includedInPrice = false, $calculator = 'default')
    {
        /* @var $rate TaxRateInterface */
        $rate = $this->getTaxRateFactory()->createNew();
        $rate->setName($name);
        $rate->setZone($this->getZoneByCode($zoneCode));
        $rate->setAmount($amount);
        $rate->setIncludedInPrice($includedInPrice);
        $rate->setCalculator($calculator);
        $rate->setCode($code);

        $this->setReference('Sylius.TaxRate.'.$name, $rate);

        return $rate;
    }
}
