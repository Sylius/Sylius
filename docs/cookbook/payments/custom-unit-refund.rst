How to add another implementation of UnitRefundInterface?
=========================================================

.. note::

    This cookbook describes customization of a feature available only with `Sylius/RefundPlugin <https://github.com/Sylius/RefundPlugin/>`_ installed.

Why would you add a new unit?
-----------------------------

In the current implementation, there are 2 basic unit refunds in RefundPlugin:

    * OrderItemUnitRefund
    * ShipmentRefund

But what if you want to refund something else, or refund the whole item instead of the item unit? Hopefully, you'll do it
quite easily by creating a new implementation of `UnitRefundInterface` and a few tagged services that will make use of
it.

How to implement a new unit refund?
-----------------------------------

Let's say that you don't operate on item units, but you want to refund entire items. You will need to create a new
refund model that will implement `UnitRefundInterface`. To do so, a few things need to be done. Besides creating
the `OrderItemRefund`, we will also need to create :doc:`a new refund type <custom-type-of-refund>` and make everything work
together.

**1. Create a new refund type:**

This has been already described in the (How to add another type of refund?)[custom-type-of-refund] cookbook.
The code for the new refund type is as follows:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Entity\Refund;

    class RefundType extends \Sylius\RefundPlugin\Model\RefundType
    {
        public const ORDER_ITEM = 'order_item';

        public static function orderItem(): self
        {
            return new self(self::ORDER_ITEM);
        }
    }

Then the `RefundEnumType` needs to base on our new `RefundType`:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Entity\Refund\Type;

    use App\Entity\Refund\RefundType;
    use Sylius\RefundPlugin\Model\RefundTypeInterface;

    final class RefundEnumType extends \Sylius\RefundPlugin\Entity\Type\RefundEnumType
    {
        protected function createType($value): RefundTypeInterface
        {
            return new RefundType($value);
        }
    }

To make it work, we change the value of two parameters to the following:

.. code-block:: yaml

    parameters:
        sylius_refund.refund_enum_type: App\Entity\Refund\Type\RefundEnumType
        sylius_refund.refund_type: App\Entity\Refund\RefundType

**2. Add a new unit refund:**

Now, having the new refund type, we can create a new refund model implementing `UnitRefundInterface`:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Entity\Refund;

    use Sylius\RefundPlugin\Model\UnitRefundInterface;

    final class OrderItemRefund implements UnitRefundInterface
    {
        public function __construct(private int $itemId, private int $total)
        {
        }

        public function id(): int
        {
            return $this->itemId;
        }

        public function total(): int
        {
            return $this->total;
        }

        public static function type(): RefundType
        {
            return RefundType::orderItem();
        }
    }

**3. Disable OrderItemUnitRefund:**

As our new behavior bases on the order items, we need to disable the current behavior that is based on the order item
units. We can achieve that by disabling the converters and the refunder by removing tags from the services:

.. code-block:: yaml

    Sylius\RefundPlugin\Converter\OrderItemUnitLineItemsConverter:
        tags: []

    Sylius\RefundPlugin\Converter\RequestToOrderItemUnitRefundConverter:
        tags: []

    Sylius\RefundPlugin\Refunder\OrderItemUnitsRefunder:
        tags: []

**4. Create the OrderItemTotalProvider:**

RefundPlugin doesn't know anything about order items, so we need to tell them how to retrieve the total of the order.

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Provider;

    use Sylius\Component\Core\Model\OrderItemInterface;
    use Sylius\Component\Resource\Repository\RepositoryInterface;
    use Sylius\RefundPlugin\Provider\RefundUnitTotalProviderInterface;
    use Webmozart\Assert\Assert;

    final class OrderItemTotalProvider implements RefundUnitTotalProviderInterface
    {
        public function __construct(private RepositoryInterface $orderItemRepository)
        {
        }

        public function getRefundUnitTotal(int $id): int
        {
            /** @var OrderItemInterface $orderItem */
            $orderItem = $this->orderItemRepository->find($id);
            Assert::notNull($orderItem);

            return $orderItem->getTotal();
        }
    }

As you can see, it just gets the order item and returns its total. Now a piece of configuration:

.. code-block:: yaml

    App\Provider\OrderItemTotalProvider:
        arguments:
            - '@sylius.repository.order_item'
        tags: [{ name: 'sylius_refund.refund_unit_total_provider', refund_type: 'order_item' }]

**5. Create the ItemRefunded event with a listener:**

As we are refunding the order items, we need to update the payment state of the order.
The event itself will be dispatched by the `OrderItemsRefunder` later.

The `ItemRefunded` event is as follows:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Event;

    class ItemRefunded
    {
        public function __construct(private string $orderNumber)
        {
        }

        public function orderNumber(): string
        {
            return $this->orderNumber;
        }
    }

