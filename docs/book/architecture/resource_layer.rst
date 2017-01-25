.. index::
   single: Resource Layer

Resource Layer
==============

We created an abstraction on top of Doctrine, in order to have a consistent and flexible way to manage all the resources. By "resource" we understand every model in the application.
Simplest examples of Sylius resources are "product", "order", "tax_category", "promotion", "user", "shipping_method" and so on...

There are two types of resources in **Sylius**:

* registered by default - their names begin with ``sylius.*`` for example: ``sylius.product``
* custom resources, from your application which have a separate convention. We place them under ``sylius_resource:`` ``resource_name:`` in the ``config.yml``. For these we recommend using the naming convention of ``app.*`` for instance ``app.my_entity``.

Sylius resource management system lives in the **SyliusResourceBundle** and can be used in any Symfony2 project.

Services
~~~~~~~~

For every resource you have four essential services available:

* Factory
* Manager
* Repository
* Controller

Let us take the "product" resource as an example. By default, it is represented by an object of a class that implements the ``Sylius\Component\Core\Model\ProductInterface``.

Factory
~~~~~~~

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
~~~~~~~

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
~~~~~~~~~~

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

.. tip::

    An important feature of the repositories are the ``add($resource)`` and ``remove($resource)`` methods,
    which take a resource as an argument and perform the adding/removing action with a flush inside.

    These actions can be used when the performance of operations may be neglected. If you are willing
    to perform operations on sets of data we are suggesting to use the manager instead.

Every Sylius repository supports paginating resources. To create a `Pagerfanta instance <https://github.com/whiteoctober/Pagerfanta>`_ use the ``createPaginator`` method:

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

Paginator can be created for a specific criteria and with desired sorting:

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
~~~~~~~~~~

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
                form: AppStoreBundle/Form/Type/CustomFormType # Use this form type!
                template: AppStoreBundle:Product:create.html.twig # Use a custom template.
                factory:
                    method: createForStore # Use a custom factory method to create a product.
                    arguments: [$store] # Pass the store name from the url.
                redirect:
                    route: app_product_index # Redirect the user to his products.
                    parameters: [$store]

All other methods have the same level of flexibility and are documented in the :doc:`Resource Bundle Guide </bundles/SyliusResourceBundle/index>`.

.. _`ObjectManager`: http://www.doctrine-project.org/api/common/2.4/class-Doctrine.Common.Persistence.ObjectManager.html
