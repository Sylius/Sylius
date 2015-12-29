SyliusResourceBundle
====================

There is plenty of things you need to handle for every single Resource in your web application.

Several "Admin Generator" are available for Symfony, but we needed something really simple, that will allow us to have reusable controllers but maintain performance and standard Symfony workflow.
We did not want to generate any code or write "Admin" class definitions in PHP. The big goal was to have exactly same workflow as we would write controllers manually but without actually writing it!

Another idea was to not limit ourselves to a single persistence backend. ``Resource`` component provides us with generic purpose persistence services and you can use this bundle with multiple persistence backends. So far we support:

* Doctrine ORM
* Doctrine MongoDB ODM
* Doctrine PHPCR ODM

* InMemory (soon)
* ElasticSearch (maybe)

.. toctree::
   :numbered:

   installation
   configuration
   services
   routing
   forms
   show_resource
   index_resources
   create_resource
   update_resource
   delete_resource
   reference
