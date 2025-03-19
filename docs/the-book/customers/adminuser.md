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

# AdminUser

The **AdminUser** entity extends the **User** entity. It is created to enable administrator accounts that have access to the administration panel.

## How to create an AdminUser programmatically?

The **AdminUser** is created just like every other entity, it has its factory. By default, it will have an administration **role** assigned.

```php
/** @var AdminUserInterface $admin */
$admin = $this->container->get('sylius.factory.admin_user')->createNew();

$admin->setEmail('administrator@test.com');
$admin->setPlainPassword('pswd');

$this->container->get('sylius.repository.admin_user')->add($admin);
```

## Administration Security

In **Sylius** by default you have got the administration panel routes (`/admin/*`) secured by a firewall - its configuration can be found in the [security.yaml](https://github.com/Sylius/Sylius/blob/master/config/packages/security.yaml) file.

Only the logged-in **AdminUsers** are eligible to enter these routes.

<div data-full-width="false"><figure><img src="../../.gitbook/assets/sylius-docs-plusfeature-start (1).png" alt=""><figcaption></figcaption></figure></div>

## Access Control via Administrator Roles (ACL/RBAC)

RBAC (_Role Based Access Control_) or ACL (_Access Control Layer_) is an approach to restricting system access for users using the roles system. It is required by the majority of companies on the enterprise level, thus it is provided in the Sylius Plus edition.

A Role is a set of permissions to perform certain operations within the system, which is assigned to a chosen Administrator.

In Sylius Plus implementation of this system, we are basing on routing to determine what kind of permissions are there to be assigned. This allows us to for example give a role access to only show actions of a chosen entity (like Products or Orders).

It is important to know that one Administrator can have multiple roles assigned.

The RBAC system in Sylius Plus let’s you also to temporarily disable the Permission Checker for a chosen Administrator.

{% hint style="info" %}
You can disable the permission checker for administrator not only via the UI but also with a Symfony command:\
`bin/console sylius-plus-rbac:disable-admin-permission-checker <email>`
{% endhint %}

The Sylius Plus fixture suite provides a few roles as examples of how you can shape the roles in your system:

* `SUPER_ADMIN` with access to everything including role management
* `PRODUCT_MANAGER` with access to product catalog management with inventory, associations, options, taxons, etc.
* `FULFILLMENT_WORKER` with access to order management, product catalog show, inventory management, and shipments

### Customizing the permissions tree

**How to add a new permission?**

Let’s assume that you would like to add a new permission to ACL. You will need to add these few lines to the `config.yml`:

```yaml
# config/packages/_sylius.yaml
# ...

sylius_plus:
    permissions:
        # Each permission must have a unique id, if you want the route to be protected, as id you need to enter the name route.
        app_admin_product_import:
            parent: data_transfer # Here, specify parent in the permission tree.
            label: product_import # Here, specify the name that will be displayed in the admin panel.
            enabled: true # Here you specify whether the permission is to be active, this field is not required, by default is set to true.
```

You can also add permission while defining the route. However, this will not work when you have defined or imported permissions with the same id in the `config.yml`:

```yaml
# config/routes/sylius_admin.yaml
# ...

app_admin_product_import:
    path: /admin/products/import
    methods: [GET]
    defaults:
        _sylius_plus_rbac:
            parent: data_transfer
            label: product_import
            enabled: true
```

For this permission you will need to add translations:

```xml
sylius_plus.rbac.parent.data_transfer
sylius_plus.rbac.action.product_import
```

**How to modify permission?**

If you would like to modify an existing permission of for example the permission to payment complete:

```yaml
# config/packages/_sylius.yaml
# ...

sylius_plus:
    permissions:
        sylius_admin_order_payment_complete:
            parent: orders_shop
            label: order_payment_complete
```

You can also modify the permission if the route is overwritten, only this will not work when you have defined or imported permissions with the same `id` in config.yml:

```yaml
# config/routes/sylius_admin.yaml
# ...

sylius_admin_order_payment_complete:
    path: /admin/orders/{orderId}/payments/{id}/complete
    methods: [PUT]
    defaults:
        # ...

        _sylius_plus_rbac:
            parent: orders_shop
            label: order_payment_complete
```

You can find the default configuration of some permissions in the `src/Resources/config/permissions.yaml` file.

**How to delete permission?**

If you want to remove a permission, you have to overwrite the permission configuration and and set the enabled field to false:

```yaml
# config/packages/_sylius.yaml
# ...

sylius_plus:
    permissions:
        sylius_admin_order_payment_complete:
            enabled: false
```

or for overwriting a route, although this will not work when you have defined or imported permissions with the same `id` in the `config.yml`:

```yaml
# config/routes/sylius_admin.yaml
# ...

sylius_admin_order_payment_complete:
    path: /admin/orders/{orderId}/payments/{id}/complete
    methods: [PUT]
    defaults:
        # ...

        _sylius_plus_rbac:
            enabled: false
```

**“Access denied” view customization**

When an administrator does not have access to a given route, the Twig’s `path()` and `url()` functions will return `ACCESS_DENIED`. You can adjust the view using the CSS and JavaScript selectors. For example:

```css
a[href="ACCESS_DENIED"].button {
   display: none !important;
}
```

More examples can be found in the `src/Resources/public/*` path.

You can also use a twig function:

```
{% raw %}
{% if sylius_plus_rbac_has_permission("sylius_admin_order_payment_complete") %}
    {# ... #}

{% endif %}
{% endraw %}
```

**Administrators per Channel**

It is possible to choose a channel to which an Administrator has access. It is done on the Administrator’s configuration page. If a channel is not chosen for an Administrator then they will have access to all channels.

Having chosen a channel on an Administrator, their access will get restricted within the Sales section of the main menu in the Admin Panel. Thus they will see only orders, payments, shipments, return requests, invoices and credit memos from the channel they have access to.

<div data-full-width="false"><figure><img src="../../.gitbook/assets/sylius-docs-plusfeature-end.png" alt=""><figcaption></figcaption></figure></div>

### Additional Admin User Fixtures

Three new fields have been added to the Admin User fixtures: `channel`, `roles` and `enable_permission_checker`. They can be configured as below:

```yaml
sylius_fixtures:
    suites:
        default:
            fixtures:
                channel:
                    options:
                        custom:
                             -   email: 'sylius@example.com'
                                 username: 'sylius'
                                 password: 'sylius'
                                 channel: 'DEFAULT_CHANNEL_CODE'
                                 roles:
                                     - 'SUPER_ADMIN_CODE'
                                 enable_permission_checker: true
```
