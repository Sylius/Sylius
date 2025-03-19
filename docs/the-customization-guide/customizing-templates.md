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

# Customizing Templates

Sylius offers two primary template types: Shop and Admin. Furthermore, it allows you to create custom templates tailored to your specific needs, providing flexibility for unique requirements.

## Why Customize Templates?

The main reason for modifying existing templates is to implement a unique layout that aligns with your brand. Even if you use Sylius's default layout, minor adjustments may be necessary to meet specific business needs, such as adding a logo.

#### Template Customization Methods

There are three ways to customize Sylius templates:

1. **Using Twig Hooks (Recommended Approach)**\
   Twig Hooks provide a flexible and efficient way to customize templates. They allow you to insert custom blocks into predefined hooks without duplicating the entire template. This makes them ideal for creating plugins and ensuring compatibility with future updates. With Twig Hooks, you can also hide existing blocks, reorder elements, or adjust the layout effortlessly, maintaining clean and modular code.
2. **Overriding Templates (Not Recommended Approach)**\
   Template overriding involves placing modified templates in the `templates/bundles` directory of your project, following the Symfony convention. While this method allows full control over the template, it requires duplicating the original file, which can lead to maintenance challenges. This approach is particularly problematic in plugins, where multiple plugins might attempt to modify the same template. Such conflicts can result in unpredictable behavior, making updates and compatibility much harder to manage compared to the flexibility offered by Twig Hooks.
3. **Implementing Sylius Themes**\
   Themes provide different designs for multiple channels within a Sylius instance. Although it requires a few additional steps, themes offer enhanced flexibility.

## Customizing Templates via Twig Hooks

Twig Hooks is a robust and powerful feature that serves as an alternative to the Sonata Block Events and Sylius Template Events systems. In essence, Twig Hooks function as a hierarchical configuration system for defining template insertion points in Sylius. Each hook represents a specific location in the template where content can be injected. Hooks are organized in a tree-like structure, enabling clear modularity and prioritization of templates.

### Architecture

Twig hooks can be represented using the following simple graph:

<figure><img src="../.gitbook/assets/image (11).png" alt=""><figcaption></figcaption></figure>

* **Hook**: A designated insertion point within a template.
* **Hookable**: The content you want to insert into a specific hook. A single hook can include an unlimited number of hookables.

This implies that both hooks and hookables can contain template content that influences the visual aspects of a page. However, we recommend keeping hook templates as simple as possible. Whenever feasible, place the content inside hookables to maintain a clear separation of concerns and improve maintainability.

### Hooks and Hookables configurations

#### Basic information

* The configuration must be done using YAML.
* It is recommended to place your custom configurations in the `config/packages/sylius_twig_hooks.yaml` file, but it is not obligatory.
* The basic example configuration could look like that:

```yaml
sylius_twig_hooks:
    hooks:
        hook_name:
            first_hookable_name:
                template: 'custom_template.html.twig'
            second_hookable_name:
                component: 'app_shop:custom_component'
```

#### Affecting Existing Configuration

To add, modify, or remove content from a specific, existing page, you first need to identify the relevant configuration responsible for generating the visual content.\
We recommend exploring Sylius's internal configurations, available at the following links:

* [Shop Section Configuration](https://github.com/Sylius/Sylius/tree/2.0/src/Sylius/Bundle/ShopBundle/Resources/config/app/twig_hooks)
* [Admin Section Configuration](https://github.com/Sylius/Sylius/tree/2.0/src/Sylius/Bundle/AdminBundle/Resources/config/app/twig_hooks)

These links point to the respective Twig Hooks configurations for the shop and admin sections.

Another way to identify the correct hook or hookable name is to use the Symfony profiler directly on the page, specifically in the Twig Hooks menu.

#### Introducing New Hooks

If you expand your layout beyond the predefined Sylius templates and hook configurations, it is most likely that you will need to add new hooks. This can be done within a Twig template using a template expression:

<pre class="language-twig"><code class="lang-twig"><strong>
</strong>&#x3C;!-- Template content -->
&#x3C;div id="container">
    {% hook 'hook_name' %}
&#x3C;/div>
&#x3C;!-- Additional content -->
</code></pre>

Then you need to define the respective configuration, which would look something like this:

```yaml
sylius_twig_hooks:
    hooks:
        'hook_name':
            'first_hookable_name':
                template: 'hookable.html.twig'
```

Keep in mind that you now need to render the template with the hook\_name hook. Here are two important cases you should be aware of.

1. Hooks inherit their parent names and append their own. Thanks to this mechanism, it is not necessary to write the entire composite name in the hook. If the hook is fired inside another hookable, the configuration must reflect that. For instance:

```yaml
sylius_twig_hooks:
    hooks:
        'parent_hook_name': # We assume that an absolute template (meaning it is not rendered by the Twig Hooks template expression) with this hook name has already been rendered.
            'first_parent_hookable_name':
                template: 'parent_hookable.html.twig'
        'parent_hook_name.hook_name':
            'first_hookable_name':
                template: 'hookable.html.twig'
```

```twig
<!-- Template content -->
<div id="container">
    {% raw %}
{% hook 'hook_name' %}
{% endraw %} <!-- It’s still the same name, but it is configured as ‘parent_hook_name.hook_name’.-->
</div>
<!-- Additional content -->
```

2.  A hookable can also act as a hook, as long as its related template contains another hook

    expression. Think of it in terms of a tree-like structure: only the deepest level of hookables are purely hookables, which, in the context of a tree structure, we would refer to as leaves.

#### Hooks prefixing

Mechanizm described above can be controlled manually. If you need to set the hook name explicitly without merging ancesor's names:

```twig
<!-- Template content -->
<div id="container">
    {% raw %}
{% hook 'hook_name' with { _prefixes: ['custom_prefix_not_related_with_a_parent'] } %}
{% endraw %}
</div>
<!-- Additional content -->
```

And now the configuration should be like that:

```yaml
sylius_twig_hooks:
    hooks:
        'parent_hook_name':
            'first_parent_hookable_name':
                template: 'parent_hookable.html.twig'
        'custom_prefix_not_related_with_a_parent.hook_name':
            'first_hookable_name':
                template: 'hookable.html.twig'
```

### Components

{% hint style="info" %}
This concept relies on Symfony UX. To understand the ideas behind the following information, refer to:

* [Twig Component Documentation](https://ux.symfony.com/twig-component)
* [Live Component Documentation](https://ux.symfony.com/live-component)
{% endhint %}

Depending on the complexity of the view you are building or customizing, it is recommended to move the heavy logic behind it to the PHP code. This can be achieved by setting up the hookable as a Twig Component instead of using it as a simple template. If you have the Symfony Maker Bundle installed (refer to [Symfony Maker Bundle Documentation](https://symfony.com/bundles/SymfonyMakerBundle/current/index.html)), you can use the following command::

```bash
bin/console make:twig-component
```

provide a name and confirm. This will generate the class:&#x20;

```php
// src/Twig/Components/MyComponent.php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class MyComponent
{
}
```

And the corresponding template:&#x20;

<pre class="language-twig"><code class="lang-twig"><strong>{# templates/components/MyComponent.html.twig #}
</strong>&#x3C;div>
    &#x3C;!-- component html -->
&#x3C;/div>
</code></pre>

Now, instead of configuring the hookable with a template, use a component and it's ID:

```yaml
sylius_twig_hooks:
    hooks:
        'my_hook':
            'my_hookable':
                component: 'MyComponent'
```

If you're unsure how to find the component name you need to set, run the following command:

```bash
bin/console debug:twig-component
```

### Priority

It is possible to change the order of elements without rewriting the configuration. This is especially useful when customizing Sylius or plugin templates:

```yaml
sylius_twig_hooks:
    hooks:
        hook_name:
            first_hookable_name:
                template: 'custom_template.html.twig'
                priority: 100
            second_hookable_name:
                component: 'app_shop:custom_component'
                priority: 0
```

Now, the `second_hookable_name` will be rendered before the `first_hookable_name`, even though it is defined as the successor in the configuration order.

As a rule of thumb in Sylius, each hookable is configured as a multiple of 100. This approach leaves enough room to insert new content in between.

### Insert new element

Let's say we'd like to customize our offer view and we'd like to add an information about estimated shipping time:

<figure><img src="../.gitbook/assets/image (1).png" alt=""><figcaption></figcaption></figure>

There're two way's of finding out the hook name you'd like to hook-in.

1. Profiler\
   By Clicking the hooks option you'll be taken to the specific profiler page

![](<../.gitbook/assets/image (4).png>)\


Here, you have whole Call Graph regarding opened view, where you can search for specific element.

<figure><img src="../.gitbook/assets/image (6).png" alt=""><figcaption></figcaption></figure>

2. Browser Developer Tools

Another way to find the right spot is to use the key combination `CTRL+SHIFT+C` on Windows/Linux or `CMD+SHIFT+C` on macOS. Then, click on the element closest to the location where you’d like to hook in:

<figure><img src="../.gitbook/assets/image (9).png" alt=""><figcaption></figcaption></figure>

Here are comments with the hook and hookable names that wrap specific layout elements. In our example, we examined the element containing the price, as we want to inject our custom element right below it. Using the browser console, we can easily identify all the necessary information:

* **Hook name**: `sylius_shop.product.show.content.info.summary`
* **Hookable name**: `sylius_shop.product.show.content.info.summary.prices`

To learn more about the current configuration, the easiest way is to check the Sylius code directly. This code can be found in two directories: one for the Admin section and another for the Shop section.

When searching for the `sylius_shop.product.show.content.info.summary` hook name in your project (including vendor files), you should find the following file, which defines both the hook and its hookables: [product/show.yaml on GitHub](https://github.com/Sylius/Sylius/blob/2.0/src/Sylius/Bundle/ShopBundle/Resources/config/app/twig_hooks/product/show.yaml#L35-L56)

```yaml
sylius_twig_hooks:
    hooks:
        'sylius_shop.product.show.content.info.summary':
            header:
                template: '@SyliusShop/product/show/content/info/summary/header.html.twig'
                priority: 500
            average_rating:
                template: '@SyliusShop/product/show/content/info/summary/average_rating.html.twig'
                priority: 400
            prices:
                template: '@SyliusShop/product/show/content/info/summary/prices.html.twig'
                priority: 300
            catalog_promotions:
                template: '@SyliusShop/product/show/content/info/summary/catalog_promotions.html.twig'
                priority: 200
            add_to_cart:
                component: 'sylius_shop:product:add_to_cart_form'
                props:
                    product: '@=_context.product'
                    template: '@SyliusShop/product/show/content/info/summary/add_to_cart.html.twig'
                priority: 100
            short_description:
                template: '@SyliusShop/product/show/content/info/summary/short_description.html.twig'
                priority: 0
                
```

Now we have all the necessary information to hook into the process. Open the `config/packages/sylius_twig_hooks.yaml` file and add the following configuration:

<pre class="language-yaml"><code class="lang-yaml"><strong>sylius_twig_hooks:
</strong>    hooks:
        'sylius_shop.product.show.content.info.summary':
            estimated_delivery_time:
                template: 'shop/estimated_delivery_time.html.twig'
</code></pre>

Next, create the template file and place it in the `templates/shop/estimated_delivery_time.html.twig` directory.

**Key points to note:**

* The `estimated_delivery_time` hookable name is an example; feel free to use a descriptive name that suits your purpose.
* The `shop` part in the path is included for organizational purposes and does not affect the application’s functionality.
* The template name does not need to match the hookable name, but it is good practice to use consistent names for clarity.
* All templates for your application should be placed in the `templates` directory, so the relative path for your new template will be `templates/shop/estimated_delivery_time.html.twig`.

Finally, warm up the cache. You should see the following result:

<figure><img src="../.gitbook/assets/image (10).png" alt=""><figcaption></figcaption></figure>

As you can see, this is not the expected result yet. This happens because we haven’t specified the priority, and by default, it is set to 0, as you can confirm in the developer console.

If you look closely, the `short_description` hookable also has a priority of 0. This behavior is due to the configuration merging order, where your application’s templates are merged last. However, you can override this by specifying the priority explicitly:

```
sylius_twig_hooks:
    hooks:
        'sylius_shop.product.show.content.info.summary':
            estimated_delivery_time:
                template: 'shop/estimated_delivery_time.html.twig'
                priority: 250
```

Warm up the cache again, and this time, the element should be placed in the correct spot:

<figure><img src="../.gitbook/assets/image (12).png" alt=""><figcaption></figcaption></figure>

## Customizing Templates via Overriding

To determine the template you need to override:

* Navigate to the desired page.
* In the Symfony toolbar, click on the route. The profiler will display the path in **Request Attributes** under `_sylius`.

**Shop Template Example: Login Page Customization**

* **Default login template**: `@SyliusShopBundle/login.html.twig`
* **Override path**: `templates/bundles/SyliusShopBundle/login.html.twig`

Copy the original template to your path and customize it as needed. Example:

```twig
{% raw %}
{% extends '@SyliusShop/layout.html.twig' %}
{% import '@SyliusUi/Macro/messages.html.twig' as messages %}

{% block content %}
<div class="ui column stackable center page grid">
    {% if last_error %}
        {{ messages.error(last_error.messageKey|trans(last_error.messageData, 'security')) }}
    {% endif %}
    <h1>This Is My Headline</h1>
    <div class="five wide column"></div>
    <form class="ui six wide column form segment" action="{{ path('sylius_shop_login_check') }}" method="post" novalidate>
        <div class="one field">
            {{ form_row(form._username, {'value': last_username|default('')}) }}
        </div>
        <div class="one field">
            {{ form_row(form._password) }}
        </div>
        <div class="one field">
            <button type="submit" class="ui fluid large primary submit button">{{ 'sylius.ui.login_button'|trans }}</button>
        </div>
    </form>
</div>
{% endblock %}
{% endraw %}
```

Clear the cache if changes aren't visible: `php bin/console cache:clear`

## Customizing Templates via Sylius Themes

{% hint style="info" %}
Read more in the [Themes documentation in The Book](../the-book/frondend-and-themes.md) and [the bundle's documentation on GitHub](https://github.com/Sylius/SyliusThemeBundle/blob/master/docs/index.md).
{% endhint %}

## Global Twig variables

Each of the Twig templates in Sylius is provided with the `sylius` variable, that comes from the [ShopperContext](https://github.com/Sylius/Sylius/blob/2.0/src/Sylius/Component/Core/Context/ShopperContext.php).

The **ShopperContext** is composed of `ChannelContext`, `CurrencyContext`, `LocaleContext` and `CustomerContext`. Therefore it has access to the current channel, currency, locale, and customer.

The variables available in Twig are:

| Twig variable       | ShopperContext method name |
| ------------------- | -------------------------- |
| sylius.channel      | getChannel()               |
| sylius.currencyCode | getCurrencyCode()          |
| sylius.localeCode   | getLocaleCode()            |
| sylius.customer     | getCustomer()              |

#### How to use these Twig variables?

You can check for example what is the current channel by dumping the `sylius.channel` variable.

```twig
{{ dump(sylius.channel) }}
```

That’s it, this will dump the content of the current Channel object.
