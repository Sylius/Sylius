How to extend SyliusBundles and link those to an existing Sylius's Project Database?
====================================================================================

In some cases and from another Symfony project (non Sylius/Sylius-Standard || Sylius/Sylius installation) you may be needing to be able to access/manipulate existing Sylius's data.

To be able to achieve this, you will need to install the desired Sylius's bundles, extend those, and link those to another Entity Manager which point to your existing Sylius's database.

For the sake of this guide, let's assume we want to access Sylius's users and addresses. In this way we are going to use SyliusCustomerBundle, SyliusUserBundle, SyliusAddressingBundle.

Installing the SyliusBundles
----------------------------

- Install SyliusCustomerBundle
- Install SyliusUserBundle
- Install SyliusAddressingBundle

.. tip::

    Read more about how to install SyliusCustomerBundle :doc:`here </components_and_bundles/bundles/SyliusCustomerBundle/installation>`.

.. tip::

    Read more about how to install SyliusUserBundle :doc:`here </components_and_bundles/bundles/SyliusUserBundle/installation>`.

.. tip::

    Read more about how to install SyliusAddressingBundle :doc:`here </components_and_bundles/bundles/SyliusAddressingBundle/installation>`.

Extending the SyliusBundles
---------------------------

1. Generating our own bundles:

- Generate CustomerBundle
- Generate UserBundle
- Generate AddressingBundle

.. tip::

    Read more about how to generate your own Symfony's bundle `here <https://symfony.com/doc/current/bundles/SensioGeneratorBundle/commands/generate_bundle.html>`_.

2. Extending the Sylius's bundles:

.. code-block:: php

    <?php

    namespace CustomerBundle;

    use Symfony\Component\HttpKernel\Bundle\Bundle;

    class CustomerBundle extends Bundle
    {
        public function getParent()
        {
            return 'SyliusCustomerBundle';
        }
    }

.. code-block:: php

    <?php

    namespace UserBundle;

    use Symfony\Component\HttpKernel\Bundle\Bundle;

    class UserBundle extends Bundle
    {
        public function getParent()
        {
            return 'SyliusUserBundle';
        }
    }

.. code-block:: php

    <?php

    namespace AddressingBundle;

    use Symfony\Component\HttpKernel\Bundle\Bundle;

    class AddressingBundle extends Bundle
    {
        public function getParent()
        {
            return 'SyliusAddressingBundle';
        }
    }

3. Override the Sylius's bundles config and link our models to some_other_em:

.. code-block:: php

    <?php

    namespace CustomerBundle\DependencyInjection;

    use Symfony\Component\Config\Definition\Builder\TreeBuilder;
    use Symfony\Component\Config\Definition\ConfigurationInterface;

    final class Configuration implements ConfigurationInterface
    {
        public function getConfigTreeBuilder()
        {
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('sylius_customer');

            return $treeBuilder;
        }
    }

.. code-block:: php

    <?php

    namespace CustomerBundle\DependencyInjection;

    use Symfony\Component\Config\FileLocator;
    use Symfony\Component\DependencyInjection\ContainerBuilder;
    use Symfony\Component\DependencyInjection\Loader;
    use Symfony\Component\HttpKernel\DependencyInjection\Extension;

    class CustomerExtension extends Extension
    {
        public function load(array $configs, ContainerBuilder $container)
        {
            $configuration = new Configuration();
            $config = $this->processConfiguration($configuration, $configs);

            $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
            $loader->load('services.yml');
        }
    }

.. code-block:: yaml

    # src/CustomerBundle/Resources/config/config.yml

    sylius_customer:
        driver: doctrine/orm
        resources:
            customer:
                options:
                    object_manager: some_other_em
                classes:
                    model: CustomerBundle\Entity\Customer
                    interface: Sylius\Component\Customer\Model\CustomerInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    factory: Sylius\Component\Resource\Factory\Factory
            customer_group:
                options:
                    object_manager: some_other_em
                classes:
                    model: Sylius\Component\Customer\Model\CustomerGroup
                    interface: Sylius\Component\Customer\Model\CustomerGroupInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    factory: Sylius\Component\Resource\Factory\Factory

.. code-block:: php

    <?php

    namespace UserBundle\DependencyInjection;

    use Symfony\Component\Config\Definition\Builder\TreeBuilder;
    use Symfony\Component\Config\Definition\ConfigurationInterface;

    final class Configuration implements ConfigurationInterface
    {
        public function getConfigTreeBuilder()
        {
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('sylius_user');

            return $treeBuilder;
        }
    }

