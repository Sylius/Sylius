# How to add a custom shipping calculator?

Sylius comes with several built-in shipping fee calculators like **flat rate** or **per unit**, but in real-world projects, you often need more domain-specific logic.

This guide walks you through building a **custom shipping calculator** that multiplies the total shipment weight by a per-channel shipping rate.

***

## Use Case

We want to define shipping prices that scale with the total weight of the shipment. For example, if:

* A product weighs 10 (unit-less),
* The per-weight unit rate is $10.00 for a channel,
* Then the shipment cost = `10 × $10 = $100`.

***

## 1. **Create the Calculator Class**

```php
<?php

// src/Shipping/Calculator/WeightBasedRateCalculator.php

namespace App\Shipping\Calculator;

use Sylius\Component\Core\Exception\MissingChannelConfigurationException;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Webmozart\Assert\Assert;

final class WeightBasedRateCalculator implements CalculatorInterface
{
    public function calculate(ShipmentInterface $subject, array $configuration): int
    {
        Assert::isInstanceOf($subject, \Sylius\Component\Core\Model\ShipmentInterface::class);

        $channelCode = $subject->getOrder()->getChannel()->getCode();

        if (!isset($configuration[$channelCode])) {
            throw new MissingChannelConfigurationException(sprintf(
                'Channel %s has no amount defined for shipping method %s',
                $subject->getOrder()->getChannel()->getName(),
                $subject->getMethod()->getName(),
            ));
        }

        $rate = (int) $configuration[$channelCode]['amount'];
        $totalWeight = array_sum(array_map(
            fn($unit) => $unit->getShippable()->getWeight(),
            iterator_to_array($subject->getUnits())
        ));

        return $rate * $totalWeight;
    }

    public function getType(): string
    {
        return 'weight_based_rate';
    }
}
```

***

## 2. **Register the Calculator as a Service**

```yaml
# config/services.yaml

services:
    sylius.calculator.shipping.weight_based_rate:
        class: App\Shipping\Calculator\WeightBasedRateCalculator
        tags:
            - { name: sylius.shipping_calculator, calculator: weight_based_rate, form_type: App\Form\Type\Shipping\Calculator\ChannelBasedWeightRateConfigurationType, label: sylius.form.shipping_calculator.weight_based_rate.label }
```

***

## 3. **Create the Configuration Form Type**

```php
<?php

// src/Form/Type/Shipping/Calculator/ChannelBasedWeightRateConfigurationType.php

namespace App\Form\Type\Shipping\Calculator;

use Sylius\Bundle\CoreBundle\Form\Type\ChannelCollectionType;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ChannelBasedWeightRateConfigurationType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'entry_type' => WeightRateConfigurationType::class,
            'entry_options' => fn (ChannelInterface $channel): array => [
                'label' => $channel->getName(),
                'currency' => $channel->getBaseCurrency()->getCode(),
            ],
        ]);
    }

    public function getParent(): string
    {
        return ChannelCollectionType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_channel_based_shipping_calculator_weight_based_rate';
    }
}
```

```php
<?php

// src/Form/Type/Shipping/Calculator/WeightRateConfigurationType.php

namespace App\Form\Type\Shipping\Calculator;

use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class WeightRateConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('amount', MoneyType::class, [
            'label' => 'sylius.ui.amount',
            'currency' => $options['currency'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults(['data_class' => null])
            ->setRequired('currency')
            ->setAllowedTypes('currency', 'string');
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_shipping_calculator_weight_rate';
    }
}
```

***

## 4. **Register the Form Type**

```yaml
# config/services.yaml

services:
    sylius.form.type.shipping.calculator.channel_based_weight_based_rate_configuration:
        class: App\Form\Type\Shipping\Calculator\ChannelBasedWeightRateConfigurationType
        tags: ['form.type']
```

***

## 5. **Add translations**

```yaml
# translations/messages.en.yaml

sylius:
    form:
        shipping_calculator:
            weight_based_rate:
                label: 'Rate per weight unit'
```

***

## Example Setup

* Product "Adventurous Aurora Cap" has a **weight of 10**.

<figure><img src="../.gitbook/assets/image (1) (1).png" alt=""><figcaption></figcaption></figure>

* Shipping method **"UPS"** uses the new `Rate per weight unit` calculator. \
  Admin sets **$10.00 per weight unit** for the channel.

<figure><img src="../.gitbook/assets/image (4) (1).png" alt=""><figcaption></figcaption></figure>

{% hint style="info" %}
💡 The `amount` is defined in the smallest currency unit (e.g., cents for USD/EUR).\
If you configure $10.00, Sylius stores it as `1000`.
{% endhint %}

* At checkout, the shipping cost is calculated as `10 × $10 = $100`.

<figure><img src="../.gitbook/assets/image (3) (1).png" alt=""><figcaption></figcaption></figure>

***

{% hint style="success" %}
### 🧠 Notes

* The `weight` field is unit-less by default. You can treat it as grams, kilograms, or custom units—just be consistent.
* calculator supports per-channel configuration.
* You can extend it to use tiered pricing (e.g., weight ranges) if needed.
{% endhint %}
