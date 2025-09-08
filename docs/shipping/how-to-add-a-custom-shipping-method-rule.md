# How to add a custom shipping method rule?

Shipping method rules in Sylius allow you to define conditions under which a shipping method is available. In this guide, you will learn how to create a custom rule that checks if a shipping method is eligible based on the total volume of the shipment.

#### Example Use Case

Let's say you want a shipping method that should only be available when the shipment volume does not exceed shipping volume. To implement this, you can create a custom shipping method rule.

***

## 1. Create a New Shipping Method Rule Checker

To create a custom rule, you need to implement a `RuleCheckerInterface` class. This class will define the logic to check if the shipping method is eligible based on the rule configuration.

**Example: `TotalVolumeLessThanOrEqualRuleChecker`**

```php
<?php

// src/Shipping/Checker/Rule/TotalVolumeLessThanOrEqualRuleChecker.php

namespace App\Shipping\Checker\Rule;

use Sylius\Component\Shipping\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

final class TotalVolumeLessThanOrEqualRuleChecker implements RuleCheckerInterface
{
    public const TYPE = 'total_volume_less_than_or_equal';

    public function isEligible(ShippingSubjectInterface $shippingSubject, array $configuration): bool
    {
        return $shippingSubject->getShippingVolume() <= $configuration['volume'];
    }
}
```

**What This Does:** The `isEligible()` method checks if the shipping subject’s volume is less than or equal to the configured volume value.

***

## 2. Prepare a Configuration Form Type for Your New Rule

The configuration for shipping method rules is handled via form types. You need to create a custom form type that will allow users to input the volume limit for the rule.

**Example: `TotalVolumeLessThanOrEqualConfigurationType`**

```php
<?php

// src/Form/Type/Rule/TotalVolumeLessThanOrEqualConfigurationType.php

namespace App\Form\Type\Rule;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class TotalVolumeLessThanOrEqualConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('volume', NumberType::class, [
            'constraints' => [
                new NotBlank(['groups' => ['sylius']]),
                new Type(['type' => 'numeric', 'groups' => ['sylius']]),
                new GreaterThan(['value' => 0, 'groups' => ['sylius']])
            ],
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_shipping_method_rule_total_volume_less_than_or_equal_configuration';
    }
}
```

**What This Does:** This form type creates a field for users to input the maximum shipping volume when configuring the shipping method in the admin interface. It includes validation to ensure the value is numeric and greater than 0.

***

## 3. Register the Rule Checker as a Service

To make your custom rule available within the Sylius shipping method configuration, you need to register the `RuleChecker` class as a service in your `services.yaml` file.

**Example: Registering the Rule Checker Service**

```yaml
# config/services.yaml
app.shipping_method_rule_checker.total_volume_less_than_or_equal:
    class: App\Shipping\Checker\Rule\TotalVolumeLessThanOrEqualRuleChecker
    tags:
        - { name: sylius.shipping_method_rule_checker, type: total_volume_less_than_or_equal, form_type: App\Form\Type\Rule\TotalVolumeLessThanOrEqualConfigurationType, label: app.form.shipping_method_rule.total_volume_less_than_or_equal }
```

**What This Does:** This configuration registers the `TotalVolumeLessThanOrEqualRuleChecker` as a service, making it available to Sylius. It also tags the service as a shipping method rule checker, providing the form type for the rule configuration and the label that will appear in the shipping method form.

***

## 4. Add Translations

For the admin interface to display the proper label for your custom rule, add the following translation entry.

**Example: Translations for the Rule Label**

```yaml
# translations/messages.en.yaml

app:
    form:
        shipping_method_rule:
            total_volume_less_than_or_equal: 'Total volume less than or equal'
```

**What This Does:** This ensures that the label for the custom rule is correctly displayed in the Sylius admin interface.

***

## 5. Configure the Product Variant to Not Meet the Requirements

To test the rule, configure a product variant with a shipping volume that does not meet the rule's requirements.

**Example: Setting a Shipping Volume**

* Edit the "Ocean Wave Jeans" product variant and set its properties. This will make it not meet the volume limit in the next step.

<figure><img src="../.gitbook/assets/image (51).png" alt=""><figcaption></figcaption></figure>

***

## 6. Configure the Shipping Method with a Maximum Volume <a href="#result-1" id="result-1"></a>

Next, configure the new shipping method rule to enforce the volume constraint.

**Example: Setting a Shipping Method**

* Set the shipping method (e.g., `DHL Express`) to have a maximum volume limit of 100.

<figure><img src="../.gitbook/assets/image (52).png" alt=""><figcaption></figcaption></figure>

***

### ✅ Result

<figure><img src="../.gitbook/assets/image (53).png" alt=""><figcaption></figcaption></figure>

After completing these steps, you will have successfully created a custom shipping method rule that only makes a shipping method available if the product's volume meets the specified conditions. In this case, the "Ocean Wave Jeans" product variant, with its volume set to 1000, will not be eligible for the `DHL Express` shipping method, which has a maximum volume of 100.

