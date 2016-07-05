.. index::
   single: Architecture

Architecture Overview
=====================

Before we dive separately into every Sylius concept, you need to have an overview of how our main application is structured.
You already know that Sylius is built from components and Symfony2 bundles, which are integration layers with the framework.

All bundles share the same conventions for naming things and the way of data persistence. Sylius, by default, uses the Doctrine ORM for managing all entities.

For deeper understanding of how Doctrine works, please refer to the `excellent documentation on their official website <http://doctrine-orm.readthedocs.org/en/latest/>`_.

Fullstack Symfony
-----------------

.. image:: ../_images/symfonyfs.png
    :scale: 15%
    :align: center

**Sylius** is based on Symfony2, which is a leading PHP framework to create web applications. Using Symfony allows
developers to work better and faster by providing them with certainty of developing an application that is fully compatible
with the business rules, that is structured, maintainable and upgradable, but also it allows to save time by providing generic re-usable modules.

`Learn more about Symfony <http://symfony.com/what-is-symfony>`_.

Doctrine
--------

.. image:: ../_images/doctrine.png
    :align: center

**Doctrine** is a family of PHP libraries focused on providing data persistence layer.
The most important are the object-relational mapper (ORM) and the database abstraction layer (DBAL).
One of Doctrine's key features is the possibility to write database queries in Doctrine Query Language (DQL) - an object-oriented dialect of SQL.

To learn more about Doctrine - see `their documentation <http://www.doctrine-project.org/about.html>`_.

Twig
----

.. image:: ../_images/twig.png
    :scale: 30%
    :align: center

**Twig** is a modern template engine for PHP that is really fast, secure and flexible. Twig is being used by Symfony.

To read more about Twig, `go here <http://twig.sensiolabs.org/>`_.

Division into Components, Bundles, Platform
-------------------------------------------

Components
''''''''''

Every single component of Sylius can be used standalone. Taking the ``Taxation`` component as an example,
it's only responsibility is to calculate taxes,it does not matter whether these will be taxes for products or anything else, it is fully decoupled.
In order to let the Taxation component operate on your objects you need to have them implementing the ``TaxableInterface``.
Since then they can have taxes calculated.
Such approach is true for every component of Sylius.
Besides components that are strictly connected to the e-commerce needs, we have plenty of components that are more general. For instance Attribute, Mailer, Locale etc.

All the components are packages, available via `Packagist <https://packagist.org/>`_.

Bundles
'''''''

These are the Symfony Bundles - therefore if you are a Symfony Developer, and you would like to use the Taxation component in your system,
but you do not want to spend time on configuring forms or services in the container. You can include the ``TaxationBundle`` in your application
with minimal or even no configuration to have access to all the services, models, configure tax rates, tax categories and use that for any taxes you will need.

Platform
''''''''

This is a fullstack Symfony Application, based on Symfony Standard. Sylius Platform gives you the classic, quite feature rich webshop.
Before you start using Sylius you will need to decide whether you will need a full platform with all the features we provide, or maybe you will use decoupled bundles and components
to build something very custom, maybe smaller, with different features.
But of course the platform is highly customizable to fit different bussiness models.

Division into Core, Admin, Shop, Api
------------------------------------

Core
''''

...

Admin
'''''

...

Shop
''''

...

Api
'''

...

Resource Layer
--------------

We created an abstraction on top of Doctrine, in order to have a consistent and flexible way to manage all the resources. By "resource" we understand every model in the application.
Simplest examples of Sylius resources are "product", "order", "tax_category", "promotion", "user", "shipping_method" and so on...

There are two types of resources in **Sylius**:

* registered by default - their names begin with ``sylius.*`` for example: ``sylius.product``
* custom resources, from your application which have a separate convention. We place them under ``sylius_resource:`` ``resource_name:`` in the ``config.yml``. For these we reccomend using the naming convenion of ``app.*`` for instance ``app.my_entity``.

Sylius resource management system lives in the **SyliusResourceBundle** and can be used in any Symfony2 project.

Services
````````

For every resource you have four essential services available:

* Factory
* Manager
* Repository
* Controller

Let us take the "product" resource as an example. By default, it is represented by an object of a class that implements the ``Sylius\Component\Core\Model\ProductInterface``.

Factory
'''''''

The factory service gives you an ability to create new default objects. It can be accessed via the *sylius.factory.product* id (for the Product resource of course).

.. code-block:: php

    <?php

    public function myAction()
    {
        $factory = $this->container->get('sylius.factory.product');

        /** @var ProductInterface $product **/
        $product = $factory->createNew();
    }

.. note::

    Creating resources via this factory method makes the code more testable, and allows you to change the model class easily.

Manager
'''''''

The manager service is just an alias to appropriate Doctrine's `ObjectManager`_ and can be accessed via the *sylius.manager.product* id.
API is exactly the same and you are probably already familiar with it:

.. code-block:: php

    <?php

    public function myAction()
    {
        $manager = $this->container->get('sylius.manager.product');

        // Assuming that the $product1 exists in the database we can perform such operations:
        $manager->remove($product1);

        // If we have created the $product2 using a factory, we can persist it in the database.
        $manager->persist($product2);

        // Before performing a flush, the changes we have made, are not saved. There is only the $product1 in the database.
        $manager->flush(); // Saves changes in the database.

        //After these operations we have only $product2 in the database. The $product1 has been removed.
    }

Repository
''''''''''

