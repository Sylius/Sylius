# How to add a custom translatable model?

We can extend the approach described in the [How to Add a Custom Model](https://docs.sylius.com/the-customization-guide/customizing-models/how-to-add-a-custom-model) guide by making our custom model **translatable**. This is particularly useful for any entity whose content may vary based on locale, such as descriptions, instructions, and names.

In this example, we assume you have already created a custom `Supplier` model. We will now add email and make **`name`** and **`description`** fields translatable.

## Making the custom model translatable

### **Step 1: Create the Translation Entity**

We’ll start by creating a `SupplierTranslation` entity that will hold the locale-specific fields.

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\AbstractTranslation;
use Sylius\Component\Resource\Model\TranslationInterface;
use Sylius\Resource\Model\ResourceInterface;

#[ORM\Entity]
#[ORM\Table(name: 'app_supplier_translation')]
class SupplierTranslation extends AbstractTranslation implements ResourceInterface, TranslationInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
}
```

***

### **Step 2: Update the Supplier Entity to Use Translations**

Extend the `Supplier` entity to manage the translatable fields through the translation mechanism.

```php
<?php

namespace App\Entity;

use App\Repository\SupplierRepository;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Resource\Model\TranslatableInterface;
use Sylius\Resource\Model\TranslatableTrait;
use Sylius\Resource\Model\TranslationInterface;

#[ORM\Entity]
#[ORM\Table(name: 'app_supplier')]
class Supplier implements ResourceInterface, TranslatableInterface
{
    use TranslatableTrait {
        __construct as private initializeTranslationsCollection;
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $email = null;

    private ?string $name = null;

    private ?string $description = null;

    public function __construct()
    {
        $this->initializeTranslationsCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->getTranslation()->getName();
    }

    public function setName(?string $name): void
    {
        $this->getTranslation()->setName($name);
    }

    public function getDescription(): ?string
    {
        return $this->getTranslation()->getDescription();
    }

    public function setDescription(?string $description): void
    {
        $this->getTranslation()->setDescription($description);
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    protected function createTranslation(): TranslationInterface
    {
        return new SupplierTranslation();
    }
}
```

***

### **Step 3: Create Form Types:**

To enable proper multilingual input for your `Supplier` entity in the Sylius Admin, you'll need to:

* **Create a main form type** that includes both static fields (like `email`) and translatable fields (like `name`, `description`) via `ResourceTranslationsType`.
* **Create a separate form type for the translation entity**, specifying what fields should be localized.
* **Register both form types as services** (if you're not using autoconfiguration).

#### SupplierType (Main Form Type)

This form type defines the top-level fields for the `Supplier` entity, including:

* A static `email` field
* A dynamic `translations` collection using `ResourceTranslationsType`

📁 **File path**: `src/Form/Type/SupplierType.php`

```php
<?php

namespace App\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;

final class SupplierType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'sylius.ui.email',
            ])
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => SupplierTranslationType::class,
                'label' => 'sylius.ui.translations',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'app_supplier';
    }
}
```

#### SupplierTranslationType (Translation Subform)

This form defines which fields can be translated per locale. Here we include:

* `name` (required)
* `description` (optional)

📁 **File path**: `src/Form/Type/SupplierTranslationType.php`

```php
<?php

namespace App\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class SupplierTranslationType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'sylius.ui.name',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'sylius.ui.description',
                'required' => false,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'app_supplier_translation';
    }
}
```

#### Register the Form Types

If you are **not** using Symfony's autoconfiguration, register the form types manually in your service config.

📁 **File path**: `config/services.yaml`

```yaml
services:    
    app.form.type.supplier_translation:
        class: App\Form\Type\SupplierTranslationType
        arguments:
            - '%app.model.supplier_translation.class%'
            - [sylius] # default validation group
        tags:
            - { name: 'form.type' }

    app.form.type.supplier:
        class: App\Form\Type\SupplierType
        arguments:
            - '%app.model.supplier.class%'
            - [sylius]
        tags:
            - { name: 'form.type' }
