Installation
============

We assume you're familiar with `Composer <http://packagist.org>`_, a dependency manager for PHP.

Use following command to add the bundle to your `composer.json` and download package.

.. code-block:: bash

    $ composer require sylius/assortment-bundle:*

Adding required bundles to the kernel
-------------------------------------

First, you need to enable the bundle inside the kernel.
If you're not using any other Sylius bundles, you will also need to add `SyliusResourceBundle` and its dependencies to kernel.
Don't worry, everything was automatically installed via Composer.

If you're not using `FOSRestBundle`, then you also have to select a serializer component.
We recommend `JMSSerializerBundle`, you can include it in your `composer.json` by executing following command.

.. code-block:: bash

    $ composer require jms/serializer-bundle:0.11.*

.. code-block:: php

    <?php

    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle($this),
            new Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),
            new Sylius\Bundle\AssortmentBundle\SyliusAssortmentBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),

            // Other bundles...
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
        );
    }

.. note::

    Please register the bundle before *DoctrineBundle*. This is important as we use listeners which have to be processed first.

Creating your entities
----------------------

You have to create your own **Product** entity, living inside your application code.
We think that **keeping the app-specific bundle structure simple** is a good practice, so
let's assume you have your ``DemoBundle`` registered under ``Acme\DemoBundle`` namespace.

.. code-block:: php

    <?php

    // src/Acme/DemoBundle/Entity/Product.php
    namespace Acme\DemoBundle\Entity;

    use Sylius\Bundle\AssortmentBundle\Entity\Product as BaseProduct;
    use Doctrine\ORM\Mapping as ORM;

    /**
     * @ORM\Entity
     */
    class Product extends BaseProduct
    {
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        protected $id;
    }

And that would be all about entities.

Container configuration
-----------------------

Put this configuration inside your ``app/config/config.yml``.

.. code-block:: yaml

    sylius_assortment:
        driver: doctrine/orm # Configure the doctrine orm driver used in documentation.
        classes:
            product:
                model: App\AppBundle\Entity\Product # Your product entity.

And configure doctrine extensions which are used in assortment bundle:

.. code-block:: yaml
    stof_doctrine_extensions:
        orm:
            default:
                sluggable: true
                timestampable: true

Routing configuration
---------------------

We will show an example here, how you can configure routing.
Routing is based on `SyliusResourceBundle`.

Add folowing to your ``app/config/routing.yml``.

.. code-block:: yaml

    sylius_assortment:
        resource: @SyliusAssortmentBundle/Resources/config/routing.yml
        prefix: /assortment

Updating database schema
------------------------

Remember to update your database schema.

For "**doctrine/orm**" driver run the following command.

.. code-block:: bash

    $ php app/console doctrine:schema:update --force

.. warning::

    This should be done only in **dev** environment! We recommend using Doctrine migrations, to safely update your schema.
