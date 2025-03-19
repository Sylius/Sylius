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

# Orders

The **Order** model in Sylius is a key component where many eCommerce concepts converge. An order can represent either an active shopping cart or a placed order.

An **Order** consists of a collection of **OrderItem** instances, each representing a product variant with a chosen quantity from your store.

Each order is:

* **Assigned to the channel** where it was created;
* **Associated with the language** the customer used while placing the order;
* **Created with the base currency** of the current channel by default.

## Creating an Order Programmatically

To create an order programmatically, you’ll need a factory:

```php
/** @var FactoryInterface $orderFactory */
$orderFactory = $this->container->get('sylius.factory.order');

/** @var OrderInterface $order */
$order = $orderFactory->createNew();
```

Next, you’ll need to assign a channel to the order. You can retrieve the channel from the context or the repository:

```php
/** @var ChannelInterface $channel */
$channel = $this->container->get('sylius.context.channel')->getChannel();

$order->setChannel($channel);
```

Then, set the locale and currency code:

```php
/** @var string $localeCode */
$localeCode = $this->container->get('sylius.context.locale')->getLocaleCode();

$order->setLocaleCode($localeCode);

$currencyCode = $this->container->get('sylius.context.currency')->getCurrencyCode();
$order->setCurrencyCode($currencyCode);
```

Additionally, the order should have a **Customer** assigned:

```php
/** @var CustomerInterface $customer */
$customer = $this->container->get('sylius.repository.customer')->findOneBy(['email' => 'shop@example.com']);

$order->setCustomer($customer);
```

### Adding Items to an Order

To add **OrderItems** to an order, first retrieve a **ProductVariant** from the repository:

```php
/** @var ProductVariantInterface $variant */
$variant = $this->container->get('sylius.repository.product_variant')->findOneBy([]);
```

Then, create a new **OrderItem**:

```php
/** @var OrderItemInterface $orderItem */
$orderItem = $this->container->get('sylius.factory.order_item')->createNew();
$orderItem->setVariant($variant);
```

Modify the quantity of the item using the **OrderItemQuantityModifier**:

```php
$this->container->get('sylius.order_item_quantity_modifier')->modify($orderItem, 3);
```

Add the item to the order:

```php
$order->addItem($orderItem);
```

Next, process the order using the **CompositeOrderProcessor** to recalculate everything:

```php
$this->container->get('sylius.order_processing.order_processor')->process($order);
```

{% hint style="warning" %}
By default, all **OrderProcessors** only work on orders in the **cart** state. If you need to process orders in different states, you will need to modify the `canBeProcessed` method of the **Order**.
{% endhint %}

This **CompositeOrderProcessor** is one of the most powerful concepts. It handles the whole order calculation logic and allows for really granular operations over the order. It is called multiple times in the checkout process, and internally it works like this:

<figure><img src="../../.gitbook/assets/sylius_order_processor.png" alt=""><figcaption></figcaption></figure>

Finally, save the order using the repository:

```php
/** @var OrderRepositoryInterface $orderRepository */
$orderRepository = $this->container->get('sylius.repository.order');

$orderRepository->add($order);
```

## Order State Machine

An order in Sylius has a state machine with the following states:

* `cart` – before checkout is completed, this is the initial state
* `new` – when checkout is completed, the cart becomes a new order
* `fulfilled` – when both payment and shipping are completed
* `cancelled` – when the order is canceled

<figure><img src="../../.gitbook/assets/sylius_order.png" alt=""><figcaption></figcaption></figure>

{% hint style="info" %}
The state machine of order is an obvious extension of the [state machine of checkout](checkout.md).
{% endhint %}

## Shipments of an Order

Each **Order** in Sylius can hold a collection of **Shipments**, with each shipment having its own shipping method and state machine. This allows you to split an order into multiple shipments, each with its shipping process (e.g., sending physical items via a courier and digital products via email).

To learn more about shipments, check the [Shipments Documentation](shipments.md).

### Order's Shipment State Machine

<figure><img src="../../.gitbook/assets/sylius_order_shipping.webp" alt=""><figcaption></figcaption></figure>

### Adding a Shipment to an Order

To add a shipment to an order, first create a shipment and assign a shipping method:

```php
/** @var ShipmentInterface $shipment */
$shipment = $this->container->get('sylius.factory.shipment')->createNew();
$shipment->setMethod($this->container->get('sylius.repository.shipping_method')->findOneBy(['code' => 'UPS']));
$order->addShipment($shipment);
```

Next, process the order using the **OrderProcessor** and save the changes:

```php
$this->container->get('sylius.order_processing.order_processor')->process($order);
$this->container->get('sylius.manager.order')->flush();
```

### Shipping Costs

Shipping costs are stored as **Adjustments**. When a shipment is added to the cart, the order processor assigns a shipping adjustment to the order that holds the cost.

### Shipping a Shipment Using State Machine Transition

To manually trigger shipping transitions, you can apply the following transitions:

```php
$stateMachineFactory = $this->container->get('sm.factory');

$stateMachine = $stateMachineFactory->get($order, OrderShippingTransitions::GRAPH);
$stateMachine->apply(OrderShippingTransitions::TRANSITION_REQUEST_SHIPPING);
$stateMachine->apply(OrderShippingTransitions::TRANSITION_SHIP);

$this->container->get('sylius.manager.order')->flush();
```

After these transitions, the `shippingState` of the order will be `shipped`.

## Payments of an Order

An **Order** in Sylius holds a collection of **Payments**, each with its payment method and state. This allows an order to be paid for using multiple methods, each tracked independently.

### Order's Payment State Machine

<figure><img src="../../.gitbook/assets/sylius_order_payment.png" alt=""><figcaption></figcaption></figure>

### Adding a Payment to an Order

To add a payment to an order, create a payment and assign a payment method:

```php
/** @var PaymentInterface $payment */
$payment = $this->container->get('sylius.factory.payment')->createNew();

$payment->setMethod($this->container->get('sylius.repository.payment_method')->findOneBy(['code' => 'offline']));
$payment->setCurrencyCode($currencyCode);

$order->addPayment($payment);
```

### Completing a Payment Using State Machine Transition

To complete the payment process, apply the necessary transitions:

```php
$stateMachineFactory = $this->container->get('sm.factory');

$stateMachine = $stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH);
$stateMachine->apply(OrderPaymentTransitions::TRANSITION_REQUEST_PAYMENT);
$stateMachine->apply(OrderPaymentTransitions::TRANSITION_PAY);

$this->container->get('sylius.manager.order')->flush();
```

If this is the only payment assigned to the order, the `paymentState` of the order will now be `paid`.

## <mark style="color:blue;">\[Plugin] Creating an Order via Admin Panel</mark>

To create orders from the Admin panel, you can use the [Sylius/AdminOrderCreationPlugin](https://github.com/Sylius/AdminOrderCreationPlugin). This plugin allows administrators to:

* Create orders for customers.
* Choose products, set custom prices, and select payment and shipping methods.
* Reorder previously placed orders.

## <mark style="color:blue;">\[Plugin] Customer Order Operations: Reorder & Cancellation</mark>

Using Sylius plugins, your customers can:

* **Cancel unpaid orders** in the "My Account" section with the [Customer Order Cancellation Plugin](https://github.com/Sylius/CustomerOrderCancellationPlugin).
* **Reorder previously placed orders** with the [Customer Reorder Plugin](https://github.com/Sylius/CustomerReorderPlugin).
