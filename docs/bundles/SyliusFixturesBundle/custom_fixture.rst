Custom fixture
==============

Basic fixture
-------------

Let's create a fixture that loads all countries from the ``Intl`` library. We'll extend the ``AbstractFixture`` in order
to skip the configuration part for now:

.. code-block:: php

    namespace AppBundle\Fixture;

    use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
    use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;

    final class CountryFixture extends AbstractFixture implements FixtureInterface
    {
        private $countryManager;

        public function __construct(ObjectManager $countryManager)
        {
            $this->countryManager = $countryManager;
        }

        public function getName()
        {
            return 'country';
        }

        public function load(array $options)
        {
            $countriesCodes = array_keys(\Intl::getRegionBundle()->getCountryNames());

            foreach ($countriesCodes as $countryCode) {
                $country = new Country($countryCode);

                $this->countryManager->persist($country);
            }

            $this->countryManager->flush();
        }
    }

The next step is to register this fixture:

.. code-block:: xml

    <service id="app.fixture.country" class="AppBundle\Fixture\CountryFixture">
        <argument type="service" id="doctrine.orm.entity_manager" />
        <tag name="sylius_fixtures.fixture" />
    </service>


Fixture is now registered and ready to use in your suite:

.. code-block:: yaml

    sylius_fixtures:
        suites:
            my_suite:
                fixtures:
                    country: ~

Configurable fixture
--------------------

Loading all countries may be useful, but what if you want to load only some defined countries in one suite and all
the countries in the another one? You don't need to create multiple fixtures, a one configurable fixture will do the job!

.. code-block:: php

    // ...

    final class CountryFixture extends AbstractFixture implements FixtureInterface
    {
        // ...

        public function load(array $options)
        {
            foreach ($options['countries'] as $countryCode) {
                $country = new Country($countryCode);

                $this->countryManager->persist($country);
            }

            $this->countryManager->flush();
        }

        protected function configureOptionsNode(ArrayNodeDefinition $optionsNode)
        {
            $optionsNodeBuilder
                ->arrayNode('countries')
                    ->performNoDeepMerging()
                    ->defaultValue(array_keys(\Intl::getRegionBundle()->getCountryNames()))
                    ->prototype('scalar')
            ;
        }
    }

.. note::

    The ``AbstractFixture`` implements the ``ConfigurationInterface::getConfigTreeBuilder()`` and exposes a handy
    ``configureOptionsNode()`` method to reduce the boilerplate. It is possible to test this configuration
    using `SymfonyConfigTest`_ library. For examples of that tests have a look at `Sylius Fixtures Configuration Tests`_.

Now, it is possible for the fixture to create different outcomes by just changing its configuration:

.. code-block:: yaml

    sylius_fixtures:
        suites:
            my_suite:
                fixtures:
                    country: ~ # Creates all countries
            my_another_suite:
                fixtures:
                    country:
                        options: ~ # Still creates all countries
            my_customized_suite:
                fixtures:
                    country:
                        options:
                            countries: # Creates only defined countries
                                - PL
                                - FR
                                - DE

.. _`SymfonyConfigTest`: https://github.com/matthiasnoback/SymfonyConfigTest
.. _`Sylius Fixtures Configuration Tests`: https://github.com/Sylius/Sylius/tree/master/src/Sylius/Bundle/CoreBundle/Tests/Fixture
