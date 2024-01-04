How to authorize a payment before capturing.
============================================

Sometimes, due to legal constraint in some countries, you'll want to only authorize a payment and capture it later.

Authorizing payments
--------------------

Sylius supports the use of `Payums payment authorization <https://github.com/Payum/Payum/blob/master/docs/symfony/authorize.md>`_.
Not all payment gateways support this and it is up to the payment plugin to make use of this functionality.

To use authorize status for your payments, your plugin must set a flag in it's GatewayConfig called `use_authorize`. This is easily done with a hidden input field.

.. code-block:: php

    class MyGatewayGatewayConfigurationType extends AbstractType
    {
        /**
         * {@inheritdoc}
         */
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder
                // Add other config fields for your gateway here.

                // Enable the use of authorize. This can also be a normal select field if the gateway supports both.
                ->add('use_authorize', HiddenType::class, [
                    'data' => 1,
                ])
            ;
        }
    }

Capture payment after authorizing
---------------------------------

As an admin, you can mark the payment as captured from the order view page or through the Payments API.
Capturing the payment in the gateway is up to the plugin, which can hook into the state machine or events.

.. note::

    For an example of how this can be implemented see `the QuickPay gateway plugin <https://github.com/Setono/SyliusQuickpayPlugin>`_.
