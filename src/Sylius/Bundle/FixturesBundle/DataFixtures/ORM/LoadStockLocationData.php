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
use Sylius\Component\Core\Model\StockLocationInterface;

/**
 * Stock location data.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class LoadStockLocationData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $location1 = $this->createLocation('LONDON-1', 'London Werehouse');
        $location2 = $this->createLocation('NASHVILLE-1', 'Nashville 1');
        $location3 = $this->createLocation('NASHVILLE-2', 'Nashville 2');
        $location4 = $this->createLocation('WARSAW-1', 'Warsaw Werehouse');

        foreach (array($location1, $location2, $location3, $location4) as $location) {
            $manager->persist($location);
            $manager->flush();

            $this->dispatchEvent('sylius.stock_location.post_create', $location);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 10;
    }

    /**
     * @param string $code
     * @param string $name
     *
     * @return StockLocationInterface
     */
    protected function createLocation($code, $name)
    {
        $stocklocation = $this->getStockLocationRepository()->createNew();
        $stocklocation->setCode($code);
        $stocklocation->setName($name);
        $stocklocation->setAddress($this->createAddress());

        $this->setReference('Sylius.StockLocation.'.$code, $stocklocation);

        return $stocklocation;
    }
}
