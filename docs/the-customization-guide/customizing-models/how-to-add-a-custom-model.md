---
description: Extending Sylius with New Entities
---

# How to add a custom model?

In some cases, you may need to add custom models to extend Sylius to meet unique business requirements. Here, we’ll walk through the process of adding a `Supplier` entity, which could be useful for managing suppliers in your shop.

## How to add a custom Entity to Sylius?

**1. Define Your Requirements**

For the `Supplier` entity, we’ll need three fields:

* `name`: The supplier’s name
* `description`: A description of the supplier
* `enabled`: A flag indicating if the supplier is active

**2. Generate the Entity**

To simplify the entity generation, we can use Symfony’s `SymfonyMakerBundle`.

{% hint style="warning" %}
Ensure that `SymfonyMakerBundle` is enabled in your project since it is not included by default.
{% endhint %}

Run the following command to create the entity:

```bash
php bin/console make:entity
```

The generator will prompt you for the entity name and fields. Complete these fields to match your requirements.

{% hint style="info" %}
If you encounter an error during entity generation, consider using the "force annotation fix" option in the Maker Bundle.
{% endhint %}

**3. Update the Database with Migrations**

Assuming your database is up-to-date, create a migration for the new entity:

```bash
php bin/console doctrine:migrations:diff
```

Then, apply the migration to update your database schema:

```bash
php bin/console doctrine:migrations:migrate
```

**4. Implement the `ResourceInterface`**

To make your new entity compatible with Sylius resources, implement the `ResourceInterface` in your `Supplier` class.

In `src/Entity/Supplier.php`:

```php
<?php

namespace App\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

class Supplier implements ResourceInterface
{
    // ...
}
```

**5. Extend the Repository from `EntityRepository`**

In the generated repository file, extend `EntityRepository` to leverage Doctrine’s repository functionality, and remove any unnecessary constructors.

In `src/Repository/SupplierRepository.php`:

```php
<?php

namespace App\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class SupplierRepository extends EntityRepository
{
    // ...
}
```

**6. Register the Supplier Entity as a Sylius Resource**

Create (or update) a `sylius_resource.yaml` file to register your new entity as a Sylius resource.

In `config/packages/sylius_resource.yaml`:

```yaml
sylius_resource:
    resources:
        app.supplier:
            driver: doctrine/orm
            classes:
                model: App\Entity\Supplier
                repository: App\Repository\SupplierRepository
```

Verify the registration by running:

```bash
php bin/console debug:container | grep supplier
```

The output should display information related to the `Supplier` entity:

**7. Configure a Grid for Supplier Management**

To enable easy management of suppliers in the admin panel, configure a grid for the `Supplier` entity.

In `config/packages/_sylius.yaml`:

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
                enabled:
                    type: twig
                    label: sylius.ui.enabled
                    options:
                        template: "@SyliusUi/Grid/Field/enabled.html.twig"
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

**8. Define Routing for Supplier Administration**

Define routes for managing the `Supplier` entity in the admin panel.

In `config/routes.yaml`:

```yaml
app_admin_supplier:
    resource: |
        alias: app.supplier
        section: admin
        templates: "@SyliusAdmin\\shared\\crud"
        redirect: update
        grid: app_admin_supplier
        vars:
            all:
                subheader: app.ui.supplier
            index:
                icon: 'file image outline'
    type: sylius.resource
    prefix: /admin
```

**9. Add Supplier to the Admin Menu**

Add links to access the new `Supplier` entity management in the admin menu. See how to add items to the admin menu here.

**10. Check the Admin Panel**

Navigate to `https://localhost:8000/admin/suppliers/` to view and manage the `Supplier` entity in the Sylius admin panel.

## Learn More

* [**GridBundle Documentation**](https://github.com/Sylius/SyliusGridBundle/blob/master/docs/index.md)
* [**ResourceBundle Documentation**](https://github.com/Sylius/SyliusResourceBundle/blob/master/docs/index.md)
