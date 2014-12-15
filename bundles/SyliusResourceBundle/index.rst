SyliusResourceBundle
====================

Easy CRUD and persistence for Symfony2 apps.

During our work on Sylius, we noticed a lot of duplicated code across all controllers. We started looking for a good solution to the problem.
We're not big fans of administration generators (they're cool, but not for our use case!) - we wanted something simpler and more flexible.

Another idea was to not limit ourselves to one persistence backend. Initial implementation included custom manager classes, which was quite of an overhead, so we decided to simply 
stick with Doctrine Common Persistence interfaces. If you are using Doctrine ORM or any of the ODM's, you're already familiar with those concepts.
Resource bundle relies mainly on the `ObjectManager` and `ObjectRepository` interfaces.

The last annoying problem this bundle tries to solve, is having separate "backend" and "frontend" controllers, or any other duplication for displaying the same resource,
with different presentation (view). We also wanted an easy way to filter some resources from list, sort them or display them by id, slug or any other criteria - without having to define
another super simple action for that purpose.

If these are issues you're struggling with, this bundle may be helpful!

Please note that this bundle **is not an admin generator**. It won't create forms, filters and grids for you. It only provides format agnostic controllers as foundation to build on, with some basic sorting and filter mechanisms.

.. toctree::
   :numbered:

   installation
   configuration
   Getting single resource <show_resource>
   Getting collection of resources <index_resources>
   Creating resource <create_resource>
   Updating resource <update_resource>
   Deleting resource <delete_resource>
   Managing flash messages <flash>
   Doctrine tools <doctrine>
   Extra tools <extra_tools>
   Defining custom action <custom_actions>
   Collection form type <form_collection>
   summary
