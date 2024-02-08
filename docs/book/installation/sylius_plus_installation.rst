
.. rst-class:: plus-doc

Sylius Plus Installation
========================

Sylius Plus is an advanced extension to Sylius applications that adds new features and experience.
As it is a private package it cannot be installed by every Sylius user, but only by those having the license.

Installing Sylius Plus as a plugin to a Sylius application
----------------------------------------------------------

**Important Requirements**

+---------------+-----------------------+
| PHP           | ^8.0                  |
+---------------+-----------------------+
| sylius/sylius | ~1.11.2 || ~1.12.0    |
+---------------+-----------------------+
| Symfony       | ^5.4 || ^6.0          |
+---------------+-----------------------+

**0.** Prepare project:

.. tip::

    If it is a new project you are initiating, then first install Sylius-Standard in **version ^1.11 or ^1.12** according to
    :doc:`these instructions </book/installation/installation>`.

    If you're installing Plus package to an existing project, then make sure you're upgraded to ``sylius/sylius ^1.11`` or ``sylius/sylius ^1.12``.

**1.** Configure access to the private Packagist package in composer by using the Access Token you have been given with your license.

.. code-block:: bash

    composer config --global --auth http-basic.sylius.repo.packagist.com token YOUR_TOKEN

**2.** Configure the repository with Sylius Plus for your organisation, require it and then run ``composer update``:

.. code-block:: bash

    composer config repositories.plus composer https://sylius.repo.packagist.com/ShortNameOfYourOrganization/
    composer require "sylius/plus:^1.0.0@beta" --no-update
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
    # ...
        - { resource: "@SyliusPlusPlugin/Resources/config/config.yaml" }

.. warning::

    Make sure you are importing Sylius Plus configuration as the last resource. You can encounter problems with customization when it is not the last imported resource. Especially when Invoicing Plugin or Refund Plugin is installed.

**5.** Configure Shop, Admin and Admin API routing:

.. code-block:: yaml

    # config/routes/sylius_shop.yaml
    # ...

    sylius_plus_shop:
        resource: "@SyliusPlusPlugin/Resources/config/shop_routing.yaml"
        prefix: /{_locale}
        requirements:
            _locale: ^[a-z]{2}(?:_[A-Z]{2})?$

.. code-block:: yaml

    # config/routes/sylius_admin.yaml:
    # ...

    sylius_plus_admin:
        resource: "@SyliusPlusPlugin/Resources/config/admin_routing.yaml"
        prefix: /admin

.. warning:: Skip `sylius_plus_shop` if you are not using SyliusShopBundle
          and `sylius_plus_admin` if you are not using SyliusAdminBundle.

**6.** Update security providers in ``config/packages/security.yaml``:

.. code-block:: yaml

    # config/packages/security.yaml
    providers:
        # ...
        sylius_shop_user_provider:
            id: Sylius\Plus\CustomerPools\Infrastructure\Provider\UsernameAndCustomerPoolProvider
        sylius_api_shop_user_provider:
            id: Sylius\Plus\CustomerPools\Infrastructure\Provider\UsernameAndCustomerPoolProvider

