Calculating taxes
=================


.. warning::

    When using the CoreBundle (i.e: full stack Sylius framework), the taxes are already calculated at each cart change.
    It is implemented by the ``TaxationProcessor`` class, which is called by the `OrderTaxationListener``.

In order to calculate tax amount for given taxable, we need to find out the applicable tax rate.
The tax rate resolver service is available under ``sylius.tax_rate_resolver`` id, while the delegating tax calculator is accessible via ``sylius.tax_calculator`` name.

Resolving rate and using calculator
-----------------------------------

.. code-block:: php

    <?php

    namespace Acme\ShopBundle\Taxation

    use Acme\ShopBundle\Entity\Order;
    use Sylius\Bundle\TaxationBundle\Calculator\CalculatorInterface;
    use Sylius\Bundle\TaxationBundle\Resolver\TaxRateResolverInterface;

    class TaxApplicator
    {
        private $calculator;
        private $taxRateResolver;

        public function __construct(
            CalculatorInterface $calculator,
            TaxRateResolverInterface $taxRateResolver,
        )
        {
            $this->calculator = $calculator;
            $this->taxRateResolver = $taxRateResolver;

        }

        public function applyTaxes(Order $order)
        {
            $tax = 0;

            foreach ($order->getItems() as $item) {
                $taxable = $item->getProduct();
                $rate = $this->taxRateResolver->resolve($taxable);

                if (null === $rate) {
                    continue; // Skip this item, there is no matching tax rate.
                }

                $tax += $this->calculator->calculate($item->getTotal(), $rate);
            }

            $order->setTaxTotal($tax); // Set the calculated taxes.
        }
    }
