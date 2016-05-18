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
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Variation\Generator\VariantGenerator;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Intl\Intl;

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
            $this->defaultLocale = $container->getParameter('locale');
            $this->fakers[$this->defaultLocale] = FakerFactory::create($this->defaultLocale);
            $this->faker = $this->fakers[$this->defaultLocale];
        }

        $this->fakers['es_ES'] = FakerFactory::create('es_ES');
    }

    public function __call($method, $arguments)
    {
        $matches = [];
        if (preg_match('/^get(.*)Repository$/', $method, $matches)) {
            return $this->get('sylius.repository.'.$matches[1]);
        }
        if (preg_match('/^get(.*)Factory$/', $method, $matches)) {
            return $this->get('sylius.factory.'.$matches[1]);
        }
        
        if (!method_exists($this, $method)) {
            throw new \Exception(sprintf('Method %s does not exist', $method));
        }

        return call_user_func_array([$this, $method], $arguments);
    }

    /**
     * @return VariantGenerator
     */
    protected function getVariantGenerator()
    {
        return $this->get('sylius.generator.variant');
    }

    /**
     * Get zone reference by its code.
     *
     * @param string $code
     *
     * @return ZoneInterface
     */
    protected function getZoneByCode($code)
    {
        return $this->getReference('Sylius.Zone.'.$code);
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
        $address = $this->getAddressFactory()->createNew();
        $address->setFirstname($this->faker->firstName);
        $address->setLastname($this->faker->lastName);
        $address->setCity($this->faker->city);
        $address->setStreet($this->faker->streetAddress);
        $address->setPostcode($this->faker->postcode);

        /* @var CountryInterface $country */
        $allCountries = Intl::getRegionBundle()->getCountryNames($this->defaultLocale);
        $countries = array_slice($allCountries, 0, count($allCountries) - 5, true);

        $countryCode = array_rand($countries);
        $country = $this->getReference('Sylius.Country.'.$countryCode);

        if ($province = $country->hasProvinces() ? $this->faker->randomElement($country->getProvinces()->toArray()) : null) {
            $address->setProvinceCode($province->getCode());
        }

        $address->setCountryCode($countryCode);

        return $address;
    }
}
