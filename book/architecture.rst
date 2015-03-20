.. index::
   single: Architecture

Architecture Overview
=====================

Before we dive separately into every Sylius concept, you need to have an overview of how our main application is structured.
You already know that Sylius is built from components and Symfony2 bundles, which are integration layers with the framework.

All bundles share the conventions for naming things and the same way of data persistence. Sylius, by default, uses Doctrine ORM for managing all entities.

For deeper understanding of how Doctrine works, please refer to the `excellent documentation on their official website <http://doctrine-orm.readthedocs.org/en/latest/>`_.

Resource Layer
--------------

We created an abstraction on top of Doctrine, in order to have a consistent and flexible way to manage all the resources. By "resource" we understand every model in the application.
Simplest examples of Sylius resources are "product", "order", "tax_category", "promotion", "user", "shipping_method" and so on...

Sylius resource management system lives in the **SyliusResourceBundle** and can be used in any Symfony2 project.

Services
````````

For every resource you have three very important services available:

* Manager
* Repository
* Controller

Let us take the "product" resource as an example. By default, It is represented by ``Sylius\Component\Core\Model\Product`` class and implement proper ``ProductInterface``.

Manager
'''''''

The manager service is just an alias to appropriate Doctrine's *ObjectManager* and can be accessed via the *sylius.manager.product* id.
API is exactly the same and you are probably already familiar with it:

.. code-block:: php

    <?php

    public function myAction()
    {
        $manager = $this->container->get('sylius.manager.product');

        $manager->persist($product1);
        $manager->remove($product2);
        $manager->flush(); // Save changes in database.
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
        $product = $repository->findOneBy(array('slug' => 'my-super-product')); // Get one product by defined criteria.

        $products = $repository->findAll(); // Load all the products!
        $products = $repository->findBy(array('special' => true)); // Find products matching some custom criteria.
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

        $products = $repository->createPaginator(array('foo' => true), array('createdAt' => 'desc'));
        $products->setMaxPerPage(3);
        $products->setCurrentPage($request->query->get('page', 1));
    }

To create a new object instance, you can simply call the ``createNew()`` method on the repository.

Now let's try something else than product, we'll create a new TaxRate model.

.. code-block:: php

    <?php

    public function myAction()
    {
        $repository = $this->container->get('sylius.repository.tax_rate');
        $taxRate = $repository->createNew();
    }

.. note::

    Creating resources via this factory method makes the code more testable, and allows you to change the model class easily.

Controller
''''''''''

This service is the most important for every resource and provides a format agnostic CRUD controller with the following actions:

* [GET]      showAction() for getting a single resource
* [GET]      indexAction() for retrieving a collection of resources
* [GET/POST] createAction() for creating new resource
* [GET/PUT]  updateAction() for updating an existing resource
* [DELETE]   deleteAction() for removing an existing resource

As you can see, these actions match the common operations in any REST API and yes, they are format agnostic.
That means, all Sylius controllers can serve HTML, JSON or XML, depending on what do you request.

Additionally, all these actions are very flexible and allow you to use different templates, forms, repository methods per route.
The bundle is very powerful and allows you to register your own resources as well. To give you some idea of what is possible, here are some examples!

Displaying a resource with custom template and repository methods:

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
                    method: findForStore # Use custom repository method.
                    arguments: [$slug] # Pass the slug from the url to the repository.

Creating a product using custom form and redirection method:

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
                    method: createForStore # Use custom factory method to create a product.
                    arguments: [$store] # Pass the store name from the url.
                redirect:
                    route: app_product_index # Redirect the user to his products.
                    parameters: [$store]

All other methods have the same level of flexibility and are documented in the [SyliusResourceBundle guide].

Core and Web Interface
----------------------

Main application is constructed from two main bundles:

**SyliusCoreBundle**, which is the glue for all other bundles. It is the integration layer of Core component - the heart of Sylius, providing the whole e-commerce framework.
**SyliusWebBundle**, which contains the default web interface, assets, templates and menu builders.

Third Party Libraries
---------------------

Sylius uses a lot of libraries for various tasks:

* [SymfonyCMF] for content management
* [Gaufrette] for filesystem abstraction (store images locally, Amazon S3 or external server)
* [Imagine] for images processing, generating thumbnails and cropping
* [Snappy] for generating PDF files
* [HWIOAuthBundle] for facebook/amazon/google logins
* [Pagerfanta] for pagination

Final Thoughts
--------------

...

Learn more
----------

* ...
