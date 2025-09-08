# Customizing Fixtures

## What are fixtures?

In Sylius, **fixtures** are plain PHP objects used to initialize or modify your application's state. They're especially useful for:

* Populating the database with entities (e.g., products, customers)
* Uploading files
* Dispatching events
* Preparing the environment for testing or development

Fixtures can do anything needed to set up your system’s state.

### Fixture Basics

A fixture must implement the `Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface` and be tagged with `sylius_fixtures.fixture` to be recognized in a fixture suite.

```yaml
sylius_fixtures:
    suites:
        my_suite_name:
            fixtures:
                my_fixture:  # Fixture key
                    priority: 0  # Lower numbers run later
                    options: ~   # Options passed to the fixture
```

{% hint style="info" %}
**Note:**\
This interface extends the `ConfigurationInterface`, known from `Configuration` classes inside `DependencyInjection` directories in Symfony bundles.
{% endhint %}

## Why Customize Fixtures?

There are two primary reasons to customize fixtures:

1. **Development & Testing** – to preload data for QA environments or demo setups.
2. **Production Initialization** – to define initial shop configuration (channels, currencies, methods, etc.).

{% hint style="warning" %}
The default Sylius fixture suite is tailored to a fashion store. Customizing fixtures is recommended if your business sells different products (e.g., books, food).
{% endhint %}

## How to modify the existing Sylius fixtures?

{% hint style="info" %}
### Listing Existing Fixtures

To view available fixtures in your project, run:&#x20;

`php bin/console sylius:fixtures:list`
{% endhint %}

### Example: Modifying the Shop Configuration

Create a `config/packages/sylius_fixtures.yaml` to define your shop setup. Here's an example:

```yaml
sylius_fixtures:
    suites:
        default: # Used by the `sylius:fixtures:load` command
            fixtures:
                currency:
                    options:
                        currencies: ['CZK', 'HUF']
                channel:
                    options:
                        custom:
                            cz_web_store: # Creating a new channel
                                name: "CZ Web Store"
                                code: "CZ_WEB"
                                locales:
                                    - "%locale%"
                                currencies:
                                    - "CZK"
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
                            ups_eu: # Creating a new shipping method and assigning it to both channels
                                code: "ups_eu"
                                name: "UPS_eu"
                                enabled: true
                                channels:
                                    - "CZ_WEB"
                                    - "HUN_WEB"
                payment_method:
                    options:
                        custom:
                            cash_on_delivery_cz:
                                code: "cash_on_delivery_eu"
                                name: "Cash on delivery_eu"
                                channels:
                                    - "CZ_WEB"
                            bank_transfer:
                                code: "bank_transfer_eu"
                                name: "Bank transfer_eu"
                                channels:
                                    - "CZ_WEB"
                                    - "HUN_WEB"
                                enabled: true

```

## Customizing Fixtures for Extended Models

If you’ve added custom fields to an entity, you'll also need to update the fixture logic.\
Let's assume that `ShippingMethod` has been extended with `deliveryConditions` field.

#### Scenario: Adding `deliveryConditions` to `ShippingMethod`

**1. Extend the Example Factory**

```php
<?php

namespace App\Fixture\Factory;

use Sylius\Bundle\CoreBundle\Fixture\Factory\ShippingMethodExampleFactory as BaseShippingMethodExampleFactory;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ShippingMethodExampleFactory extends BaseShippingMethodExampleFactory
{
    // ...
    public function create(array $options = []): ShippingMethodInterface
    {
        $shippingMethod = parent::create($options);

        if (!isset($options['deliveryConditions'])) {
            return $shippingMethod;
        }

        // Access locales through the parent's public API (if available)
        // or find another way to get locales
        foreach ($this->getLocalesFromRepository() as $localeCode) {
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

    private function getLocalesFromRepository(): iterable
    {
        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findAll();
        foreach ($locales as $locale) {
            yield $locale->getCode();
        }
    }
}
```

{% hint style="warning" %}
Since Sylius 2.0.8, fixture factory constructor args are `protected`—easy to extend. On older versions, you must override the full constructor.
{% endhint %}

2. Extend the Fixture Class

```php
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
```

**3. Register Services**

Update your `config/services.yaml`:

```yaml
services:
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
```

{% hint style="warning" %}
Disable autowiring for fixtures to avoid duplicate service definitions:

```yaml
App\:
    resource: '../src/*'
    exclude: '../src/{Entity,Fixture,Migrations,Tests,Kernel.php}'

```
{% endhint %}

4. **Use Your Extended Field**

Now you can **add the `deliveryConditions` key to your `shipping_method` fixture** in `sylius_fixtures.yaml`:

```yaml
sylius_fixtures:
    suites:
        default:
            fixtures:
                shipping_method:
                    options:
                        custom:
                            geis:
                                code: "geis"
                                name: "Geis"
                                enabled: true
                                channels:
                                    - "CZ_WEB"
                                deliveryConditions: "3-5 days"

```

### Learn more

* [SyliusFixturesBundle Docs](https://github.com/Sylius/SyliusFixturesBundle/blob/v1.9.0/docs/index.md)
