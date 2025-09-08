# Customizing Forms

Forms in Sylius are flexible and extendable, allowing you to modify them to better fit your business needs.

### Why would you customize a Form?

You might need to customize a form in Sylius to:\
✅ **Add new fields** – e.g., a secondary phone number for customers.\
✅ **Modify existing fields** – change labels, make them required, or adjust their CSS classes.\
✅ **Remove fields** – get rid of unnecessary form fields.

### How to customize a Form?

Let’s say you want to customize the **Customer Profile Form** by:

* Adding a `secondaryPhoneNumber` field.
* Removing the `gender` field.
* Changing the label for `lastName` from `sylius.form.customer.last_name` to `app.form.customer.surname`.

1. &#x20;**Ensure Your Model Supports New Fields**

Before adding a new field, make sure it exists in the model. For example, `secondaryPhoneNumber` must be added to the **Customer entity** and properly mapped in your database. To learn how to do that, check [this part of the doc](../customizing-models/).

2. **Create a Form Extension**

To find the base class of the form you want to extend, run:

```bash
php bin/console debug:container | grep form.type.customer_profile
```

This will return:

<pre class="language-bash"><code class="lang-bash">sylius.form.type.customer_profile                                                                                                                          
  Sylius\Bundle\CustomerBundle\Form\Type\CustomerProfileType                                                                     
sylius_shop.form.type.customer_profile
<strong>  Sylius\Bundle\ShopBundle\Form\Type\CustomerProfileType
</strong></code></pre>

{% hint style="info" %}
The form with `sylius.` prefix is the one, that is a base for another 2 contexts, `sylius_shop` and `sylius_admin` . Extending only the forms within a specific context is the recommended approach.
{% endhint %}

{% hint style="danger" %}
**Remember:** Using the form type that has been returned from the service with  `sylius.` prefix will apply the extension to **both** forms!
{% endhint %}

We need to extend `Sylius\Bundle\ShopBundle\Form\Type\CustomerProfileType`.

Create a new form extension class:

```php
<?php

declare(strict_types=1);

namespace App\Form\Extension;

use Sylius\Bundle\ShopBundle\Form\Type\CustomerProfileType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class CustomerProfileTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Adding new fields works just like in the parent form type.
            ->add('secondaryPhoneNumber', TextType::class, [
                'required' => false,
                'label' => 'app.form.customer.secondary_phone_number',
            ])
            // To remove a field from a form simply call ->remove(`fieldName`).
            ->remove('gender')
            // You can change the label by adding again the same field with a changed `label` parameter.
            ->add('lastName', TextType::class, [
                'label' => 'app.form.customer.surname',
            ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [CustomerProfileType::class];
    }
}
```

{% hint style="success" %}
If you add a new label, remember to define it in your translation files (`translations/messages.en.yaml`).
{% endhint %}

3. Register the Form Extension

If **autoconfiguration is disabled**, register the extension in `config/services.yaml`:

```yaml
services:
    app.form.extension.type.customer_profile:
        class: App\Form\Extension\CustomerProfileTypeExtension
        tags:
            - { name: form.type_extension }
```

If **autoconfiguration is enabled**, no manual registration is needed.

{% hint style="warning" %}
You can check if the extension is correctly registered by running:

```bash
php bin/console debug:form "Sylius\Bundle\ShopBundle\Form\Type\CustomerProfileType"
```
{% endhint %}

#### **Update the templates:**

Make sure your form templates reflect the new changes:

* **Render the new fields** you added.
* **Remove the old fields** you removed.

Now you need to find the correct hook that is associated with the form you want to customize.

To do this, go to the page containing the form and use your browser's developer tools to inspect it. Look for the comments surrounding the form.

<mark style="color:green;">< — BEGIN HOOK | name: "sylius\_shop.account.profile\_update.update.content.main.form" — ></mark>

It means the hook we want to customize is:

{% code overflow="wrap" %}
```
sylius_shop.account.profile_update.update.content.main.form
```
{% endcode %}

Create an appropriate twig for the `secondaryPhoneNumber` field:

```twig
// templates/account/profile_update/update/content/main/form/secondary_phone_number.html.twig

<div>{{ form_row(hookable_metadata.context.form.secondaryPhoneNumber) }}</div>
```

Update the form hook with your new field template:

```yaml
// config/packages/twig_hooks.yaml

sylius_twig_hooks:
    hooks:
        'sylius_shop.account.profile_update.update.content.main.form':
            secondary_phone_number:
                template: 'account/profile_update/update/content/main/form/secondary_phone_number.html.twig'
                priority: 600
```

To remove the old field from the template, just find and disable the hook responsible for it:

```yaml
// config/packages/twig_hooks.yaml

sylius_twig_hooks:
    hooks:
        'sylius_shop.account.profile_update.update.content.main.form.additional_information':
            gender:
                enabled: false
```

Then your final result should look like:

<figure><img src="../../.gitbook/assets/Screenshot from 2025-04-09 11-31-08.png" alt=""><figcaption></figcaption></figure>

{% hint style="info" %}
Find out more about the amazing features of Twig Hooks [here](https://stack.sylius.com/twig-hooks/getting-started)
{% endhint %}

### Customizing Forms That Are Already Extended

Some forms in Sylius are **already extended** in the core system. Example:

* `ProductVariantType` is extended by `ProductVariantTypeExtension` in `Sylius/Bundle/CoreBundle/Form/Extension/`.

If you want to add **another extension** to an already extended form, define the priority:

```yaml
services:
    app.form.extension.type.product_variant:
        class: App\Form\Extension\ProductVariantTypeMyExtension
        tags:
            - { name: form.type_extension, extended_type: Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType, priority: -5 }
```

Extensions with **higher priority values** run first.

### Handling Dynamically Added Form Fields

Some form fields in Sylius are added dynamically using **event listeners**.\
For example, the **ProductVariantTypeExtension** in `CoreBundle` adds `channelPricings` dynamically:

```php
<?php

// ...

final class ProductVariantTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // ...

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $productVariant = $event->getData();

            $event->getForm()->add('channelPricings', ChannelCollectionType::class, [
                'entry_type' => ChannelPricingType::class,
                'entry_options' => function (ChannelInterface $channel) use ($productVariant) {
                    return [
                        'channel' => $channel,
                        'product_variant' => $productVariant,
                        'required' => false,
                    ];
                },
                'label' => 'sylius.form.variant.price',
            ]);
        });
    }

    // ...

}
```

#### **How to Remove Dynamically Added Fields**

To modify or remove dynamically added fields, you need to **listen for the same event** and adjust the form accordingly:

```php
$builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
    $event->getForm()->remove('channelPricings');
});
```

{% hint style="success" %}
[See more in the Symfony docs](https://symfony.com/doc/current/form/create_custom_field_type.html)
{% endhint %}

### **Good to know**

✅ You can apply all these form customizations **directly in your application** or as part of a **Sylius plugin**.\
✅ If you're customizing forms frequently, using **extensions** is recommended to avoid overriding entire forms.

***

With this guide, you should have a solid understanding of how to customize forms in Sylius to match your project’s needs! 🚀