Repository is defined as a service for every resource and shares the API with standard Doctrine *ObjectRepository*. It contains two additional methods for creating a new object instance and a paginator provider.

The repository service is available via the *sylius.repository.product* id and can be used like all the repositories you have seen before.

.. code-block:: php

    <?php

    public function myAction()
    {
        $repository = $this->container->get('sylius.repository.product');

        $product = $repository->find(4); // Get product with id 4, returns null if not found.
        $product = $repository->findOneBy(['slug' => 'my-super-product']); // Get one product by defined criteria.

        $products = $repository->findAll(); // Load all the products!
        $products = $repository->findBy(['special' => true]); // Find products matching some custom criteria.
    }

Every Sylius repository supports paginating resources. To create a `Pagerfanta instance <https://github.com/whiteoctober/Pagerfanta>`_ use the ``createPaginator`` method.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $repository = $this->container->get('sylius.repository.product');

        $products = $repository->createPaginator();
        $products->setMaxPerPage(3);
        $products->setCurrentPage($request->query->get('page', 1));

        // Now you can return products to template and iterate over it to get products from current page.
    }

Paginator can be created for a specific criteria and with desired sorting.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $repository = $this->container->get('sylius.repository.product');

        $products = $repository->createPaginator(['foo' => true], ['createdAt' => 'desc']);
        $products->setMaxPerPage(3);
        $products->setCurrentPage($request->query->get('page', 1));
    }

Controller
''''''''''

This service is the most important for every resource and provides a format agnostic CRUD controller with the following actions:

* [GET]      showAction() for getting a single resource
* [GET]      indexAction() for retrieving a collection of resources
* [GET/POST] createAction() for creating new resource
* [GET/PUT]  updateAction() for updating an existing resource
* [DELETE]   deleteAction() for removing an existing resource

As you see, these actions match the common operations in any REST API and yes, they are format agnostic.
This means, all Sylius controllers can serve HTML, JSON or XML, depending on what you request.

Additionally, all these actions are very flexible and allow you to use different templates, forms, repository methods per route.
The bundle is very powerful and allows you to register your own resources as well. To give you some idea of what is possible, here are some examples!

Displaying a resource with a custom template and repository methods:

.. code-block:: yaml

    # routing.yml

    app_product_show:
        path: /products/{slug}
        methods: [GET]
        defaults:
            _controller: sylius.controller.product:showAction
            _sylius:
                template: AppStoreBundle:Product:show.html.twig # Use a custom template.
                repository:
                    method: findForStore # Use a custom repository method.
                    arguments: [$slug] # Pass the slug from the url to the repository.

Creating a product using custom form and a redirection method:

.. code-block:: yaml

    # routing.yml

    app_product_create:
        path: /my-stores/{store}/products/new
        methods: [GET, POST]
        defaults:
            _controller: sylius.controller.product:createAction
            _sylius:
                form: app_user_product # Use this form type!
                template: AppStoreBundle:Product:create.html.twig # Use a custom template.
                factory: 
                    method: createForStore # Use a custom factory method to create a product.
                    arguments: [$store] # Pass the store name from the url.
                redirect:
                    route: app_product_index # Redirect the user to his products.
                    parameters: [$store]

All other methods have the same level of flexibility and are documented in the :doc:`Resource Bundle Guide </bundles/SyliusResourceBundle/index>`.

Core, Admin and Ui
------------------

Main application is constructed from four main bundles:

**SyliusCoreBundle**, which is the glue for all other bundles. It is the integration layer of Core component - the heart of Sylius, providing the whole e-commerce framework.
**SyliusUiBundle**, which contains the default web interface, assets, templates and menu builders.
**SyliusAdminBundle**, which contains the default administration of the whole system, that is easily extensible.
**SyliusShopBundle**, that takes care of the things visible for the customer like the customer account or the cart.

Third Party Libraries
---------------------

Sylius uses a lot of libraries for various tasks:

* [SymfonyCMF] for content management
* [Gaufrette] for filesystem abstraction (store images locally, Amazon S3 or external server)
* [Imagine] for images processing, generating thumbnails and cropping
* [Snappy] for generating PDF files
* [HWIOAuthBundle] for facebook/amazon/google logins
* [Pagerfanta] for pagination

.. _`ObjectManager`: http://www.doctrine-project.org/api/common/2.4/class-Doctrine.Common.Persistence.ObjectManager.html
