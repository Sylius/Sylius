---
layout:
  title:
    visible: true
  description:
    visible: false
  tableOfContents:
    visible: true
  outline:
    visible: true
  pagination:
    visible: true
---

# Customizing Serialization of API

To customize which fields appear in Sylius API responses, follow these steps for adding, removing, or renaming fields.

## How to add an existing field to a response?

To add an existing field, such as `createdAt`, to the response of a `ProductVariant` in the shop context, we need to define a serialization group for it in a configuration file.

{% tabs %}
{% tab title="XML" %}
```xml
<?xml version="1.0" ?>
<!-- config/serialization/ProductVariant.xml -->

<serializer
    xmlns="http://symfony.com/schema/dic/serializer-mapping"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://symfony.com/schema/dic/serializer-mapping https://symfony.com/schema/dic/serializer-mapping/serializer-mapping-1.0.xsd"
>
    <class name="Sylius\Component\Core\Model\ProductVariant">
        <attribute name="createdAt">
            <group>sylius:shop:product_variant:show</group>
        </attribute>
    </class>
</serializer>
```
{% endtab %}

{% tab title="YAML" %}
```yaml
# config/serialization/ProductVariant.yaml

Sylius\Component\Core\Model\ProductVariant:
    attributes:
        createdAt:
            groups: ['sylius:shop:product_variant:show']
```
{% endtab %}
{% endtabs %}

By adding `createdAt` with the group `sylius:shop:product_variant:show`, this field is now part of the response at the `/shop/product-variants/{code}` endpoint.

{% hint style="info" %}
**Finding the Serialization Group for an Endpoint**

If you need to find the serialization group for an endpoint you want to modify, check the configuration files in `%kernel.project_dir%/vendor/sylius/sylius/src/Sylius/Bundle/ApiBundle/Resources/config/api_platform/resources`. \
\
The group names generally follow this pattern: `<context>:<resource>:<operation>`, reflecting the endpoint's usage context, resource name, and operation type.
{% endhint %}

## How to add a custom field to a response?

To add a new field, like `additionalText`, to a `Customer` response, create a custom serializer called `CustomerNormalizer`. This serializer will add extra data to the response.

```php
<?php
// src/Serializer/CustomerNormalizer.php

declare(strict_types=1);

namespace App\Serializer;

use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class CustomerNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'customer_normalizer_already_called';

    /**
     * @param CustomerInterface $object
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        Assert::isInstanceOf($object, CustomerInterface::class);
        Assert::keyNotExists($context, self::ALREADY_CALLED);

        $context[self::ALREADY_CALLED] = true;

        $data = $this->normalizer->normalize($object, $format, $context);
        $data['additionalText'] = 'Custom text or logic that will be added to this field.';

        return $data;
    }

    /** @param array<string, mixed> $context */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof CustomerInterface;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [CustomerInterface::class => false];
    }
}
```

Then, register this serializer in `config/services.yaml`:

```yaml
# config/services.yaml

services:
    App\Serializer\CustomerNormalizer:
        tags:
            - { name: 'serializer.normalizer', priority: 100 }
```

This serializer class checks if the object being normalized is an instance of `CustomerInterface`. If it is, it adds the `additionalText` field to the response, along with any other existing fields.

```json
{
    //...
    "id": 123,
    "email": "sylius@example.com",
    "additionalText": "Custom text or logic that will be added to this field.",
    //...
}
```

## How to add a custom field to a response for Specific Contexts Only (e.g., Shop)?

If you want the custom field to appear only in a specific context, such as in the shop API responses, extend the serializer to check for the current section and serialization groups.\
\
**Modify the Serializer for Context-Specific Use**

Update the serializer to include the `SectionProviderInterface` and restrict the added field to the `ShopApiSection` context.

```php
<?php
// src/Serializer/CustomerNormalizer.php

declare(strict_types=1);

namespace App\Serializer;

use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\SerializationGroupsSupportTrait;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class CustomerNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;
    use SerializationGroupsSupportTrait;

    public function __construct(
        private readonly SectionProviderInterface $sectionProvider,
        private readonly array $serializationGroups,
    ) {
    }

    private const ALREADY_CALLED = 'customer_normalizer_already_called';

    /**
     * @param CustomerInterface $object
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        Assert::isInstanceOf($object, CustomerInterface::class);
        Assert::keyNotExists($context, self::ALREADY_CALLED);
        Assert::isInstanceOf($this->sectionProvider->getSection(), ShopApiSection::class);
        Assert::true($this->supportsSerializationGroups($context, $this->serializationGroups));

        $context[self::ALREADY_CALLED] = true;

        $data = $this->normalizer->normalize($object, $format, $context);
        $data['additionalText'] = 'Custom text or logic that will be added to this field.';

        return $data;
    }

    /** @param array<string, mixed> $context */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return
            $data instanceof CustomerInterface &&
            $this->sectionProvider->getSection() instanceof ShopApiSection &&
            $this->supportsSerializationGroups($context, $this->serializationGroups)
        ;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [CustomerInterface::class => false];
    }
}
```

**Register the Service for the Specific Context**

Specify that this service applies only in the `ShopApiSection` and for the `sylius:shop:customer:show` serialization group.

