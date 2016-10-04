.. _basic_usage:

Basic Usage
===========

In all examples is used an exemplary class implementing **ShippableInterface**, which looks like:

.. code-block:: php

    <?php

    use Sylius\Component\Shipping\Model\ShippableInterface;

    class Wardrobe implements ShippableInterface
    {
        /**
         * @var int
         */
        private $weight;

        /**
         * @var int
         */
        private $volume;

        /**
         * @var int
         */
        private $width;

        /**
         * @var int
         */
        private $height;

        /**
         * @var int
         */
        private $width;

        /**
         * @var int
         */
        private $depth;

        /**
         * {@inheritdoc}
         */
        public function getShippingWeight()
        {
            return $this->weight;
        }

        /**
         * @param int $weight
         */
        public function setShippingWeight($weight)
        {
            $this->weight = $weight;
        }

        /**
         * {@inheritdoc}
         */
        public function getShippingVolume()
        {
            return $this->volume;
        }

        /**
         * @param int $volume
         */
        public function setShippingVolume($volume)
        {
            $this->volume = $volume;
        }

        /**
         * {@inheritdoc}
         */
        public function getShippingWidth()
        {
            // TODO: Implement getShippingWidth() method.
        }

        /**
         * {@inheritdoc}
         */
        public function getShippingHeight()
        {
            // TODO: Implement getShippingHeight() method.
        }

        /**
         * {@inheritdoc}
         */
        public function getShippingDepth()
        {
            // TODO: Implement getShippingDepth() method.
        }
    }

Shipping Method
---------------

Every shipping method has three identifiers, an ID code and name. You can access those by calling ``->getId()``, ``->gerCode()`` and ``->getName()``
methods respectively. The name is mutable, so you can change them by calling  ``->setName('FedEx')`` on the shipping method instance.

Setting Rule
~~~~~~~~~~~~

Every shipping method can have many rules, which define its additional requirements. If a **Shipment** does not fulfill
these requirements (e.g. a rule states that the expected quantity of shipment items should be 2, but the Shipment has
only one **ShippingItem**), then the **ShippingMethod** having this rule cannot be used on this **Shipment**.

.. code-block:: php

    <?php

    use Sylius\Component\Shipping\Model\Rule;
    use Sylius\Component\Shipping\Model\ShippingMethod;

    $shippingMethod = new ShippingMethod();
    $rule = new Rule();

    $shippingMethod->addRule($rule);
    $shippingMethod->hasRule($rule); // returns true
    $shippingMethod->getRules(); // collection of rules with count equals 1
    $shippingMethod->removeRule($rule);
    $shippingMethod->hasRule($rule); // returns false

Shipping Method Translation
---------------------------

**ShippingMethodTranslation** allows shipping method's name translation according to given locales. To see how to use translation
please go to :ref:`component_resource_translations_usage`.

Rule
----

A **Rule** defines additional requirements for a **ShippingMethod**, which have to be fulfilled by the **Shipment**,
if it has to be delivered in a way described by this **ShippingMethod**.

.. code-block:: php

    <?php

    use Sylius\Component\Shipping\Model\Rule;
    use Sylius\Component\Shipping\Model\ShippingMethod;
    use Sylius\Component\Shipping\Model\RuleInterface;

    $shippingMethod = new ShippingMethod();
    $rule = new Rule();
    $rule->setConfiguration(array('count' => 1, 'equal' => true));
    $rule->setType(RuleInterface::TYPE_ITEM_COUNT);
    $shippingMethod->addRule($rule);


Shipment Item
-------------

You can use a **ShippingItem** for connecting a shippable object with a proper **Shipment**.
Note that a **ShippingItem** can exist without a **Shipment** assigned.

.. code-block:: php

    <?php

    use Sylius\Component\Shipping\Model\Shipment;
    use Sylius\Component\Shipping\Model\ShipmentItem;
    use Sylius\Component\Shipping\Model\ShipmentInterface;

    $shipment = new Shipment();
    $wardrobe = new Wardrobe();
    $shipmentItem = new ShipmentItem();

    $shipmentItem->setShipment($shipment);
    $shipmentItem->getShipment(); // returns shipment object
    $shipmentItem->setShipment(null);

    $shipmentItem->setShippable($wardrobe);
    $shipmentItem->getShippable(); // returns shippable object

    $shipmentItem->getShippingState(); // returns const STATE_READY
    $shipmentItem->setShippingState(ShipmentInterface::STATE_SOLD);

Shipment
--------

Every **Shipment** can have the types of state defined in the **ShipmentInterface** and the **ShippingMethod**,
which describe the way of delivery.

.. code-block:: php

    <?php

    use Sylius\Component\Shipping\Model\ShippingMethod;
    use Sylius\Component\Shipping\Model\Shipment;
    use Sylius\Component\Shipping\Model\ShipmentInterface;

    $shippingMethod = new ShippingMethod();

    $shipment = new Shipment();
    $shipment->getState(); // returns const checkout
    $shipment->setState(ShipmentInterface::STATE_CANCELLED);

    $shipment->setMethod($shippingMethod);
    $shipment->getMethod();