.. code-block:: php

    <?php

    namespace UserBundle\DependencyInjection;

    use Symfony\Component\Config\FileLocator;
    use Symfony\Component\DependencyInjection\ContainerBuilder;
    use Symfony\Component\DependencyInjection\Loader;
    use Symfony\Component\HttpKernel\DependencyInjection\Extension;

    class UserExtension extends Extension
    {
        public function load(array $configs, ContainerBuilder $container)
        {
            $configuration = new Configuration();
            $config = $this->processConfiguration($configuration, $configs);

            $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
            $loader->load('services.yml');
        }
    }

.. code-block:: yaml

    # src/UserBundle/Resources/config/config.yml

    sylius_user:
        driver: doctrine/orm
        resources:
            shop:
                user:
                    options:
                        object_manager: some_other_em
                    classes:
                        model: UserBundle\Entity\ShopUser
                        repository: Sylius\Bundle\UserBundle\Doctrine\ORM\UserRepository
                        interface: Sylius\Component\User\Model\UserInterface
                        controller: Sylius\Bundle\UserBundle\Controller\UserController
                        factory: Sylius\Component\Resource\Factory\Factory
                    templates: 'SyliusUserBundle:User'
                    resetting:
                        token:
                            ttl: P1D
                            length: 16
                            field_name: passwordResetToken
                        pin:
                            length: 4
                            field_name: passwordResetToken
                    verification:
                        token:
                            length: 16
                            field_name: emailVerificationToken
            oauth:
                user:
                    options:
                        object_manager: some_other_em
                    classes:
                        model: Sylius\Component\User\Model\UserOAuth
                        interface: Sylius\Component\User\Model\UserOAuthInterface
                        controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                        factory: Sylius\Component\Resource\Factory\Factory
                        form: Sylius\Bundle\UserBundle\Form\Type\UserType
                    templates: 'SyliusUserBundle:User'
                    resetting:
                        token:
                            ttl: P1D
                            length: 16
                            field_name: passwordResetToken
                        pin:
                            length: 4
                            field_name: passwordResetToken
                    verification:
                        token:
                            length: 16
                            field_name: emailVerificationToken

.. code-block:: php

    <?php

    namespace AddressingBundle\DependencyInjection;

    use Symfony\Component\Config\Definition\Builder\TreeBuilder;
    use Symfony\Component\Config\Definition\ConfigurationInterface;

    final class Configuration implements ConfigurationInterface
    {
        public function getConfigTreeBuilder()
        {
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('sylius_addressing');

            return $treeBuilder;
        }
    }

.. code-block:: php

    <?php

    namespace AddressingBundle\DependencyInjection;

    use Symfony\Component\Config\FileLocator;
    use Symfony\Component\DependencyInjection\ContainerBuilder;
    use Symfony\Component\DependencyInjection\Loader;
    use Symfony\Component\HttpKernel\DependencyInjection\Extension;

    class AddressingExtension extends Extension
    {
        public function load(array $configs, ContainerBuilder $container)
        {
            $configuration = new Configuration();
            $config = $this->processConfiguration($configuration, $configs);

            $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
            $loader->load('services.yml');
        }
    }

.. code-block:: yaml

    # src/AddressingBundle/Resources/config/config.yml

    sylius_addressing:
        driver: doctrine/orm
        resources:
            address:
                options:
                    object_manager: some_other_em
                classes:
                    model: AddressingBundle\Entity\Address
                    interface: Sylius\Component\Addressing\Model\AddressInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    factory: Sylius\Component\Resource\Factory\Factory
                    form: Sylius\Bundle\AddressingBundle\Form\Type\AddressType
            country:
                options:
                    object_manager: some_other_em
                classes:
                    model: Sylius\Component\Addressing\Model\Country
                    interface: Sylius\Component\Addressing\Model\CountryInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    factory: Sylius\Component\Resource\Factory\Factory
                    form: Sylius\Bundle\AddressingBundle\Form\Type\CountryType
            province:
                options:
                    object_manager: some_other_em
                classes:
                    model: Sylius\Component\Addressing\Model\Province
                    interface: Sylius\Component\Addressing\Model\ProvinceInterface
                    controller: Sylius\Bundle\AddressingBundle\Controller\ProvinceController
                    factory: Sylius\Component\Resource\Factory\Factory
                    form: Sylius\Bundle\AddressingBundle\Form\Type\ProvinceType
            zone:
                options:
                    object_manager: some_other_em
                classes:
                    model: Sylius\Component\Addressing\Model\Zone
                    interface: Sylius\Component\Addressing\Model\ZoneInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    factory: Sylius\Component\Resource\Factory\Factory
                    form: Sylius\Bundle\AddressingBundle\Form\Type\ZoneType
            zone_member:
                options:
                    object_manager: some_other_em
                classes:
                    model: Sylius\Component\Addressing\Model\ZoneMember
                    interface: Sylius\Component\Addressing\Model\ZoneMemberInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    factory: Sylius\Component\Resource\Factory\Factory
                    form: Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberType

