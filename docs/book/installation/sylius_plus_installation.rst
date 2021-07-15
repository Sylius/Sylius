
.. rst-class:: plus-doc

Sylius Plus Installation
========================

Sylius Plus is an advanced extension to Sylius applications that adds new features and experience.
As it is a private package it cannot be installed by every Sylius user, but only by those having the license.

Installing Sylius Plus as a plugin to a Sylius application
----------------------------------------------------------

**Important Requirements**

+---------------+-----------------------+
| PHP           | ^7.3                  |
+---------------+-----------------------+
| sylius/sylius | ^1.9                  |
+---------------+-----------------------+
| Symfony       | ^5.2                  |
+---------------+-----------------------+

**0.** Prepare project:

.. tip::

    If it is a new project you are initiating, then first install Sylius-Standard in **version ^1.10** according to
    :doc:`these instructions </book/installation/installation>`.

    If you're installing Plus package to an existing project, then make sure you're upgraded to ``sylius/sylius ^1.10``.

**1.** Configure access to the private Packagist package in composer by using the Access Token you have been given with your license.

.. code-block:: bash

    composer config --global --auth http-basic.sylius.repo.packagist.com token YOUR_TOKEN

**2.** Configure the repository with Sylius Plus for your organisation, require it and then run ``composer update``:

.. code-block:: bash

    composer config repositories.plus composer https://sylius.repo.packagist.com/ShortNameOfYourOrganization/
    composer require sylius/plus --no-update
    composer update --no-scripts
    composer sync-recipes

**3.** Configure Sylius Plus in ``config/bundles.php``:

.. code-block:: php

    // config/bundles.php

    return [
       //...
       Sylius\Plus\SyliusPlusPlugin::class => ['all' => true],
    ];

**4.** Import Sylius Plus configuration files:

.. code-block:: yaml

    # config/packages/_sylius.yaml
    imports:
    ...
        - { resource: "@SyliusPlusPlugin/Resources/config/config.yaml" }

**5.** Configure Shop, Admin and Admin API routing:

.. code-block:: yaml

    # config/routes/sylius_shop.yaml
    ...

    sylius_plus_shop:
        resource: "@SyliusPlusPlugin/Resources/config/shop_routing.yaml"
        prefix: /{_locale}
        requirements:
            _locale: ^[a-z]{2}(?:_[A-Z]{2})?$

.. code-block:: yaml

    # config/routes/sylius_admin.yaml:
    ...

    sylius_plus_admin:
        resource: "@SyliusPlusPlugin/Resources/config/admin_routing.yaml"
        prefix: /admin
.. warning::

    Not needed for Sylius Plus >= `1.0.0-ALPHA.1`

    .. code-block:: yaml

        # config/routes/sylius_admin_api.yaml:
        ...

        sylius_plus_admin_api:
            resource: "@SyliusPlusPlugin/Resources/config/api_routing.yaml"
            prefix: /api/v1

**6.** Add traits that enhance Sylius models:

* AdminUser
* Channel
* Customer
* Order
* ProductVariant
* Shipment

.. code-block:: php

    // src/Entity/User/AdminUser.php

    <?php

    declare(strict_types=1);

    namespace App\Entity\User;

    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\ORM\Mapping\Entity;
    use Doctrine\ORM\Mapping\Table;
    use Sylius\Component\Core\Model\AdminUser as BaseAdminUser;
    use Sylius\Plus\Entity\AdminUserInterface;
    use Sylius\Plus\Entity\AdminUserTrait;
    use Sylius\Plus\Rbac\Domain\Model\RoleableTrait;
    use Sylius\Plus\Rbac\Domain\Model\ToggleablePermissionCheckerTrait;

    /**
     * @Entity
     * @Table(name="sylius_admin_user")
     */
    class AdminUser extends BaseAdminUser implements AdminUserInterface
    {
        use AdminUserTrait;
        use ToggleablePermissionCheckerTrait;
        use RoleableTrait;

        public function __construct()
        {
            parent::__construct();

            $this->rolesResources = new ArrayCollection();
        }
    }

.. code-block:: php

    // src/Entity/Channel/Channel.php

    <?php

    declare(strict_types=1);

    namespace App\Entity\Channel;

    use Doctrine\ORM\Mapping\Entity;
    use Doctrine\ORM\Mapping\Table;
    use Sylius\Plus\Entity\ChannelInterface;
    use Sylius\Plus\Entity\ChannelTrait;
    use Sylius\Component\Core\Model\Channel as BaseChannel;

    /**
     * @Entity
     * @Table(name="sylius_channel")
     */
    class Channel extends BaseChannel implements ChannelInterface
    {
        use ChannelTrait;
    }

