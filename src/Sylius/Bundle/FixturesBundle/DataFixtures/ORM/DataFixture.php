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

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Faker\Factory as FakerFactory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base data fixture.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
abstract class DataFixture extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    /**
     * Container.
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Faker.
     *
     * @var Generator
     */
    protected $faker;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->faker = FakerFactory::create();
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function __call($method, $arguments)
    {
        $matches = array();
        if (preg_match('/^get(.*)Repository$/', $method, $matches)) {
            return $this->get('sylius.repository.'.$matches[1]);
        }

        return call_user_func_array(array($this, $method), $arguments);
    }

    protected function getZoneMemberRepository($zoneType)
    {
        return $this->get('sylius.repository.zone_member_'.$zoneType);
    }

    /**
     * @return VariantGenerator
     */
    protected function getVariantGenerator()
    {
        return $this->get('sylius.generator.variant');
    }

    /**
     * Get zone reference by its name.
     *
     * @param string $name
     *
     * @return ZoneInterface
     */
    protected function getZoneByName($name)
    {
        return $this->getReference('Sylius.Zone.'.$name);
    }

    /**
     * Get service by id.
     *
     * @param string $id
     *
     * @return object
     */
    protected function get($id)
    {
        return $this->container->get($id);
    }
}