```yaml
# config/services.yaml

services:
    App\Serializer\CustomerNormalizer:
        arguments:
            - '@sylius.section_resolver.uri_based'
            - ['sylius:shop:customer:show']
        tags:
            - { name: 'serializer.normalizer', priority: 100 }
```

{% hint style="warning" %}
Unfortunately, we cannot use more than one normalizer per resource, so if you need to add a serializer for a resource that already has one defined, remember to overwrite the base one or define your own with a higher priority.
{% endhint %}

## How to remove a field from a response

Removing a field from a response can be challenging because Symfony combines all serialization group configurations for a resource. The simplest solution is to create a new serialization group that includes only the fields you want in the response, then assign this group to the desired endpoint. This way, fields that are not part of the new group are excluded from the response.

Let’s assume that the `Product` resource returns such a response:

```json
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
```

**Create a Custom API Resources Configuration File**

Define a new serialization group specifically for the fields you want to expose. In this example, we’ll use the `GET` endpoint for the `Product` resource in the shop API, assigning a new normalization group named `shop:product:custom_show`.

{% tabs %}
{% tab title="XML" %}
```xml
<?xml version="1.0" encoding="UTF-8" ?>
<!-- config/api_platform/Product.xml -->

<resources
    xmlns="https://api-platform.com/schema/metadata/resources-3.0"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="https://api-platform.com/schema/metadata/resources-3.0 https://api-platform.com/schema/metadata/resources-3.0.xsd"
>
    <resource class="%sylius.model.product.class%">
        <operations>
            <operation name="sylius_api_shop_product_get" class="ApiPlatform\Metadata\Get" uriTemplate="/shop/products/{code}">
                <normalizationContext>
                    <values>
                        <value name="groups">
                            <values>
                                <value>shop:product:custom_show</value>
                            </values>
                        </value>
                    </values>
                </normalizationContext>
            </operation>
        </operations>
    </resource>
</resources>
```
{% endtab %}

{% tab title="YAML" %}
```yaml
# config/api_platform/Product.yaml

resources:
    '%sylius.model.product.class%':
        operations:
            ApiPlatform\Metadata\Get:
                name: sylius_api_shop_product_get
                uriTemplate: '/shop/products/{code}'
                normalizationContext:
                    groups: ['shop:product:custom_show'
```
{% endtab %}
{% endtabs %}

Here, we define a `GET` operation for the `Product` resource in the shop context with a custom serialization group, `shop:product:custom_show`. This limits the response to fields that are part of this group.

**Define the Fields to Include in the New Group**

In the `config/serialization` directory, specify only the fields you want to include in the `shop:product:custom_show` group. Fields that are not assigned to this group (such as `translations` in this example) will be excluded from the response.

{% tabs %}
{% tab title="XML" %}
```xml
<!-- config/serialization/Product.xml -->

<!--...-->
<attribute name="updatedAt">
    <group>shop:product:custom_show</group>
</attribute>
<!-- here `translation` attribute would be declared -->
<attribute name="mainTaxon">
    <group>shop:product:custom_show</group>
</attribute>
<!--...-->
```
{% endtab %}

{% tab title="YAML" %}
```yaml
# config/serialization/Product.yaml

Sylius\Component\Core\Model\Product:
    attributes:
#        ...
        updatedAt:
            groups: ['shop:product:custom_read']
#        here `translation` attribute would be declared
        mainTaxon:
            groups: ['shop:product:custom_read']
#        ...
```
{% endtab %}
{% endtabs %}

With this setup, only fields assigned to the `shop:product:custom_show` group (like `updatedAt` and `mainTaxon` in this example) will appear in the API response. The `translations` field and any other fields not included in `shop:product:custom_show` are excluded.

{% hint style="info" %}
The same result could be also achieved by using a custom serializer for the given resource.
{% endhint %}

## How to rename a field of a response

To rename a field in the response, such as changing `options` to `optionValues`, update the serialization configuration file to use a `serialized-name` attribute.

That’s how the response looks like now:

```json
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
```

**Set a Custom Serialized Name**\
The simplest method to achieve this is to modify the serialization configuration file that we’ve already created. Let’s add to the `config/serialization/Product.xml` file config for `options` with a `serialized-name` attribute description:

{% tabs %}
{% tab title="XML" %}
```xml
<!-- config/serialization/Product.xml -->

<!--...-->
<attribute name="options" serialized-name="optionValues">
    <group>sylius:admin:product:index</group>
    <!--...-->
</attribute>
<!--...-->
```
{% endtab %}

{% tab title="YAML" %}
```yaml
# config/serialization/Product.yaml

Sylius\Component\Core\Model\Product:
    attributes:
#        ...
        options:
            serialized_name: optionValues
            groups: 
               - 'shop:product:custom_read'
#            ...
```
{% endtab %}
{% endtabs %}

{% hint style="info" %}
You can also achieve this by utilizing the serializer class.
{% endhint %}

And here we go, now your response should look like this:

```json
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
```

{% hint style="info" %}
**Learn more** about:

* API Platform [serialization groups](https://api-platform.com/docs/core/serialization/#using-serialization-groups)
* [Symfony Serializer Component](https://symfony.com/doc/current/components/serializer.html)
{% endhint %}
