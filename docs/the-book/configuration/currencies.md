---
layout:
  title:
    visible: true
  description:
    visible: false
  tableOfContents:
    visible: true
  outline:
    visible: true
  pagination:
    visible: true
---

# Currencies

Sylius supports multiple currencies per store and makes it very easy to manage them.

There are several approaches to processing several currencies, but we decided to use the simplest solution: storing all money values in the **base currency per channel** and converting them to other currencies with exchange rates.

{% hint style="warning" %}
The **base currency** to the first channel is set during the installation of Sylius and it has an **exchange rate** equal to “1.000”.
{% endhint %}

{% hint style="info" %}
In the dev environment, you can easily check the base currency in the Symfony debug toolbar.
{% endhint %}

### Currency Context

By default, the user can switch the current currency in the frontend of the store.

To manage the currently used currency, we use the **CurrencyContext**. You can always access it through the `sylius.context.currency` id.

```php
public function fooAction()
{
    $currency = $this->get('sylius.context.currency')->getCurrency();
}
```

### Getting the list of available currencies for a channel

If you want to get a list of currently available currencies for a given channel, you can get them from the `Channel`. You can also get the current `Channel` from the container.

```php
public function fooAction()
{
    // If you don't have it, you can get the current channel from the container
    $channel = $this->container->get('sylius.context.channel')->getChannel();

    $currencies = $channel->getCurrencies();
}
```

{% hint style="info" %}
If you want to learn more about `Channels`, what they represent, and how they work; read the previous chapter [Channels](channels.md)
{% endhint %}

### Currency Converter

The `Sylius\Component\Currency\Converter\CurrencyConverter` is a service available under the `sylius.currency_converter` id.

It allows you to convert money values from one currency to another.

This solution is used for displaying an _approximate_ value of price when the desired currency is different from the base currency of the current channel.

### Switching Currency of a Channel

We may of course change the currency used by a channel. For that we have the `sylius.storage.currency` service, which implements the `Sylius\Component\Core\Currency\CurrencyStorageInterface` with methods `->set(ChannelInterface $channel, $currencyCode)` and `->get(ChannelInterface $channel)`.

```
$container->get('sylius.storage.currency')->set($channel, 'PLN');
```

### Displaying Currencies in the templates

There are some useful helpers for rendering money values in the front end. Simply import the money macros of the `ShopBundle` in your twig template and use the functions to display the value:

```
..
{% raw %}
{% import "@SyliusShop/Common/Macro/money.html.twig" as money %}
{% endraw %}
..

<span class="price">{{ money.format(price, 'EUR') }}</span>
```

Sylius provides you with some handy Global Twig variables to facilitate displaying money values even more.
