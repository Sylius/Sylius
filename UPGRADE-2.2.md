# UPGRADE FROM `2.1` TO `2.2`

### Deprecations

1. Direct usage of `loader.svg` and `loader.gif` assets is deprecated.
   Use `@SyliusAdmin/shared/helper/loader.html.twig` or `@SyliusShop/shared/macro/loader.html.twig` instead.

### Twig Hooks

1. `'sylius_shop.product.show.content.info.overview.images.main_image'` hook has been deprecated and will be replaced
with `'sylius_shop.product.show.content.info.overview.images.main_image_component'`.

To activate the image update function after changing the product variant, overwrite the configuration:

```yaml
# src/Sylius/Bundle/ShopBundle/Resources/config/app/twig_hooks/product/show.yaml

sylius_twig_hooks:
    hooks:
        'sylius_shop.product.show.content.info.overview.images':
            main_image:
                enabled: false
            main_image_component:
                component: 'sylius_shop:product:images'
                props:
                    product: '@=_context.product'
                    template: '@SyliusShop/product/show/content/info/overview/images/main_image_component.html.twig'
```