**7.** Add traits that enhance Sylius models:

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
    use Doctrine\ORM\Mapping as ORM;
    use Sylius\Component\Core\Model\AdminUser as BaseAdminUser;
    use Sylius\Component\Core\Model\AdminUserInterface;
    use Sylius\Plus\ChannelAdmin\Domain\Model\AdminChannelAwareTrait;
    use Sylius\Plus\Entity\LastLoginIpAwareInterface;
    use Sylius\Plus\Entity\LastLoginIpAwareTrait;
    use Sylius\Plus\Rbac\Domain\Model\AdminUserInterface as RbacAdminUserInterface;
    use Sylius\Plus\Rbac\Domain\Model\RoleableTrait;
    use Sylius\Plus\Rbac\Domain\Model\ToggleablePermissionCheckerTrait;

    /**
     * @ORM\Entity
     * @ORM\Table(name="sylius_admin_user")
     */
    class AdminUser extends BaseAdminUser implements AdminUserInterface, RbacAdminUserInterface, LastLoginIpAwareInterface
    {
        use AdminChannelAwareTrait;
        use LastLoginIpAwareTrait;
        use RoleableTrait;
        use ToggleablePermissionCheckerTrait;

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

    use Doctrine\ORM\Mapping as ORM;
    use Sylius\Component\Core\Model\Channel as BaseChannel;
    use Sylius\Component\Core\Model\ChannelInterface;
    use Sylius\Plus\BusinessUnits\Domain\Model\BusinessUnitAwareTrait;
    use Sylius\Plus\BusinessUnits\Domain\Model\ChannelInterface as BusinessUnitsChannelInterface;
    use Sylius\Plus\CustomerPools\Domain\Model\ChannelInterface as CustomerPoolsChannelInterface;
    use Sylius\Plus\CustomerPools\Domain\Model\CustomerPoolAwareTrait;
    use Sylius\Plus\Returns\Domain\Model\ChannelInterface as ReturnsChannelInterface;
    use Sylius\Plus\Returns\Domain\Model\ReturnRequestsAllowedAwareTrait;

    /**
     * @ORM\Entity
     * @ORM\Table(name="sylius_channel")
     */
    class Channel extends BaseChannel implements ChannelInterface, ReturnsChannelInterface, BusinessUnitsChannelInterface, CustomerPoolsChannelInterface
    {
        use BusinessUnitAwareTrait;
        use CustomerPoolAwareTrait;
        use ReturnRequestsAllowedAwareTrait;
    }

.. code-block:: php

    // src/Entity/Customer/Customer.php
    <?php

    declare(strict_types=1);

    namespace App\Entity\Customer;

    use Doctrine\ORM\Mapping as ORM;
    use Sylius\Component\Core\Model\Customer as BaseCustomer;
    use Sylius\Component\Core\Model\CustomerInterface;
    use Sylius\Plus\CustomerPools\Domain\Model\CustomerInterface as CustomerPoolsCustomerInterface;
    use Sylius\Plus\CustomerPools\Domain\Model\CustomerPoolAwareTrait;
    use Sylius\Plus\Loyalty\Domain\Model\CustomerInterface as LoyaltyCustomerInterface;
    use Sylius\Plus\Loyalty\Domain\Model\LoyaltyAwareTrait;

    /**
     * @ORM\Entity
     * @ORM\Table(name="sylius_customer")
     */
    class Customer extends BaseCustomer implements CustomerInterface, CustomerPoolsCustomerInterface, LoyaltyCustomerInterface
    {
        use CustomerPoolAwareTrait;
        use LoyaltyAwareTrait;
    }

.. code-block:: php

    // src/Entity/Order/Order.php
    <?php

    declare(strict_types=1);

    namespace App\Entity\Order;

    use Doctrine\ORM\Mapping as ORM;
    use Sylius\Component\Core\Model\Order as BaseOrder;
    use Sylius\Component\Core\Model\OrderInterface;
    use Sylius\Plus\Returns\Domain\Model\OrderInterface as ReturnsOrderInterface;
    use Sylius\Plus\Returns\Domain\Model\ReturnRequestAwareTrait;

    /**
     * @ORM\Entity
     * @ORM\Table(name="sylius_order")
     */
    class Order extends BaseOrder implements OrderInterface, ReturnsOrderInterface
    {
        use ReturnRequestAwareTrait;
    }

.. code-block:: php

    // src/Entity/Product/ProductVariant.php
    <?php

    declare(strict_types=1);

    namespace App\Entity\Product;

    use Doctrine\ORM\Mapping as ORM;
    use Sylius\Component\Core\Model\ProductVariant as BaseProductVariant;
    use Sylius\Component\Core\Model\ProductVariantInterface;
    use Sylius\Component\Product\Model\ProductVariantTranslationInterface;
    use Sylius\Plus\Inventory\Domain\Model\InventorySourceStocksAwareTrait;
    use Sylius\Plus\Inventory\Domain\Model\ProductVariantInterface as InventoryProductVariantInterface;

    /**
     * @ORM\Entity()
     * @ORM\Table(name="sylius_product_variant")
     */
    class ProductVariant extends BaseProductVariant implements ProductVariantInterface, InventoryProductVariantInterface
    {
        use InventorySourceStocksAwareTrait {
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

    use Doctrine\ORM\Mapping as ORM;
    use Sylius\Component\Core\Model\Shipment as BaseShipment;
    use Sylius\Component\Core\Model\ShipmentInterface;
    use Sylius\Plus\Inventory\Domain\Model\InventorySourceAwareTrait;
    use Sylius\Plus\Inventory\Domain\Model\ShipmentInterface as InventoryShipmentInterface;

    /**
     * @ORM\Entity()
     * @ORM\Table(name="sylius_shipment")
     */
    class Shipment extends BaseShipment implements ShipmentInterface, InventoryShipmentInterface
    {
        use InventorySourceAwareTrait;
    }

**8.** Install wkhtmltopdf binary:

Default configuration assumes enabled PDF file generator. If you don't want to use that feature change your app configuration:

.. code-block:: yaml

    # config/packages/sylius_plus.yaml
    sylius_plus:
        pdf_generator:
            enabled: false

.. warning::

    Sylius Plus uses both the Sylius Invoicing and Sylius Refund plugins which have their own configuration for disabling PDF Generator.


Check if you have wkhtmltopdf binary.
If not, you can download it `here <https://wkhtmltopdf.org/downloads.html>`_.

By default wkhtmltopdf is installed in ``/usr/local/bin/wkhtmltopdf`` directory.

.. tip::

    If you not sure if you have already installed wkhtmltopdf and where it is located, write the following command in the terminal:
    ``which wkhtmltopdf``

In case wkhtmltopdf is not located in ``/usr/local/bin/wkhtmltopdf``, add the following snippet at the end of
your application's ``.env`` file:

.. code-block:: yaml

    ###> knplabs/knp-snappy-bundle ###
    WKHTMLTOPDF_PATH=/your-path
    ###< knplabs/knp-snappy-bundle ###

**9.** Update the database using migrations:

.. code-block:: bash

    bin/console doctrine:migrations:migrate

**10.** Install Sylius with Sylius Plus fixtures:

.. code-block:: bash

    bin/console sylius:install -s plus

.. tip::

    If you want to completely (re)install the application, you can run this command with the no interaction flag ``-n``.

    .. code-block:: bash

        bin/console sylius:install -s plus -n

**11.** Install JS libraries using Yarn:

.. code-block:: bash

    yarn install
    yarn build
    bin/console assets:install --ansi

**12.** Rebuild cache for proper display of all translations:

.. code-block:: bash

    bin/console cache:clear
    bin/console cache:warmup

**13.** For more details check the installation guides for all plugins installed as dependencies with Sylius Plus.

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
