# UPGRADE FROM `v1.12.X` TO `v1.13.0`

1. The `Sylius\Bundle\CoreBundle\CatalogPromotion\Command\RemoveInactiveCatalogPromotion` command and its handler
   `Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler\RemoveInactiveCatalogPromotionHandler` have been deprecated.
   Use `Sylius\Bundle\CoreBundle\CatalogPromotion\Command\RemoveCatalogPromotion` command instead.

2. Passing `Symfony\Component\Messenger\MessageBusInterface` to `Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionRemovalProcessor`
   as a second and third argument is deprecated.

3. Not passing `Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionRemovalAnnouncerInterface` to `Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionRemovalProcessor`
   as a second argument is deprecated.

4. Not passing `Doctrine\Persistence\ObjectManager` to `Sylius\Component\Core\Updater\UnpaidOrdersStateUpdater`
   as a fifth argument is deprecated.

5. To allow to autoconfigure order processors and cart context and define a priority for them in `1.13` we have introduced
   `Sylius\Bundle\OrderBundle\Attribute\AsCartContext` and `Sylius\Bundle\OrderBundle\Attribute\AsOrderProcessor` attributes. By default, Sylius still configures them using interfaces, but this way you cannot define a priority.
   If you want to define a priority, you need to set the following configuration in your `_sylius.yaml` file:
   ```yaml
   sylius_core:
       autoconfigure_with_attributes: true
   ```
   and use one of the new attributes accordingly to a type of your class, e.g.:
   ```php
    <?php

    declare(strict_types=1);

    namespace App\OrderProcessor;

    use Sylius\Bundle\OrderBundle\Attribute\AsOrderProcessor;
    use Sylius\Component\Order\Model\OrderInterface;
    use Sylius\Component\Order\Processor\OrderProcessorInterface;

    #[AsOrderProcessor(priority: 10)] //priority is optional
    //#[AsOrderProcessor] can be used as well
    final class OrderProcessorWithAttributeStub implements OrderProcessorInterface
    {
        public function process(OrderInterface $order): void
        {
        }
    }
   ```
