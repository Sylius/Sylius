# How to add a custom cart promotion rule?

Adding custom promotion rules is a common need in real-world shops. For example, you might want to offer exclusive discounts to _premium customers_. To implement this, you'll create a new `PromotionRule` that checks whether a customer qualifies.

***

### 1. Create a New Promotion Rule Checker

Define a custom `RuleChecker` that determines rule eligibility:

```php
<?php

namespace App\Promotion\Checker\Rule;

use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

class PremiumCustomerRuleChecker implements RuleCheckerInterface
{
    public const TYPE = 'premium_customer';

    public function isEligible(PromotionSubjectInterface $subject, array $configuration): bool
    {
        return $subject->getCustomer()?->isPremium() === true;
    }
}
```

{% hint style="warning" %}
Ensure the `getCustomer()` method exists on your `PromotionSubjectInterface`.
{% endhint %}

***

### 2. Add a Premium Field to the Customer Entity

First, extend the `Customer` entity to include a `premium` boolean field:

```php
<?php

// App\Entity\Customer\Customer

declare(strict_types=1);

namespace App\Entity\Customer;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Customer as BaseCustomer;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_customer')]
class Customer extends BaseCustomer
{
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $premium = false;

    public function isPremium(): bool
    {
        return $this->premium;
    }

    public function setPremium(bool $premium): void
    {
        $this->premium = $premium;
    }
}
```

**Apply the Doctrine migration:**

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

***

### 3. Extend the Customer Admin Form

Now, extend the customer form to allow editing the premium field in the Sylius admin:

```php
<?php

// src/Form/Extension/CustomerTypeExtension.php

namespace App\Form\Extension;

use Sylius\Bundle\AdminBundle\Form\Type\CustomerType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

final class CustomerTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('premium', CheckboxType::class, [
            'label' => 'Premium',
            'required' => false,
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [CustomerType::class];
    }
}
```

{% hint style="warning" %}
If your autowiring is disabled, you will need also to register your `CustomerTypeExtension` in `config/services.yaml`:

```yaml
services:    
    App\Form\Extension\CustomerTypeExtension:
        tags:
            - { name: form.type_extension }
```
{% endhint %}

***

### 4. Create the Template Section for the Premium Field

Inspect the admin customer form to find the appropriate hook. The relevant section is `sylius_admin.customer.update.content.form.sections.general`.

<figure><img src="../.gitbook/assets/image (36).png" alt=""><figcaption></figcaption></figure>

Create a new Twig template for this field:

```twig
{# templates/admin/customer/form/sections/general/premium.html.twig #}

{{ form_row(hookable_metadata.context.form.premium) }}
```

Then configure the hook in your `sylius.yaml`:

```yaml
# config/packages/_sylius.yaml

sylius_twig_hooks:
    hooks:
        'sylius_admin.customer.update.content.form.sections.general':
            premium:
                template: '/admin/customer/form/sections/general/premium.html.twig'
                priority: -50
```

{% hint style="success" %}
Learn more about twig hooks [here](https://stack.sylius.com/twig-hooks/getting-started)!
{% endhint %}

***

### 5. Create a Configuration Form Type (Optional)

Even if no configuration is needed, Sylius expects a form type to exist for every rule.

```php
<?php

// src/Form/Type/Rule/PremiumCustomerConfigurationType.php

namespace App\Form\Type\Rule;

use Symfony\Component\Form\AbstractType;

class PremiumCustomerConfigurationType extends AbstractType
{
    public function getBlockPrefix(): string
    {
        return 'app_promotion_rule_premium_customer_configuration';
    }
}
```

{% hint style="warning" %}
If your autowiring is disabled, you will need also to register your `PremiumCustomerConfigurationType` in `config/services.yaml`:

```yaml
services:    
    App\Form\Type\Rule\PremiumCustomerConfigurationType:
        tags:
            - { name: form.type }
```
{% endhint %}

***

### 6. Register Services

Add both the rule checker and form type to your `config/services.yaml`:

```yaml
# config/services.yaml
services:
    App\Promotion\Checker\Rule\PremiumCustomerRuleChecker:
        tags:
            - {
                name: sylius.promotion_rule_checker,
                type: premium_customer,
                form_type: App\Form\Type\Rule\PremiumCustomerConfigurationType,
                label: 'Premium customer'
              }
```

***

### ✅ Result

You can now select the **Premium customer** rule type when creating or editing a cart promotion in the Sylius Admin panel.

<figure><img src="../.gitbook/assets/image (35).png" alt=""><figcaption></figcaption></figure>

To make this rule applicable, edit a customer and mark them as **premium**.

<figure><img src="../.gitbook/assets/image (14) (1).png" alt=""><figcaption></figcaption></figure>

Promotions using this rule will now apply (or not) based on whether the customer is marked as premium— regardless of whether a coupon is used:

<figure><img src="../.gitbook/assets/image (16).png" alt=""><figcaption></figcaption></figure>

***

{% hint style="success" %}
If your rule requires configuration fields (like thresholds or limits), see how it’s handled in [`ItemTotalConfigurationType`](https://github.com/Sylius/Sylius/blob/v2.0.7/src/Sylius/Bundle/PromotionBundle/Form/Type/Rule/ItemTotalConfigurationType.php).
{% endhint %}
