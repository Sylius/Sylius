Interfaces
==========

Model Interfaces
----------------

.. _component_rbac_model_identity-interface:

IdentityInterface
~~~~~~~~~~~~~~~~~

This interface should be implemented by model representing a single Authorization Identity, which has certain Roles assigned.

.. note::
    For more detailed information go to `Sylius API IdentityInterface`_.

.. _Sylius API IdentityInterface: http://api.sylius.org/Sylius/Component/Rbac/Model/IdentityInterface.html

.. _component_rbac_model_permission-interface:

PermissionInterface
~~~~~~~~~~~~~~~~~~~

This interface should be implemented by model representing a single Permission.

.. note::
    This interface extends :ref:`component_resource_model_code-aware-interface`, :ref:`component_resource_model_timestampable-interface` and :ref:`component_resource_model_resource-interface`.

    For more detailed information go to `Sylius API PermissionInterface`_.

.. _Sylius API PermissionInterface: http://api.sylius.org/Sylius/Component/Rbac/Model/PermissionInterface.html

.. _component_rbac_model_role-interface:

RoleInterface
~~~~~~~~~~~~~

This interface should be implemented by model representing a single Role.

.. note::
    This interface extends :ref:`component_resource_model_code-aware-interface`, :ref:`component_resource_model_timestampable-interface` and :ref:`component_resource_model_resource-interface`.

    For more detailed information go to `Sylius API RoleInterface`_.

.. _Sylius API RoleInterface: http://api.sylius.org/Sylius/Component/Rbac/Model/RoleInterface.html

Service Interfaces
------------------

.. _component_rbac_provider_current-identity-provider-interface:

CurrentIdentityProviderInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Service implementing this interface should return an instance of currently used identity.

.. note::
    For more detailed information go to `Sylius API CurrentIdentityProviderInterface`_.

.. _Sylius API CurrentIdentityProviderInterface: http://api.sylius.org/Sylius/Component/Rbac/Provider/CurrentIdentityProviderInterface.html

.. _component_rbac_provider_permission-provider-interface:

PermissionProviderInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Service implementing this interface should return an instance of permission by given code.


.. note::
    For more detailed information go to `Sylius API PermissionProviderInterface`_.

.. _Sylius API PermissionProviderInterface: http://api.sylius.org/Sylius/Component/Rbac/Provider/PermissionProviderInterface.html

.. _component_rbac_authorization_authorization-checker-interface:

AuthorizationCheckerInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Service implementing this interface should check whether current identity has specific permission.

.. note::
    For more detailed information go to `Sylius API AuthorizationCheckerInterface`_.

.. _Sylius API AuthorizationCheckerInterface: http://api.sylius.org/Sylius/Component/Rbac/Authorization/AuthorizationCheckerInterface.html

.. _component_rbac_authorization_permission-map-interface:

PermissionMapInterface
~~~~~~~~~~~~~~~~~~~~~~

Service implementing this interface should return permissions by given code.

.. note::
    For more detailed information go to `Sylius API PermissionMapInterface`_.

.. _Sylius API PermissionMapInterface: http://api.sylius.org/Sylius/Component/Rbac/Authorization/PermissionMapInterface.html

.. _component_rbac_repository_permission-repository-interface:

PermissionRepositoryInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

In order to decouple from storage that provides permissions, you should create repository class which implements this interface.

.. note::
    For more detailed information go to `Sylius API PermissionRepositoryInterface`_.

.. _Sylius API PermissionRepositoryInterface: http://api.sylius.org/Sylius/Component/Rbac/Repository/PermissionRepositoryInterface.html

.. _component_rbac_repository_role-repository-interface:

RoleRepositoryInterface
~~~~~~~~~~~~~~~~~~~~~~~

In order to decouple from storage that provides roles, you should create repository class which implements this interface.

.. note::
    This interface extends :ref:`component_resource_repository_repository-interface`.

    For more detailed information go to `Sylius API RoleRepositoryInterface`_.

.. _Sylius API RoleRepositoryInterface: http://api.sylius.org/Sylius/Component/Rbac/Repository/RoleRepositoryInterface.html

.. _component_rbac_resolver_permission-resolver-interface:

PermissionsResolverInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Service implementing this interface should return permissions with **all** their child permissions.

.. note::
    For more detailed information go to `Sylius API PermissionsResolverInterface`_.

.. _Sylius API PermissionsResolverInterface: http://api.sylius.org/Sylius/Component/Rbac/Resolver/PermissionsResolverInterface.html

.. _component_rbac_resolver_roles-resolver-interface:

RolesResolverInterface
~~~~~~~~~~~~~~~~~~~~~~

Service implementing this interface should return roles with their child roles.

.. note::
    For more detailed information go to `Sylius API RolesResolverInterface`_.

.. _Sylius API RolesResolverInterface: http://api.sylius.org/Sylius/Component/Rbac/Resolver/RolesResolverInterface.html
