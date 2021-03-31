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

**2.** Customize credit memo template to include the reason:

    .. code-block:: html

        <div class="credit-memo">
            Reason: {{ creditMemo.reason }}

            <!-- ... -->
        </div>

**3.** Override the default credit memo model in ``src/Entity/Refund/CreditMemo.php``:

    .. code-block:: php

        namespace App\Entity\Refund;

        use Doctrine\ORM\Mapping as ORM;
        use Sylius\RefundPlugin\Entity\CreditMemo as BaseCreditMemo;

        /**
         * @ORM\Entity
         * @ORM\Table(name="sylius_refund_credit_memo")
         */
        class CreditMemo extends BaseCreditMemo
        {
            /**
             * @ORM\Column
             *
             * @var string|null
             */
            private $reason;

            public function getReason(): ?string
            {
                return $this->reason;
            }

            public function setReason(?string $reason): void
            {
                $this->reason = $reason;
            }
        }

**4.** Configure ResourceBundle to use overridden model in ``config/packages/sylius_refund.yaml``:

    .. code-block:: yaml

        sylius_resource:
            resources:
                sylius_refund.credit_memo:
                    classes:
                        model: App\Entity\Refund\CreditMemo

**5.** Decorate credit memo generator to set the reason while generating the invoice. Create a class in ``src/Refund/CreditMemoGenerator.php``:

    .. code-block:: php

        namespace App\Refund;

        use App\Entity\Refund\CreditMemo;
        use Sylius\Component\Core\Model\OrderInterface;
        use Sylius\RefundPlugin\Entity\CreditMemoInterface;
        use Sylius\RefundPlugin\Generator\CreditMemoGeneratorInterface;

        final class CreditMemoGenerator implements CreditMemoGeneratorInterface
        {
            /** @var CreditMemoGeneratorInterface */
            private $creditMemoGenerator;

            public function __construct(CreditMemoGeneratorInterface $creditMemoGenerator)
            {
                $this->creditMemoGenerator = $creditMemoGenerator;
            }

            public function generate(OrderInterface $order, int $total, array $units, array $shipments, string $comment): CreditMemoInterface
            {
                /** @var CreditMemo $creditMemo */
                $creditMemo = $this->creditMemoGenerator->generate($order, $total, $units, $shipments, $comment);
                $creditMemo->setReason('Charged too much');

                return $creditMemo;
            }
        }

**6.** And then configure Symfony's dependency injection to use that class in ``config/services.yaml``:

    .. code-block:: yaml

    services:
        # ...

        App\Refund\CreditMemoGenerator:
            decorates: 'Sylius\RefundPlugin\Generator\CreditMemoGenerator'
            arguments:
                - '@App\Refund\CreditMemoGenerator.inner'
