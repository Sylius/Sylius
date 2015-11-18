RBAC
====

Sylius Rbac component implements a hierarchical variant of RBAC, which means that we have separate concept of Users (Identity), Roles and Permissions.
Every Identity can have multiple roles, which inherit all permissions from their child roles. Permissions are defined as tree as well, top level tree has all the permissions defined in the lower nodes.

.. toctree::
   :maxdepth: 2

   installation
   basic_usage
   models
   interfaces