.. code-block:: php

    // src/Entity/Customer/Customer.php

    <?php

    declare(strict_types=1);

    namespace App\Entity\Customer;

    use Doctrine\ORM\Mapping\Entity;
    use Doctrine\ORM\Mapping\Table;
    use Sylius\Plus\Entity\CustomerInterface;
    use Sylius\Plus\Entity\CustomerTrait;
    use Sylius\Component\Core\Model\Customer as BaseCustomer;

    /**
     * @Entity
     * @Table(name="sylius_customer")
     */
    class Customer extends BaseCustomer implements CustomerInterface
    {
        use CustomerTrait;
    }

.. code-block:: php

    // src/Entity/Order/Order.php

    <?php

    declare(strict_types=1);

    namespace App\Entity\Order;

    use Doctrine\ORM\Mapping\Entity;
    use Doctrine\ORM\Mapping\Table;
    use Sylius\Plus\Entity\OrderInterface;
    use Sylius\Plus\Entity\OrderTrait;
    use Sylius\Component\Core\Model\Order as BaseOrder;

    /**
     * @Entity
     * @Table(name="sylius_order")
     */
    class Order extends BaseOrder implements OrderInterface
    {
        use OrderTrait;
    }

.. code-block:: php

    // src/Entity/Product/ProductVariant.php

    <?php

    declare(strict_types=1);

    namespace App\Entity\Product;

    use Doctrine\ORM\Mapping\Entity;
    use Doctrine\ORM\Mapping\Table;
    use Sylius\Component\Core\Model\ProductVariant as BaseProductVariant;
    use Sylius\Component\Product\Model\ProductVariantTranslationInterface;
    use Sylius\Plus\Entity\ProductVariantInterface;
    use Sylius\Plus\Entity\ProductVariantTrait;

    /**
    * @Entity
    * @Table(name="sylius_product_variant")
    */
    class ProductVariant extends BaseProductVariant implements ProductVariantInterface
    {
        use ProductVariantTrait {
            __construct as private initializeProductVariantTrait;
        }

        public function __construct()
        {
            parent::__construct();

            $this->initializeProductVariantTrait();
        }

        protected function createTranslation(): ProductVariantTranslationInterface
        {
            return new ProductVariantTranslation();
        }
    }

.. code-block:: php

    // src/Entity/Shipping/Shipment.php

    <?php

    declare(strict_types=1);

    namespace App\Entity\Shipping;

    use Doctrine\ORM\Mapping\Entity;
    use Doctrine\ORM\Mapping\Table;
    use Sylius\Component\Core\Model\Shipment as BaseShipment;
    use Sylius\Plus\Entity\ShipmentInterface;
    use Sylius\Plus\Entity\ShipmentTrait;

    /**
     * @Entity
     * @Table(name="sylius_shipment")
     */
    class Shipment extends BaseShipment implements ShipmentInterface
    {
        use ShipmentTrait;
    }

**7.** Add wkhtmltopdf binary for Invoicing purposes.

If you do not have the ``wkhtmltopdf`` binary, download it `here <https://wkhtmltopdf.org/downloads.html>`_.

In case wkhtmltopdf is not located in ``/usr/local/bin/wkhtmltopdf``, add the following snippet at the end of
your application's ``.env`` file:

.. code-block:: yaml

    ###> knplabs/knp-snappy-bundle ###
    WKHTMLTOPDF_PATH=/your-path
    ###< knplabs/knp-snappy-bundle ###

**8.** Install Sylius with Sylius Plus fixtures:

.. code-block:: bash

    bin/console sylius:install --fixture-suite plus

.. tip::

    If you want to completely (re)install the application, you can run this command with the no interaction flag ``-n``.

    .. code-block:: bash

        bin/console sylius:install --fixture-suite plus -n

**9.** Copy templates that are overridden by Sylius Plus into ``templates/bundles``:

.. code-block:: bash

    cp -fr vendor/sylius/plus/src/Resources/templates/bundles/* templates/bundles

**10.** Install JS libraries using Yarn:

.. code-block:: bash

    yarn install
    yarn build
    bin/console assets:install --ansi

**11.** Rebuild cache for proper display of all translations:

.. code-block:: bash

    bin/console cache:clear
    bin/console cache:warmup

**12.** For more details check the installation guides for all plugins installed as dependencies with Sylius Plus.

* `Sylius/InvoicingPlugin <https://github.com/Sylius/InvoicingPlugin/blob/master/README.md#installation>`_
* `Sylius/RefundPlugin <https://github.com/Sylius/RefundPlugin/blob/master/README.md#installation>`_

**Phew! That's all, you can now run the application just like you usually do with Sylius (using Symfony Server for example).**

Upgrading Sylius Plus
---------------------

To upgrade Sylius Plus in an existing application, please follow upgrade instructions from
`Sylius/PlusInformationCenter <https://github.com/Sylius/PlusInformationCenter>`_ repository.

.. image:: ../../_images/sylius_plus/banner.png
    :align: center
    :target: https://sylius.com/plus/?utm_source=docs