```

### Step 4: Update Your Routing and Resource Configuration

Now that your `Supplier` entity and its translation logic are ready, the next step is to:

1. **Register the translatable resource** with Sylius Resource Bundle.
2. **Configure the routes** to expose CRUD operations in the Sylius Admin Panel.
3. **Configure the grid** to see the list of newly created resources.
4. **Add missing translations** if needed for your resource name.

#### Register the Resource and Translation

This tells Sylius how to manage your custom `Supplier` resource, including its Doctrine model and associated translation entity.

📁 **File path**: `config/packages/_sylius.yaml`

<pre class="language-yaml"><code class="lang-yaml"><strong>sylius_resource:
</strong>    resources:
        app.supplier:
            driver: doctrine/orm
            classes:
                model: App\Entity\Supplier
            translation:
                classes:
                    model: App\Entity\SupplierTranslation
</code></pre>

📌 **Explanation**:

* `driver: doctrine/orm`: Tells Sylius to use Doctrine ORM for persistence.
* `translation.classes.model`: Points to the `SupplierTranslation` entity, enabling multilingual support.

***

#### Define Admin Routes

Create admin routes so that suppliers can be managed through the Sylius Admin UI using the default CRUD template.

📁 **File path**: `config/routes.yaml`

```yaml
app_admin_supplier:
    resource: |
        alias: app.supplier
        section: admin
        templates: "@SyliusAdmin\\shared\\crud"
        except: ['show']
        redirect: update
        grid: app_admin_supplier
        form:
            type: App\Form\Type\SupplierType
        vars:
            all:
                subheader: app.ui.supplier
            index:
                icon: 'file image outline'
    type: sylius.resource
    prefix: /admin
```

📌 **Explanation**:

* `alias: app.supplier`: Tells Sylius to use the resource alias defined in `_sylius.yaml`.
* `section: admin`: Registers the resource inside the admin panel.
* `form.type`: Connects your custom `SupplierType` form.
* `grid`: Should match the grid you'll configure for listing suppliers.
* `redirect: update`: Automatically redirects to the update form after creation.
* `except: ['show']`: Omits the `show` action (optional).

***

#### Configure grid

To manage `Supplier` entities from the Sylius Admin Panel, you need to configure a **grid**. This grid defines how supplier records appear and which actions are available (create, update, delete).

📁 **File path**: `config/packages/_sylius.yaml`

```yaml
sylius_grid:
    grids:
        app_admin_supplier:
            driver:
                name: doctrine/orm
                options:
                    class: App\Entity\Supplier
            fields:
                name:
                    type: string
                    label: sylius.ui.name
                description:
                    type: string
                    label: sylius.ui.description
            actions:
                main:
                    create:
                        type: create
                item:
                    update:
                        type: update
                    delete:
                        type: delete
