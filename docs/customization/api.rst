Customizing API
===============

We are using the API Platform to create all endpoints in Sylius API.
API Platform allows configuring an endpoint by ``yaml`` and ``xml`` files or by annotations.
In this guide, you will learn how to customize Sylius API endpoints using ``xml`` configuration.

How to prepare project for customization?
-----------------------------------------

If your project was created before v1.10, make sure your API Platform config follows the one below:

.. code-block:: yaml

    # config/packages/api_platform.yaml
    api_platform:
        mapping:
            paths:
                - '%kernel.project_dir%/vendor/sylius/sylius/src/Sylius/Bundle/ApiBundle/Resources/config/api_resources'
                - '%kernel.project_dir%/config/api_platform'
                - '%kernel.project_dir%/src/Entity'
        patch_formats:
            json: ['application/merge-patch+json']
        swagger:
            versions: [3]

And if you want to modify serialization add this code to framework config:

.. code-block:: yaml

    # config/packages/framework.yaml
    //...
    serializer:
        mapping:
            paths: [ '%kernel.project_dir%/config/serialization' ]

How to add an additional endpoint?
----------------------------------

Let's assume that you want to add a new endpoint to the ``Order`` resource that will be dispatching a command.
If you want to customize any API resource, you need to copy the entire configuration of this resource from
``%kernel.project_dir%/vendor/sylius/sylius/src/Sylius/Bundle/ApiBundle/Resources/config/api_resources/`` to ``%kernel.project_dir%/config/api_platform``.
Add the following configuration in the config copied to ``config/api_platform/Order.xml``:

.. code-block:: xml

    <collectionOperations>
        <collectionOperation name="custom_operation">
            <attribute name="method">POST</attribute>
            <attribute name="path">/shop/orders/custom-operation</attribute>
            <attribute name="messenger">input</attribute>
            <attribute name="input">App\Command\CustomCommand</attribute>
        </collectionOperation>
    </collectionOperations>

And that's all, now you have a new endpoint with your custom logic.

.. tip::

    Read more about API Platform endpoint configuration `here <https://api-platform.com/docs/core/operations/>`_

How to remove an endpoint?
--------------------------

Let's assume that your shop is offering only digital products. Therefore, while checking out,
your customers do not need to choose a shipping method for their orders.

Thus you will need to modify the configuration file of the ``Order`` resource and remove the shipping method choosing endpoint from it.
To remove the endpoint you only need to delete the unnecessary configuration from your ``config/api_platform/Order.xml`` which is a copied configuration file, that overwrites the one from Sylius.

.. code-block:: xml

    <!-- delete this configuration -->
    <itemOperation name="shop_select_shipping_method">
        <!-- ... -->
    </itemOperation>

How to rename an endpoint's path?
---------------------------------

If you want to change an endpoint's path, you just need to change the ``path`` attribute in your config:

.. code-block:: xml

    <itemOperations>
            <itemOperation name="admin_get">
                <attribute name="method">GET</attribute>
                <attribute name="path">/admin/orders/renamed-path/{id}</attribute>
            </itemOperation>
    </itemOperations>

How to modify the endpoints prefixes?
-------------------------------------

Let's assume that you want to have your own prefixes on paths (for example to be more consistent with the rest of your application).
As the first step you need to change the ``paths`` or ``route_prefix`` attribute in all needed resources.
The next step is to modify the security configuration in ``config/packages/security.yaml``, you need to overwrite the parameter:

.. code-block:: xml

    parameters:
        sylius.security.new_api_shop_route: "%sylius.security.new_api_route%/retail"

.. warning::

    Changing prefix without security configuration update can expose confidential data (like customers addresses).

After these two steps you can start to use endpoints with new prefixes

How to customize serialization?
-------------------------------

Let's say that you want to change the serialized fields in your responses.
For an example we will use ``Product`` resource and customize its fields.

Adding a field to response
~~~~~~~~~~~~~~~~~~~~~~~~~~

Let's say that you want to serialize existing field named ``averageRating`` to ``Product`` in admin response so the administrator would be able to check what is the average rating of product.

First let's copy serialization configuration file named ``Product.xml`` from ``%kernel.project_dir%/vendor/sylius/sylius/src/Sylius/Bundle/ApiBundle/Resources/config/serialization/``
to ``config/serialization/Product.xml``

Then let's find the attribute ``averageRating``:

.. code-block:: xml

    <!--...-->
    <attribute name="averageRating">
            <group>shop:product:read</group>
    </attribute>
    <!--...-->


and add serialization group that is used by endpoint we want to modify

