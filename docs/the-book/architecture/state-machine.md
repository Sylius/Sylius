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

# State Machine

In Sylius, the default approach to managing frequent changes in the system is through the **Symfony Workflow,** which offers a highly flexible and well-organized solution. However, Sylius also provides the option to use the **Winzou State Machine** if preferred.&#x20;

Both options allow you to define a set of states stored on an entity and manage transitions between them. Additionally, each state machine can be configured with callbacks—events triggered during specific transitions—making it easy to customize how your system responds to changes.

### States

States of a state machine are defined as constants on the model of an entity that the state machine is controlling.

How to configure states? Let’s see the example from the **Checkout** state machine.

{% tabs %}
{% tab title="Symfony Workflow" %}
```yaml
# CoreBundle/Resources/config/app/workflow/sylius_order_checkout.yaml
framework:
    workflows:
        !php/const Sylius\Component\Core\OrderCheckoutTransitions::GRAPH:
            type: state_machine
            marking_store:
                type: method
                property: checkoutState
            supports:
                - Sylius\Component\Core\Model\OrderInterface
            initial_marking: !php/const Sylius\Component\Core\OrderCheckoutStates::STATE_CART
            places:
                - !php/const Sylius\Component\Core\OrderCheckoutStates::STATE_CART
                - !php/const Sylius\Component\Core\OrderCheckoutStates::STATE_ADDRESSED
                - !php/const Sylius\Component\Core\OrderCheckoutStates::STATE_SHIPPING_SELECTED
                - !php/const Sylius\Component\Core\OrderCheckoutStates::STATE_SHIPPING_SKIPPED
                - !php/const Sylius\Component\Core\OrderCheckoutStates::STATE_PAYMENT_SELECTED
                - !php/const Sylius\Component\Core\OrderCheckoutStates::STATE_PAYMENT_SKIPPED
                - !php/const Sylius\Component\Core\OrderCheckoutStates::STATE_COMPLETED
```
{% endtab %}

{% tab title="Winzou State Machine" %}
```yaml
# CoreBundle/Resources/config/app/state_machine/sylius_order_checkout.yml
winzou_state_machine:
    sylius_order_checkout:
        class: "%sylius.model.order.class%"
        property_path: checkoutState
        graph: sylius_order_checkout
        state_machine_class: "%sylius.state_machine.class%"
        states:
            cart: ~
            addressed: ~
            shipping_selected: ~
            shipping_skipped: ~
            payment_skipped: ~
            payment_selected: ~
            completed: ~
```
{% endtab %}
{% endtabs %}

### Transitions

On the graph it would be the connection between two states, defining that you can move from one state to another subsequently.

How to configure transitions? Let’s see the example of our **Checkout** state machine. Having states configured we can have a transition between the `cart` state to the `addressed` state.

{% tabs %}
{% tab title="Symfony Workflow" %}
```yaml
# CoreBundle/Resources/config/app/workflow/sylius_order_checkout.yaml
framework:
    workflows:
        !php/const Sylius\Component\Core\OrderCheckoutTransitions::GRAPH:            
            transitions:
                !php/const Sylius\Component\Core\OrderCheckoutTransitions::TRANSITION_ADDRESS:
                    from: 
                        - !php/const Sylius\Component\Core\OrderCheckoutStates::STATE_CART
                        - !php/const Sylius\Component\Core\OrderCheckoutStates::STATE_ADDRESSED
                        - !php/const Sylius\Component\Core\OrderCheckoutStates::STATE_SHIPPING_SELECTED
                        - !php/const Sylius\Component\Core\OrderCheckoutStates::STATE_SHIPPING_SKIPPED
                        - !php/const Sylius\Component\Core\OrderCheckoutStates::STATE_PAYMENT_SELECTED
                        - !php/const Sylius\Component\Core\OrderCheckoutStates::STATE_PAYMENT_SKIPPED
                    to: !php/const Sylius\Component\Core\OrderCheckoutStates::STATE_ADDRESSED
```
{% endtab %}

