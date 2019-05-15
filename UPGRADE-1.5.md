# UPGRADE FROM `v1.4.X` TO `v1.5.0`

Require upgraded Sylius version using Composer:

```bash
composer require sylius/sylius:~1.5.0
```

Copy [a new migration file](https://raw.githubusercontent.com/Sylius/Sylius-Standard/94888ff604f7dfdcdc7165e82ce0119ce892c17e/src/Migrations/Version20190508083953.php) and run new migrations:

```bash
bin/console doctrine:migrations:migrate
```
