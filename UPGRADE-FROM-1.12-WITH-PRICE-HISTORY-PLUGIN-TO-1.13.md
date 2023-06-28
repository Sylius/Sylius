⚙️ Upgrade from Sylius 1.12 with PriceHistoryPlugin to Sylius 1.13
==================================================================

We encourage you to use the upgrade instructions based on Rector as it is more convenient and faster to accomplish.
The legacy installation guide is available [here](UPGRADE-FROM-1.12-WITH-PRICE-HISTORY-PLUGIN-TO-1.13-LEGACY.md).

Upgrade with Rector
-------------------

1. Remove the PriceHistoryPlugin from composer.json by running:

    ```bash

    composer remove sylius/price-history-plugin --no-scripts

    ```

1. Update your `<project_root>/rector.php` file:

    ```diff

    + use Sylius\SyliusRector\Set\SyliusPriceHistory;
    
    return static function (RectorConfig $rectorConfig): void {
        // ...
    -    $rectorConfig->sets([SyliusPriceHistory::PRICE_HISTORY_PLUGIN])
    +    $rectorConfig->sets([SyliusPriceHistory::UPGRADE_SYLIUS_1_12_WITH_PRICE_HISTORY_PLUGIN_TO_SYLIUS_1_13]);
    };

    ```

1. Run:

    ```bash
  
    vendor/bin/rector

    ```

1. Make sure to remove the following config from your `config/packages/sylius_price_history_plugin.yaml` file:

    ```diff

    -  - { resource: "@SyliusPriceHistoryPlugin/config/config.yaml" }

    ```

   And also the route from your `config/routes/sylius_price_history_plugin.yaml`:

    ```diff

    - sylius_price_history_admin:
    -    resource: '@SyliusPriceHistoryPlugin/config/admin_routing.yaml'
    -    prefix: '/%sylius_admin.path_name%'

    ```

1. Update your resources configuration for `ChannelPricingLogEntry` and `ChannelPriceHistoryConfig` if you have changed them in your project:

    ```diff

    - sylius_price_history:
    -     batch_size: 100
    + sylius_core:
    +     price_history:
    +         batch_size: 100
         resources:
             channel_price_history_config:
                 classes:
                     model: App\Entity\ChannelPriceHistoryConfig
                     ...
             channel_pricing_log_entry:
                 classes:
                     model: App\Entity\ChannelPricingLogEntry
                     ...

    ```

1. The class `Sylius\PriceHistoryPlugin\Infrastructure\EventSubscriber\ChannelPricingLogEntryEventSubscriber` has been replaced by `Sylius\Bundle\CoreBundle\PriceHistory\EventListener\ChannelPricingLogEntryEventListener`.

1. The `Sylius\PriceHistoryPlugin\Application\Calculator\ProductVariantLowestPriceCalculator` class along with its interface has been removed.
   If you have used it in your project, you should also remove it from your code.

   Use `Sylius\Component\Core\Calculator\ProductVariantPriceCalculator` instead, as it has been extended with the `calculateLowestPrice` method.

1. Go through the rest of the [Sylius 1.13 upgrade file](UPGRADE-1.13.md).
