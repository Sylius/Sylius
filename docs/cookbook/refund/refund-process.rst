How to customize refund process?
================================

The Refund Plugin provides possibility to refund full orders as well as partially.
With this plugin there comes a process where credit memos and refund payments are created.

In vanilla Plugin we are creating refund payment right after when the credit memo has been created.
But what about situations where requirements ask as to change their places?

We will find out about it in this tutorial.

Changing order of refund process.
---------------------------------

Let's say that you need to change the order of refund process.
There is an easy way to customize it. You just need to override the priority in service declaration in config file:

.. code-block:: yaml

    # config/services.yaml
    Sylius\RefundPlugin\ProcessManager\CreditMemoProcessManager:
            tags:
                - { name: sylius_refund.units_refunded.process_step, priority: 0 }

Or like this:

.. code-block:: yaml

    # config/services.yaml
    Sylius\RefundPlugin\ProcessManager\RefundPaymentProcessManager:
            tags:
                - { name: sylius_refund.units_refunded.process_step, priority: 200 }

The process will work according to the priority (descending).

.. tip::

    You can find the default values of refund process in `%kernel.project_dir%/vendor/sylius/refund-plugin/src/Resources/config/services/event_bus.xml`
    on services tagged as `sylius_refund.units_refunded.process_step`

After one of this changes, the refund process will be shifted and the Credit Memo will be generated after Refund Payment.

.. tip::

    You can learn more about the refund process `here <https://github.com/Sylius/RefundPlugin#post-refunding-process>`
