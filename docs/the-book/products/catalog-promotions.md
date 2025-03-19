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

# Catalog Promotions

The **Catalog Promotions** system in Sylius offers a powerful way to apply promotions across multiple products at once. If you're familiar with [**Cart Promotions**](../carts-and-orders/cart-promotions.md), Catalog Promotions will feel familiar, but they apply directly to products in your catalog, rather than at checkout.

Catalog Promotions are managed through a combination of **scopes** and **actions**:

* **Scopes** define which products (e.g., by taxon or specific product variants) the promotion applies to.
* **Actions** determine what happens when the promotion is applied, such as a percentage discount.

You can also:

* Set **start and end dates** for Catalog Promotions.
* Assign **priority** (with higher-priority promotions applied first).
* Set a promotion as **exclusive**, ensuring only one promotion is applied when multiple promotions could apply.

Additionally, promotions can be assigned to specific **channels**.

{% hint style="warning" %}
Applying promotions to a large catalog may take some time to process. Expect a delay of 2-10 minutes starting from the specified activation time, depending on the size of your catalog.
{% endhint %}

## Catalog Promotion Parameters

A **Catalog Promotion** has a few key parameters, including a unique **code** and a **name**:

```json
{
    "code": "t_shirt_promotion" // unique
    "name": "T-shirt Promotion"
    // ...
}
```

The **code** should contain only letters, numbers, dashes, and underscores (like all Sylius codes). Using snake\_case for codes is recommended.

### **Channels**

You can specify which **channels** the promotion applies to:

```json
{
    //...
    "channels": [
        "/api/v2/admin/channels/FASHION_WEB", //IRI
        "/api/v2/admin/channels/HOME_WEB"
    ]
    // ...
}
```

### **Scopes**

**Scopes** define the range of products affected by the promotion. For example, you can apply a promotion to specific product variants:

```json
{
    //...
    "scopes": [
        {
            "type": "for_variants",
            "configuration": {
                "variants": [
                    "Everyday_white_basic_T_Shirt-variant-1", //Variant Code
                    "Everyday_white_basic_T_Shirt-variant-4"
                ]
            }
        }
    ]
    // ...
}
```

{% hint style="info" %}
Variant codes are used instead of IRIs because this is a configuration, not a relationship to another resource.
{% endhint %}

| Scope type     | Scope Configuration Array        |
| -------------- | -------------------------------- |
| `for_products` | `['products' => [$productCode]]` |
| `for_taxons`   | `['taxons' => [$taxonCode]]`     |
| `for_variants` | `['variants' => [$variantCode]]` |

### **Actions**

**Actions** define the behavior of the promotion, such as applying a percentage discount:

```json
{
    //...
    "actions": [
        {
            "type": "percentage_discount",
            "configuration": {
                "amount": 0.5 //float
            }
        }
    ]
    // ...
}
```

| Action type           | Action Configuration Array                       |
| --------------------- | ------------------------------------------------ |
| `fixed_discount`      | `[$channelCode => ['amount' => $amountInteger]]` |
| `percentage_discount` | `['amount' => $amountFloat]`                     |

### **Translations**

You can also add **translations** to provide labels and descriptions for different languages:

```json
{
    //...
    "translations": {
        "en_US": {
            "label": "Summer discount",
            "description": "The grass so green, the sun so bright. Life seems a dream, no worries in sight.",
            "locale": "en_US" //Locale Code
            }
        }
    }
    // ...
}
```

## How to create a Catalog Promotion?

To create a catalog promotion, you can either use the **API** or do it **programmatically**.

#### **API Creation**

First, authorize yourself as an admin (guests cannot create promotions), then call the `POST` endpoint to create a basic catalog promotion:

```bash
curl -X 'POST' \
  'https://hostname/api/v2/admin/catalog-promotions' \
  -H 'accept: application/ld+json' \
  -H 'Authorization: Bearer authorizationToken' \
  -H 'Content-Type: application/ld+json' \
  -d '{
    "code": "t_shirt_promotion",
    "name": "T-shirt Promotion"
    }'
```

If successful, you will receive a `201` status code, indicating that the promotion has been created with only a name and code.

#### **Programmatically**

You can also create a catalog promotion using Sylius' factory service:

```php
/** @var CatalogPromotionInterface $promotion */
$promotion = $this->container->get('sylius.factory.catalog_promotion')->createNew();

$promotion->setCode('t_shirt_promotion');
$promotion->setName('T-shirt Promotion');
```

However, both API and programmatically created promotions are not useful without additional configurations such as scopes and actions.

## Adding a Scope and Action to a Catalog Promotion

To make a Catalog Promotion functional, you need to add **scopes** and **actions**.

#### **API Extension**

Extend the previous `POST` request to include scope, action, and translation configurations:

```bash
curl -X 'POST' \
  'https://hostname/api/v2/admin/catalog-promotions' \
  -H 'accept: application/ld+json' \
  -H 'Authorization: Bearer authorizationToken' \
  -H 'Content-Type: application/ld+json' \
  -d '{
    "code": "t_shirt_promotion",
    "name": "T-shirt Promotion",
    "channels": [
        "/api/v2/admin/channels/FASHION_WEB"
    ],
    "scopes": [
        {
          "type": "for_variants",
          "configuration": {
            "variants": ["Everyday_white_basic_T_Shirt-variant-1", "Everyday_white_basic_T_Shirt-variant-4"]
          }
        }
    ],
    "actions": [
        {
          "type": "percentage_discount",
          "configuration": {
            "amount": 0.5
          }
        }
    ],
    "translations": {
        "en_US": {
          "label": "T-shirt Promotion",
          "description": "T-shirt Promotion description",
          "locale": "en_US"
        }
    }'
```

