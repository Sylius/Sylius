.. index::
    single: Refunds

.. rst-class:: plugin-feature

Refunds
=======

Return is a two-step (Returns & Refunds) process of a customer giving previously purchased item(s) back to the shop,
and in turn receiving a financial refund in the original payment method, exchange for another item etc..
Sylius is providing both steps of this process: one via a `free open-source plugin <https://github.com/Sylius/RefundPlugin>`_
(Refunds) and the other via the `Sylius Plus <https://sylius.com/plus/>`_ version (Returns).

How to refund money to a Customer in Sylius?
--------------------------------------------

Having the plugin installed you will notice a new button on admin Order show page, the "Refunds" button in the top-right corner.
On the refunds paged for this Order you are able to refund order items all at once or one by one fully or partially,
you can also return the shipping cost. The original payment method of the order is chosen, but it can be modified.

Credit Memos
~~~~~~~~~~~~

After creating a refund it is documented in the system with a Credit Memo document, which is an opposite to an invoice.
Credit Memos look like invoices, although not for incomes but for store's account charges. Credit Memos appear
as a separate section under Sales in the left admin menu.

Refund Payments
~~~~~~~~~~~~~~~

Alongside the Credit Memo document, a new refund Payment is created in response to a Refund. This is a convenient object,
with which you can automate the process of paying the refunds to the customer;s account.

Learn more
----------

* `Sylius/RefundPlugin <https://github.com/Sylius/RefundPlugin>`_
* :doc:`Returns </book/orders/returns>`
* :doc:`Other Sylius plugins </book/plugins/index>`
