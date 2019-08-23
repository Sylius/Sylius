# UPGRADE FROM `v1.5.X` TO `v1.6.0`

Require upgraded Sylius version using Composer:

```bash
composer require sylius/sylius:~1.6.0
```

Copy [a new migration file](https://raw.githubusercontent.com/Sylius/Sylius-Standard/master/src/Migrations/Version20190621035710.php) and run new migrations:

```bash
bin/console doctrine:migrations:migrate
```