```

📌 **Explanation**:

* **Grid name**: `app_admin_supplier` – you can reference this in routes and templates.
* **Driver**: `doctrine/orm` uses Doctrine to fetch `App\Entity\Supplier` data.
* **Fields**:
  * `name`, `description` – basic string fields rendered with default labels.
  * `enabled` – a custom field rendered using a Twig template.
* **Actions**:
  * `main.create` – shows a **Create** button at the top of the grid.
  * `item.update` & `item.delete` – appear on each row, allowing editing or removal of the specific entity.

***

#### Configure Translations

To display human-readable labels for your `Supplier` entity in the admin panel, define UI translation strings.

📁 **File path**: `translations/messages.en.yaml`

<pre class="language-yaml"><code class="lang-yaml"><strong>app:
</strong>    ui:
        suppliers: 'Suppliers'
        supplier: 'Supplier'
</code></pre>

📌 **Explanation**:

* These keys are used in grid labels, menu items, form titles, and other UI elements.
* You can reuse them across templates and configuration files (e.g., `label: app.ui.supplier`).
* Make sure to clear your Symfony cache after adding new translation keys:

```bash
bin/console cache:clear
```

***

### Step 5: Update the Database with Migrations

Now that your entities and resource configuration are complete, you'll need to update your database schema. This is done using Doctrine Migrations.

#### Generate the Migration

Use the following command to detect and generate a new migration file based on the changes to your entities (specifically `Supplier` and `SupplierTranslation`):

```bash
php bin/console doctrine:migrations:diff
```

{% hint style="info" %}
**Tip**: Make sure your database is already in sync with the current codebase before running this, or it may generate unintended changes.
{% endhint %}

#### Run the Migration

After the migration file is created (in `migrations/`), apply it to update your actual database schema:

```bash
php bin/console doctrine:migrations:migrate
```

You should see SQL statements executed for creating the `app_supplier` and `app_supplier_translation` tables.

***

## 🎯 Pimp Your Translations Section Using Twig Hooks and a Dedicated Macro

By default, Sylius renders translations as they are stored — with base styling. But you can **easily align your translation UI** with other admin sections using a **predefined Twig macro** from `SyliusAdmin`.\
It helps unify your form sections and make translations **clean, readable, and well-integrated**.

***

#### 🧩 1: Use the Built-In Macro to Render Translations

Use [macro](https://github.com/Sylius/Sylius/blob/v2.0.7/src/Sylius/Bundle/AdminBundle/templates/shared/helper/translations.html.twig) to automatically apply consistent layout, translation tabs, and localization context.

{% hint style="success" %}
The `with_hook` macro works hand-in-hand with Sylius's Twig hook system to render clean, localized translation forms.\
But that’s just the beginning — the helper contains more useful methods to streamline translation UIs.\
👉 **Explore all available macros and helper functions** [**here**](https://github.com/Sylius/Sylius/tree/v2.0.7/src/Sylius/Bundle/AdminBundle/templates/shared/helper)**.**

👉 **Find out more about the twig hooks** [**here**](https://stack.sylius.com/twig-hooks/getting-started)**.**
{% endhint %}

***

#### 🧱 2: Create Hookable Twig Templates

These templates will override the default rendering and allow the macro to integrate properly with your form.

```twig
{# templates/admin/supplier/form/sections/general.html.twig #}

<div class="card mb-3">
    <div class="card-header">
        <div class="card-title">
            {{ 'sylius.ui.general'|trans }}
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            {% hook 'general' %}
        </div>
    </div>
</div>
```

```twig
{# templates/admin/supplier/form/sections/translations.html.twig #}

{% import '@SyliusAdmin/shared/helper/translations.html.twig' as translations %}
{% set form = hookable_metadata.context.form %}
{% set prefixes = hookable_metadata.prefixes %}

<div class="card mb-3">
    <div class="card-header">
        <div class="card-title">
            {{ 'sylius.ui.translations'|trans }}
        </div>
    </div>
    <div class="card-body">
        {{ translations.with_hook(form.translations, prefixes, null, {
            accordion_flush: true
        }) }}
    </div>
</div>
```

```twig
{# templates/admin/supplier/form/sections/general/email.html.twig #}

{{ form_row(hookable_metadata.context.form.email) }}
```

#### 📝 3: Define Individual Translation Fields

Create small templates to hook each translatable field.

```twig
{# templates/admin/supplier/form/sections/translations/description.html.twig #}

{% set form = hookable_metadata.context.form %}

<div class="col-12 col-md-12">
    {{ form_row(form.description) }}
</div>
```

```twig
{# templates/admin/supplier/form/sections/translations/name.html.twig #}

{% set form = hookable_metadata.context.form %}

<div class="col-12 col-md-12">
    {{ form_row(form.name) }}
</div>
```

***

#### 🔧 4: Configure the twig hooks:

For Creating a Supplier (`/admin/suppliers/new`)

```yaml
# config/packages/_sylius.yaml

sylius_twig_hooks:
    hooks:
        'sylius_admin.supplier.create.content':
            form:
                template: '@SyliusAdmin/shared/crud/common/content/form.html.twig'
                configuration:
                    render_rest: false
                priority: 0

        'sylius_admin.supplier.create.content.form.sections':
            general:
                template: 'admin/supplier/form/sections/general.html.twig'
                priority: 100
            translations:
                template: 'admin/supplier/form/sections/translations.html.twig'
                priority: 0

        'sylius_admin.supplier.create.content.form.sections.general':
            default:
                enabled: false
            email:
                template: 'admin/supplier/form/sections/general/email.html.twig'
                priority: 0

        'sylius_admin.supplier.create.content.form.sections.translations':
            name:
                template: 'admin/supplier/form/sections/translations/name.html.twig'
                priority: 100
            description:
                template: 'admin/supplier/form/sections/translations/description.html.twig'
                priority: 0
```

For Editing a Supplier (`/admin/suppliers/{id}/edit`)&#x20;

```yaml
# config/packages/_sylius.yaml

sylius_twig_hooks:
    hooks:
        'sylius_admin.supplier.update.content':
            form:
                template: '@SyliusAdmin/shared/crud/common/content/form.html.twig'
                configuration:
                    render_rest: false
                priority: 0

        'sylius_admin.supplier.update.content.form.sections':
            general:
                template: 'admin/supplier/form/sections/general.html.twig'
                priority: 100
            translations:
                template: 'admin/supplier/form/sections/translations.html.twig'
                priority: 0

        'sylius_admin.supplier.update.content.form.sections.general':
            default:
                enabled: false
            email:
                template: 'admin/supplier/form/sections/general/email.html.twig'
                priority: 0

        'sylius_admin.supplier.update.content.form.sections.translations':
            name:
                template: 'admin/supplier/form/sections/translations/name.html.twig'
                priority: 100
            description:
                template: 'admin/supplier/form/sections/translations/description.html.twig'
                priority: 0
```

#### ✅ 5: Final Result: Translations Section in the Admin Panel

After completing the form and hook customization steps, your supplier form in the Sylius Admin should now display a clean and localized **Translations** section.

This layout includes:

* A **General** section with static fields like `email`
* A **Translations** section rendered using the `with_hook` macro
* Accordion-style **language tabs**, each containing the `name` and `description` fields

This structure provides a user-friendly editing experience and ensures consistency with the rest of the Sylius admin layout.

<figure><img src="../../.gitbook/assets/image (18) (1).png" alt=""><figcaption></figcaption></figure>

