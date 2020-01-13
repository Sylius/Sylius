# UPGRADE FROM `v1.4.X` TO `v1.5.0`

Require upgraded Sylius version using Composer:

```bash
composer require sylius/sylius:~1.5.0
```

Copy [a new migration file](https://raw.githubusercontent.com/Sylius/Sylius-Standard/94888ff604f7dfdcdc7165e82ce0119ce892c17e/src/Migrations/Version20190508083953.php) and run new migrations:

```bash
bin/console doctrine:migrations:migrate
```

### Routing

- If you want to support extended locale codes, as introduced in [#10178](https://github.com/Sylius/Sylius/pull/10178), you should modify `_locale` requirement in `config/routes/sylius_shop.yml`

    ```yaml
    sylius_shop:
        resource: "@SyliusShopBundle/Resources/config/routing.yml"
        prefix: /{_locale}
        requirements:
            _locale: ^[A-Za-z]{2,4}(_([A-Za-z]{4}|[0-9]{3}))?(_([A-Za-z]{2}|[0-9]{3}))?$
    ```
