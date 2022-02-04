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

### Potential BC-breaks

#### WinzouStateMachine

In Sylius we are using WinzouStateMachine where as example `sylius_order` state machine has declared 14 callbacks on one state.
If this will be customized and number of callbacks comes up to 16 and higher - the priority of callbacks will become randomized.

Sylius state machine callbacks from now on will have priorities declared. Ending at -100 with step of 100.
Please note that those priorities are being executed in ascending order. You can find all the new priorities at
`Sylius/Bundle/CoreBundle/Resources/config/app/state_machine`.

Be aware that if those priorities were customized, this would lead to problems. 
You should check and adjust priorities on your application.

#### Promoted properties from PHP 8.0

We've introduced promoted properties all over the code where it was possible. Please, pay attention especially to these classes:
- `Sylius\Bundle\AdminBundle\Controller\CustomerStatisticsController`
- `Sylius\Bundle\AdminBundle\Controller\Dashboard\StatisticsController`
- `Sylius\Bundle\AdminBundle\Controller\DashboardController`
- `Sylius\Bundle\ShopBundle\Controller\ContactController`
- `Sylius\Bundle\ShopBundle\Controller\CurrencySwitchController`
- `Sylius\Bundle\ShopBundle\Controller\HomepageController`
- `Sylius\Bundle\ShopBundle\Controller\LocaleSwitchController`
- `Sylius\Bundle\ShopBundle\Controller\SecurityWidgetController`
- `Sylius\Bundle\UiBundle\Controller\SecurityController`

In all of them constructor argument `$templatingEngine`, previously typed as `object` was changed to `EngineInterface|Environment`.
It should not cause any problems (only such services would work in these controllers), but is theoretically making the type
requirement stricter.

### Minimum price & Promotions

We added MinimumPrice to channelPricings entity, this price should be taken into account when customizing any promotions in Sylius.
All calculating and distributing services provided by default depends on MinimumPrice.

### Calendar & Shipping

Service `sylius.calendar` has been deprecated. Use `Sylius\Calendar\Provider\DateTimeProviderInterface` instead.

Add a new bundle to your list of used bundles in `config/bundles.php` if they are not already there:

    ```diff
    +   Sylius\Calendar\SyliusCalendarBundle::class => ['all' => true],
    ```

### Order prices recalculator

Passing a `Sylius\Component\Core\Calculator\ProductVariantPriceCalculatorInterface` to `Sylius\Component\Core\OrderProcessing\OrderPricesRecalculator` constructor is deprecated since Sylius 1.11 and will be prohibited in 2.0.
Use `Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface` instead.

### Behat

- Service `sylius.behat.context.hook.calendar` has been removed, use in your suites `Sylius\Calendar\Tests\Behat\Context\Hook\CalendarContext` instead.
- Service `sylius.behat.context.setup.calendar` has been removed, use in your suites `Sylius\Calendar\Tests\Behat\Context\Setup\CalendarContext` instead.

### API v2

For changes according to the API v2, please visit [API v2 upgrade file](UPGRADE-API-1.11.md).
