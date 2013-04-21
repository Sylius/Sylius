SyliusResourceBundle
====================

Easy CRUD and persistence for Symfony2 apps.

During our work on Sylius, we noticed a lot of duplicated code across all controllers. We started looking for good solution of the problem.
We're not big fans of administration generators (they're cool, but not for our usecase!) - we wanted something simpler and more flexible.

Another idea was to not limit ourselves to one persistence backend. Initial implementation included custom manager classes, which was quite of overhead, so we decided to simply 
stick with Doctrine Common Persistence interfaces. If you are using Doctrine ORM or any of the ODM's, you're already familiar with those concepts.
Resource bundle relies on mainly `ObjectManager` and `ObjectRepository` interfaces.

The last annoying problem this bundle is trying to solve, is having separate "backend" and "frontend" controllers, or any other duplication for displaying the same resource,
with different presentation (view). We also wanted an easy way to filter some resources from list, sort them or display by id, slug or any other criteria - without having to defining
another super simple action for that purpose.

If these are issues you're struggling with, this bundle may be helpful!

Please note that this bundle **is not admin generator**. It won't create forms, filters and grids for you. It only provides format agnostic controllers as foundation to build on, with some basic sorting and filter mechanisms.

.. toctree::
   :numbered:

   installation
   Getting single resource <show_resource>
   Getting collection of resources <index_resources>
   Creating resource <create_resource>
   Updating resource <update_resource>
   Deleting resource <delete_resource>
   summary