the listener:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Listener;

    use App\Event\ItemRefunded;
    use Sylius\RefundPlugin\StateResolver\OrderPartiallyRefundedStateResolverInterface;

    final class ItemRefundedEventListener
    {
        public function __construct(private OrderPartiallyRefundedStateResolverInterface $orderPartiallyRefundedStateResolver)
        {
        }

        public function __invoke(ItemRefunded $itemRefunded): void
        {
            $this->orderPartiallyRefundedStateResolver->resolve($itemRefunded->orderNumber());
        }
    }

and the configuration:

.. code-block:: yaml

    App\Listener\ItemRefundedEventListener:
        arguments:
            - '@Sylius\RefundPlugin\StateResolver\OrderPartiallyRefundedStateResolverInterface'
        tags: [{ name: 'messenger.message_handler', bus: 'sylius.event_bus' }]

**6. Create the OrderItemsRefunder:**

Refunder will make use of previously created event by dispatching it at the end of refunding process. The refunding
process basically processes the `OrderItemRefund` objects one by one to create a refund for each of them.

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Refunder;

    use App\Entity\Refund\OrderItemRefund;
    use App\Event\ItemRefunded;
    use Sylius\RefundPlugin\Creator\RefundCreatorInterface;
    use Sylius\RefundPlugin\Filter\UnitRefundFilterInterface;
    use Sylius\RefundPlugin\Model\UnitRefundInterface;
    use Sylius\RefundPlugin\Refunder\RefunderInterface;
    use Symfony\Component\Messenger\MessageBusInterface;

    final class OrderItemsRefunder implements RefunderInterface
    {
        public function __construct(
            private RefundCreatorInterface $refundCreator,
            private MessageBusInterface $eventBus,
            private UnitRefundFilterInterface $unitRefundFilter,
        ) {
        }

        public function refundFromOrder(array $units, string $orderNumber): int
        {
            $units = $this->unitRefundFilter->filterUnitRefunds($units, OrderItemRefund::class);
            $refundedTotal = 0;

            /** @var UnitRefundInterface $unit */
            foreach ($units as $unit) {
                $this->refundCreator->__invoke(
                    $orderNumber,
                    $unit->id(),
                    $unit->total(),
                    $unit->type()
                );

                $refundedTotal += $unit->total();
            }

            $this->eventBus->dispatch(new ItemRefunded($orderNumber));

            return $refundedTotal;
        }
    }

Now add a tag to the service:

.. code-block:: yaml

    App\Refunder\OrderItemsRefunder:
        arguments:
            - '@Sylius\RefundPlugin\Creator\RefundCreatorInterface'
            - '@sylius.event_bus'
            - '@Sylius\RefundPlugin\Filter\UnitRefundFilterInterface'
        tags: ['sylius_refund.refunder']

**7. Create the OrderItemLineItemsConverter:**

RefundPlugin generates a credit memo based on a refund that was made. However, as it's handled under the hood by processing
line items, we need to provide a converter that will convert the `OrderItemRefund` objects to the `LineItem` objects.

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Converter;

    use App\Entity\Refund\OrderItemRefund;
    use Sylius\Component\Core\Model\OrderItemInterface;
    use Sylius\Component\Resource\Repository\RepositoryInterface;
    use Sylius\RefundPlugin\Converter\LineItemsConverterUnitRefundAwareInterface;
    use Sylius\RefundPlugin\Entity\LineItem;
    use Sylius\RefundPlugin\Entity\LineItemInterface;
    use Sylius\RefundPlugin\Provider\TaxRateProviderInterface;
    use Webmozart\Assert\Assert;

    final class OrderItemLineItemsConverter implements LineItemsConverterUnitRefundAwareInterface
    {
        public function __construct(
            private RepositoryInterface $orderItemRepository,
            private TaxRateProviderInterface $taxRateProvider
        ) {
        }

        public function convert(array $units): array
        {
            Assert::allIsInstanceOf($units, $this->getUnitRefundClass());

            $lineItems = [];

            /** @var OrderItemRefund $unit */
            foreach ($units as $unit) {
                $lineItems = $this->addLineItem($this->convertUnitRefundToLineItem($unit), $lineItems);
            }

            return $lineItems;
        }

        public function getUnitRefundClass(): string
        {
            return OrderItemRefund::class;
        }

        private function convertUnitRefundToLineItem(OrderItemRefund $unitRefund): LineItemInterface
        {
            /** @var OrderItemInterface|null $orderItem */
            $orderItem = $this->orderItemRepository->find($unitRefund->id());
            Assert::notNull($orderItem);
            Assert::lessThanEq($unitRefund->total(), $orderItem->getTotal());

            $grossValue = $unitRefund->total();
            $taxAmount = (int) ($grossValue * $orderItem->getTaxTotal() / $orderItem->getTotal());
            $netValue = $grossValue - $taxAmount;

            /** @var string|null $productName */
            $productName = $orderItem->getProductName();
            Assert::notNull($productName);

            return new LineItem(
                $productName,
                1,
                $netValue,
                $grossValue,
                $netValue,
                $grossValue,
                $taxAmount,
                $this->taxRateProvider->provide($orderItem)
            );
        }

        /**
         * @param LineItemInterface[] $lineItems
         *
         * @return LineItemInterface[]
         */
        private function addLineItem(LineItemInterface $newLineItem, array $lineItems): array
        {
            foreach ($lineItems as $lineItem) {
                if ($lineItem->compare($newLineItem)) {
                    $lineItem->merge($newLineItem);

                    return $lineItems;
                }
            }

            $lineItems[] = $newLineItem;

            return $lineItems;
        }
    }

