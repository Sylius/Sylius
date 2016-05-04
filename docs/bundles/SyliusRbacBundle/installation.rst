Installation
============

We assume you're familiar with `Composer <http://packagist.org>`_, a dependency manager for PHP.
Use the following command to add the bundle to your `composer.json` and download the package.

If you have `Composer installed globally <http://getcomposer.org/doc/00-intro.md#globally>`_.

.. code-block:: bash

    $ composer require sylius/rbac-bundle

Otherwise you have to download .phar file.

.. code-block:: bash

    $ curl -sS https://getcomposer.org/installer | php
    $ php composer.phar require sylius/rbac-bundle

Adding required bundles to the kernel
-------------------------------------

You need to enable the bundle inside the kernel.

If you're not using any other Sylius bundles, you will also need to add `SyliusResourceBundle` and its dependencies to kernel.
This bundle also uses `DoctrineCacheBundle`. Don't worry, everything was automatically installed via Composer.

.. code-block:: php

    <?php

    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle($this),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
            new Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),

            new Sylius\Bundle\RbacBundle\SyliusRbacBundle(),
            new Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),

            // Other bundles...
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
        );
    }

.. note::

    Please register the bundle before *DoctrineBundle*. This is important as we use listeners which have to be processed first.

Container configuration
-----------------------

Put this configuration inside your ``app/config/config.yml``.

.. code-block:: yaml

    sylius_rbac:
        driver: doctrine/orm # Configure the doctrine orm driver used in the documentation.

And configure doctrine extensions which are used by the bundle.

.. code-block:: yaml

    stof_doctrine_extensions:
        orm:
            default:
                timestampable: true

Implement IdentityInterface
---------------------------

Your ``User`` class needs to implement ``Sylius\Component\Rbac\Model\IdentityInterface`` and define associated roles.

.. code-block:: php

    <?php

    // src/App/AppBundle/Entity/User.php
    namespace App\AppBundle\Entity;

    use Doctrine\Common\Collections\ArrayCollection;
    use Sylius\Component\Rbac\Model\IdentityInterface;
    use Sylius\Component\Rbac\Model\RoleInterface;

    class User implements IdentityInterface
    {
        private $authorizationRoles;

        public function __construct()
        {
            $this->authorizationRoles = new ArrayCollection();
        }

        public function getAuthorizationRoles()
        {
            return $this->authorizationRoles;
        }

        // Your methods for adding/removing roles.
    }

Mapping the relation
--------------------

Updating database schema
------------------------

Run the following command.

.. code-block:: bash

    $ php app/console doctrine:schema:update --force

.. warning::

    This should be done only in **dev** environment! We recommend using Doctrine migrations, to safely update your schema.

Congratulations! The bundle is now installed and ready to configure your first roles and permissions.
