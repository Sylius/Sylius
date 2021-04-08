# UPGRADE FROM `v1.9.X` TO `v1.10.0`

### New API

1. API CartShippingMethod key `cost` has been changed to `price`.

1. To have better control over the serialization process, we introduced `shop` and `admin` prefixes to names of serialization groups on `src/Sylius/Bundle/ApiBundle/Resources/config/api_resources/*` and `src/Sylius/Bundle/ApiBundle/Resources/config/serialization/*`.
   Several additional serialization groups have been rephrased, to improve readability and predictability of them.
   If you are using they on your custom entity `api_resource` configuration or serialization groups, you should check if one of these changes may affect on your app. If yes, change all occurs by this pattern:

- `product_review:update` changed to: `admin:product_review:update` and `shop:product_review:update`

1. We've removed `Sylius\Bundle\ApiBundle\ApiPlatform\Bridge\Doctrine\ORM\SubresourceDataProvider` is not longer needed because `ApiPlatform\Core\Bridge\Doctrine\Orm\SubresourceDataProvider` is the same file.
