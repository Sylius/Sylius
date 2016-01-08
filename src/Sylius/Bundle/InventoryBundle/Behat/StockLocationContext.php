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
use Symfony\Component\EventDispatcher\GenericEvent;

class StockLocationContext extends DefaultContext
{
    /**
     * @Given /^there are stock locations:$/
     * @Given /^the following stock locations exist:$/
     */
    public function thereAreStockLocations(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $this->thereisStockLocation(
                $data['name'],
                isset($data['code']) ? $data['code'] : null,
                isset($data['enabled']) ? $data['enabled'] : 'yes',
                true);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^I created stock location "([^""]*)"$/
     * @Given /^there is stock location "([^""]*)"$/
     * @Given /^there is an enabled stock location "([^""]*)"$/
     */
    public function thereIsStockLocation($name, $code = null, $enabled = 'yes', $flush = true)
    {
        $repository = $this->getRepository('stock_location');
        $factory = $this->getFactory('stock_location');
        $dispatcher = $this->getService('event_dispatcher');

        if (null === $stockLocation = $repository->findOneBy(['name' => $name])) {
            $stockLocation = $factory->createNew();
            $stockLocation->setName(trim($name));
        }

        if (null === $code) {
            $code = strtolower($name);
        }
        $stockLocation->setCode(trim($code));
        $stockLocation->setEnabled($enabled == 'yes');

        $manager = $this->getEntityManager();
        $manager->persist($stockLocation);

        if ($flush) {
            $manager->flush();
        }

        $dispatcher->dispatch('sylius.stock_location.post_create', new GenericEvent($stockLocation));

        return $stockLocation;
    }

    /**
     * @Given /^there is a disabled stock location "([^""]*)"$/
     */
    public function thereIsDisabledStockLocation($name)
    {
        $this->thereisStockLocation($name, null, 'no', true);
    }
}
