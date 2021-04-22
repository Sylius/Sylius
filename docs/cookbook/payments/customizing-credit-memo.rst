How to customize a Credit Memo?
===============================

Customizing a downloadable credit memo is a really common task, which leverages the extendability of traditional Symfony applications.
This cookbook includes four exemplary customizations with a varying degree of difficulty and impact.

Customizing Credit Memo's template
----------------------------------

The first exemplary customization is to change background color of the heading of line items table.

**1.** Copy ``vendor/sylius/refund-plugin/src/Resources/views/Download/creditMemo.html.twig`` into ``templates/bundles/SyliusRefundPlugin/Download/creditMemo.html.twig``.

**2.** Change CSS styling included in the template:

    .. code-block:: html

        <style>
            /* ... */
            .credit-memo table tr.heading td { background: #0d71bb; border-bottom: 1px solid #ddd; font-weight: bold; }
            /* ... */
        </style>

Displaying additional Customer's data on Credit Memo
----------------------------------------------------

There might be some cases in which you want to get access to order or customer data.
You can access these through ``creditMemo.order`` or ``creditMemo.order.customer`` respectively.

**1.** Copy ``vendor/sylius/refund-plugin/src/Resources/views/Download/creditMemo.html.twig`` into ``templates/bundles/SyliusRefundPlugin/Download/creditMemo.html.twig``.

**2.** Customize buyer's data included in the template:

    .. code-block:: html

        <td>
            {{ 'sylius_refund.ui.buyer'|trans([], 'messages', creditMemo.localeCode) }}<br/>
            <strong>{{ from.fullName }} </strong><br/>
            <!-- ... -->
            {{ creditMemo.order.customer.phoneNumber }}<br/>
            <!-- ... -->
        </td>

Displaying additional line item data (such as gross unit price) on Credit Memo
------------------------------------------------------------------------------

By default, a credit memo does not include unit gross price in the line items table - however, it is provided within
line items data included with credit memo.

**1.** Copy ``vendor/sylius/refund-plugin/src/Resources/views/Download/creditMemo.html.twig`` into ``templates/bundles/SyliusRefundPlugin/Download/creditMemo.html.twig``.

**2.** Customize products table data by adding one column in the template:

    .. code-block:: html

        <tr class="heading">
            <!-- ... -->
            <td>{{ 'sylius_refund.ui.unit_net_price'|trans([], 'messages', creditMemo.localeCode) }}</td>
            <td>{{ 'app.ui.unit_gross_price'|trans([], 'messages', creditMemo.localeCode) }}</td>
            <td>{{ 'sylius_refund.ui.net_value'|trans([], 'messages', creditMemo.localeCode) }}</td>
            <!-- ... -->
        </tr>

        {% for item in creditMemo.lineItems %}
            <tr class="item">
                <!-- ... -->
                <td>{{ '%0.2f'|format(item.unitNetPrice/100) }}</td>
                <td>{{ '%0.2f'|format(item.unitGrossPrice/100) }}</td>
                <td>{{ '%0.2f'|format(item.netValue/100) }}</td>
                <!-- ... -->
            </tr>
        {% endfor %}

**3.** Add missing translations for newly added string in ``translations/messages.en.yml``:

    .. code-block:: yaml

        app:
            ui:
                unit_gross_price: Unit gross price

Displaying additional elements on Credit Memo
---------------------------------------------

There might be times when you want to calculate some extra data on-the-fly or get some which are not connected on
entity level with credit memo.

**1.** Copy ``vendor/sylius/refund-plugin/src/Resources/views/Download/creditMemo.html.twig`` into ``templates/bundles/SyliusRefundPlugin/Download/creditMemo.html.twig``.

**2.** Embed a controller in the credit memo template:

    .. code-block:: html

        <div class="credit-memo">
            Some unique data: {{ render(controller('App\\Controller\\FooController::extraData', { 'creditMemo': creditMemo })) }}

            <!-- ... -->
        </div>

**3.** Create the referenced controller in a file called ``src/Controller/FooController.php``:

    .. code-block:: php

        namespace App\Controller;

        use Sylius\RefundPlugin\Entity\CreditMemoInterface;
        use Symfony\Component\HttpFoundation\Response;
        use Twig\Environment;

        final class FooController
        {
            /** @var Environment */
            private $twig;

            public function __construct(Environment $twig)
            {
                $this->twig = $twig;
            }

            public function extraData(CreditMemoInterface $creditMemo): Response
            {
                return new Response($this->twig->render('CreditMemo/extraData.html.twig', [
                    'creditMemo' => $creditMemo,
                    // Customise it to your needs, this one makes no sense
                    'extraData' => $creditMemo->getNetValueTotal() * random_int(0, 42),
                ]));
            }
        }

**4.** Created the template referenced in the controller in a file called ``templates/CreditMemo/extraData.html.twig``:

    .. code-block:: html

        <strong>{{ extraData }}</strong>