Adding shipment item
~~~~~~~~~~~~~~~~~~~~

You can add many shipment items to shipment, which connect shipment with shippable object.

.. code-block:: php

    <?php

    use Sylius\Component\Shipping\Model\Shipment;
    use Sylius\Component\Shipping\Model\ShipmentItem;

    $shipmentItem = new ShipmentItem();
    $shipment = new Shipment();

    $shipment->addItem($shipmentItem);
    $shipment->hasItem($shipmentItem); // returns true
    $shipment->getItems(); // returns collection of shipment items
    $shipment->getShippingItemCount(); // returns 1
    $shipment->removeItem($shipmentItem);

Tracking shipment
~~~~~~~~~~~~~~~~~

You can also define tracking code for your shipment:

.. code-block:: php

    <?php

    use Sylius\Component\Shipping\Model\Shipment;

    $shipment->isTracked();// returns false
    $shipment->setTracking('5346172074');
    $shipment->getTracking(); // returns 5346172074
    $shipment->isTracked();// returns true

.. _component_shipping_checker-rule-checker-interface:

RuleCheckerInterface
--------------------

This example shows how use an exemplary class implementing **RuleCheckerInterface**.

.. code-block:: php

    <?php

    use Sylius\Component\Shipping\Model\Shipment;
    use Sylius\Component\Shipping\Model\ShipmentItem;
    use Sylius\Component\Shipping\Model\Rule;
    use Sylius\Component\Shipping\Checker\ItemCountRuleChecker;

    $rule = new Rule();
    $rule->setConfiguration(array('count' => 5, 'equal' => true));

    $wardrobe = new Wardrobe();

    $shipmentItem = new ShipmentItem();
    $shipmentItem->setShippable($wardrobe);

    $shipment = new Shipment();
    $shipment->addItem($shipmentItem);

    $ruleChecker = new ItemCountRuleChecker();
    $ruleChecker->isEligible($shipment, $rule->getConfiguration()); // returns false, because
    // quantity of shipping item in shipment is smaller than count from rule's configuration

.. hint::
    You can read more about each of the available checkers in the :doc:`checkers` chapter.

Delegating calculation to correct calculator instance
-----------------------------------------------------

**DelegatingCalculator** class delegates the calculation of charge for particular shipping subject to a correct calculator instance,
based on the name defined on the shipping method. It uses **ServiceRegistry** to keep all calculators registered inside
container. The calculators are retrieved by name.

.. code-block:: php

    <?php

    use Sylius\Component\Shipping\Model\ShippingMethod;
    use Sylius\Component\Shipping\Calculator\DefaultCalculators;
    use Sylius\Component\Shipping\Calculator\PerItemRateCalculator;
    use Sylius\Component\Shipping\Calculator\FlexibleRateCalculator;
    use Sylius\Component\Shipping\Model\Shipment;
    use Sylius\Component\Shipping\Model\ShipmentItem;
    use Sylius\Component\Shipping\Calculator\DelegatingCalculator;
    use Sylius\Component\Registry\ServiceRegistry;

    $configuration = array(
        'first_item_cost'       => 1000,
        'additional_item_cost'  => 200,
        'additional_item_limit' => 2
    );
    $shippingMethod = new ShippingMethod();
    $shippingMethod->setConfiguration($configuration);
    $shippingMethod->setCalculator(DefaultCalculators::FLEXIBLE_RATE);

    $shipmentItem = new ShipmentItem();

    $shipment = new Shipment();
    $shipment->setMethod($shippingMethod);
    $shipment->addItem($shipmentItem);

    $flexibleRateCalculator = new FlexibleRateCalculator();
    $perItemRateCalculator = new PerItemRateCalculator();

    $calculatorRegistry = new ServiceRegistry(CalculatorInterface::class);
    $calculatorRegistry->register(DefaultCalculators::FLEXIBLE_RATE, $flexibleRateCalculator);
    $calculatorRegistry->register(DefaultCalculators::PER_ITEM_RATE, $perItemRateCalculator);

    $delegatingCalculators = new DelegatingCalculator($calculatorRegistry);
    $delegatingCalculators->calculate($shipment); // returns 1000

    $configuration2 = array('amount' => 200);
    $shippingMethod2 = new ShippingMethod();
    $shippingMethod2->setConfiguration($configuration2);
    $shippingMethod2->setCalculator(DefaultCalculators::PER_ITEM_RATE);

    $shipment->setMethod($shippingMethod2);
    $delegatingCalculators->calculate($shipment); // returns 200

.. caution::
       The method ``->register()`` and  ``->get()`` used in ``->calculate`` throw `InvalidArgumentException`_.
       The method ``->calculate`` throws `UndefinedShippingMethodException`_ when given shipment does not have a shipping method defined.

.. hint::
    You can read more about each of the available calculators in the :doc:`calculators` chapter.

.. _InvalidArgumentException: http://php.net/manual/en/class.invalidargumentexception.php
.. _UndefinedShippingMethodException: http://api.sylius.org/Sylius/Component/Shipping/Calculator/UndefinedShippingMethodException.html
