⚙️ Upgrade from Sylius 1.12 with PriceHistoryPlugin to Sylius 1.13
==================================================================

We encourage you to use the upgrade instructions based on Rector as it is more convenient and faster to accomplish.
The rector installation guide is available [here](UPGRADE-FROM-1.12-WITH-PRICE-HISTORY-PLUGIN-TO-1.13.md).

Legacy upgrade
--------------

1. Remove the PriceHistoryPlugin from composer.json by running:

    ```bash
  
    composer remove sylius/price-history-plugin --no-scripts

    ```

1. Make sure to remove the following config from your `config/packages/sylius_price_history_plugin.yaml` file:

    ```diff

    -  - { resource: "@SyliusPriceHistoryPlugin/config/config.yaml" }

    ```

   And the route from your `config/routes/sylius_price_history_plugin.yaml`:

    ```diff

    - sylius_price_history_admin:
    -    resource: '@SyliusPriceHistoryPlugin/config/admin_routing.yaml'
    -    prefix: '/%sylius_admin.path_name%'

    ```

   And also remove the plugin from your `packages/bundles.php` file:

    ```diff

    - Sylius\PriceHistoryPlugin\SyliusPriceHistoryPlugin::class => ['all' => true],

    ```

1. Update the `Channel` entity to use interface from `Core`:

    ```diff

    - use Sylius\PriceHistoryPlugin\Domain\Model\ChannelInterface;
    + use Sylius\Component\Core\Model\ChannelInterface;

    ```

   Then remove the trait:

    ```diff

    - use ChannelPriceHistoryConfigAwareTrait;

    ```