This will create a Catalog Promotion with:

* Scope: targeting specific product variants.
* Action: applying a 50% discount.
* Translation: localized label and description.

#### **Programmatically**

Here’s how to configure a Catalog Promotion programmatically:

```php
/** @var CatalogPromotionInterface $catalogPromotion */
$catalogPromotion = $this->container->get('sylius.factory.catalog_promotion')->createNew();
$catalogPromotion->setCode('t_shirt_promotion');
$catalogPromotion->setName('T-shirt Promotion');

$catalogPromotion->setCurrentLocale('en_US');
$catalogPromotion->setFallbackLocale('en_US');
$catalogPromotion->setLabel('T-shirt Promotion');
$catalogPromotion->setDescription('T-shirt Promotion description');

$catalogPromotion->addChannel('FASHION_WEB');

/** @var CatalogPromotionScopeInterface $catalogPromotionScope */
$catalogPromotionScope = $this->container->get('sylius.factory.catalog_promotion_scope')->createNew();
$catalogPromotionScope->setCatalogPromotion($catalogPromotion);
$catalogPromotion->addScope($catalogPromotionScope);

/** @var CatalogPromotionActionInterface $catalogPromotionAction */
$catalogPromotionAction = $this->container->get('sylius.factory.catalog_promotion_action')->createNew();
$catalogPromotionAction->setCatalogPromotion($catalogPromotion);
$catalogPromotion->addAction($catalogPromotionAction);

/** @var MessageBusInterface $eventBus */
$eventBus = $this->container->get('sylius.event_bus');
$eventBus->dispatch(new CatalogPromotionCreated($catalogPromotion->getCode()));
```

{% hint style="info" %}
When creating a promotion programmatically, remember to dispatch the `CatalogPromotionCreated` event to ensure it is applied to your catalog.
{% endhint %}

## Asynchronous vs. Synchronous Processing of Catalog Promotions

**Catalog Promotions** are processed asynchronously by default, meaning changes may take some time to reflect in the product catalog. This delay depends on the size of the catalog.

To enable asynchronous processing, run the following command to start the messenger consumer:

```bash
php bin/console messenger:consume main catalog_promotion_removal
```

**Synchronous Processing**

If you prefer **synchronous processing** (immediate application of promotions), you can override the configuration by setting up the `config/packages/messenger.yaml` file:

```
framework:
    messenger:
        transports:
            main: 'sync://'
```

{% hint style="warning" %}
Synchronous processing is only recommended for small catalogs, as it may degrade user experience with larger data sets.
{% endhint %}

## How Catalog Promotions Are Applied

When a new promotion is created or updated, the system triggers **API Platform events** (for API) and **Resource events** (for UI). These events dispatch the `CatalogPromotionCreated` or `CatalogPromotionUpdated` events to the event bus.

The **CatalogPromotionUpdateListener** listens for these events and recalculates the product catalog using the updated promotion data. Recalculation across the entire product catalog is handled by the `BatchedApplyCatalogPromotionsOnVariantsCommandDispatcher`, which applies the promotions to all relevant product variants.

{% hint style="info" %}
To manually reapply a catalog promotion, refer to the "Adding a Scope and Action to a Catalog Promotion" section.
{% endhint %}

## Removal of Catalog Promotions

Removing a **Catalog Promotion** involves:

1. Turning off the promotion.
2. Recalculating the product catalog.
3. Deleting the promotion resource.

When using **asynchronous mode**, you'll need to start the worker for two transports:

* The **main transport** handles recalculating the catalog.
* The **catalog\_promotion\_removal transport** handles the removal of the promotion.

Here’s the command to start both processes:

```bash
php bin/console messenger:consume main catalog_promotion_removal
```

Each transport has its own **failure transport**, which stores any unprocessed messages. These transports are:

* **main\_failed** for the main transport.
* **catalog\_promotion\_removal\_failed** for the catalog promotion removal transport.

You can read more about handling failures in the Symfony Messenger documentation.

{% hint style="info" %}
For synchronous processing, no additional configuration is required.
{% endhint %}

## Managing Catalog Promotion Priority

Catalog promotions in Sylius must have unique priorities to ensure the correct application order. Here’s how priority is handled in different scenarios:

**Creating a New Catalog Promotion**

* **Higher priority**: Adding a promotion with a priority higher than all existing promotions does not affect the others.
* **Lower priority**: Adding a promotion with a lower priority increases the priority of all existing promotions by 1.
* **Equal priority**: Adding a promotion with the same priority as an existing one increases the priority of all promotions with equal or higher priority by 1.
* **Priority of -1**: A priority of -1 assigns the new promotion a priority one greater than the current highest value.
* **Negative priorities (< -1)**: These start from the lowest existing priority, counting backward. If a calculated priority is already taken, all equal or higher priorities are increased by 1.

**Updating an Existing Catalog Promotion**

* Updating a promotion’s priority to match another promotion decreases the matching promotion’s priority by 1.
