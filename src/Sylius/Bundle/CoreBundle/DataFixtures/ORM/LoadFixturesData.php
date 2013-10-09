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

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Generator;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Intl\Intl;
use Nelmio\Alice\Fixtures;
use Faker\Factory as FakerFactory;

/**
 * Base data fixture.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class LoadFixturesData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * Container.
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var AddressProvider
     */
    protected $addressProvider;

    /**
     * @var CountryProvider
     */
    protected $countryProvider;

    /**
     * @var ProductProvider
     */
    protected $productProvider;

    /**
     * @var string
     */
    protected $locale;

    /**
     * Faker.
     *
     * @var Generator
     */
    protected $faker;

    public function __construct()
    {
        $this->faker = FakerFactory::create();
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        // load regular fixtures
        $regulars = array('countries', 'exchange_rates', 'promotions', 'users', 'properties', 'zones', 'options', 'taxation', 'shipping', 'payment_methods', 'taxonomies', 'taxonomies');
        array_walk($regulars, function(&$file) {
            $file = __DIR__ . "/../DATA/$file.yml";
        });
        Fixtures::load($regulars, $manager, $this->getAliceOptions());
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;

        $this->locale = $this->container->getParameter('sylius.locale');

        $this->addressProvider = new AddressProvider($this->locale, $this->faker);
        $this->countryProvider = new CountryProvider($this->locale, $this->faker);
        $this->productProvider = new ProductProvider($this->locale, $this->faker);
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

    /**
     * Return the options needed by Alice to load the fixtures.
     *
     * @return array
     */
    protected function getAliceOptions()
    {
        return array(
            'locale' => $this->locale,
            'providers' => array(
                $this,
                $this->addressProvider,
                $this->countryProvider,
                $this->productProvider,
            ),
        );
    }
}