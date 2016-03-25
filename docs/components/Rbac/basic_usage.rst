Basic Usage
===========

To use RBAC without Symfony2, you need to implement your own services.

Permissions and Roles
---------------------

Permissions are connected to Roles, rather than concrete users.
Every Identity can have multiple roles, which inherits all permissions from their child roles.

.. code-block:: php

    <?php

    use Sylius\Component\Rbac\Model\Role;
    use Sylius\Component\Rbac\Model\Permission;

    // Assume that in your application you want to have two roles and some permissions to manage customer.

    // Let's start with creating root permission.
    $manageCustomer = new Permission();
    $manageCustomer->setCode('sylius.manage.customer');
    $manageCustomer->setDescription('Manage customers');

    // Next create more specific permissions.
    $deleteCustomer = new Permission();
    $deleteCustomer->setCode('sylius.delete.customer');
    $deleteCustomer->setDescription('Delete customer');

    $createCustomer = new Permission();
    $createCustomer->setCode('sylius.create.customer');
    $createCustomer->setDescription('Create customer');

    // Now take care of permission inheritance.
    $manageCustomer->addChild($deleteCustomer);

    $manageCustomer->addChild($createCustomer);
    //Great! Now we have Customer Manager permission which inherits above permissions.

    // Roles are defined as tree structure as well, it is the same rule as with permissions.
    // Let's start with our root role.
    $administrator = new Role();
    $administrator->setCode('administrator');
    $administrator->setName('Administrator');

    $customerManager = new Role();
    $customerManager->setCode('customer_manager');
    $customerManager->setName('Customer Manager');

    // Take care of role inheritance.
    $administrator->addChild($customerManager);

    $customerManager->addPermission($manageCustomer);
    
    // Now Administrator and Catalog Manager have permission to manage customer.

.. _component_rbac_authorization_authorization-checker:

AuthorizationChecker
--------------------

The **AuthorizationChecker** checks whether current identity has permission.

.. code-block:: php

    <?php

    use Sylius\Component\Rbac\Authorization\AuthorizationChecker;
    use Sylius\Component\Rbac\Provider\CurrentIdentityProviderInterface;
    use Sylius\Component\Resource\Repository\InMemoryRepository;
    use Sylius\Component\Rbac\Authorization\PermissionMap;
    use Sylius\Component\Rbac\Provider\PermissionProvider;
    use Sylius\Component\Rbac\Resolver\PermissionsResolver;
    use Sylius\Component\Rbac\Resolver\RolesResolver;

    class CurrentIdentityProvider implements CurrentIdentityProviderInterface
    {
        /**
         * Get the identity.
         *
         * @return IdentityInterface
         */
        public function getIdentity()
        {
            // TODO: Implement getIdentity() method. It should return your identity object for example Employee which implements IdentityInterface.
        }
    }

    $permissions = new InMemoryRepository();

    $currentIdentityProvider = new CurrentIdentityProvider();
    $permissionProvider = new PermissionProvider($permissions); // Retrieves your permission from storage by given code.
    $permissionResolver = new PermissionsResolver(); // Retrieves permissions from given role.
    $rolesResolver = new RolesResolver(); // Retrieves roles from given identity.
    $permissionMap = new PermissionMap($permissionProvider, $permissionResolver); // Retrieves permission from given role and it can check if given role has permission by his code.

    $authChecker = new AuthorizationChecker($currentIdentityProvider, $permissionMap, $rolesResolver);
    $authChecker->isGranted('sylius.manage.customer'); // It will check if current identity has permission. Output will be true or false.

.. _component_rbac_authorization_permission-map:

PermissionMap
-------------

The **PermissionMap** allows you to get permissions from given role.

