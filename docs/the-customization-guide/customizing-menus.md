# Customizing Menus

Sylius provides various menus across its Shop and Admin interfaces — from customer account navigation to product form tabs and button groups. In Sylius 2.0, menu customization is handled in two main ways:

* [**Twig hooks**](customizing-templates.md) – the recommended and default approach for most Admin “action button” menus
* **Event listeners** – still available for structural menus such as sidebars and tabs

This guide walks you through customizing each type.

**Path**: `/admin/`\
**Customization method**: Event listener\
Add new items or groups to the expandable navigation menu on the left side of the admin panel.

To add items to the Admin panel menu, use the `sylius.menu.admin.main` event.

{% hint style="success" %}
**🧠 Using Icons**\
Sylius uses Symfony UX Icons to render scalable SVG icons from the Tabler Icons set. You’ll see these used in menu customization examples below via the `ux_icon()` Twig helper:

```
{{ ux_icon('tabler:plus', { class: 'icon' }) }}  
```

Icons can be styled using custom CSS or Tailwind utility classes and embedded in any Twig template to enhance admin or shop UI elements. For more details, see the [Symfony UX Icons documentation](https://ux.symfony.com/icons?set=tabler\&query=).
{% endhint %}

#### Step 1. Create a listener

```php
<?php

namespace App\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $newSubmenu = $menu
            ->addChild('new')
            ->setLabel('Custom Admin Menu')
        ;

        $newSubmenu
            ->addChild('new-subitem')
            ->setLabel('Custom Admin Menu Item')
        ;
    }
}
```

#### Step 2. Register your listener

<pre class="language-yaml"><code class="lang-yaml"># config/services.yaml
services:
<strong>    app.listener.admin.menu_builder:
</strong>        class: App\Menu\AdminMenuListener
        tags:
            - { name: kernel.event_listener, event: sylius.menu.admin.main, method: addAdminMenuItems }
</code></pre>

After these steps, your custom menu item will appear at the bottom of the admin menu.

<figure><img src="../.gitbook/assets/Screenshot 2025-05-09 at 09.54.18.png" alt="" width="279"><figcaption></figcaption></figure>

### Admin Customer Show Menu

**Path**: `/admin/customers/{id}`\
**Customization method**: Twig hook\
Add action buttons (e.g. “Create Customer”) to the top-right corner of the customer detail view.

In Sylius 2.0, the customer show menu uses a Twig hook: `sylius_admin.customer.show.content.header.title_block.actions`.

#### Step 1. Configure your new hook for the custom action

```yaml
# config/packages/_sylius.yaml

sylius_twig_hooks:
    hooks:
        'sylius_admin.customer.show.content.header.title_block.actions':
            create:
                template: 'admin/customer/show/content/header/title_block/actions/create.html.twig'
```

#### Step 2. Add hook content

```twig
{# templates/admin/customer/show/content/header/title_block/actions/create.html.twig #}

<a href="{{ path('sylius_admin_customer_create') }}" class="dropdown-item">
    {{ ux_icon(action.icon|default('tabler:plus'), {'class': 'icon dropdown-item-icon'}) }}
    {{ 'sylius.ui.create'|trans }}
</a>
```

<figure><img src="../.gitbook/assets/Screenshot 2025-05-09 at 12.19.13.png" alt="" width="264"><figcaption></figcaption></figure>

### Admin Order Show Menu

**Path**: `/admin/orders/{id}`\
**Customization method**: Twig hook\
Add action buttons like “Complete Payment” to the order detail view.

This menu is built with a Twig hook: `sylius_admin.order.show.content.header.title_block.actions`.

#### Step 1.  Configure your new hook for the custom action

```yaml
# config/packages/_sylius.yaml

sylius_twig_hooks:
    hooks:
        'sylius_admin.order.show.content.header.title_block.actions':
            complete_payment:
                template: 'admin/order/show/content/header/title_block/actions/complete_payment.html.twig'
```

#### Step 2. Add hook content

```twig
{# templates/admin/order/show/content/header/title_block/actions/complete_payment.html.twig #}
{% set order = hookable_metadata.context.resource %}
{% set payment = order.getPayments.first %}

{% if sylius_sm_can(payment, constant('Sylius\\Component\\Payment\\PaymentTransitions::GRAPH'), constant('Sylius\\Component\\Payment\\PaymentTransitions::TRANSITION_COMPLETE')) %}
    <form action="{{ path('sylius_admin_order_payment_complete', {'orderId': order.id, 'id': payment.id}) }}" method="POST" novalidate>
        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" name="_csrf_token" value="{{ csrf_token(payment.id) }}" />
        <button type="submit" class="dropdown-item" {{ sylius_test_html_attribute('complete-payment', payment.id) }}>
            {{ ux_icon('tabler:check', {'class': 'icon dropdown-item-icon'}) }}
            {{ 'sylius.ui.complete'|trans }}
        </button>
    </form>
{% endif %}
```

<figure><img src="../.gitbook/assets/Screenshot 2025-05-09 at 10.22.44.png" alt="" width="375"><figcaption></figcaption></figure>

***

### Admin Product Form Tabs

**Path**: `/admin/products/new` and `/admin/products/{id}/edit`\
**Customization method**: Twig hook\
Add custom tabs and form sections (e.g. for manufacturer details) to the product creation and edit forms.

This menu is built with the following Twig hooks:&#x20;

`/admin/products/new` :

* `sylius_admin.product.create.content.form.side_navigation`
* `sylius_admin.product.create.content.form.sections`

`/admin/products/{id}/edit`:

* `sylius_admin.product.update.content.form.side_navigation`
* `sylius_admin.product.update.content.form.sections`

#### Step 1. Configure your new hook for the custom tab

```yaml
# config/packages/_sylius.yaml

sylius_twig_hooks:
    hooks:
        'sylius_admin.product.create.content.form.side_navigation':
            manufacturer:
                template: 'admin/product/form/side_navigation/manufacturer.html.twig'

        'sylius_admin.product.create.content.form.sections':
            manufacturer:
                template: 'admin/product/form/sections/manufacturer.html.twig'

        'sylius_admin.product.update.content.form.side_navigation':
            manufacturer:
                template: 'admin/product/form/side_navigation/manufacturer.html.twig'
        
        'sylius_admin.product.update.content.form.sections':
            manufacturer:
                template: 'admin/product/form/sections/manufacturer.html.twig'
```

{% hint style="info" %}
&#x20;The `template` attribute should point to the file rendering your additional form fields.
{% endhint %}

#### Step 2. Create templates for your tab (side\_navigation) and content (sections)

```twig
{# templates/admin/product/form/side_navigation/manufacturer.html.twig #}

<button
    type="button"
    class="list-group-item list-group-item-action {% if hookable_metadata.configuration.active|default(false) %}active{% endif %}"
    data-bs-toggle="tab"
    data-bs-target="#product-manufacturer"
    role="tab"
>
    Manufacturer
</button>
```

```twig
{# templates/admin/product/form/sections/manufacturer.html.twig #}

<div class="tab-pane {% if hookable_metadata.configuration.active|default(false) %}show active{% endif %}" id="product-manufacturer" role="tabpanel" tabindex="0">
    <div class="card mb-3">
        <div class="card-header">
            <div class="card-title">
                Manufacturer
            </div>
        </div>
        <div class="card-body">
            <div class="tab-content">
                Manufacturer content
            </div>
        </div>
    </div>
</div>
```

<figure><img src="../.gitbook/assets/Screenshot 2025-05-09 at 11.26.50.png" alt=""><figcaption></figcaption></figure>

***

### Admin Product Variant Form Tabs

**Path**: `/admin/products/{productId}/variants/create` and `/admin/products/{productId}/variants/{id}/edit`\
**Customization method**: Twig hook\
Add extra tabs for variants, such as a custom media section.

This menu is built with the following Twig hooks:&#x20;

* `/admin/products/{productId}/variants/create` :
  * `sylius_admin.product_variant.create.content.form.side_navigation`
  * `sylius_admin.product_variant.create.content.form.sections`
* `/admin/products/{productId}/variants/{id}/edit`:
  * `sylius_admin.product_variant.update.content.form.side_navigation`
  * `sylius_admin.product_variant.update.content.form.sections`

#### Step 1. Configure your new hook for the custom tab

```yaml
# config/packages/_sylius.yaml

sylius_twig_hooks:
    hooks:
        'sylius_admin.product_variant.create.content.form.sections':
            custom_media:
                template: 'admin/product_variant/form/sections/custom_media.html.twig'

        'sylius_admin.product_variant.create.content.form.side_navigation':
            custom_media:
                template: 'admin/product_variant/form/side_navigation/custom_media.html.twig'

        'sylius_admin.product_variant.update.content.form.sections':
            custom_media:
                template: 'admin/product_variant/form/sections/custom_media.html.twig'
                
        'sylius_admin.product_variant.update.content.form.side_navigation':
            custom_media:
                template: 'admin/product_variant/form/side_navigation/custom_media.html.twig'
```

#### Step 2. Create templates for your tab (side\_navigation) and content (sections)

```twig
{# templates/admin/product_variant/form/side_navigation/custom_media.html.twig #}

<button
    type="button"
    class="list-group-item list-group-item-action {% if hookable_metadata.configuration.active|default(false) %}active{% endif %}"
    data-bs-toggle="tab"
    data-bs-target="#product-variant-custom-media"
    role="tab"
>
    Custom Media
</button>
```

```twig
{# templates/admin/product_variant/form/sections/custom_media.html.twig #}

<div class="tab-pane {% if hookable_metadata.configuration.active|default(false) %}show active{% endif %}" id="product-variant-custom-media" role="tabpanel" tabindex="0">
    <div class="card mb-3">
        <div class="card-header">
            <div class="card-title">
                Custom Media
            </div>
        </div>
        <div class="card-body">
            <div class="tab-content">
                Custom Media content
            </div>
        </div>
    </div>
</div>
```

<figure><img src="../.gitbook/assets/Screenshot 2025-05-09 at 11.47.33.png" alt=""><figcaption></figcaption></figure>

### Shop Account Menu

**Path**: `/account/dashboard/`\
**Customization method**: Event listener\
Add a custom entry to the customer panel’s sidebar navigation.

#### Step 1. Create a listener

```php
<?php

namespace App\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AccountMenuListener
{
    public function addAccountMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $menu
            ->addChild('new', ['route' => 'sylius_shop_account_dashboard'])
            ->setLabel('Custom Account Menu Item')
            ->setLabelAttribute('icon', 'tabler:star')
        ;
    }
}
```

{% hint style="info" %}
In Sylius 2.0, menu icons use the [Tabler icon set](https://tabler-icons.io/), not Semantic UI.
{% endhint %}

#### Step 2. Register your listener

```yaml
# config/services.yaml
services:
    app.listener.shop.menu_builder:
        class: App\Menu\AccountMenuListener
        tags:
            - { name: kernel.event_listener, event: sylius.menu.shop.account, method: addAccountMenuItems }
```

<figure><img src="../.gitbook/assets/Screenshot 2025-05-09 at 09.55.53.png" alt="" width="375"><figcaption></figcaption></figure>

***

### Summary

If you're upgrading from Sylius 1.x and your menu customization is not working, check whether you're using a deprecated event and replace it with the appropriate Twig hook.
