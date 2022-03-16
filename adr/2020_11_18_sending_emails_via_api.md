# API - Sending emails

* Status: accepted
* Date: 2020-11-18

## Context and Problem Statement

To achieve 100% API coverage, we need to handle emails by API.

## Considered Options

### Using events

Using events allow us to trigger any handler once operation is completed. 

* Sylius\ApiBundle\Event\OrderCompleted

Once this event is triggered we handle it with ```src/Sylius/Bundle/ApiBundle/EventHandler/OrderCompletedHandler.php```

Then command ```src/Sylius/Bundle/ApiBundle/Command/SendOrderConfirmation.php ``` is dispatched, which triggers command handler (```src/Sylius/Bundle/ApiBundle/CommandHandler/SendOrderConfirmationHandler.php```)
 
SendOrderConfirmationHandler is sending email based on its destination (in this case OrderConfirmation)

```
$this->emailSender->send(
    Emails::ORDER_CONFIRMATION_RESENT,
    [$order->getCustomer()->getEmail()],
    [
        'order' => $order,
        'channel' => $order->getChannel(),
        'localeCode' => $order->getLocaleCode(),
    ]
);
```

* Good, because it's easy to integrate other logic after the initial command will succeed.
* Good, because it's easy to trigger the same operation several times.
* Good, because it's easier to handle mailing server downs gracefully.

* Bad, because adds a lot of abstraction, can be hard to understand.

### Direct email call

in ```src/Sylius/Bundle/ApiBundle/CommandHandler/Checkout/CompleteOrderHandler.php```
we create direct call to email

```php
$this->emailManager->sendConfirmationEmail($cart);
```

* Good, because its easy to understand.

* Bad, because it can generate a lot of problems in future as it should be sent asynchronously, email manager class should be changed to handle it somehow, hard to implement.

## Decision Outcome

Chosen option: "Using events", because it allows us to send email using events, commands and handlers. Thanks to this we can queue few messages in async transport.