{% tab title="Winzou State Machine" %}
```yaml
# CoreBundle/Resources/config/app/state_machine/sylius_order_checkout.yml
winzou_state_machine:
    sylius_order_checkout:
        transitions:
            address:
                from: [cart, addressed, shipping_selected, shipping_skipped, payment_selected, payment_skipped]  # here you specify which state is the initial
                to: addressed    
```
{% endtab %}
{% endtabs %}

### Listeners / Callbacks

{% tabs %}
{% tab title="Symfony Workflow" %}
**Listeners** can be used to execute actions in reaction to a given transition in Symfony Workflow.

**How do you configure Listeners attached to events in Symfony Workflow?**&#x20;

You need to create a Listener that will be waiting for the chosen transition and will invoke the desired behaviors.\
\
Below you can see the `ProcessCartListner` configured for the `order_checkout` state machine's transitions.

```php
/** src/Sylius/Bundle/CoreBundle/EventListener/Workflow/OrderCheckout/ProcessCartListener.php **/
<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderCheckout;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Webmozart\Assert\Assert;

final class ProcessCartListener
{
    public function __construct(private OrderProcessorInterface $orderProcessor)
    {
    }

    public function __invoke(CompletedEvent $event): void
    {
        /** @var OrderInterface $order */
        $order = $event->getSubject();
        Assert::isInstanceOf($order, OrderInterface::class);

        $this->orderProcessor->process($order);
    }
}
[...]
```

```xml
<!-- src/Sylius/Bundle/CoreBundle/Resources/config/services/listeners/workflow/order_checkout.xml -->
<?xml version="1.0" encoding="UTF-8"?>

<container xmlns="http://symfony.com/schema/dic/services" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">
    <services>
        <defaults public="false" />

        <service id="Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderCheckout\ProcessCartListener">
            <argument type="service" id="sylius.order_processing.order_processor" />
            <tag name="kernel.event_listener" event="workflow.sylius_order_checkout.completed.address" priority="-200"/>
            <tag name="kernel.event_listener" event="workflow.sylius_order_checkout.completed.select_shipping"  priority="-200"/>
            <tag name="kernel.event_listener" event="workflow.sylius_order_checkout.completed.skip_shipping" priority="-200"/>
            <tag name="kernel.event_listener" event="workflow.sylius_order_checkout.completed.select_payment" priority="-200"/>
            <tag name="kernel.event_listener" event="workflow.sylius_order_checkout.completed.skip_payment" priority="-200"/>
        </service>
[...]
```
{% endtab %}

{% tab title="Winzou State Machine" %}
**Callbacks** in Winzou State Machine are used to execute some code before or after applying transitions. Winzou StateMachineBundle adds the ability to use Symfony services in the callbacks.

**How do you configure callbacks in Winzou State Machine?**&#x20;

Having a configured transition, you can attach a callback to it before or after. A callback is simply a method of a service you want to be executed.

Below you can see how the `sylius_process_cart` callback is configured on the `sylius_order_checkout` state machine.

```yaml
# CoreBundle/Resources/config/app/state_machine/sylius_order_checkout.yml
winzou_state_machine:
     sylius_order_checkout:
          callbacks:
               # callbacks may be called before or after specified transitions, in the checkout state machine we've got callbacks only after transitions
               after:
                    sylius_process_cart:
                        on: ["select_shipping", "address", "select_payment", "skip_shipping", "skip_payment"]
                        do: ["@sylius.order_processing.order_processor", "process"]
                        args: ["object"]
                        priority: -200
```
{% endtab %}
{% endtabs %}

### Configuration

In order to use a state machine, you have to define a graph beforehand. A graph is a definition of states, transitions, and optionally callbacks - all attached to an object from your domain. Multiple graphs may be attached to the same object.

In **Sylius** the best example of a state machine is the one from checkout. It has seven states available: `cart`, `addressed`, `shipping_selected`, `shipping_skipped`, `payment_skipped`, `payment_selected` and `completed` - which can be achieved by applying some transitions to the entity. For example, when selecting a shipping method during the shipping step of checkout we should apply the `select_shipping` transition, and after that the state would become `shipping_selected`.

### Learn more

* [Winzou StateMachine Bundle](https://github.com/winzou/StateMachineBundle)
* [Customization Guide: State machines](../../the-customization-guide/customizing-state-machines.md)