.. tip::

    You can create your own serialization group for every endpoint or use the one out of the box. If you don't know the name of group for endpoint you want to modify,
    you can find it by searching for your class configuration file in `%kernel.project_dir%/vendor/sylius/sylius/src/Sylius/Bundle/ApiBundle/Resources/config/api_resources``
    and look for path that you want to modify.

In this case the new ``group`` is called ``admin:product:read``:

.. code-block:: xml

    <!--...-->
    <attribute name="averageRating">
            <group>admin:product:read</group>
            <group>shop:product:read</group>
    </attribute>
    <!--...-->

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

Let's say that you want to add a new field named ``additionalText`` to ``Product``.
First we need to create a new serializer that will support our ``Product`` resource, but in this case we have a ``ProductNormalizer`` provided from Sylius.
Unfortunately we cannot use more than one normalizer per resource, hence we will override existing one.

Let's than copy code of ProductNormalizer from ``vendor/sylius/sylius/src/Sylius/Bundle/ApiBundle/Serializer/ProductNormalizer.php`` :

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Serializer;

    use Sylius\Component\Core\Model\ProductInterface;
    use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
    use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
    use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
    use Webmozart\Assert\Assert;

    final class ProductNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
    {
        use NormalizerAwareTrait;

        private const ALREADY_CALLED = 'product_normalizer_already_called';

        public function normalize($object, $format = null, array $context = [])
        {
            Assert::isInstanceOf($object, ProductInterface::class);
            Assert::keyNotExists($context, self::ALREADY_CALLED);

            $context[self::ALREADY_CALLED] = true;

            $data = $this->normalizer->normalize($object, $format, $context);
            $variant = $this->defaultProductVariantResolver->getVariant($object);
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
        tags:
            - { name: 'serializer.normalizer', priority: 100 }

.. warning::

    As we can use only one Normalizer per resource we need to set priority higher then one from Sylius.
    Default value for Sylius Normalizers is typically 64, but if you want to be sure, check the values in ``src/Sylius/Bundle/ApiBundle/Resources/config/services/serializers.xml``

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
        "code": "product_code",
        "variants": [
            "/api/v2/shop/product-variants/product-variant-0",
        ],
        "additionalText": "my additional field with text",
        //...
    }

Removing a field from a response
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Let's say that for some reason you want to remove some field from serialization.
One of possible solution could be that you use serialization groups.
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
The easiest solution to remove the field is to create a new serialization group and use it for fields you want to have and declare this group in the endpoint.

First let's add the ``config/api_platform/Product.xml`` configuration file. See ``How to add an additional endpoint?`` for more information.
Then let's modify the endpoint. For this example i will use GET item in shop, but you can also create some custom endpoint:

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

Now we need to modify the file ``config/serialization/Product.xml`` and add this custom serialization group to fields we want to show:

.. code-block:: xml

    <!--...-->
    <attribute name="updatedAt">
            <group>admin:product:read</group>
    </attribute>
    <attribute name="translations">
        <group>admin:product:create</group>
        <group>admin:product:read</group>
        <group>admin:product:update</group>
        <group>shop:product:read</group>
    </attribute>
    <attribute name="mainTaxon">
        <group>admin:product:create</group>
        <group>admin:product:read</group>
        <group>admin:product:update</group>
        <group>shop:product:read</group>
        <group>shop:product:custom_read</group>
    </attribute>
    <!--...-->

.. note::

    In example the ``translations`` doesn't have the new group ``shop:product:custom_read`` so it won't be shown by that endpoint.
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

Renaming name of response fields is very simple. In this example
let's modify the ``optionValues`` name to ``options``, that's how response looks like now:

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

The simplest method to achieve this is to modify serialization configuration file.
We can use the file ``config/serialization/Product.xml`` from example above and find the attribute named ``optionValues``

.. code-block:: xml

    <!--...-->
    <attribute name="optionValues">
            <group>admin:product:read</group>
            <group>shop:product:read</group>
    </attribute>
    <!--...-->

And just add a ``serialized-name`` into attribute description with new name:

.. code-block:: xml

    <!--...-->
    <attribute name="optionValues" serialized-name="option">
            <group>admin:product:read</group>
            <group>shop:product:read</group>
    </attribute>
    <!--...-->

You can also achieve this by utilising serializer class.
In this example we will modify it, so the name of field would be changed. Just add some custom logic:

.. code-block:: php

    //...
    $data = $this->normalizer->normalize($object, $format, $context);

    $data['options'] = $data['optionValues']; // this will change the name of your field
    unset($data['optionValues']); // optionally you can also remove old `optionValues` field

    return $data;
    //...

And here we go, now your response should look like this:

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

.. tip::

    Read more about API Platform `serialization <https://api-platform.com/docs/core/serialization>`_
