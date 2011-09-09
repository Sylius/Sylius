SyliusAddressingBundle documentation.
=====================================

This bundle provides models and interfaces for managing addresses in Symfony2 applications.

**Note!** This documentation is inspired by [FOSUserBundle docs](https://github.com/FriendsOfSymfony/FOSUserBundle/blob/master/Resources/doc/index.md).

Installation.
-------------

+ Installing dependencies.
+ Downloading the bundle.
+ Autoloader configuration.
+ Adding bundle to kernel.
+ Creating your Address class.
+ DIC configuration.
+ Importing routing cfgs.
+ Updating database schema.

### Installing dependencies.

This bundle uses Pagerfanta library and PagerfantaBundle.
The installation guide can be found [here](https://github.com/whiteoctober/WhiteOctoberPagerfantaBundle).

### Downloading the bundle.

The good practice is to download the bundle to the `vendor/bundles/Sylius/Bundle/AddressingBundle` directory.

This can be done in several ways, depending on your preference. The first
method is the standard Symfony2 method.

**Using the vendors script.**

Add the following lines in your `deps` file...

```
[SyliusAddressingBundle]
    git=git://github.com/Sylius/SyliusAddressingBundle.git
    target=bundles/Sylius/Bundle/AddressingBundle
```

Now, run the vendors script to download the bundle.

``` bash
$ php bin/vendors install
```

**Using submodules.**

If you prefer instead to use git submodules, the run the following:

``` bash
$ git submodule add git://github.com/Sylius/SyliusAddressingBundle.git vendor/bundles/Sylius/Bundle/AssortmentBundle
$ git submodule update --init
```

### Autoloader configuration.

Add the `Sylius\Bundle` namespace to your autoloader.

``` php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
    'Sylius\\Bundle' => __DIR__.'/../vendor/bundles',
));
```

### Adding bundle to kernel.

Finally, enable the bundle in the kernel.

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Sylius\Bundle\AddressingBundle\SyliusAddressingBundle(),
    );
}
```
### Creating your address class or using the standard one.

If you want to use the default address object skip this step.
Creating your own address class is pretty simple!

``` php
<?php
// src/Application/Bundle/AddressingBundle/Entity/Address.php

namespace Application\Bundle\AddressingBundle\Entity;

use Sylius\Bundle\AddressingBundle\Entity\Address as BaseAddress;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_addressing_address")
 */
class Address extends BaseAddress
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
}
```

### Container configuration.

Now you have to do the minimal configuration, no worries, it is not painful.

Open up your `config.yml` file and add this...

``` yaml
sylius_addressing:
    driver: ORM
    classes:
        model:
            address: Application\Bundle\AddressingBundle\Entity\Address
```

`Please note, that the "ORM" is currently the only supported driver.`

### Import routing files.

Now is the time to import routing files. Open up your `routing.yml` file. Customize the prefixes or whatever you want.

``` yaml
sylius_addressing_backend_address:
    resource: "@SyliusAddressingBundle/Resources/config/routing/backend/address.yml"
    prefix: /administration
```

### Updating database schema.

The last thing you need to do is updating the database schema.

For "ORM" driver run the following command.

``` bash
$ php app/console doctrine:schema:update --force
```

### Finish.

That is all, I hope it was not so bad.
Now you can visit `/administration/addresses` to see the list of addresses.

`This documentation is under construction.`
