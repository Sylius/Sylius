How to customize the refund process?
====================================

The Refund Plugin provides a possibility to refund orders fully as well as partially.
With this plugin there comes a process where credit memos and refund payments are created.

In the vanilla plugin, we are creating the refund payment right after when the credit memo has been created.
But what about situations where requirements say to change this order?

We will find out about it in this tutorial.

Changing the order of the refund process.
-----------------------------------------

Let's say that you need to change the order of the refund process.
There is an easy way to customize it. You just need to override the priority in service declaration in the config file:

.. code-block:: yaml

    # config/services.yaml
    services:
        Sylius\RefundPlugin\ProcessManager\CreditMemoProcessManager:
            arguments: ['@sylius.command_bus']
            tags:
                - { name: sylius_refund.units_refunded.process_step, priority: 0 }

Or like this:

.. code-block:: yaml

    # config/services.yaml
    services:
        Sylius\RefundPlugin\ProcessManager\RefundPaymentProcessManager:
            arguments:
                - '@Sylius\RefundPlugin\StateResolver\OrderFullyRefundedStateResolverInterface'
                - '@Sylius\RefundPlugin\Provider\RelatedPaymentIdProviderInterface'
                - '@Sylius\RefundPlugin\Factory\RefundPaymentFactoryInterface'
                - '@doctrine.orm.default_entity_manager'
                - '@sylius.event_bus'
            tags:
                - { name: sylius_refund.units_refunded.process_step, priority: 200 }

The process will work according to the priority (descending).

.. tip::

    You can find the default config of the refund process in `%kernel.project_dir%/vendor/sylius/refund-plugin/src/Resources/config/services/event_bus.xml`
    on services tagged as `sylius_refund.units_refunded.process_step`

After one of these changes, the refund process will be shifted and the Credit Memo will be generated after the Refund Payment.

.. tip::

    You can learn more about the refund process `here <https://github.com/Sylius/RefundPlugin#post-refunding-process>`