1. Update the `ChannelPricing` entity to use interface from `Core`:

    ```diff

    - use Sylius\PriceHistoryPlugin\Domain\Model\ChannelPricingInterface;
    + use Sylius\Component\Core\Model\ChannelPricingInterface;

    ```
   Then remove the trait:

    ```diff

    - use LowestPriceBeforeDiscountAwareTrait;

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

1. Change namespaces from the plugin to correct ones:

    ```

        Sylius\PriceHistoryPlugin\Application\Checker\ProductVariantLowestPriceDisplayChecker => Sylius\Component\Core\Checker\ProductVariantLowestPriceDisplayChecker 
        Sylius\PriceHistoryPlugin\Application\Checker\ProductVariantLowestPriceDisplayCheckerInterface => Sylius\Component\Core\Checker\ProductVariantLowestPriceDisplayCheckerInterface 
        Sylius\PriceHistoryPlugin\Application\Command\ApplyLowestPriceOnChannelPricings => Sylius\Bundle\CoreBundle\PriceHistory\Command\ApplyLowestPriceOnChannelPricings 
        Sylius\PriceHistoryPlugin\Application\CommandDispatcher\BatchedApplyLowestPriceOnChannelPricingsCommandDispatcher => Sylius\Bundle\CoreBundle\PriceHistory\CommandDispatcher\BatchedApplyLowestPriceOnChannelPricingsCommandDispatcher 
        Sylius\PriceHistoryPlugin\Application\CommandDispatcher\ApplyLowestPriceOnChannelPricingsCommandDispatcherInterface => Sylius\Bundle\CoreBundle\PriceHistory\CommandDispatcher\ApplyLowestPriceOnChannelPricingsCommandDispatcherInterface 
        Sylius\PriceHistoryPlugin\Application\CommandHandler\ApplyLowestPriceOnChannelPricingsHandler => Sylius\Bundle\CoreBundle\PriceHistory\CommandHandler\ApplyLowestPriceOnChannelPricingsHandler
        Sylius\PriceHistoryPlugin\Application\Logger\PriceChangeLogger => Sylius\Bundle\CoreBundle\PriceHistory\Logger\PriceChangeLogger 
        Sylius\PriceHistoryPlugin\Application\Logger\PriceChangeLoggerInterface => Sylius\Bundle\CoreBundle\PriceHistory\Logger\PriceChangeLoggerInterface 
        Sylius\PriceHistoryPlugin\Application\Processor\ProductLowestPriceBeforeDiscountProcessor => Sylius\Bundle\CoreBundle\PriceHistory\Processor\ProductLowestPriceBeforeDiscountProcessor 
        Sylius\PriceHistoryPlugin\Application\Processor\ProductLowestPriceBeforeDiscountProcessorInterface => Sylius\Bundle\CoreBundle\PriceHistory\Processor\ProductLowestPriceBeforeDiscountProcessorInterface 
        Sylius\PriceHistoryPlugin\Application\Remover\ChannelPricingLogEntriesRemoverInterface => Sylius\Bundle\CoreBundle\PriceHistory\Remover\ChannelPricingLogEntriesRemoverInterface 
        Sylius\PriceHistoryPlugin\Application\Templating\Helper\PriceHelper => Sylius\Bundle\CoreBundle\Templating\Helper\PriceHelper 
        Sylius\PriceHistoryPlugin\Application\Validator\ResourceInputDataPropertiesValidatorInterface => Sylius\Bundle\ApiBundle\Validator\ResourceInputDataPropertiesValidatorInterface 
        Sylius\PriceHistoryPlugin\Domain\Factory\ChannelFactory => Sylius\Component\Core\Factory\ChannelFactory 
        Sylius\PriceHistoryPlugin\Domain\Factory\ChannelPricingLogEntryFactory => Sylius\Component\Core\Factory\ChannelPricingLogEntryFactory 
        Sylius\PriceHistoryPlugin\Domain\Factory\ChannelPricingLogEntryFactoryInterface => Sylius\Component\Core\Factory\ChannelPricingLogEntryFactoryInterface 
        Sylius\PriceHistoryPlugin\Domain\Model\ChannelInterface => Sylius\Component\Core\Model\ChannelInterface 
        Sylius\PriceHistoryPlugin\Domain\Model\ChannelPriceHistoryConfig => Sylius\Component\Core\Model\ChannelPriceHistoryConfig 
        Sylius\PriceHistoryPlugin\Domain\Model\ChannelPricingLogEntry => Sylius\Component\Core\Model\ChannelPricingLogEntry 
        Sylius\PriceHistoryPlugin\Domain\Model\ChannelPriceHistoryConfigInterface => Sylius\Component\Core\Model\ChannelPriceHistoryConfigInterface 
        Sylius\PriceHistoryPlugin\Domain\Model\ChannelPricingInterface => Sylius\Component\Core\Model\ChannelPricingInterface 
        Sylius\PriceHistoryPlugin\Domain\Model\ChannelPricingLogEntryInterface => Sylius\Component\Core\Model\ChannelPricingLogEntryInterface 
        Sylius\PriceHistoryPlugin\Domain\Model\LowestPriceBeforeDiscountAwareInterface => Sylius\Component\Core\Model\ChannelPricingInterface 
        Sylius\PriceHistoryPlugin\Domain\Repository\ChannelPricingLogEntryRepositoryInterface => Sylius\Component\Core\Repository\ChannelPricingLogEntryRepositoryInterface 
        Sylius\PriceHistoryPlugin\Infrastructure\Cli\Command\ClearPriceHistoryCommand => Sylius\Bundle\CoreBundle\PriceHistory\Cli\Command\ClearPriceHistoryCommand
        Sylius\PriceHistoryPlugin\Infrastructure\Doctrine\ORM\ChannelPricingLogEntryRepository => Sylius\Component\Core\Repository\ChannelPricingLogEntryRepository 
        Sylius\PriceHistoryPlugin\Infrastructure\Doctrine\ORM\ChannelPricingLogEntryRepositoryInterface => Sylius\Component\Core\Repository\ChannelPricingLogEntryRepositoryInterface 
        Sylius\PriceHistoryPlugin\Infrastructure\EntityObserver\CreateLogEntryOnPriceChangeObserver => Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver\CreateLogEntryOnPriceChangeObserver 
        Sylius\PriceHistoryPlugin\Infrastructure\EntityObserver\EntityObserverInterface => Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver\EntityObserverInterface 
        Sylius\PriceHistoryPlugin\Infrastructure\EntityObserver\ProcessLowestPricesOnChannelChangeObserver => Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver\ProcessLowestPricesOnChannelChangeObserver 
        Sylius\PriceHistoryPlugin\Infrastructure\EntityObserver\ProcessLowestPricesOnChannelPriceHistoryConfigChangeObserver => Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver\ProcessLowestPricesOnChannelPriceHistoryConfigChangeObserver 
        Sylius\PriceHistoryPlugin\Infrastructure\Event\OldChannelPricingLogEntriesEvents => Sylius\Bundle\CoreBundle\PriceHistory\Event\OldChannelPricingLogEntriesEvents 
        Sylius\PriceHistoryPlugin\Infrastructure\EventListener\OnFlushEntityObserverListener => Sylius\Bundle\CoreBundle\PriceHistory\EventListener\OnFlushEntityObserverListener 
        Sylius\PriceHistoryPlugin\Infrastructure\EventSubscriber\ChannelPricingLogEntryEventSubscriber => Sylius\Bundle\CoreBundle\PriceHistory\EventListener\ChannelPricingLogEntryEventListener 
        Sylius\PriceHistoryPlugin\Infrastructure\Form\Extension\ChannelTypeExtension => Sylius\Bundle\CoreBundle\Form\Extension\ChannelTypeExtension 
        Sylius\PriceHistoryPlugin\Infrastructure\Form\Type\ChannelPriceHistoryConfigType => Sylius\Bundle\CoreBundle\Form\Type\ChannelPriceHistoryConfigType 
        Sylius\PriceHistoryPlugin\Infrastructure\Provider\ProductVariantsPricesProvider => Sylius\Component\Core\Provider\ProductVariantsPricesProvider 
        Sylius\PriceHistoryPlugin\Infrastructure\Remover\ChannelPricingLogEntriesRemover' => 'Sylius\Bundle\CoreBundle\PriceHistory\Remover\ChannelPricingLogEntriesRemover' 
        Sylius\PriceHistoryPlugin\Infrastructure\Serializer\ChannelDenormalizer => Sylius\Bundle\ApiBundle\Serializer\ChannelDenormalizer 
        Sylius\PriceHistoryPlugin\Infrastructure\Serializer\ChannelPriceHistoryConfigDenormalizer => Sylius\Bundle\ApiBundle\Serializer\ChannelPriceHistoryConfigDenormalizer 
        Sylius\PriceHistoryPlugin\Infrastructure\Serializer\ProductVariantNormalizer => Sylius\Bundle\ApiBundle\Serializer\ProductVariantNormalizer 
        Sylius\PriceHistoryPlugin\Infrastructure\Twig\PriceExtension => Sylius\Bundle\CoreBundle\Twig\PriceExtension 
        Sylius\PriceHistoryPlugin\Infrastructure\Twig\SyliusVersionExtension => Sylius\Bundle\CoreBundle\Twig\SyliusVersionExtension 
        Sylius\PriceHistoryPlugin\Infrastructure\Validator\ResourceApiInputDataPropertiesValidator => Sylius\Bundle\ApiBundle\Validator\ResourceApiInputDataPropertiesValidator

    ```

1. The `Sylius\PriceHistoryPlugin\Application\Calculator\ProductVariantLowestPriceCalculator` class along with its interface has been removed.
   If you have used it in your project, you should also remove it from your code. 

   Use `Sylius\Component\Core\Calculator\ProductVariantPriceCalculator` instead, as it has been extended with the `calculateLowestPrice` method.

1. Go through the rest of the [Sylius 1.13 upgrade file](UPGRADE-1.13.md).
