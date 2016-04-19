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
        $taxableGoods = $this->createTaxCategory('taxable', 'Taxable goods', 'Default taxation category');

        $taxableGoods->addRate($this->createTaxRate('eu_vat', 'EU VAT', 'EU', 0.23));
        $taxableGoods->addRate($this->createTaxRate('us_sales', 'US Sales Tax', 'USA', 0.08));
        $taxableGoods->addRate($this->createTaxRate('no_tax', 'No tax', 'RoW', 0.00));

        $manager->persist($taxableGoods);
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
        $category->setCode($code);
        $category->setName($name);
        $category->setDescription($description);

        $this->setReference('Sylius.TaxCategory.'.$code, $category);

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
        $rate->setCode($code);
        $rate->setName($name);
        $rate->setZone($this->getZoneByCode($zoneCode));
        $rate->setAmount($amount);
        $rate->setIncludedInPrice($includedInPrice);
        $rate->setCalculator($calculator);

        $this->setReference('Sylius.TaxRate.'.$code, $rate);

        return $rate;
    }
}
