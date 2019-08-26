Customizing Fixtures
====================

What are fixtures?
~~~~~~~~~~~~~~~~~~

Fixtures are just plain old PHP objects, that change system state during their execution - they can either
persist some entities in the database, upload some files, dispatch some events or do anything you think is needed.

.. code-block:: yaml

    sylius_fixtures:
        suites:
            my_suite_name:
                fixtures:
                    my_fixture: # Fixture name as a key
                        priority: 0 # The higher priority is, the sooner the fixture will be executed
                        options: ~ # Fixture options

They implement the ``Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface`` and need to be registered under
the ``sylius_fixtures.fixture`` tag in order to be used in suite configuration.

.. note::

    The former interface extends the ``ConfigurationInterface``, which is widely known from ``Configuration`` classes
    placed under ``DependencyInjection`` directory in Symfony bundles.

Why would you customize a fixtures?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

If you want to operate on the test data of objects appearing in your store
e.g: ``Currency``, ``Country``, ``User`` etc.

How to add new Fixtures?
~~~~~~~~~~~~~~~~~~~~~~~~

In Sylius part of fixtures are added in ``src/Sylius/Bundle/CoreBundle/Resources/config/app/fixtures.yml``, where is defined ``default`` suite that is partially-configured.
If you are planning to add new fixtures in default command ``sylius:fixtures:load``, only what you have to do is add new items in ``config\packages\_sylius.yml``

Adding new items in shop by standard yaml configuration:

.. code-block:: yaml

    sylius_fixtures:
        suites:
            default: # this key is always called wherever we use sylius:fixtures:load, below we are extending that about new fixtures
                fixtures:
                    currency:
                        options:
                            currencies: ['PLN','HUF','EUR']
                    channel:
                        options:
                            custom:
                                pl_web_store: # creating new channel
                                    name: "PL Web Store"
                                    code: "PL_WEB"
                                    locales:
                                        - "%locale%"
                                    currencies:
                                        - "PLN"
                                    enabled: true
                                    hostname: "localhost"
                                hun_web_store:
                                    name: "Hun Web Store"
                                    code: "HUN_WEB"
                                    locales:
                                        - "%locale%"
                                    currencies:
                                        - "HUF"
                                    enabled: true
                                    hostname: "localhost"
                    shipping_method:
                        options:
                            custom:
                                ups_eu: # creating new shipping_method and adding channel to it
                                    code: "ups_eu"
                                    name: "UPS_eu"
                                    enabled: true
                                    channels:
                                        - "PL_WEB"
                                ups: # adding channel to existing shipping_method
                                    channels:
                                        - "HUN_WEB"
                    payment_method:
                        options:
                            custom:
                                cash_on_delivery_pl:
                                    code: "cash_on_delivery_eu"
                                    name: "Cash on delivery_eu"
                                    channels:
                                        - "PL_WEB"
                                bank_transfer:
                                    code: "bank_transfer_eu"
                                    name: "Bank transfer_eu"
                                    channels:
                                        - "PL_WEB"
                                        - "HUN_WEB"
                                    enabled: true

Fixtures in sylius are loaded and initialized using class located at ``src/Sylius/Bundle/CoreBundle/Fixture/*``

Below is presented class ``Sylius\Bundle\CoreBundle\Fixture\CurrencyFixture`` that load our currencies from yaml configuration:

.. code-block:: php

    <?php

    ...

    class CurrencyFixture extends AbstractFixture
    {
        ...

        // here we load our options array from yaml file
        public function load(array $options): void
        {
            foreach ($options['currencies'] as $currencyCode) {
                /** @var CurrencyInterface $currency */
                $currency = $this->currencyFactory->createNew();

                $currency->setCode($currencyCode);

                $this->currencyManager->persist($currency);
            }

            $this->currencyManager->flush();
        }

        ...

        // here we configure our restriction from our input
        protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
        {
            $optionsNode
                ->children()
                    ->arrayNode('currencies')
                        ->scalarPrototype()
            ;
        }
    }

This fixture is registered in ``src/Sylius/Bundle/CoreBundle/Resources/config/services/fixtures.xml``:

.. code-block:: xml

    <service id="sylius.fixture.currency" class="Sylius\Bundle\CoreBundle\Fixture\CurrencyFixture">
        <argument type="service" id="sylius.factory.currency" />
        <argument type="service" id="sylius.manager.currency" />
        <tag name="sylius_fixtures.fixture" />
    </service>

``Currency`` is simple and short example created fixtures but many models need additionally factory to create new items.
Factories must implements ``Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface`` and ``Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory``

`Products` are most complicated because have more dependencies than rest models
Sylius delivered four ready implementation of ``Product``:

* BookProductFixture
* MugProductFixture
* StickerProductFixture
* TshirtProductFixture

How to add new custom Fixtures?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

If you want to write custom fixture you must create class that implements ``Sylius/Bundle/FixturesBundle/Fixture/FixtureInterface``

Best ways to write custom fixture is extend class ``Sylius/Bundle/FixturesBundle/Fixture/AbstractFixture`` or ``Sylius/Bundles/FixturesBundle/Fixture/AbstractResourceFixture``:

* Extending ``AbstractFixtures`` that is basic class giving only sample configuration, next we must override methods: load() and configureOptionsNode()
* In most cases enought extend ``AbstractResourceFixture``, this class is partially-configured, only what we have to do is override configureOptionsNode()

How to add new custom Fixtures in custom Models?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. tip::

    Below Example is based on other example showing how to extends entity with a new field.
    You can browse the full implementation of this example on `this GitHub Pull Request
    <https://github.com/Sylius/Customizations/pull/23>`__.

**1** First we extended our entity ``App\Entity\Shipping\ShippingMethod`` with a new field ``deliveryConditions``.

**2** Next we need extends our factory ``Sylius\Bundle\CoreBundle\Fixture\Factory\ShippingMethodExampleFactory`` with this field
in ``App\Entity\Factory\ShippingMethodExampleFactory``:

.. code-block:: php

    <?php

    ...

    final class ShippingMethodExampleFactory extends BaseShippingMethodExampleFactory implements ExampleFactoryInterface
    {
        ...

        public function create(array $options = []): ShippingMethodInterface
        {
            /** @var ShippingMethod $shippingMethod */
            $shippingMethod = parent::create($options);

            // here we protect object if part of our objects don't have new field
            if (!isset($options['deliveryConditions'])) {
                return $shippingMethod;
            }

            foreach ($this->getLocales() as $localeCode) {
                $shippingMethod->setCurrentLocale($localeCode);
                $shippingMethod->setFallbackLocale($localeCode);

                $shippingMethod->setDeliveryConditions($options['deliveryConditions']);
            }

            return $shippingMethod;
        }

        protected function configureOptions(OptionsResolver $resolver): void
        {
            parent::configureOptions($resolver);

            $resolver
                ->setDefault('deliveryConditions', 'some_default_value')
                ->setAllowedTypes('deliveryConditions', ['null', 'string'])
            ;
        }

        private function getLocales(): iterable
        {
            ...
        }
    }

**3** Now we extended ``Sylius\Bundle\CoreBundle\Fixture\ShippingMethodFixture`` in ``App\Entity\Fixture\ShippingMethodFixture``:

.. code-block:: php

    <?php

    ...

    final class ShippingMethodFixture extends BaseShippingMethodFixture implements FixtureInterface
    {
        public function getName(): string
        {
            return 'shipping_method';
        }

        protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
        {
            parent::configureResourceNode($resourceNode);

            $resourceNode
                ->children()
                    ->scalarNode('deliveryConditions')->end()
            ;
        }
    }

**4** Here we create ``config/packages/fixtures.xml`` and override our services:

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8"?>

    <container xmlns="http://symfony.com/schema/dic/services" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">
        <services>
            <defaults public="true" />

            <service id="sylius.fixture.shipping_method" class="App\Entity\Fixture\ShippingMethodFixture">
                <argument type="service" id="sylius.manager.shipping_method" />
                <argument type="service" id="sylius.fixture.example_factory.shipping_method" />
                <tag name="sylius_fixtures.fixture" />
            </service>

            <service id="sylius.fixture.example_factory.shipping_method" class="App\Entity\Factory\ShippingMethodExampleFactory">
                <argument type="service" id="sylius.factory.shipping_method" />
                <argument type="service" id="sylius.repository.zone" />
                <argument type="service" id="sylius.repository.shipping_category" />
                <argument type="service" id="sylius.repository.locale" />
                <argument type="service" id="sylius.repository.channel" />
            </service>
        </services>
    </container>

**5** At the end, only what you have to do is add new ``shipping_method`` in ``config\packages\_sylius.yml``

.. code-block:: yaml

    sylius_fixtures:
        suites:
            default:
                fixtures:
                    ...
                    shipping_method: # our new configuration with a new field
                        options:
                            custom:
                            geis:
                                code: "geis"
                                name: "geis"
                                enabled: true
                                channels:
                                    - "PL_WEB"
                                deliveryConditions: "delivered"

Learn more
##########

* :doc:`FixtureBundle </components_and_bundles/bundles/SyliusFixturesBundle/index>`
