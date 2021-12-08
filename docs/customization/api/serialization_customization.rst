How to customize serialization?
-------------------------------

Let's say that you want to change the serialized fields in your responses.
For an example we will use ``Product`` resource and customize its fields.

Adding a field to response
~~~~~~~~~~~~~~~~~~~~~~~~~~

Let's say that you want to serialize the existing field named ``averageRating`` to ``Product`` in the admin response
so the administrator would be able to check what is the average rating of product.

First let's create serialization configuration file named ``Product.xml`` in ``config/serialization/Product.xml``
and add serialization group that is used by endpoint we want to modify, in this case the new ``group`` is called ``admin:product:read``:

.. code-block:: xml

    <?xml version="1.0" ?>

    <serializer xmlns="http://symfony.com/schema/dic/serializer-mapping"
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                xsi:schemaLocation="http://symfony.com/schema/dic/serializer-mapping https://symfony.com/schema/dic/serializer-mapping/serializer-mapping-1.0.xsd"
    >
        <class name="Sylius\Component\Core\Model\Product">
            <attribute name="averageRating">
                <group>admin:product:read</group>
                <group>shop:product:read</group>
            </attribute>
        </class>
    </serializer>

.. tip::

    You can create your own serialization group for every endpoint or use the one out of the box.
    If you don't know the name of group for endpoint you want to modify, you can find it by searching
    for your class configuration file in `%kernel.project_dir%/vendor/sylius/sylius/src/Sylius/Bundle/ApiBundle/Resources/config/api_resources``
    and look for path that you want to modify.

.. tip::

    The serialization groups from Sylius look this way to reflect: ``user context``, ``resource name`` and ``type of operation``.

After this change your response should be extended with new field:

.. code-block:: javascript

    {
        //...
        "id": 123,
        "code": "product_code",
        "variants": [
            "/api/v2/shop/product-variants/product-variant-0",
        ],
        "averageRating": 3,
        //...
    }

.. tip::

    Read more about API Platform `serialization groups <https://api-platform.com/docs/core/serialization/#using-serialization-groups>`_


We were able to add a field that exists in ``Product`` class, but what if you want to extend it with custom fields?
Let's customize response now with your custom fields serialized in response.

Adding a custom field to response
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Let's say that you want to add a new field named ``additionalText`` to ``Customer``.
First we need to create a new serializer that will support this resource. Let's name it ``CustomerNormalizer``:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Serializer;

    use Sylius\Component\Core\Model\CustomerInterface;
    use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
    use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
    use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
    use Webmozart\Assert\Assert;

    final class CustomerNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
    {
        private NormalizerInterface $normalizer;

        public function __construct(NormalizerInterface $normalizer)
        {
            $this->normalizer = $normalizer;
        }

        private const ALREADY_CALLED = 'customer_normalizer_already_called';

        public function normalize($object, $format = null, array $context = [])
        {
            Assert::isInstanceOf($object, CustomerInterface::class);
            Assert::keyNotExists($context, self::ALREADY_CALLED);

            $context[self::ALREADY_CALLED] = true;

            $data = $this->normalizer->normalize($object, $format, $context);

            return $data;
        }

        public function supportsNormalization($data, $format = null, $context = []): bool
        {
            if (isset($context[self::ALREADY_CALLED])) {
                return false;
            }

            return $data instanceof CustomerInterface;
        }
    }

And now let's declare its service in config files:

.. code-block:: yaml

    # config/services.yaml
    App\Serializer\CustomerNormalizer:
        arguments:
            - '@api_platform.serializer.normalizer.item'
        tags:
            - { name: 'serializer.normalizer', priority: 100 }

Then we can add the new field:

.. code-block:: php

    //...
    $data = $this->normalizer->normalize($object, $format, $context);

    $data['additionalText'] = 'your custom text or logic that will be added to this field.';

    return $data;
    //...

Now your response should be extended with the new field:

