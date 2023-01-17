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
