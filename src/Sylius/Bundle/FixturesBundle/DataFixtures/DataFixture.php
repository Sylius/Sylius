<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use Sylius\Bundle\ProductBundle\Generator\VariantGenerator;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Base data fixture.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
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
     * Alias to default language faker.
     *
     * @var Generator
     */
    protected $faker;

    /**
     * Faker.
     *
     * @var Generator
     */
    protected $fakers;

    /**
     * Default locale.
     *
     * @var string
     */
    protected $defaultLocale;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;

        if (null !== $container) {
            $this->defaultLocale = $container->getParameter('sylius.locale');
            $this->fakers[$this->defaultLocale] = FakerFactory::create($this->defaultLocale);
            $this->faker = $this->fakers[$this->defaultLocale];
        }

        $this->fakers['es_ES'] = FakerFactory::create('es_ES');
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
        return $this->get('sylius.generator.product_variant');
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
     * Dispatch an event.
     *
     * @param string $name
     * @param object $object
     */
    protected function dispatchEvent($name, $object)
    {
        return $this->get('event_dispatcher')->dispatch($name, new GenericEvent($object));
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
     * @return AddressInterface
     */
    protected function createAddress()
    {
        /* @var $address AddressInterface */
        $address = $this->getAddressRepository()->createNew();
        $address->setFirstname($this->faker->firstName);
        $address->setLastname($this->faker->lastName);
        $address->setCity($this->faker->city);
        $address->setStreet($this->faker->streetAddress);
        $address->setPostcode($this->faker->postcode);

        do {
            $isoName = $this->faker->countryCode;
        } while ('UK' === $isoName);

        $country  = $this->getReference('Sylius.Country.'.$isoName);
        $province = $country->hasProvinces() ? $this->faker->randomElement($country->getProvinces()->toArray()) : null;

        $address->setCountry($country);
        $address->setProvince($province);

        return $address;
    }
}