4. Import our new config files to the global config

.. code-block:: yaml

    # app/config/config.yml

    imports:
        - { resource: "@CustomerBundle/Resources/config/config.yml" }
        - { resource: "@UserBundle/Resources/config/config.yml" }
        - { resource: "@AddressingBundle/Resources/config/config.yml" }

5. Add the proper ORM mapping in the global config

.. code-block:: yaml

    # app/config/config.yml

    # Doctrine Configuration
    doctrine:
        orm:
            auto_generate_proxy_classes: '%kernel.debug%'
            default_entity_manager: default
            resolve_target_entities:
                Sylius\Component\User\Model\CustomerInterface: CustomerBundle\Entity\Customer
                Sylius\Component\User\Model\UserInterface: UserBundle\Entity\ShopUser
                Sylius\Component\Addressing\Model\AddressInterface: AddressingBundle\Entity\Address
            entity_managers:
                default:
                    ...

                some_other_em:
                    naming_strategy: doctrine.orm.naming_strategy.underscore
                    connection: some_other_connexion
                    auto_mapping: false
                    mappings:
                        SyliusCustomerBundle:
                            type: xml
                            dir: "%kernel.project_dir%/vendor/sylius/customer-bundle/Resources/config/doctrine/model"
                            prefix: Sylius\Component\Customer\Model
                            is_bundle: false
                        CustomerBundle: ~
                        SyliusUserBundle:
                            type: xml
                            dir: "%kernel.project_dir%/vendor/sylius/user-bundle/Resources/config/doctrine/model"
                            prefix: Sylius\Component\User\Model
                            is_bundle: false
                        UserBundle: ~
                        SyliusAddressingBundle:
                            type: xml
                            dir: "%kernel.project_dir%/vendor/sylius/addressing-bundle/Resources/config/doctrine/model"
                            prefix: Sylius\Component\Addressing\Model
                            is_bundle: false
                        AddressingBundle: ~

6. Override the Sylius's models and add the missing relations:

As the Sylius's models which hold the declaration and the mapping of the relations between, in our case, SyliusCustomer, SyliusUser and SyliusAddressing are provided by the SyliusCoreBundle and as we don't have access to it we need to redefine the relations and their related mapping on our bundles.

.. code-block:: php

    <?php

    namespace CustomerBundle\Entity;

    use Sylius\Component\Customer\Model\Customer as BaseCustomer;
    use Doctrine\Common\Collections\Collection;
    use Doctrine\Common\Collections\ArrayCollection;
    use AddressingBundle\Entity\Address;
    use UserBundle\Entity\ShopUser;

    class Customer extends BaseCustomer
    {
        private $defaultAddress;
        private $user;
        private $addresses;

        public function __construct()
        {
            parent::__construct();

            $this->addresses = new ArrayCollection();
        }

        /**
         * Set defaultAddress
         *
         * @param Address $defaultAddress
         *
         * @return Customer
         */
        public function setDefaultAddress(Address $defaultAddress = null)
        {
            $this->defaultAddress = $defaultAddress;

            if (null !== $defaultAddress)
            {
                $this->addAddress($defaultAddress);
            }

            return $this;
        }

        /**
         * Get defaultAddress
         *
         * @return Address
         */
        public function getDefaultAddress()
        {
            return $this->defaultAddress;
        }

        /**
         * Set user
         *
         * @param ShopUser $user
         *
         * @return Customer
         */
        public function setUser(ShopUser $user = null)
        {
            $this->user = $user;

            return $this;
        }

        /**
         * Get user
         *
         * @return ShopUser
         */
        public function getUser()
        {
            return $this->user;
        }

        /**
         * Add address
         *
         * @param Address $address
         *
         * @return Customer
         */
        public function addAddress(Address $address)
        {
            if (!$this->hasAddress($address))
            {
                $this->addresses[] = $address;
                $address->setCustomer($this);
            }

            return $this;
        }

        /**
         * Remove address
         *
         * @param Address $address
         */
        public function removeAddress(Address $address)
        {
            $this->addresses->removeElement($address);
            $address->setCustomer(null);
        }

        /**
         * Get addresses
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getAddresses()
        {
            return $this->addresses;
        }

        public function hasAddress(Address $address)
        {
            return $this->addresses->contains($address);
        }
    }

.. code-block:: php

    <?php

    namespace UserBundle\Entity;

    use Sylius\Component\User\Model\User as BaseUser;

    class ShopUser extends BaseUser
    {
        private $customer;

        /**
        * Get customer
        * @return
        */
        public function getCustomer()
        {
            return $this->customer;
        }

        /**
        * Set customer
        * @return $this
        */
        public function setCustomer($customer)
        {
            $this->customer = $customer;
            return $this;
        }
    }

