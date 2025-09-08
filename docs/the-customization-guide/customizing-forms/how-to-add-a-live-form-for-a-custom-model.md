# How to add a live form for a custom model?

This guide shows how to integrate **Symfony UX Live Components** into a Sylius form for a custom model (resource). We’ll build a **Supplier** resource that:

* Manages a **collection** of Zones via a Live Collection component
* Offers an **autocomplete** for selecting a Channel via a Live Autocomplete component

## Prerequisites

For the purpose of this guide, we'll be using the [previously created](../customizing-models/how-to-add-a-custom-model.md) Supplier resource in the examples.

Also, make sure you have an understanding of Symfony UX Live Components, which you can read about here:

{% embed url="https://symfony.com/bundles/ux-live-component/current/index.html" %}

## Build your Live Components

### Live form

Once you create the Supplier resource, it comes with a fully functional form type. However, it lacks features like dynamic validation. To add these, the default form maintained by the resource bundle needs to be transformed into a dynamic one.

### Constraints

Make sure the Supplier resource includes constraints that can be validated:

```php
// src/Entity/Supplier.php

use Symfony\Component\Validator\Constraints as Assert;

class Supplier implements ResourceInterface
{
    ...

    #[Assert\NotBlank]
    private ?string $name = null;

    ...
}
```

### Register live component

First, we need to register the component. It’s important to set the correct tag. As we are dealing with the resource in the admin context (a Supplier resource managed by an admin user), the component must be registered to reflect this context accordingly.

```yaml
# config/services.yaml

services:
    app_admin.twig.component.supplier.form: # Your custom ID name, but it’s best to follow the convention.
        class: Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponent # Here we specify the base form component that already supports live actions.  
        arguments:
            - '@app.repository.supplier'
            - '@form.factory'
            - '%app.model.supplier.class%'
            - 'Sylius\Bundle\ResourceBundle\Form\Type\DefaultResourceType'
        tags:
            - { name: 'sylius.live_component.admin', key: 'app_admin:twig:component:supplier:form' } # key == identifier used in Twig hooks configuration.

```

The service has been registered, however, it's not yet used anywhere.

The next step is to override the generic hookables that render the component with the live version we created:

```yaml
# config/packages/twig_hooks.yaml

sylius_twig_hooks:
    hooks:
        'sylius_admin.supplier.create.content':
            form:
                component: 'app_admin:twig:component:supplier:form'
                props:
                    template: '@SyliusAdmin/shared/crud/common/content/form.html.twig'
                    resource: '@=_context.resource'

        'sylius_admin.supplier.update.content':
            form:
                component: 'app_admin:twig:component:supplier:form'
                props:
                    template: '@SyliusAdmin/shared/crud/common/content/form.html.twig'
                    resource: '@=_context.resource'

```

We need to provide:

```yaml
# config/packages/sylius_resource.yaml

sylius_resource:
    resources:
        app.supplier:
            driver: doctrine/orm
            classes:
                model: App\Entity\Supplier
                repository: App\Repository\SupplierRepository
```

From now on, when you focus on a required field, dynamic validation will be triggered once the field loses focus, without the need to explicitly submit the form:

<figure><img src="../../.gitbook/assets/image (1) (2) (1).png" alt=""><figcaption></figcaption></figure>

## Live collection

### Expanding the Supplier Entity

First, let’s expand the Supplier entity by adding a new field called `zones`. The easiest way to do this is by using the `MakerBundle` to create a `many-to-many` relation with the `App\Entity\Addressing\Zone` entity through the wizard. Don’t forget to create and execute the migration afterwards.

### Supplier FormType

Since we want to add a `LiveCollectionType` to our entity, we can no longer rely on the default form type from the resource bundle. Let's to create a new one:

```php
<?php

// src/Form/Type/SupplierType.php

namespace App\Form\Type;

use App\Entity\Supplier;
use Sylius\Bundle\AddressingBundle\Form\Type\ZoneChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

class SupplierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('enabled')
            ->add('zones', LiveCollectionType::class, [
                'entry_type' => ZoneChoiceType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'button_add_options' => [
                    'label' => 'sylius.form.zone.add_member'
                ],
                'button_delete_options' => [
                    'attr' => ['class' => 'btn btn-outline-danger']
                ],
                'delete_empty' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Supplier::class,
        ]);
    }
}
```

The `Supplier` configuration needs to be updated accordingly:

```yaml
# config/packages/sylius_resource.yaml

sylius_resource:
    resources:
        app.supplier:
            driver: doctrine/orm
            classes:
                model: App\Entity\Supplier
                repository: App\Repository\SupplierRepository
                form: App\Form\Type\SupplierType
```

The component also needs to be updated to use the new `SupplierType` form type:

