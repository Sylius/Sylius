# UPGRADE FROM `v1.5.X` TO `v1.6.0`

Require upgraded Sylius version using Composer:

```bash
composer require sylius/sylius:~1.6.0
```

Copy [a new migration file](https://raw.githubusercontent.com/Sylius/Sylius/1.6/app/migrations/Version20190607135638.php) and run new migrations:

```bash
bin/console doctrine:migrations:migrate
```

Update your `package.json` in order to add `jquery.dirtyforms` as mentioned in [issue #88](https://github.com/Sylius/SyliusDemo/pull/88/files) : 

```diff
{
  "dependencies": {
    "babel-polyfill": "^6.26.0",
    "jquery": "^3.4.1",
+    "jquery.dirtyforms": "^2.0.0",
    "lightbox2": "^2.9.0",
    "semantic-ui-css": "^2.2.0"
  },
  ...
}
```
