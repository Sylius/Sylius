# How to customize email templates per channel?

Let’s assume you have two channels in your Sylius store: `TOY_STORE` and `FASHION_WEB`. You want to personalize email content depending on which channel the customer used to place an order. This guide shows how to achieve this in a maintainable and scalable way.

To verify and manage channels in your system, open the **Channels grid** in the Sylius admin panel. You’ll find each channel’s code, name, and hostname.

<figure><img src="../.gitbook/assets/image (17) (1).png" alt=""><figcaption></figcaption></figure>

These codes are what you will use to differentiate content in your Twig templates.

***

## 1. Identify the Email Template to Override

Original template:

```markup
@SyliusCoreBundle/Email/orderConfirmation.html.twig
```

To override it, copy it to:

```bash
templates/bundles/SyliusCoreBundle/Email/orderConfirmation.html.twig
```

{% hint style="success" %}
You can find a full list of email templates in the [AdminBundle](https://github.com/Sylius/Sylius/tree/v2.1.2/src/Sylius/Bundle/AdminBundle/templates/email), [ShopBundle](https://github.com/Sylius/Sylius/tree/v2.1.2/src/Sylius/Bundle/ShopBundle/templates/email) and [CoreBundle](https://github.com/Sylius/Sylius/tree/2.1/src/Sylius/Bundle/CoreBundle/Resources/views/Email).
{% endhint %}

{% hint style="info" %}
Twig paths like `@SyliusCoreBundle/...` point to the original bundle templates. To override them, create files under `templates/bundles/...`, following Symfony’s bundle override system.
{% endhint %}

***

## 2. Choose a Strategy

Depending on the **number of channels** and **degree of customization**, choose one of two strategies:

### **For 1–2 Channels with Small Differences**

Use inline `if` conditions:

```twig
{# templates/bundles/SyliusCoreBundle/Email/orderConfirmation.html.twig #}

{% extends '@SyliusCore/Email/layout.html.twig' %}

{% block subject %}
    {% include '@SyliusCore/Email/Blocks/OrderConfirmation/_subject.html.twig' %}
{% endblock %}

{% block content %}
    {% if sylius.channel.code == 'TOY_STORE' %}
        Thanks for buying one of our toys!
    {% elseif sylius.channel.code == 'FASHION_WEB' %}
        Your new style is on the way!
    {% else %}
        Thanks for your purchase!
    {% endif %}

    Your order no. {{ order.number }} has been successfully placed.
{% endblock %}
```

{% hint style="success" %}
Best for 2–3 channels that have cosmetic differences.
{% endhint %}

### **For 3+ Channels or Maintainability**

Use per-channel Twig includes with fallback:

#### Parent Template:

```twig
{# templates/bundles/SyliusCoreBundle/Email/orderConfirmation.html.twig #}

{% extends '@SyliusCore/Email/layout.html.twig' %}

{% block subject %}
    {% include '@SyliusCore/Email/Blocks/OrderConfirmation/_subject.html.twig' %}
{% endblock %}

{% block content %}
    {% include [
        'Email/OrderConfirmation/' ~ sylius.channel.code ~ '.html.twig',
        'Email/OrderConfirmation/_default.html.twig'
    ] %}
{% endblock %}
```

#### Example File Structure

```
templates/
├── bundles/
│   └── SyliusShopBundle/
│       └── Email/
│           └── orderConfirmation.html.twig
└── Email/
    └── OrderConfirmation/
        ├── TOY_STORE.html.twig
        ├── FASHION_WEB.html.twig
        └── _default.html.twig
```

#### Sample Channel Files

`_default.html.twig`

```twig
{# templates/Email/OrderConfirmation/_default.html.twig #}

Your order no. {{ order.number }} has been successfully placed.
```

**`TOY_STORE.html.twig`**

```twig
{# templates/Email/OrderConfirmation/TOY_STORE.html.twig #}

Thanks for buying one of our toys!

Your order with number {{ order.number }} is currently being processed.
```

**`FASHION_WEB.html.twig`**

```twig
{# templates/Email/OrderConfirmation/FASHION_WEB.html.twig #}

Your new style is on the way!

We’ve received your order no. {{ order.number }}.
```

{% hint style="success" %}
This structure allows you to extend or localize emails without changing the parent layout.
{% endhint %}

***

## 3. Understand the Default Layout

By default, the core Sylius `orderConfirmation.html.twig` email looks like this:

```twig
{# SyliusCoreBundle/Resources/views/Email/orderConfirmation.html.twig #}

{% extends '@SyliusCore/Email/layout.html.twig' %}

{% block subject %}
    {% include '@SyliusCore/Email/Blocks/OrderConfirmation/_subject.html.twig' %}
{% endblock %}

{% block content %}
    {% include '@SyliusCore/Email/Blocks/OrderConfirmation/_content.html.twig' %}
{% endblock %}
```

* `_subject.html.twig`: contains the translated subject line
* `_content.html.twig`: includes layout, order number, and optional link

{% hint style="success" %}
You can override any of these includes or the layout itself per channel as needed.
{% endhint %}

***

## :white\_check\_mark: Result

Each email adapts to the right channel automatically. Developers can manage templates independently, and fallback logic ensures robustness.

You’ve now implemented clean, scalable multi-channel email templates in Sylius!

<figure><img src="../.gitbook/assets/image (18).png" alt=""><figcaption><p>FASHION_WEB channel</p></figcaption></figure>

<figure><img src="../.gitbook/assets/image (19).png" alt=""><figcaption><p>TOY_STORE channel</p></figcaption></figure>