```yaml
# config/services.yaml

services:
    app_admin.twig.component.supplier.form:
        class: Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponent
        arguments:
            - '@app.repository.supplier'
            - '@form.factory'
            - '%app.model.supplier.class%'
            - 'App\Form\Type\SupplierType'
        tags:
            - { name: 'sylius.live_component.admin', key: 'app_admin:twig:component:supplier:form' }

```

### Organization of form fields

{% tabs %}
{% tab title="Basic" %}
When you want to render all defined form fields as they are, no special adjustments are needed—the internal code handles everything. However, if you’d like to change the order or exclude certain fields from rendering, you can follow Symfony’s documentation. For example, you can adjust the order in the form type configuration or override the form rendering template using Twig hooks to manually render selected fields with `{{ form_row(...) }}`, and so on.

```yaml
sylius_twig_hooks:
    hooks:
        'sylius_admin.supplier.create.content':
            form:
                component: 'app.twig.component.supplier.form'
                props:
                    template: 'supplier/form.html.twig' # Your alternative form setup
                    resource: '@=_context.resource'
        'sylius_admin.supplier.update.content':
            form:
                component: 'app.twig.component.supplier.form'
                props:
                    template: 'supplier/form.html.twig' # Your alternative form setup
                    resource: '@=_context.resource'
```
{% endtab %}

{% tab title="Advanced" %}
Since we aim to provide high scalability and extensibility, the key idea is to design our solution so that further customizations in the end application can be made easily. To achieve this, we need to organize the form fields accordingly. The Sylius codebase serves as a good reference point for understanding the underlying concept.

That being said, let’s prepare the proper configuration:

```yaml
sylius_twig_hooks:
    hooks:
        ...
        
        'sylius_admin.supplier.create.content.form.sections.general':
            default:
                enabled: false
            name:
                template: '@AcmePlugin/name.html.twig'
                priority: 300
            description:
                template: '@AcmePlugin/description.html.twig'
                priority: 200
            enabled:
                template: '@AcmePlugin/enabled.html.twig'
                priority: 100
            zones:
                template: '@AcmePlugin/zones.html.twig'
                priority: 0

        ...
        
        'sylius_admin.supplier.update.content.form.sections.general':
            ... # an analogous configuration for the update operation

```

For each form field, you need to create an appropriate Twig template that renders the value. For instance:

```twig
{{ form_row(hookable_metadata.context.form.name) }}
```

Defining each field separately is a bit tedious, but it gives us the flexibility to customize each field’s configuration, order, styles, and more.

Priorities for new resources are assigned in steps of 100. When modifying existing resources, intermediate values like 150 (between 100 and 200) can be used to insert custom logic without disrupting the overall order.

This configuration:

```yaml
default:
    enabled: false
```

is necessary to disable the internal form rendering, giving us full control over the final design of the rendered form.
{% endtab %}
{% endtabs %}

<figure><img src="../../.gitbook/assets/image (2) (2) (1).png" alt=""><figcaption></figcaption></figure>

## Live autocomplete

### Expanding the Supplier Entity again

Let’s expand the Supplier entity by adding another field called `channels` with a `many-to-many` relation with the `App\Entity\Channel\Channel` entity through the Symfony Maker wizard and handle the migration afterwards.

### Channel Autocomplete Type

```php
<?php

// src/Form/Channel/ChannelAutocompleteType.php

namespace App\Form\Channel;

use App\Entity\Channel\Channel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField(route: 'sylius_admin_entity_autocomplete')]
class ChannelAutocompleteType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Channel::class,
            'placeholder' => 'Choose a Channel',
            'choice_label' => 'name',
            'searchable_fields' => ['name'],
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}

```

Take a look at the configured route: `sylius_admin_entity_autocomplete`. It must be set appropriately to reflect usage within the admin context.

### Supplier FormType

Include `channels` field in the form type:

```php
// src/Form/Type/SupplierType.php
...
->add('channels', ChannelAutocompleteType::class, [
    'multiple' => true,
    'required' => true,
    'constraints' => [
        new Count([
            'min'        => 1,
            'minMessage' => 'app.supplier.channels.min_count',
        ]),
    ],
])
```

{% tabs %}
{% tab title="Basic" %}
Ready to go. No further changes needed.
{% endtab %}

{% tab title="Advanced" %}
We need to update the Twig hooks configuration. Using negative priorities is acceptable and avoids the need to re-prioritize all entries:

```yaml
sylius_twig_hooks:
    hooks:
        'sylius_admin.supplier.create.content.form.sections.general':
            ...
            channels:
                template: '@AcmePlugin/channels.html.twig'
                priority: -100

        'sylius_admin.supplier.update.content.form.sections.general':
            ...
            channels:
                template: '@AcmePlugin/channels.html.twig'
                priority: -100

```
{% endtab %}
{% endtabs %}

<figure><img src="../../.gitbook/assets/image (15) (1).png" alt=""><figcaption></figcaption></figure>
