# UPGRADE FROM `v1.6.X` TO `v1.7.0`

Require upgraded Sylius version using Composer:

```bash
composer require sylius/sylius:~1.7.0
```

Update your `package.json` in order to add `slick-carousel` : 

```diff
{
  "dependencies": {
    "babel-polyfill": "^6.26.0",
    "jquery": "^3.4.0",
    "jquery.dirtyforms": "^2.0.0",
    "lightbox2": "^2.9.0",
    "semantic-ui-css": "^2.2.0",
+   "slick-carousel": "^1.8.1"
  },
  ...
}
```

## Template events

- `Sylius\Bundle\UiBundle\Block\BlockEventListener` has been deprecated, use `sylius_ui` configuration instead.

## Breaking changes

Those are excluded from our BC promise:

- `Sylius\Bundle\ShopBundle\EventListener\UserMailerListener` has been removed and replaced with `Sylius\Bundle\CoreBundle\EventListener\MailerListener`
