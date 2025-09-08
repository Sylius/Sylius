# How to disable the order confirmation email?

In some projects, you may want to **completely disable the order confirmation email** sent when an order is completed. This guide explains two clean and recommended ways to do that:

#### ✅ You can either:

1. **Disable the `order_confirmation` email configuration**
2. **Disable the event listener responsible for sending it (`OrderCompleteListener`)**

***

## 1. Disabling the Email via Configuration

This is the simplest and most future-proof approach.

Add or update your `config/packages/sylius_mailer.yaml` file:

```yaml
# config/packages/sylius_mailer.yaml

sylius_mailer:
    emails:
        order_confirmation:
            enabled: false
```

With this, the `order_confirmation` email will be ignored by the mailer system, even if triggered.

{% hint style="warning" %}
&#x20;After making this change, make sure to clear the cache for the configuration to take effect:

```bash
php bin/console cache:clear
```
{% endhint %}

***

## 2. Disabling the Listener (Advanced)

If you want to **completely prevent the event logic** that sends the email, disable the listener service itself.

### Create a compiler pass:

```php
<?php

// src/DependencyInjection/Compiler/DisableOrderConfirmationEmailPass.php

namespace App\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class DisableOrderConfirmationEmailPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $container->removeDefinition('sylius_shop.listener.order_complete');
    }
}
```

### Then register the compiler pass in your `Kernel`:

```php
<?php

// src/Kernel.php

namespace App;

use App\DependencyInjection\Compiler\DisableOrderConfirmationEmailPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new DisableOrderConfirmationEmailPass());
    }
}
```

{% hint style="warning" %}
&#x20;Use this method **only if you are sure** you want to completely prevent any logic in the listener, not just the email.
{% endhint %}

***

## 3. Verify the Listener is Removed

After clearing cache and rebuilding the container, use the Symfony commands to **inspect attached listeners**:

```bash
php bin/console debug:event sylius.order
```

This command lists **all listeners** attached to the `sylius.order.*` events.

{% hint style="warning" %}
If the `OrderCompleteListener` is still listed (usually from `Sylius\Component\Core\EventListener\OrderCompleteListener`), the removal didn't work — recheck your compiler pass and cache.
{% endhint %}

To list all order related listeners, you can also use:

```bash
php bin/console debug:event-dispatcher | grep sylius.order
```

{% hint style="success" %}
This helps you spot additional event listeners tied to Sylius resource lifecycle events (e.g. `sylius.order.post_create`, `sylius.order.pre_update`, etc.).
{% endhint %}

***

## Which Option Should You Choose?

| Use Case                                       | Recommended Option         |
| ---------------------------------------------- | -------------------------- |
| Stop the email, but keep the rest of the logic | ✅ Option 1: Configuration  |
| Disable all post-order-complete logic          | ⚠️ Option 2: Compiler Pass |

