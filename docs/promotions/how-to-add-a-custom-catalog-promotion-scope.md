# How to add a custom catalog promotion scope?

Catalog promotions in Sylius allow you to apply discounts automatically to selected product variants based on defined **scopes**. In this guide, we'll walk you through adding a custom scope that filters variants by checking if their name contains a specific phrase.

***

## 1. Implement a Custom Scope Checker

The scope checker determines if a given `ProductVariant` is within the scope.

### Register the Checker Service

```yaml
# config/services.yaml

services:
    App\CatalogPromotion\Checker\Variant\InByPhraseScopeChecker:
        arguments:
            - '@sylius.repository.product_variant'
        tags:
            - { name: 'sylius.catalog_promotion.variant_checker', type: 'by_phrase' }
```

> **Note:** The `type` in the tag must match your scope's type key. It connects your logic to the scope configuration.

### Create the Checker Class

```php
<?php

// src/CatalogPromotion/Checker/Variant/InByPhraseScopeChecker.php

namespace App\CatalogPromotion\Checker\Variant;

use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\VariantInScopeCheckerInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;
use Webmozart\Assert\Assert;

final class InByPhraseScopeChecker implements VariantInScopeCheckerInterface
{
    public const TYPE = 'by_phrase';

    public function inScope(CatalogPromotionScopeInterface $scope, ProductVariantInterface $productVariant): bool
    {
        $configuration = $scope->getConfiguration();
        Assert::keyExists($configuration, 'phrase');

        return str_contains($productVariant->getName(), $configuration['phrase']);
    }
}
```

Configure parameter with the correct scope type:

```yaml
# config/packages/_sylius.yaml

parameters:
    sylius.catalog_promotion.scope.by_phrase: !php/const App\CatalogPromotion\Checker\Variant\InByPhraseScopeChecker::TYPE
```

***

## 2. Create the Configuration Form Type

This allows admins to configure your custom scope via the UI.

### Form Type Class and Service

```php
<?php

// src/Form/Type/CatalogPromotionScope/ByPhraseScopeConfigurationType.php

namespace App\Form\Type\CatalogPromotionScope;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ByPhraseScopeConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('phrase', TextType::class, [
            'label' => 'Phrase',
            'constraints' => [
                new NotBlank(['groups' => ['sylius']]),
            ],
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_catalog_promotion_scope_by_phrase_configuration';
    }
}
```

### Register the form type:

```yaml
# config/services.yaml

services:
    App\Form\Type\CatalogPromotionScope\ByPhraseScopeConfigurationType:
        tags:
            - { name: 'sylius.catalog_promotion.scope_configuration_type', key: '%sylius.catalog_promotion.scope.by_phrase%' }
            - { name: 'sylius_admin.catalog_promotion.scope_configuration_type', key: '%sylius.catalog_promotion.scope.by_phrase%'}
            - { name: 'form.type' }
```

***

## 4. Translations

For the admin UI to show your scope’s label, define this key in your translation file:

```yaml
# translations/messages.en.yaml

sylius:
    ui:
        by_phrase: 'Phrase'
```

***

## 5. Custom Validation (Optional)

{% hint style="success" %}
**Tip**: For customizing validation rules (e.g., enforcing phrase constraints), refer to Sylius' [Custom Validation Guide](https://docs.sylius.com/the-customization-guide/customizing-validation#id-3.-custom-validation-for-special-cases-shippingmethod-promotions-zones).
{% endhint %}

***

### ✅ Result

<figure><img src="../.gitbook/assets/image (41).png" alt=""><figcaption></figcaption></figure>

<figure><img src="../.gitbook/assets/image (42).png" alt=""><figcaption></figcaption></figure>

You can now select **"By phrase"** as a scope type when creating or editing catalog promotions. All product variants whose names contain the given phrase will be eligible for the promotion.

{% hint style="warning" %}
If your catalog promotion is not active, make sure the Messenger worker is active:

<pre class="language-bash"><code class="lang-bash"><strong>php bin/console messenger:consume main
</strong></code></pre>
{% endhint %}
