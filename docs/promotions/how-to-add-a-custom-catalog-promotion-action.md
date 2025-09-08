# How to add a custom catalog promotion action?

Sylius offers flexible catalog promotions that allow dynamic product price adjustments per sales channel. This guide shows you how to create a custom catalog promotion action that sets a fixed price for products based on the channel. You will integrate the following components:

* Custom price calculator logic
* Admin form integration

By the end of this guide, you will have a fully integrated catalog promotion action within the **Sylius Admin Panel** and API.

***

## 1. Create the Price Calculator Service

The price calculator determines the new price for each product variant per channel using your custom logic.

### Service Registration

Register the price calculator service:

```yaml
# config/services.yaml
services:
    App\Calculator\FixedPriceCalculator:
        tags:
            - { name: 'sylius.catalog_promotion.price_calculator', type: 'fixed_price' }
```

### FixedPriceCalculator Class

Create the `FixedPriceCalculator` class to handle price calculations for each channel:

```php
<?php

// src/Calculator/FixedPriceCalculator.php

namespace App\Calculator;

use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\ActionBasedPriceCalculatorInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface as BaseAction;

final class FixedPriceCalculator implements ActionBasedPriceCalculatorInterface
{
    public const TYPE = 'fixed_price';

    public function supports(BaseAction $action): bool
    {
        return $action->getType() === self::TYPE;
    }

    public function calculate(ChannelPricingInterface $channelPricing, BaseAction $action): int
    {
        $config = $action->getConfiguration();
        $channelCode = $channelPricing->getChannelCode();
        if (empty($config[$channelCode]['price'])) {
            return $channelPricing->getPrice();
        }

        $price = (int) $config[$channelCode]['price'];
        $min = $channelPricing->getMinimumPrice() > 0 ? $channelPricing->getMinimumPrice() : 0;

        return max($price, $min);
    }
}
```

***

## 2. Build the Admin Configuration Form

This form allows admins to set fixed prices per channel.

### Service Registration

Register the admin form service:

```yaml
# config/services.yaml

services:
    App\Form\Type\CatalogPromotionAction\ChannelBasedFixedPriceActionConfigurationType:
        tags:
            - { name: 'sylius.catalog_promotion.action_configuration_type', key: 'fixed_price' }
            - { name: 'form.type' }
```

### FixedPriceConfigurationType Form

This form enables the admin to set a fixed price for each channel:

```php
<?php

// src/Form/Type/FixedPriceConfigurationType.php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThan;

final class FixedPriceConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('price', MoneyType::class, [
                'label' => 'Price',
                'currency' => $options['currency'],
                'constraints' => [
                    new NotBlank(['message' => 'Price must be set']),
                    new GreaterThan(['value' => 0, 'message' => 'Price must be greater than 0']),
                ],
            ]);
    }

    public function configureOptions($resolver): void
    {
        $resolver
            ->setRequired(['currency'])
            ->setAllowedTypes('currency', 'string');
    }

    public function getBlockPrefix(): string
    {
        return 'app_catalog_promotion_action_fixed_price_configuration';
    }
}
```

### ChannelBasedFixedPriceActionConfigurationType Form

This form handles the configuration of fixed prices for each channel:

```php
<?php

// src/Form/Type/CatalogPromotionAction/ChannelBasedFixedPriceActionConfigurationType.php

namespace App\Form\Type\CatalogPromotionAction;

use Symfony\Component\Form\AbstractType;
use Sylius\Bundle\CoreBundle\Form\Type\ChannelCollectionType;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ChannelBasedFixedPriceActionConfigurationType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'entry_type' => FixedPriceConfigurationType::class,
            'entry_options' => fn(ChannelInterface $channel) => [
                'label' => $channel->getName(),
                'currency' => $channel->getBaseCurrency()->getCode(),
            ],
        ]);
    }

    public function getParent(): string
    {
        return ChannelCollectionType::class;
    }
}
```

***

## 3. Configure Translations

Add the necessary translation labels for your custom action.

### Add Translation for Action Type

In the `translations/messages.en.yaml` file, define the label for your fixed price action:

```yaml
sylius:
    ui:
        fixed_price: 'Fixed Price'
```

***

## 4. Custom Validation (Optional)

{% hint style="success" %}
**Tip**: For customizing validation rules (e.g., enforcing price constraints), refer to Sylius' [Custom Validation Guide](https://docs.sylius.com/the-customization-guide/customizing-validation#id-3.-custom-validation-for-special-cases-shippingmethod-promotions-zones).
{% endhint %}

***

### ✅ Result

<figure><img src="../.gitbook/assets/image (48).png" alt=""><figcaption></figcaption></figure>

<figure><img src="../.gitbook/assets/image (49).png" alt=""><figcaption></figcaption></figure>

You can now select **"Fixed Price"** as a action type when creating or editing catalog promotions.&#x20;

{% hint style="warning" %}
If your catalog promotion is not active, make sure the Messenger worker is active:

```bash
php bin/console messenger:consume main
```
{% endhint %}
