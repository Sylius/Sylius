# Customizing Validation

The default validation group for all Sylius resources is `sylius`, but you can define and configure your own validation logic.

***

## How to customize validation?

Let’s say you want to change the **minimum length** of the `name` field for a `Product`.\
Important: the `name` field is located in the `ProductTranslation` model.

In the default `sylius` validation group, the minimum length is `2`.\
Suppose you want to **enforce at least 10 characters**.

***

### 1. Create a custom validation file

Create a file:\
➡️ `config/validator/ProductTranslation.yaml` \
or `config/validator/ProductTranslation.xml`

In this file, you’ll override the validation rules for your target field.\
You can base your structure on the original file located [he](https://github.com/Sylius/Sylius/blob/v2.0.7/src/Sylius/Bundle/ProductBundle/Resources/config/validation/ProductTranslation.xml)[re](https://github.com/Sylius/Sylius/blob/v2.0.7/src/Sylius/Bundle/ProductBundle/Resources/config/validation/ProductTranslation.xml).

***

Here are both examples using the new group `app_product`:

{% tabs %}
{% tab title="YAML" %}
<pre class="language-yaml"><code class="lang-yaml"><strong># config/validator/ProductTranslation.yaml
</strong><strong>
</strong><strong>Sylius\Component\Product\Model\ProductTranslation:
</strong>    properties:
        name:
            - NotBlank:
                message: sylius.product.name.not_blank
                groups: [app_product]
            - Length:
                min: 10
                minMessage: sylius.product.name.min_length
                max: 255
                maxMessage: sylius.product.name.max_length
                groups: [app_product]
</code></pre>
{% endtab %}

{% tab title="XML" %}
```xml
<?xml version="1.0" encoding="UTF-8"?>

<constraint-mapping xmlns="http://symfony.com/schema/dic/constraint-mapping" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://symfony.com/schema/dic/constraint-mapping http://symfony.com/schema/dic/services/constraint-mapping-1.0.xsd">
    <class name="Sylius\Component\Product\Model\ProductTranslation">
        <property name="name">
            <constraint name="NotBlank">
                <option name="message">sylius.product.name.not_blank</option>
                <option name="groups">app_product</option>
            </constraint>
            <constraint name="Length">
                <option name="min">10</option>
                <option name="minMessage">sylius.product.name.min_length</option>
                <option name="max">255</option>
                <option name="maxMessage">sylius.product.name.max_length</option>
                <option name="groups">app_product</option>
            </constraint>
        </property>
    </class>
</constraint-mapping>
```
{% endtab %}
{% endtabs %}

{% hint style="info" %}
When using custom validation messages, [learn here how to translate them](https://symfony.com/doc/current/validation/translations.html).
{% endhint %}

***

### 2. Register your custom validation group in the service container

Add the following to your `config/services.yaml`:

```yaml
parameters:
    sylius.form.type.product_translation.validation_groups: [app_product]
    sylius.form.type.product.validation_groups: [app_product] # So the Product class is aware of its translation validation
```

#### ✅ Result

Now, the new validation group will be applied in all forms where the Product \
&#x20;is used. This means products with names shorter than 10 characters will no longer be accepted.

<figure><img src="../.gitbook/assets/image (30).png" alt=""><figcaption></figcaption></figure>

***

### 3. Custom validation for special cases (ShippingMethod / Promotions / Zones)

Some parts of Sylius do not use the standard validation mechanism via `sylius.form.type.*.validation_groups`. These include components like **ShippingMethod rules**, **Promotion rules**, and **Promotion actions**, where the configuration is a nested array structure instead of a simple form-data mapping.

This means you must apply validation directly to the configuration structure using Symfony constraints in a different way.

Here are the models that require this special handling:

* `Sylius\Component\Shipping\Model\ShippingMethodRule`
* `Sylius\Component\Shipping\Model\ShippingMethod`&#x20;
* `Sylius\Component\Promotion\Model\CatalogPromotionAction`
* `Sylius\Component\Promotion\Model\CatalogPromotionScope`
* `Sylius\Component\Promotion\Model\PromotionRule`
* `Sylius\Component\Promotion\Model\PromotionAction`
* `Sylius\Component\Promotion\Model\PromotionCoupon`
* `Sylius\Component\Addressing\Model\ZoneMember`

***

#### Example: Validating a ShippingMethodRule

Suppose you want to enforce a **minimum order total of 10** for the rule `order_total_greater_than_or_equal`. Here's how to do it:

**1. Create a custom validation file**

➡️ `config/validator/ShippingMethodRule.yaml` or `config/validator/ShippingMethodRule.xml`

{% tabs %}
{% tab title="YAML" %}
```yaml
# config/validator/ShippingMethodRule.yaml

Sylius\Component\Shipping\Model\ShippingMethodRule:
    properties:
        configuration:
            - Sylius\Bundle\CoreBundle\Validator\Constraints\ChannelCodeCollection:
                  groups: app_shipping_method_rule_order_grater_than_or_equal
                  validateAgainstAllChannels: true
                  channelAwarePropertyPath: shippingMethod
                  constraints:
                      - Collection:
                            fields:
                                amount:
                                    - range:
                                          groups: app_shipping_method_rule_order_grater_than_or_equal
                                          min: 1000
                                          max: 1000000
                  allowExtraFields: true
```
{% endtab %}

{% tab title="XML" %}
```xml
<?xml version="1.0" encoding="UTF-8"?>

<constraint-mapping xmlns="http://symfony.com/schema/dic/constraint-mapping" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://symfony.com/schema/dic/constraint-mapping http://symfony.com/schema/dic/services/constraint-mapping-1.0.xsd">
    <class name="Sylius\Component\Shipping\Model\ShippingMethodRule">
        <property name="configuration">
            <constraint name="Sylius\Bundle\CoreBundle\Validator\Constraints\ChannelCodeCollection">
                <option name="groups">app_shipping_method_rule_order_grater_than_or_equal</option>
                <option name="validateAgainstAllChannels">true</option>
                <option name="channelAwarePropertyPath">shippingMethod</option>
                <option name="constraints">
                    <constraint name="Collection">
                        <option name="fields">
                            <value key="amount">
                                <constraint name="range">
                                    <option name="groups">app_shipping_method_rule_order_grater_than_or_equal</option>
                                    <option name="min">1000</option>
                                    <option name="max">1000000</option>
                                </constraint>
                            </value>
                        </option>
                    </constraint>
                </option>
                <option name="allowExtraFields">true</option>
            </constraint>
        </property>
    </class>
</constraint-mapping>
```
{% endtab %}
{% endtabs %}

**2. Register your custom validation group in the service container**

```yaml
parameters:
    sylius.shipping.shipping_method_rule.validation_groups:
        order_total_greater_than_or_equal: [app_shipping_method_rule_order_grater_than_or_equal]
```

⚠️ **Important Notes:**

* The parameter name must exactly match the rule key (`order_total_greater_than_or_equal`) as defined in your `ShippingMethodRule` configuration.
* Sylius uses this key to resolve the correct validation group when processing the rule, both in the **Admin UI** and **API**.
* Be aware that other rule types like `order_total_less_than_or_equal` will require separate entries.
* You can find all the base configurations of the groups [here](https://github.com/Sylius/Sylius/tree/v2.0.7/src/Sylius/Bundle/CoreBundle/Resources/config/app/sylius).

{% hint style="success" %}
To find the parameter you want to customize just run:

```bash
php bin/console debug:container --parameters --env=dev | grep validation_groups
```
{% endhint %}

✅ Result\
This ensures that when the `order_total_greater_than_or_equal` rule is used, the configured amount must be at least 10. If not, a validation error will be triggered.

<figure><img src="../.gitbook/assets/image (29).png" alt=""><figcaption></figcaption></figure>

{% hint style="info" %}
🧠 **Advanced Tip – Using Group Sequence Validation:**

If you’d like to use group sequence validation (e.g. to validate some constraints before others), [read more here](https://symfony.com/doc/current/validation/sequence_provider.html).\
Make sure to use `[Default]` as your validation group; otherwise, your `getGroupSequence()` method won’t be triggered.
{% endhint %}
