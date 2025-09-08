# How to send a custom e-mail?

By default, **Sylius** only sends emails for essential flows like order confirmation. However, you can easily extend this by configuring custom logic and templates using the built-in `SyliusMailerBundle`.

This guide shows how to send an email to all administrators **when a product variant becomes out of stock**.

{% hint style="success" %}
📖 For broader usage of the `SyliusMailerBundle`, see the [Sylius Mailer Documentation](https://github.com/Sylius/SyliusMailerBundle/blob/v2.1.0/docs/index.md).
{% endhint %}

***

## 1. Create the Email Template

This file defines how your email will look. Sylius expects a `subject` block and a `content` block.

```twig
{# templates/email/out_of_stock.html.twig #}

{% extends '@SyliusCore/Email/layout.html.twig' %}

{% block subject %}
    One of your products is out of stock.
{% endblock %}

{% block content %}
    <div style="text-align: center; margin-bottom: 30px;">
        The variant
        <div style="margin: 10px 0;">
            <span style="border: 1px solid #eee; padding: 10px; color: #1abb9c; font-size: 28px;">
                {% if variant.name %}
                  {{ variant.name }}
                {% else %}
                  {{ variant.product.name }}
                {% endif %}
            </span>
        </div>
        is currently out of stock.
    </div>
{% endblock %}
```

{% hint style="success" %}
This template will be rendered dynamically with the `variant` variable passed from the email sender logic.
{% endhint %}

***

## 2. Register the Email in Mailer Configuration

This registers your custom email under the Sylius Mailer system.

```yaml
# config/packages/sylius_mailer.yaml

sylius_mailer:
    sender:
        name: Sylius Example Store
        address: no-reply@example.com
    emails:
        out_of_stock:
            subject: 'A product is out of stock!'
            template: 'email/out_of_stock.html.twig'
```

{% hint style="warning" %}
Make sure the `template` path is correct and the file exists.
{% endhint %}

***

## 3. Create a Custom Email Manager

This class orchestrates stock checking and email sending.

```php
<?php

// src/EmailManager/OutOfStockEmailManager.php

namespace App\EmailManager;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

readonly final class OutOfStockEmailManager
{
    public function __construct(
        private SenderInterface $emailSender,
        private AvailabilityCheckerInterface $availabilityChecker,
        private RepositoryInterface $adminUserRepository,
    ) {
    }

    public function sendOutOfStockEmail(OrderInterface $order): void
    {
        $admins = $this->adminUserRepository->findAll();
        if (empty($admins)) {
            return;
        }

        foreach ($order->getItems() as $item) {
            $variant = $item->getVariant();

            if (!$this->availabilityChecker->isStockSufficient($variant, 1)) {
                $this->notifyAdminsAboutOutOfStock($admins, $variant, $order);
            }
        }
    }

    private function notifyAdminsAboutOutOfStock(array $admins, ProductVariantInterface $variant, OrderInterface $order): void
    {
        foreach ($admins as $admin) {
            $this->emailSender->send(
                'out_of_stock',
                [$admin->getEmail()],
                [
                    'variant' => $variant,
                    'channel' => $order->getChannel(),
                    'localeCode' => $order->getLocaleCode(),
                ],
            );
        }
    }
}
```

***

## 4. Register the Service

Add this to your `config/services.yaml`:

```yaml
# config/services.yaml

services:
    App\EmailManager\OutOfStockEmailManager:
        arguments:
            - '@sylius.email_sender'
            - '@sylius.checker.inventory.availability'
            - '@sylius.repository.admin_user'
```

***

## 5. Create a callback for order\_payment

### Create the Event Listener

```php
<?php

// src/EventListener/Workflow/OrderPayment/SendEmailWithOutOfStockListener.php

declare(strict_types=1);

namespace App\EventListener\Workflow\OrderPayment;

use App\EmailManager\OutOfStockEmailManager;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Webmozart\Assert\Assert;

final class SendEmailWithOutOfStockListener
{
    public function __construct(private OutOfStockEmailManager $outOfStockEmailManager)
    {
    }

    public function __invoke(CompletedEvent $event): void
    {
        $order = $event->getSubject();
        Assert::isInstanceOf($order, OrderInterface::class);

        $this->outOfStockEmailManager->sendOutOfStockEmail($order);
    }
}
```

### Register the Listener

```yaml
# config/services.yaml

services:
    app.listener.workflow.order_payment.send_email_with_out_of_stock:
        class: App\EventListener\Workflow\OrderPayment\SendEmailWithOutOfStockListener
        tags:
            - { name: kernel.event_listener, event: workflow.sylius_order_payment.completed.pay, priority: 100 }
```

{% hint style="success" %}
This uses [Symfony Workflow's event system](https://symfony.com/doc/current/workflow.html#using-events) to hook into Sylius’ order state machine.

Learn more about callbacks [here](https://docs.sylius.com/the-customization-guide/customizing-state-machines#adding-workflow-callbacks)!
{% endhint %}

***

## ✅ Test the Results

#### 1. Create a product with tracked stock

* Go to the **Admin Panel** → **Catalog** → **Products** → **Create** .
* Create a simple product and reduce one of its variant stock levels to `3`.
* Ensure that the product is **tracked**, and the stock is managed (i.e., `onHand: 3`, `tracked: true`).

<figure><img src="../.gitbook/assets/image (17).png" alt=""><figcaption></figcaption></figure>

#### 2. Place an Order

* From the shop, place an order for this product, quantity 3.

<figure><img src="../.gitbook/assets/image (1) (3).png" alt=""><figcaption></figcaption></figure>

* Open the admin panel and complete the payment on the placed order.

<figure><img src="../.gitbook/assets/image (2) (3).png" alt=""><figcaption></figcaption></figure>

#### 3. Verify Email Sent

You can use one of these tools to check if the email was sent:

* **MailHog** (local): Quick to run via Docker, view at [localhost:8025](http://localhost:8025).
* **Mailtrap** (cloud): Great for shared environments. See [mailtrap.io](https://mailtrap.io).
* **Symfony Mailer Profiler**: In `dev` mode, emails appear under the **Mailer** tab in Symfony Profiler.

<figure><img src="../.gitbook/assets/image (3) (3).png" alt=""><figcaption></figcaption></figure>

{% hint style="success" %}
Learn more about configuring your mailer and recommended email testing tools in the [Symfony Mailer documentation](https://symfony.com/doc/current/mailer.html).&#x20;
{% endhint %}

{% hint style="warning" %}
Make sure `MAILER_DSN` is not `null://` in your `.env`
{% endhint %}
