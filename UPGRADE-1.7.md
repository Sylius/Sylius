# UPGRADE FROM `v1.6.X` TO `v1.7.0`

Require upgraded Sylius version using Composer:

```bash
composer require sylius/sylius:~1.7.0
```

# Breaking changes

Those are excluded from our BC promise:

- `Sylius\Bundle\ShopBundle\EventListener\UserMailerListener` has been removed and replaced with `Sylius\Bundle\CoreBundle\EventListener\MailerListener`