and the configuration:

.. code-block:: yaml

    App\Converter\OrderItemLineItemsConverter:
        arguments:
            - '@sylius.repository.order_item'
            - '@Sylius\RefundPlugin\Provider\TaxRateProviderInterface'
        tags: ['sylius_refund.line_item_converter']

**8. Create the RequestToOrderItemRefundConverter:**

Similar to the previous step, we need to provide a converter that will convert the request to the `OrderItemRefund`
objects.

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Converter;

    use App\Entity\Refund\OrderItemRefund;
    use Sylius\RefundPlugin\Converter\RefundUnitsConverterInterface;
    use Sylius\RefundPlugin\Converter\RequestToRefundUnitsConverterInterface;
    use Symfony\Component\HttpFoundation\Request;

    final class RequestToOrderItemRefundConverter implements RequestToRefundUnitsConverterInterface
    {
        public function __construct(private RefundUnitsConverterInterface $refundUnitsConverter)
        {
        }

        /**
         * @return OrderItemRefund[]
         */
        public function convert(Request $request): array
        {
            return $this->refundUnitsConverter->convert(
                $request->request->all()['sylius_refund_items'] ?? [],
                OrderItemRefund::class
            );
        }
    }

and the configuration:

.. code-block:: yaml

    App\Converter\RequestToOrderItemRefundConverter:
        arguments:
            - '@Sylius\RefundPlugin\Converter\RefundUnitsConverterInterface'
        tags: ['sylius_refund.request_to_refund_units_converter']

It's almost done! If you want to be able to refund the order items in the admin panel, one more step is needed.

**9. Adjust the order refund form to the current state:**

Under the `templates/Admin/OrderRefund` directory, create the `_items.html.twig` file. The template could look like this:

.. code-block:: html+twig

    {% import '@SyliusAdmin/Common/Macro/money.html.twig' as money %}

    {% for item in order.items %}
        {% set variant = item.variant %}
        {% set product = variant.product %}
        <tr class="unit">
            <td class="single line">
                {% include '@SyliusAdmin/Product/_info.html.twig' %}
            </td>
            <td class="right aligned total">
                <span class="unit-total">{{ money.format(item.total, order.currencyCode) }}</span>
                {% set refundedTotal = unit_refunded_total(item.id, constant('App\\Entity\\Refund\\RefundType::ORDER_ITEM')) %}
                {% if refundedTotal != 0 %}
                    <br/>
                    <strong>{{ 'sylius_refund.ui.refunded'|trans }}:</strong>
                    <span class="unit-refunded-total">{{ money.format(refundedTotal, order.currencyCode) }}</span>
                {% endif %}
            </td>
            <td class="aligned collapsing partial-refund">
                {% set inputName = "sylius_refund_items["~item.id~"][amount]" %}
                {% set hiddenInputName = "sylius_refund_items["~item.id~"][partial-id]" %}

                <div class="ui labeled input">
                    <div class="ui label">{{ order.currencyCode|sylius_currency_symbol }}</div>
                    <input data-refund-input type="number" step="0.01" name="{{ inputName }}" {% if not can_unit_be_refunded(item.id, constant('App\\Entity\\Refund\\RefundType::ORDER_ITEM')) %} disabled{% endif %}/>
                </div>
            </td>
            <td class="aligned collapsing">
                <button data-refund="{{ unit_refund_left(item.id, constant('App\\Entity\\Refund\\RefundType::ORDER_ITEM'), item.total) }}" type="button" class="ui button primary" {% if not can_unit_be_refunded(item.id, constant('App\\Entity\\Refund\\RefundType::ORDER_ITEM')) %}disabled{% endif %}>
                    {{ 'sylius_refund.ui.refund'|trans }}
                </button>
            </td>
        </tr>
    {% endfor %}

The template above will be used in `sylius_refund.admin.order.refund.form.table.body` template event as e.g.
`custom_items` block. Remember to disable `items` block, which handles order item units by the occasion.

.. code-block:: yaml

    sylius_ui:
        events:
            sylius_refund.admin.order.refund.form.table.body:
                blocks:
                    items: false
                    custom_items:
                        template: "Admin/OrderRefund/_items.html.twig"
                        priority: 10

Great! Now you can refund the order items instead order item units in the admin panel.
