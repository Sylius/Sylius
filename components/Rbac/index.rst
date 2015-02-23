RBAC
====

Role-Based-Access-Control implementation in pure PHP.

Why RBAC?
---------

* Large applications have many permissions to manage
* Permissions are connected to Roles, rather than concrete users
* User role in the system may change, his permissions should change immedietaly as well
* Management of permissions per user can be a real pain and requires a lot of resources

What is RBAC?
-------------

Sylius Rbac component implements a hierarchical variant of RBAC, which means that we have separate concept of Users (Identity), Roles and Permissions.

Every Identity can have multiple roles, which inherit all permissions from their child roles. Permissions are defined as tree as well, top level tree has all the permissions defined in the lower nodes.

.. toctree::
   :numbered:

   installation
   basic_usage
   summary
