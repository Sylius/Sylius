Custom business logic
=====================

Templates customization is just the beginning of the broad spectrum of customization possibilities in Sylius. There
are very few things in Sylius you're not able to customize or override. Let's take a look at one of the typical example of
customizing Sylius default logic, in this case, logic related to shipments and their cost. It's time for a custom shipping
calculator.

Custom shipping calculator
--------------------------

Each shipping calculator is able to calculate a shipping cost for the provided order. This calculation is usually based on
bought products and some configuration done by Administrator. By default Sylius provides ``FlatRateCalculator`` and
``PerUnitRateCalculator`` (their names are quite self-explaining), but it's sometimes not enough. So let's say your store packs
ordered products in parcels and you need to charge a customer for each of them.

You should start with the implementation of your custom shipping calculator service. Remember, that it must implement the
``CalculatorInterface`` from **Shipping Component**. Let's name it ``ParcelCalculator`` and place it in ``src/ShippingCalculator``
directory.

.. code-block:: php

    # src/ShippingCalculator/ParcelCalculator.php

    <?php

    declare(strict_types=1);

    namespace App\ShippingCalculator;

    use Sylius\Component\Shipping\Calculator\CalculatorInterface;
    use Sylius\Component\Shipping\Model\ShipmentInterface;

    final class ParcelCalculator implements CalculatorInterface
    {
        public function calculate(ShipmentInterface $subject, array $configuration): int
        {
            $parcelSize = $configuration['size'];
            $parcelPrice = $configuration['price'];

            $numberOfPackages = ceil($subject->getUnits()->count() / $parcelSize);

            return (int) ($numberOfPackages * $parcelPrice);
        }

        public function getType(): string
        {
            return 'parcel';
        }
    }

Two more things are needed to make it work. A form type, that would be used to pass some data to the ``$configuration`` array
in the calculator service, and a proper service registration in the ``services.yaml`` file.

.. code-block:: php

    # src/Form/Type/ParcelShippingCalculatorType.php

    <?php

    declare(strict_types=1);

    namespace App\Form\Type;

    use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\NumberType;
    use Symfony\Component\Form\FormBuilderInterface;

    final class ParcelShippingCalculatorType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder
                ->add('size', NumberType::class)
                ->add('price', MoneyType::class, [
                    'currency' => 'USD',
                ])
            ;
        }
    }

.. attention::

    The currency needed for ``MoneyType`` in the proposed implementation hardcoded just for testing reasons. In a real application,
    you should get the proper currency code from the repository, context or some configuration file.

.. code-block:: yaml

    # config/services.yml

    services:
        //...

        App\ShippingCalculator\ParcelCalculator:
            tags:
                -
                    {
                        name: sylius.shipping_calculator,
                        calculator: "parcel",
                        label: "Parcel",
                        form_type: App\Form\Type\ParcelShippingCalculatorType
                    }

That's it! You should now be able to select your shipping calculator during the creation or edition of a shipping method.

.. image:: /_images/getting-started-with-sylius/shipping-calculator.png
    :scale: 55%
    :align: center

|

You can also see the results of your customization on checkout shipping step, how the shipping fee changes depending on how
many products you have in the cart.

For 1 product:

.. image:: /_images/getting-started-with-sylius/shipping-cost-1.png
    :scale: 55%
    :align: center

|

For 4 products:

.. image:: /_images/getting-started-with-sylius/shipping-cost-2.png
    :scale: 55%
    :align: center

|

Amazing job! You've just provided your own logic into a Sylius-based system. Therefore, your store can provide a unique
experience for your Customers. Basing on this knowledge, you're ready to customize your shop even more and make it as suitable
to your business needs as possible.

Learn more
##########

* :doc:`Customizations </customization/index>`
* :doc:`Shipments </book/orders/shipments>`
* :doc:`Checkout </book/orders/checkout>`
* :doc:`Orders </book/orders/orders>`
* :doc:`Adjustments </book/orders/adjustments>`
