# How to customize catalog promotion labels?

Sylius provides flexible ways to display and style catalog promotion labels on your storefront. You can:

* Change the labels
* Customize them using Twig hooks

This guide walks you through customizing the labels separately for:

* **Product Show Page**
* **Product Index Page (Product Cards)**

***

## **Product Show Page Customization**

To display and customize catalog promotion labels on the product show page:

### **1. Inspect the Element to Find Hook Definitions**

<figure><img src="../.gitbook/assets/image (46).png" alt=""><figcaption></figcaption></figure>

The catalog promotion labels are rendered via the following Twig hook:

```
sylius_shop.product.show.content.info.summary.catalog_promotions 
```

### **2. Create the Custom Template**

Once you’ve identified the hook, create a custom Twig template for how the labels should appear:

```twig
{# templates/shop/product/show/content/info/summary/catalog_promotions.html.twig #}

{% set variant = hookable_metadata.context.variant|default(null) %}
{% set applied_promotions = hookable_metadata.context.appliedPromotions|default({}) %}
{% set with_description = hookable_metadata.context.withDescription|default(false) %}

{% if variant is not null %}
    {% set applied_promotions = variant.getChannelPricingForChannel(sylius.channel).getAppliedPromotions() %}
    {% set with_description = true %}

    <div class="bg-light border rounded p-3 mt-4" data-applied-promotions-locale="{{ sylius.localeCode }}" {{ sylius_test_html_attribute('applied-catalog-promotions') }}>
        <h6 class="text-muted mb-3">Catalog Promotions</h6>
        <div class="d-flex flex-column gap-2">
            {% for applied_promotion in applied_promotions %}
                <span class="btn btn-sm btn-success text-white w-fit" data-test-promotion-label>
                    {{ applied_promotion.label }}
                </span>
            {% endfor %}
        </div>
    </div>
{% endif %}
```

### **3. Configure the Twig Hook**

To ensure Sylius uses your new template, register the template:

```yaml
# config/packages/_sylius.yaml

sylius_twig_hooks:
    hooks:
        'sylius_shop.product.show.content.info.summary':
            catalog_promotions:
                template: 'shop/product/show/content/info/summary/catalog_promotions.html.twig'
                priority: 200
```

### ✅ Result

<figure><img src="../.gitbook/assets/image (43).png" alt=""><figcaption></figcaption></figure>

After implementing these changes, the catalog promotion labels will be displayed with your custom template on the product show page.

***

## Product Index Page

Inspect a product card (e.g., on the category or search result page).

### **1. Inspect the Element to Find Hook Definitions**

<figure><img src="../.gitbook/assets/image (44).png" alt=""><figcaption></figcaption></figure>

The catalog promotion labels are rendered via the following hook:

```
sylius_shop.shared.product.card.prices.catalog_promotions
```

Under this hook, you'll see the component name:

```
sylius_shop:catalog_promotions
```

### 2. Create the Template for Index Page Labels

Due to Sylius using **anonymous Twig components** for product cards, you must override them via a component name:

```twig
{# templates/components/catalog_promotions.html.twig #}

{% if variant is not null %}
    {% set applied_promotions = variant.getChannelPricingForChannel(sylius.channel).getAppliedPromotions() %}
    {% set with_description = true %}

    <div data-applied-promotions-locale="{{ sylius.localeCode }}">
        {% for applied_promotion in applied_promotions %}
            <div class="mb-3">
                <div class="badge bg-warning text-dark" style="transform: translateY(-1px)" data-test-promotion-label>{{ applied_promotion.label }}</div>
                {% if applied_promotion.description and with_description %}<small class="text-success">{{ applied_promotion.description }}</small>{% endif %}
            </div>
        {% endfor %}
    </div>
{% endif %}
```

### 3. Configure the Hook for the Product Index Page

Define your custom component by template name defined in `templates/components` directory:

```yaml
# config/packages/_sylius.yaml

sylius_twig_hooks:
    hooks:
        'sylius_shop.shared.product.card.prices':
            catalog_promotions:
                component: 'catalog_promotions'
                priority: 200
```

### ✅ Result

<figure><img src="../.gitbook/assets/image (45).png" alt=""><figcaption></figcaption></figure>

After implementing these changes, the catalog promotion labels will be displayed with your custom template on the product index page.

{% hint style="warning" %}
Anonymous components cannot be overridden with a `template:` key. Instead, use `component:` with the filename (without the `.html.twig` extension). Find out more about anonymous components [here](https://symfony.com/bundles/ux-twig-component/current/index.html#anonymous-components)
{% endhint %}
