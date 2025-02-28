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

# Customizing API

Sylius uses API Platform to manage all API endpoints. This lets you configure endpoints using YAML or XML files or PHP class attributes. Here’s how to add, remove, and modify Sylius API endpoints.

## How to add an endpoint to the Sylius API?

To add a custom endpoint for the `Order` resource, create a configuration file in the `config/api_platform/` directory.

{% tabs %}
{% tab title="XML" %}
```xml
<?xml version="1.0" encoding="UTF-8" ?>
<!-- config/api_platform/Order.xml -->

<resources
    xmlns="https://api-platform.com/schema/metadata/resources-3.0"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="https://api-platform.com/schema/metadata/resources-3.0 https://api-platform.com/schema/metadata/resources-3.0.xsd"
>
    <resource class="%sylius.model.order.class%">
        <operations>
            <operation
                name="custom_operation"
                class="ApiPlatform\Metadata\Post"
                uriTemplate="/shop/orders/custom-operation"
                messenger="input"
                input="App\Command\CustomCommand"
            />
        </operations>
    </resource>
</resources>
```
{% endtab %}

{% tab title="YAML" %}
```yaml
# config/api_platform/Order.yaml

resources:
    '%sylius.model.order.class%':
        operations:
            ApiPlatform\Metadata\Post:
                name: custom_operation
                uriTemplate: '/shop/orders/custom-operation'
                messenger: input
                input: App\Command\CustomCommand
```
{% endtab %}
{% endtabs %}

This configuration defines a new endpoint at `/shop/orders/custom-operation` that runs `CustomCommand` when accessed.

{% hint style="warning" %}
**Order Modification Restrictions**\
By default, Sylius API restricts order modifications to the "cart" state. If you need to perform actions on orders outside the cart state, add your custom endpoint to the `sylius.api.doctrine_extension.order_shop_user_item.filter_cart.allowed_non_get_operations` parameter. This will enable modifications for other order states.
{% endhint %}

## How to remove an endpoint from the Sylius API?

If you don’t need certain endpoints (e.g., for shipping if you only sell digital products), you can disable them.

```yaml
# config/packages/sylius_api.yaml

sylius_api:
    operations_to_remove:
        - 'sylius_api_shop_order_shipment_patch'
```

This configuration removes the specified endpoint from your API.

## How to rename an endpoint’s path?

To change the path of an existing endpoint, redefine it in your configuration with the new path.

{% tabs %}
{% tab title="XML" %}
```xml
<?xml version="1.0" encoding="UTF-8" ?>
<!-- config/api_platform/Order.xml -->

<resources
    xmlns="https://api-platform.com/schema/metadata/resources-3.0"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="https://api-platform.com/schema/metadata/resources-3.0 https://api-platform.com/schema/metadata/resources-3.0.xsd"
>
    <resource class="%sylius.model.order.class%">
        <operations>
            <operation
                name="sylius_api_shop_order_post"
                class="ApiPlatform\Metadata\Post"
                uriTemplate="/shop/orders/custom-create"
                itemUriTemplate="/shop/orders/{tokenValue}"
                messenger="input"
                input="Sylius\Bundle\ApiBundle\Command\Cart\PickupCart"
            >
                <denormalizationContext>
                    <values>
                        <value name="groups">
                            <values>
                                <value>sylius:shop:order:create</value>
                            </values>
                        </value>
                    </values>
                </denormalizationContext>
                <normalizationContext>
                    <values>
                        <value name="groups">
                            <values>
                                <value>sylius:shop:cart:show</value>
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
# config/api_platform/Order.yaml

resources:
    '%sylius.model.order.class%':
        operations:
            ApiPlatform\Metadata\Post:
                name: sylius_api_shop_order_post
                uriTemplate: '/shop/orders/custom-create'
                itemUriTemplate: '/shop/orders/{tokenValue}'
                messenger: input
                input: Sylius\Bundle\ApiBundle\Command\Cart\PickupCart
                denormalizationContext:
                    groups: ['sylius:shop:order:create']
                normalizationContext:
                    groups: ['sylius:shop:cart:show']
```
{% endtab %}
{% endtabs %}

{% hint style="info" %}
**Learn more** about endpoint operations in the API Platform Documentation [here](https://api-platform.com/docs/core/operations/).
{% endhint %}