.. code-block:: php

    <?php

    namespace AddressingBundle\Entity;

    use Sylius\Component\Addressing\Model\Address as BaseAddress;
    use CustomerBundle\Entity\Customer;

    class Address extends BaseAddress
    {
        private $customer;

        /**
         * Set customer
         *
         * @param Customer $customer
         *
         * @return Address
         */
        public function setCustomer(Customer $customer = null)
        {
            $this->customer = $customer;

            return $this;
        }

        /**
         * Get customer
         *
         * @return Customer
         */
        public function getCustomer()
        {
            return $this->customer;
        }
    }

7. Add the proper ORM mapping to our models:

.. code-block:: yaml

    # src/CustomerBundle/Resources/config/doctrine/Customer.orm.yml

    CustomerBundle\Entity\Customer:
        type: entity
        table: sylius_customer
        oneToOne:
            defaultAddress:
                targetEntity: AddressingBundle\Entity\Address
                joinColumn:
                    name: default_address_id
                    onDelete: SET NULL
                cascade: ["persist"]
            user:
                targetEntity: UserBundle\Entity\ShopUser
                mappedBy: customer
                cascade: ["persist"]
        oneToMany:
            addresses:
                targetEntity: AddressingBundle\Entity\Address
                mappedBy: customer
                cascade: ["all"]

.. code-block:: yaml

    # src/UserBundle/Resources/config/doctrine/ShopUser.orm.yml

    UserBundle\Entity\ShopUser:
        type: entity
        table: sylius_shop_user
        oneToOne:
            customer:
                targetEntity: CustomerBundle\Entity\Customer
                inversedBy: user
                joinColumn:
                    name: customer_id
                    referencedColumnName: id
                    nullable: false
                cascade: ["persist"]

.. code-block:: yaml

    # src/AddressingBundle/Resources/config/doctrine/Address.orm.yml

    AddressingBundle\Entity\Address:
        type: entity
        table: sylius_address
        manyToOne:
            customer:
                targetEntity: CustomerBundle\Entity\Customer
                inversedBy: addresses
                joinColumn:
                    name: customer_id
                    referencedColumnName: id
                    nullable: true
                    onDelete: CASCADE

8. Final steps:

- Clear both caches

At this point you should be able to test the ORM mapping of our "some_other_em" entity manager by calling:

.. code-block:: bash

    $ php bin/console doctrine:schema:update --dump-sql --em=some_other_em

It should returns(as we did not add any new property to our models):

.. code-block:: bash

    Nothing to update - your database is already in sync with the current entity metadata.

9. An "issue":

If you try in another hand to call a schema update on the default EM:

.. code-block:: bash

    $ php bin/console doctrine:schema:update --dump-sql

It should returns:

.. code-block:: bash

    [Doctrine\Common\Persistence\Mapping\MappingException]
    The class 'UserBundle\Entity\ShopUser' was not found in the chain configured namespaces App\Entity, Sylius\Component\Customer\Model, Sylius\Component\User\Model, Sylius\Component\Ad
    dressing\Model

This seems to be a "known issue" related to the shema-tool CLI command, as obviously this command uses all the metadata collected across all mapping drivers.

To fix this I overriden the UpdateSchemaDoctrineCommand and excluded all the Sylius metadatas when the default entity manager is specified.

.. code-block:: php

    <?php

    namespace App\Command;

    use Symfony\Component\Console\Input\InputOption;
    use Symfony\Component\Console\Input\InputArgument;
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Output\OutputInterface;
    use Doctrine\ORM\Tools\SchemaTool;
    use Doctrine\Bundle\DoctrineBundle\Command\Proxy\UpdateSchemaDoctrineCommand;

    class DoctrineUpdateCommand extends UpdateSchemaDoctrineCommand
    {

        protected function executeSchemaCommand(InputInterface $input, OutputInterface $output, SchemaTool $schemaTool, array $metadatas)
        {
            $newMetadatas = array();
            foreach ($metadatas as $metadata)
            {
                if (empty($input->getOption('em')) || $input->getOption('em') == 'default')
                {
                    if (explode('\\', $metadata->getName())[0] != 'Sylius')
                    {
                        array_push($newMetadatas, $metadata);
                    }
                }
                else
                {
                    array_push($newMetadatas, $metadata);
                }
            }

            parent::executeSchemaCommand($input, $output, $schemaTool, $newMetadatas);
        }

    }