.. code-block:: javascript

    {
        //...
        "id": 123,
        "email": "sylius@example.com",
        "firstName": "sylius",
        "additionalText": "my additional field with text",
        //...
    }

But let's consider another case where the Normalizer exists for a given Resource.
Here we will also add a new field named ``additionalText`` but this time to ``Product``.
First, we need to create a serializer that will support our ``Product`` resource but in this case, we have a ``ProductNormalizer`` provided by Sylius.
Unfortunately, we cannot use more than one normalizer per resource, hence we will override the existing one.

Let's then copy the code of ProductNormalizer from ``vendor/sylius/sylius/src/Sylius/Bundle/ApiBundle/Serializer/ProductNormalizer.php`` :

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Serializer;

    use Sylius\Component\Core\Model\ProductInterface;
    use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
    use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
    use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
    use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
    use Webmozart\Assert\Assert;

    final class ProductNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
    {
        private NormalizerInterface $normalizer;

        private ProductVariantResolverInterface $productVariantResolver;
    
        public function __construct(
            NormalizerInterface $normalizer,
            ProductVariantResolverInterface $productVariantResolver
        ) {
            $this->normalizer = $normalizer;
            $this->productVariantResolver = $productVariantResolver;
        }

        private const ALREADY_CALLED = 'product_normalizer_already_called';

        public function normalize($object, $format = null, array $context = [])
        {
            Assert::isInstanceOf($object, ProductInterface::class);
            Assert::keyNotExists($context, self::ALREADY_CALLED);

            $context[self::ALREADY_CALLED] = true;

            $data = $this->normalizer->normalize($object, $format, $context);
            $variant = $this->productVariantResolver->getVariant($object);
            $data['defaultVariant'] = $variant === null ? null : $this->iriConverter->getIriFromItem($variant);

            return $data;
        }

        public function supportsNormalization($data, $format = null, $context = []): bool
        {
            if (isset($context[self::ALREADY_CALLED])) {
                return false;
            }

            return $data instanceof ProductInterface;
        }
    }

And now let's declare its service in config files:

.. code-block:: yaml

    # config/services.yaml
    App\Serializer\ProductNormalizer:
        arguments:
            - '@api_platform.serializer.normalizer.item'
            - '@sylius.product_variant_resolver.default'
        tags:
            - { name: 'serializer.normalizer', priority: 100 }

.. warning::

    As we can use only one Normalizer per resource we need to set priority for it, higher then the priority of the Sylius one.
    You can find the priority value of the Sylius Normalizer in ``src/Sylius/Bundle/ApiBundle/Resources/config/services/serializers.xml``

Then we can add the new field:

.. code-block:: php

    //...
    $data = $this->normalizer->normalize($object, $format, $context);

    $data['additionalText'] = 'your custom text or logic that will be added to this field.';

    return $data;
    //...

And your response should be extended with the new field:

.. code-block:: javascript

    {
        //...
        "id": 123,
        "code": "product_code",
        "variants": [
            "/api/v2/shop/product-variants/product-variant-0",
        ],
        "additionalText": "my additional field with text",
        //...
    }

Removing a field from a response
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Let's say that for some reason you want to remove a field from serialization.
One possible solution could be that you use serialization groups.
Those will limit the fields from your resource, according to serialization groups that you will choose.

.. tip::

    Read more about API Platform `serialization groups <https://api-platform.com/docs/core/serialization/#using-serialization-groups>`_

Let's assume that ``Product`` resource returns such a response:

.. code-block:: javascript

    {
        //...
        "id": 123,
        "code": "product_code",
        "variants": [
            "/api/v2/shop/product-variants/product-variant-0",
        ],
        "translations": {
            "en_US": {
              "@id": "/api/v2/shop/product-translations/123",
              "@type": "ProductTranslation",
              "id": 123,
              "name": "product name",
              "slug": "product-name"
        }
    }

Then let's say you want to remove ``translations``.

Utilising serialization groups to remove fields might be quite tricky as Symfony combines all of the serialization files into one.
The easiest solution to remove the field is to create a new serialization group, use it for the fields you want to have, and declare this group in the endpoint.

First, let's add the ``config/api_platform/Product.xml`` configuration file. See :doc:`How to add and remove endpoint </customization/api/adding_and_removing_endpoints>` for more information.
Then let's modify the endpoint. For this example, we will use GET item in the shop, but you can also create some custom endpoint:

.. code-block:: xml

    <!--...-->
    <itemOperation name="shop_get">
        <attribute name="method">GET</attribute>
        <attribute name="path">/shop/products/{code}</attribute>
        <attribute name="openapi_context">
            <attribute name="summary">Use code to retrieve a product resource.</attribute>
        </attribute>
        <attribute name="normalization_context">
            <attribute name="groups">shop:product:read</attribute>
        </attribute>
    </itemOperation>
    <!--...-->

then let's change the serialization group in ``normalization_context`` attribute to `shop:product:custom_read`:

.. code-block:: xml

    <!--...-->
    <attribute name="normalization_context">
        <attribute name="groups">shop:product:custom_read</attribute>
    </attribute>
    <!--...-->

Now we can define all the fields we want to expose in the ``config/serialization/Product.xml``:

.. code-block:: xml

    <!--...-->
    <attribute name="updatedAt">
        <group>shop:product:custom_read</group>
    </attribute>
    <!-- here `translation` attribute would be declared -->
    <attribute name="mainTaxon">
        <group>shop:product:custom_read</group>
    </attribute>
    <!--...-->

.. note::

    In xml example the ``translations`` is not declared with ``<group>shop:product:custom_read</group>`` group, so endpoint won't return this value.
    The rest of the fields that we want to show have the new serialization group declared.

In cases, where you would like to remove small amount of fields, the serializer would be a way to go.
First step is to create a class as in ``Adding a custom field to response`` and register its service.

Then modify it's logic with this code:

.. code-block:: php

    //...
    $data = $this->normalizer->normalize($object, $format, $context);

    unset($data['translations']); // removes `translations` from response

    return $data;
    //...

Now your response fields should look like this:

.. code-block:: javascript

    {
        //...
        "id": 123,
        "code": "product_code",
        "variants": [
            "/api/v2/shop/product-variants/product-variant-0",
        ],
        // the translations which were here are now removed
    }

Renaming a field of a response
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Changing the name of response fields is very simple. In this example
let's modify the ``options`` name to ``optionValues``, that's how response looks like now:

.. code-block:: javascript

    {
        //...
        "id": 123,
        "code": "product_code",
        "product": "/api/v2/shop/products/product_code",
        "options": [
            "/api/v2/shop/product-option-values/product_size_s"
        ],
        //...
    }

The simplest method to achieve this is to modify the serialization configuration file that we've already created.
Let's add to the ``config/serialization/Product.xml`` file config for ``options`` with a ``serialized-name`` attribute description:

.. code-block:: xml

    <!--...-->
    <attribute name="options">
        <group>admin:product:read</group>
        <group>shop:product:read</group>
    </attribute>
    <!--...-->

And just add a ``serialized-name`` into the attribute description with a new name:

.. code-block:: xml

    <!--...-->
    <attribute name="options" serialized-name="optionValues">
        <group>admin:product:read</group>
        <group>shop:product:read</group>
    </attribute>
    <!--...-->

You can also achieve this by utilising serializer class.
In this example we will modify it, so the name of field would be changed. Just add some custom logic:

.. code-block:: php

    //...
    $data = $this->normalizer->normalize($object, $format, $context);

    $data['optionValues'] = $data['options']; // this will change the name of your field
    unset($data['options']); // optionally you can also remove old `options` field

    return $data;
    //...

And here we go, now your response should look like this:

.. code-block:: javascript

    {
        //...
        "id": 123,
        "code": "product_code",
        "product": "/api/v2/shop/products/product_code",
        "optionValues": [
            "/api/v2/shop/product-option-values/product_size_s"
        ],
        //...
    }
