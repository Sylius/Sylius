# UPGRADE FROM `v1.10.X` TO `v1.11.0`

### "polishsymfonycommunity/symfony-mocker-container" moved to dev-requirements

This obvious dev dependency was part of Sylius requirements. In 1.11 we've moved it to proper place. However, it may lead to app break, as this container could be used in your Kernel, if you used Sylius-Standard as your template. In such a case, please update your `src/Kernel.php` class as follows:

```diff
     protected function getContainerBaseClass(): string
     {
-        if ($this->isTestEnvironment()) {
+        if ($this->isTestEnvironment() && class_exists(MockerContainer::class)) {
            return MockerContainer::class;
         }
 
         return parent::getContainerBaseClass();
     }
```

### Drop support for PHP 7.4

Due to the drop of support PHP `7.4` Sylius also will not support it since version `1.11`.

### Potential BC-break

In Sylius we are using WinzouStateMachine where as example `sylius_order` state machine has declared 14 callbacks on one state.
If this will be customized and number of callbacks comes up to 16 and higher - the priority of callbacks will become randomized.

Sylius state machine callbacks from now on will have priorities declared. Ending at -100 with step of 100.
Please note that those priorities are being executed in ascending order. You can find all the new priorities at
`Sylius/Bundle/CoreBundle/Resources/config/app/state_machine`.

Be aware that if those priorities were customized, this would lead to problems. 
You should check and adjust priorities on your application.

### Minimum price & Promotions

We added MinimumPrice to channelPricings entity, this price should be taken into account when customizing any promotions in Sylius.
All calculating and distributing services provided by default depends on MinimumPrice.

### API v2

For changes according to the API v2, please visit [API v2 upgrade file](UPGRADE-API-1.11.md).
