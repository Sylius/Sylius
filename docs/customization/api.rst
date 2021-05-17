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
===============================

Let's assume that you want to modify responses with your custom fields serialized in response.
For an example we will use ``Product`` resource and customize its fields.

Adding a field to response
==========================

Let's say that you want to add a new field named ``additionalText`` to ``Product``.
First let's create a new serializer that will supports our ``Product`` resource.

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace App\Serializer;

    use Sylius\Component\Core\Model\ProductInterface;
    use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
    use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
    use Webmozart\Assert\Assert;

    final class ProductSerializer implements ContextAwareNormalizerInterface
    {
        /** @var NormalizerInterface */
        private $objectNormalizer;

        public function __construct(NormalizerInterface $objectNormalizer) {
            $this->objectNormalizer = $objectNormalizer;
        }

        public function normalize($object, $format = null, array $context = [])
        {
            Assert::isInstanceOf($object, ProductInterface::class);

            $data = $this->objectNormalizer->normalize($object, $format, $context);

            return $data;
        }

        public function supportsNormalization($data, $format = null, $context = []): bool
        {
            return $data instanceof ProductInterface;
        }
    }

And now let's declare it's service in config files.

.. code-block:: xml

    <service id="App\Serializer\ProductSerializer">
            <argument type="service" id="api_platform.serializer.normalizer.item" />
            <tag name="serializer.normalizer" />
    </service>

Then we can add the new field.

.. code-block:: php

    //...
    $data = $this->objectNormalizer->normalize($object, $format, $context);

    $data['additionalText'] = 'your custom text or logic that will be added to this field.';

    return $data;
    //...

Now your response should be extended with new field

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

Removing a field from response
==============================

Let's say that for some reason you want to remove some field from serialization.
Your possible solution could be that you use serialization groups.
Those will limit the fields from your resource, according to serialization groups that you will choose.

.. tip::

    Read more about API Platform `serialization groups <https://api-platform.com/docs/core/serialization/#using-serialization-groups>`_

But if you want to remove the field by utilising serializer, first step is to create a class as in ``Adding a field from response`` and register it's service.

Let's assume that ``Product`` resource returns

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

Then let's say you want to remove ``translations``. We can do it by adding

.. code-block:: php

    //...
    $data = $this->objectNormalizer->normalize($object, $format, $context);

    unset($data['translations']); // removes `translations` from response

    return $data;
    //...

Now your response fields should look like this

.. code-block:: javascript

    {
        //...
        "id": 123,
        "code": "product_code",
        "variants": [
            "/api/v2/shop/product-variants/product-variant-0",
        ]
    }

Renaming a field from response
==============================

As simple as any other steps, renaming name of response fields is also very simple.
Let's modify the ``optionValues`` name to ``options`` that's how response looks like now

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

Now let's modify the serialization class that we used before with some simple logic

.. code-block:: php

    //...
    $data = $this->objectNormalizer->normalize($object, $format, $context);

    $data['options'] = $data['optionValues']; // this will change the name of your field
    unset($data['optionValues']); // optionally you can also remove old `optionValues` field

    return $data;
    //...

And here we go, now your response should look like this

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
