Models
======

.. _component_rbac_model_permission:

Permission
----------

The **Permission** object represents an permission.
**Permissions** are defined as a tree, top level tree has all the permissions defined in the lower nodes.
Permissions have the following properties:

+-------------+----------------------------------------------------------------------------------+
| Property    | Description                                                                      |
+=============+==================================================================================+
| id          | Unique id of the Permission                                                      |
+-------------+----------------------------------------------------------------------------------+
| code        | Unique code of Permission (e.g. "sylius.customer.manage", "sylius.channel.show") |
+-------------+----------------------------------------------------------------------------------+
| description | (e.g. "Manage customers", "Show channel")                                        |
+-------------+----------------------------------------------------------------------------------+
| parent      | Reference to parent Permission                                                   |
+-------------+----------------------------------------------------------------------------------+
| children    | Collection of children Permissions                                               |
+-------------+----------------------------------------------------------------------------------+
| left        | Reference to left leaf                                                           |
+-------------+----------------------------------------------------------------------------------+
| right       | Reference to right leaf                                                          |
+-------------+----------------------------------------------------------------------------------+
| level       | Tree level                                                                       |
+-------------+----------------------------------------------------------------------------------+
| createdAt   | Date when Permission was created                                                 |
+-------------+----------------------------------------------------------------------------------+
| updatedAt   | Date of last change                                                              |
+-------------+----------------------------------------------------------------------------------+

.. note::
    This model implements the :ref:`component_rbac_model_permission-interface`.

    For more detailed information go to `Sylius API Permission`_.

.. _Sylius API Permission: http://api.sylius.org/Sylius/Component/Rbac/Model/Permission.html

.. _component_rbac_model_role:

Role
----

The **Role** object represents an role.
Every Identity can have multiple roles, which inherit all permissions from their child roles.
If you want to have **Roles** in your model, just implement :ref:`component_rbac_model_identity-interface`.

+---------------+-----------------------------------------------------------------------+
| Property      | Description                                                           |
+===============+=======================================================================+
| id            | Unique id of the Role                                                 |
+---------------+-----------------------------------------------------------------------+
| code          | Code of Role                                                          |
+---------------+-----------------------------------------------------------------------+
| name          | Role name (e.g. "Administrator", "Catalog Manager", Shipping Manager) |
+---------------+-----------------------------------------------------------------------+
| description   | (e.g. "Administrator user", "Shipping Department")                    |
+---------------+-----------------------------------------------------------------------+
| parent        | Reference to parent Role                                              |
+---------------+-----------------------------------------------------------------------+
| children      | Collection of children Roles                                          |
+---------------+-----------------------------------------------------------------------+
| left          | Reference to left leaf                                                |
+---------------+-----------------------------------------------------------------------+
| right         | Reference to right leaf                                               |
+---------------+-----------------------------------------------------------------------+
| level         | Tree level                                                            |
+---------------+-----------------------------------------------------------------------+
| permissions   | Collection of Permissions                                             |
+---------------+-----------------------------------------------------------------------+
| securityRoles | Collection of security roles (e.g. {"ROLE_ADMINISTRATION_ACCESS"})    |
+---------------+-----------------------------------------------------------------------+
| createdAt     | Date when Role was created                                            |
+---------------+-----------------------------------------------------------------------+
| updatedAt     | Date of last change                                                   |
+---------------+-----------------------------------------------------------------------+

.. note::
    This model implements the :ref:`component_rbac_model_role-interface`.

    For more detailed information go to `Sylius API Role`_.

.. _Sylius API Role: http://api.sylius.org/Sylius/Component/Rbac/Model/Role.html

.. hint::
    For example implementation of tree model you can use `Doctrine extension`_.

.. _Doctrine extension: https://github.com/Atlantic18/DoctrineExtensions/blob/master/doc/tree.md
