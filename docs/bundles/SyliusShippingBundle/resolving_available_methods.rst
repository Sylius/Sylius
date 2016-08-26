Resolving available shipping methods
====================================

In many use cases, you want to decide which shipping methods are available for user.
Sylius has a dedicated service which serves this purpose.

ShippingMethodsResolver
-----------------------

This service also works with the ``ShippingSubjectInterface``. To get all shipping methods which support given subject, simply call the ``getSupportedMethods`` function.

.. code-block:: php

    public function myAction()
    {
        $resolver = $this->get('sylius.shipping_methods_resolver');
        $shipment = $this->get('sylius.repository.shipment')->find(5);

        foreach ($resolver->getSupportedMethods($shipment) as $method) {
            echo $method->getName();
        }
    }

You can also pass the criteria array to initially filter the shipping methods pool.

.. code-block:: php

    public function myAction()
    {
        $country = $this->getUser()->getCountry();
        $resolver = $this->get('sylius.shipping_methods_resolver');
        $shipment = $this->get('sylius.repository.shipment')->find(5);

        foreach ($resolver->getSupportedMethods($shipment, array('country' => $country)) as $method) {
            echo $method->getName();
        }
    }

In forms
--------

To display a select field with all the available methods for given subject, you can use the ``sylius_shipping_method_choice`` type.
It supports two special options, required ``subject`` and optional ``criteria``.

.. code-block:: php

    <?php

    class ShippingController extends Controller
    {
        public function selectMethodAction(Request $request)
        {
            $shipment = $this->get('sylius.repository.shipment')->find(5);

            $form = $this->get('form.factory')->create('sylius_shipping_method_choice', array('subject' => $shipment));
        }
    }

This form type internally calls the **ShippingMethodsResolver** service and creates a list of available methods.
