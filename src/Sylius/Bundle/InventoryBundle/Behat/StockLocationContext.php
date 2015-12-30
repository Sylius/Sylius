<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;
use Sylius\Component\Inventory\Model\StockLocation;

class StockLocationContext extends DefaultContext
{
    /**
     * @Given /^there are stock locations:$/
     * @Given /^the following stock locations exist:$/
     */
    public function thereAreStockLocations(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $this->thereisStockLocation($data['name'], $data['code'], false);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^I created stock location "([^""]*)"$/
     * @Given /^there is stock location "([^""]*)"$/
     * @Given /^there is an enabled stock location "([^""]*)"$/
     */
    public function thereIsStockLocation($name, $code = null, $flush = true)
    {
        /* @var $zone StockLocation */
        $stockLocation = $this->getFactory('stock_location')->createNew();
        $stockLocation->setName(trim($name));
        if (null === $code) {
            $code = strtolower($name);
        }
        $stockLocation->setCode(trim($code));

        $manager = $this->getEntityManager();
        $manager->persist($stockLocation);

        if ($flush) {
            $manager->flush();
        }

        return $stockLocation;
    }
}