.. code-block:: php

    <?php

    use Sylius\Component\Resource\Repository\InMemoryRepository;
    use Sylius\Component\Rbac\Authorization\PermissionMap;
    use Sylius\Component\Rbac\Provider\PermissionProvider;
    use Sylius\Component\Rbac\Resolver\PermissionsResolver;
    use Sylius\Component\Rbac\Model\Role;
    use Sylius\Component\Rbac\Model\Permission;

    $manageCustomer = new Permission();
    $manageCustomer->setCode('sylius.manage.customer');
    $manageCustomer->setDescription('Manage customers');

    $customerManager = new Role();
    $customerManager->setCode('customer_manager');
    $customerManager->setName('Customer Manager');

    $customerManager->addPermission($manageCustomer);

    $permissions = new InMemoryRepository();

    $permissionProvider = new PermissionProvider($permissions); // Retrieves your permission from storage by given code.
    $permissionResolver = new PermissionsResolver(); // Retrieves permissions from given role.
    $permissionMap = new PermissionMap($permissionProvider, $permissionResolver); // Retrieves permission from given role and it can check if given role has permission by code.

    $permissionMap->getPermissions($customerManager); // Retrieves permissions for given role.
    $permissionMap->hasPermission($customerManager, 'sylius.manage.customer'); // Output will be true.

.. caution::
    This service can throw `PermissionNotFoundException`_.

.. _PermissionNotFoundException: http://api.sylius.org/Sylius/Component/Rbac/Exception/PermissionNotFoundException.html

.. _component_rbac_authorization_cached-permission-map:

CachedPermissionMap
-------------------

If you need to get faster access to permissions you can use cache system in your application.


.. _component_rbac_provider_permission-provider:

PermissionProvider
------------------

.. code-block:: php

    <?php

    use Sylius\Component\Rbac\Provider\PermissionProvider;
    use Sylius\Component\Resource\Repository\InMemoryRepository;

    $permissions = new InMemoryRepository();

    $permissionProvider = new PermissionProvider($permissions);
    $permissionProvider->getPermission('sylius.manage.customer'); // It returns permission by code from your storage.

.. _component_rbac_resolver_resolvers_and_iterators:

Resolvers and Iterators
-----------------------

Permissions and roles are in tree model so basically resolvers and iterators have implemented logic to fetch leafs of given permission or role.

.. code-block:: php

    <?php

    use Sylius\Component\Resource\Repository\InMemoryRepository;
    use Sylius\Component\Rbac\Model\Role;
    use Sylius\Component\Rbac\Model\Permission;
    use Sylius\Component\Rbac\Resolver\NestedSetPermissionsResolver;
    use Sylius\Component\Rbac\Resolver\NestedSetRolesResolver;
    use Sylius\Component\Rbac\Model\IdentityInterface;
    use Sylius\Component\Rbac\Model\RoleInterface;

    class User implements IdentityInterface
    {
        /**
         * Get roles.
         *
         * @return RoleInterface[]
         */
        public function getAuthorizationRoles()
        {
            // TODO: Implement getAuthorizationRoles() method.
        }
    }

    $permissions = // Implementation of PermissionRepositoryInterface.

    $nestedSetPermissionsResolver = new NestedSetPermissionsResolver($permissions);
    $nestedSetRolesResolver = new NestedSetRolesResolver($permissions);

    $user = new User();

    $manageCustomer = new Permission();
    $manageCustomer->setCode('sylius.manage.customer');
    $manageCustomer->setDescription('Manage customers');

    $deleteCustomer = new Permission();
    $deleteCustomer->setCode('sylius.delete.customer');
    $deleteCustomer->setDescription('Delete customer');

    $createCustomer = new Permission();
    $createCustomer->setCode('sylius.create.customer');
    $createCustomer->setDescription('Create customer');

    $manageCustomer->addChild($deleteCustomer);

    $manageCustomer->addChild($createCustomer);

    $manageCustomer->getChildren();

    $administrator = new Role();
    $administrator->setCode('administrator');
    $administrator->setName('Administrator');

    $customerManager = new Role();
    $customerManager->setCode('customer_manager');
    $customerManager->setName('Customer Manager');

    $administrator->addChild($customerManager);

    $administrator->addPermission($manageCustomer);

    $nestedSetPermissionsResolver->getPermissions($administrator); // Output will be {$manageCustomer, $deleteCustomer, $createCustomer}
    $nestedSetRolesResolver->getRoles($user); // Output will be {$administrator, $customerManager}

.. note::
    For more detailed information go to `Sylius API Resolvers`_.

.. _Sylius API Resolvers: http://api.sylius.org/Sylius/Component/Rbac/Resolver.html
