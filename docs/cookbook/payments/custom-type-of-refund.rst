How to add another type of refund?
==================================

.. note::

    This cookbook describes customization of a feature available only with `Sylius/RefundPlugin <https://github.com/Sylius/RefundPlugin/>`_ installed.

Why would you add type of refund?
---------------------------------

Refund Plugin provides a generic solution for refunding orders, it is enough for a basic refund but many shops need more custom functionalities.
For example, one may need to add loyalty points as a different refund type than order item unit and shipment.

How to implement a new type of refund?
--------------------------------------

In the current implementation, there are 2 basic types that are defined in RefundPlugin:

    * order item unit
    * shipment

If you would like to add another one, e.g. ``loyalty``, which might be used then to refund the loyalty points.
You need to first add it to the RefundType enum.

**1. Add the new type to the RefundType and RefundTypeInterface:**

Extended RefundTypeInterface should look like this:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Model\Refund;

    interface RefundTypeInterface
    {
        public const LOYALTY = 'loyalty';

        public static function loyalty(): self;
    }

And extended RefundType should look like:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Model\Refund;

    use Sylius\RefundPlugin\Model\RefundType as BaseRefundType;

    final class RefundType extends BaseRefundType implements RefundTypeInterface
    {
        public static function loyalty(): self
        {
            return new self(self::LOYALTY);
        }
    }

You need also to set the parameter with new RefundType in your configuration file:

.. code-block:: yaml

    # config/packages/sylius_refund.yaml
    parameters:
        sylius_refund.refund_type: App\Model\Refund\RefundType

**2. Overwrite RefundEnumType to use your extended RefundType:**

Extended RefundEnumType should look like this:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Entity\Refund\Type;

    use App\Model\Refund\RefundType;
    use Sylius\RefundPlugin\Entity\Type\RefundEnumType as BaseRefundEnumType;
    use Sylius\RefundPlugin\Model\RefundTypeInterface;

    final class RefundEnumType extends BaseRefundEnumType
    {
        protected function createType($value): RefundTypeInterface
        {
            return new RefundType($value);
        }
    }

And set the parameter with new RefundEnumType in your configuration file:

.. code-block:: yaml

    # config/packages/sylius_refund.yaml
    parameters:
        sylius_refund.refund_enum_type: App\Entity\Refund\Type\RefundEnumType

**3. Modify the refund flow:**

Once we have the new type of refund added, we will need to use it and display its input on the refund form.
You can achieve this by using :doc:`Cookbook  - How to customize the refund form? </cookbook/payments/custom-field-on-refund-payment>`
and add in handler your custom logic for refunding e.g. loyalty points.
