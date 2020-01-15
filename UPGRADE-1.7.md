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

You may need to remove `SonataCoreBundle` from your list of used bundles in `config/bundles.php`(if you are not using it explicitly):

```diff
    winzou\Bundle\StateMachineBundle\winzouStateMachineBundle::class => ['all' => true],
-   Sonata\CoreBundle\SonataCoreBundle::class => ['all' => true],
    Sonata\BlockBundle\SonataBlockBundle::class => ['all' => true],
```

And you should remove `config/packages/sonata_core.yaml` as well.

## Template events

- `Sylius\Bundle\UiBundle\Block\BlockEventListener` has been deprecated, use `sylius_ui` configuration instead.

## Breaking changes

Those are excluded from our BC promise:

- `Sylius\Bundle\ShopBundle\EventListener\UserMailerListener` has been removed and replaced with `Sylius\Bundle\CoreBundle\EventListener\MailerListener`
