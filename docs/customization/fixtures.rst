Customizing Fixtures
====================

What are fixtures?
~~~~~~~~~~~~~~~~~~

Fixtures are just plain old PHP objects, that change system state during their execution - they can either
persist entities in the database, upload files, dispatch events or do anything you think is needed.

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

Why would you customize fixtures?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

There are two main use cases for customizing fixture suites, in each of them you can adapt the data of your shop to be realistic,
the default fixtures suite of Sylius is selling clothes, if you are selling food you'd probably need your own fixtures to show that:

    * preparing test data for the development purposes like demo applications prepared for QA
    * preparing the shop configuration for the production instance

How to modify the existing Sylius fixtures?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

In Sylius, fixtures are configured in ``src/Sylius/Bundle/CoreBundle/Resources/config/app/fixtures.yml``.
It includes the ``default`` suite that is partially-configured.
If you are planning to modify the default fixtures applied by the ``sylius:fixtures:load`` command, modify the ``config\packages\sylius_fixtures.yaml`` file.

Modifying the shop configuration (channels, currencies, payment and shipping methods)
'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''

.. code-block:: yaml

    sylius_fixtures:
        suites:
            default: # this key is always called whenever the sylius:fixtures:load command is called, below we are extending it with new fixtures
                fixtures:
                    currency:
                        options:
                            currencies: ['PLN','HUF']
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
                                ups_eu: # creating a new shipping_method and adding channel to it
                                    code: "ups_eu"
                                    name: "UPS_eu"
                                    enabled: true
                                    channels:
                                        - "PL_WEB"
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

It is more complicated to create fixtures for products, because they have more dependencies (to Variants, Options etc.). In order to prepare a Product
you have to create not only the product itself but other related entities via their own factories.
Sylius delivers four ready implementations of ``Product`` fixtures, that have their relevant options (like sizes for T-shirts):

* ``BookProductFixture``
* ``MugProductFixture``
* ``StickerProductFixture``
* ``TshirtProductFixture``

You can modify their YAML fixture configs, but only within the capabilities delivered by those fixtures classes.

How to customize fixtures for customized models?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. tip::

    The following example is based on `other example of extending an entity with a new field <https://github.com/Sylius/Customizations/pull/7>`_.
    You can browse the full implementation of this example on `this GitHub Pull Request <https://github.com/Sylius/Customizations/pull/23>`__.

Let's suppose you have extended ``App\Entity\Shipping\ShippingMethod`` with a new field ``deliveryConditions``,
just like in the example mentioned above.

**1.** To cover that in fixtures, you will need to override the ``ShippingMethodExampleFactory`` and add this field:

.. code-block:: php

    <?php

    // src/Fixture/Factory/ShippingMethodExampleFactory.php

    namespace App\Fixture\Factory;

    // ...
    use Sylius\Bundle\CoreBundle\Fixture\Factory\ShippingMethodExampleFactory as BaseShippingMethodExampleFactory;

    final class ShippingMethodExampleFactory extends BaseShippingMethodExampleFactory
    {
        //...

        public function create(array $options = []): ShippingMethodInterface
        {
            /** @var ShippingMethod $shippingMethod */
            $shippingMethod = parent::create($options);

            // Protect object if part of our objects don't have new field
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
            /** @var LocaleInterface[] $locales */
            $locales = $this->localeRepository->findAll();
            foreach ($locales as $locale) {
                yield $locale->getCode();
            }
        }
    }

**2.** Extend the ``Sylius\Bundle\CoreBundle\Fixture\ShippingMethodFixture`` in ``App\Fixture\ShippingMethodFixture``:

.. code-block:: php

    <?php

    // src/Fixture/ShippingMethodFixture.php

    namespace App\Fixture;

    use Sylius\Bundle\CoreBundle\Fixture\ShippingMethodFixture as BaseShippingMethodFixture;
    use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

    final class ShippingMethodFixture extends BaseShippingMethodFixture
    {
        protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
        {
            parent::configureResourceNode($resourceNode);

            $resourceNode
                ->children()
                    ->scalarNode('deliveryConditions')->end()
            ;
        }
    }

**3.** Configure the services in the ``config/services.yaml`` file:

.. code-block:: yaml

    sylius.fixture.example_factory.shipping_method:
        class: App\Fixture\Factory\ShippingMethodExampleFactory
        arguments:
            - "@sylius.factory.shipping_method"
            - "@sylius.repository.zone"
            - "@sylius.repository.shipping_category"
            - "@sylius.repository.locale"
            - "@sylius.repository.channel"
            - "@sylius.repository.tax_category"
        public: true

    sylius.fixture.shipping_method:
        class: App\Fixture\ShippingMethodFixture
        arguments:
            - "@sylius.manager.shipping_method"
            - "@sylius.fixture.example_factory.shipping_method"
        tags:
            - { name: sylius_fixtures.fixture }

.. tip::

    When creating fixtures services manually, remember to turn off autowiring for them:

    .. code-block:: yaml

        App\:
            resource: '../src/*'
            exclude: '../src/{Entity,Fixture,Migrations,Tests,Kernel.php}'

    If you leave autowiring on, errors like `Fixture with name "your_custom_fixture" is already registered.` will most probably appear. Your fixture service will register twice (as `app.fixture.bla` by you and as `App\Fixture\BlaFixture` by DI autoconfigure).

**4.** Add new Shipping Methods with delivery conditions in ``config/packages/fixtures.yaml``:

.. code-block:: yaml

    sylius_fixtures:
        suites:
            default:
                fixtures:
                    # ...
                    shipping_method: # our new configuration with the new field
                        options:
                            custom:
                            geis:
                                code: "geis"
                                name: "Geis"
                                enabled: true
                                channels:
                                    - "PL_WEB"
                                deliveryConditions: "3-5 days"

Learn more
~~~~~~~~~~

* :doc:`The Book: Fixtures </book/architecture/fixtures>`
* `FixturesBundle <https://github.com/Sylius/SyliusFixturesBundle/blob/master/docs/index.md>`_
