# UPGRADE FROM `v1.10.X` TO `v1.11.0`

## Preconditions

### PHP 8.0 support

Sylius v1.11 comes with bump of minimal dependencies of PHP to v8.0. We strongly advice to make upgrade process step by step,
so it is highly recommended updating your PHP version being still on Sylius v1.10, as it is supporting both PHP7.4 and PHP8.0.

After ensuring, that previous step succeed, you may move forward to the Sylius v1.11 update.

## Main update

### "pagerfanta/pagerfanta" semantic_ui_translated removed

The `pagination.html.twig` has been changed to use the Twig view.

There are differences in the markup between the PHP template and the Twig template.

The wrapping container from 2.x branch of Pagerfanta in the PHP template was:

```html
<div class="ui stackable fluid pagination menu">
```

while in the Twig template in 3.x branch it's:

```html
<div class="ui pagination menu">
```

The "stackable" class affects responsive display and "fluid" affects whether the pagination menu is full-width.

### "polishsymfonycommunity/symfony-mocker-container" moved to dev-requirements

This obvious dev dependency was part of Sylius requirements. In 1.11 we've moved it to proper place. However, 
it may lead to app break, as this container could be used in your Kernel, if you used Sylius-Standard as your template. 
In such a case, please update your `src/Kernel.php` class as follows:

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

If you were using MockerContainer in your app, you should also execute the following command:

```bash
composer req --dev polishsymfonycommunity/symfony-mocker-container
```

### API Platform required folders

If you don't already have, add an empty directory `api_platform` in your `config` directory and customize there any API resources.

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

Passing a `Sylius\Component\Core\Calculator\ProductVariantPriceCalculatorInterface` to `Sylius\Component\Core\OrderProcessing\OrderPricesRecalculator` 
constructor is deprecated since Sylius 1.11 and will be prohibited in 2.0. Use `Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface` instead.

### Messenger transport

If you don't already have configured the messenger transport, configure it according to your needs by setting an environment variable `MESSENGER_TRANSPORT_DSN`.

### Behat

- Service `sylius.behat.context.hook.calendar` has been removed, use in your suites `Sylius\Calendar\Tests\Behat\Context\Hook\CalendarContext` instead.
- Service `sylius.behat.context.setup.calendar` has been removed, use in your suites `Sylius\Calendar\Tests\Behat\Context\Setup\CalendarContext` instead.

### Potential BC-breaks

#### State machine callbacks

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

#### Form type extensions

All form type extensions supplied by Sylius now specify a priority of 100, instead of relying on the default value of 0.
This means that your form type extensions, including autowired ones, may now consistently override the effect of these
stock form type extensions without you having to explicitly specify their priorities. However, if you relied on the old
default values, you might have to review priorities of your own form type extensions, as well as any that you have overridden.
Please note that **unlike state machine callbacks**, form extension priorities are being executed in descending order. 

### API v2

For changes according to the API v2, please visit [API v2 upgrade file](UPGRADE-API-1.11.md).
