How to have the Credit Memos created after the Refund Payments?
===============================================================

.. note::

    This cookbook requires having the `Refund Plugin <https://github.com/Sylius/RefundPlugin>`_ installed in your application.

.. tip::

    Read about the features of Refund Plugin in the documentation :doc:`here </book/orders/refunds>`.


By default the refund payments are created right after the credit memos have been created.
Although one may need to change it due to business requirements.

Let's see how to achieve this!

Credit Memos created after the Refund Payments
----------------------------------------------

All you need to do is to override the priority in service declaration in the config file.
Give the `CreditMemoProcessManager`, which is responsible for the Credit Memo generation, the lowest possible priority (``0``).
The priorites of services are interpreted in the descending order, thus this change will make it run after the service responsible for
Refund Payments.

.. code-block:: yaml

    # config/services.yaml
    services:
        Sylius\RefundPlugin\ProcessManager\CreditMemoProcessManager:
            arguments: ['@sylius.command_bus']
            tags:
                - { name: sylius_refund.units_refunded.process_step, priority: 0 }

You can also achieve it the other way round, by giving the service responsible for Payments
- ``RefundPaymentProcessManager`` - the highest priority, let it be ``200``.

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

The process managers will work according to the new priorities (descending), and as a result, all Refund Payments will be created before their Credit Memos.

.. tip::

    You can find the default config of all the services run in the the refund process in
    ``%kernel.project_dir%/vendor/sylius/refund-plugin/src/Resources/config/services/event_bus.xml``
    tagged as ``sylius_refund.units_refunded.process_step``


Learn more
----------

* `The refund process - details <https://github.com/Sylius/RefundPlugin#post-refunding-process>`_
