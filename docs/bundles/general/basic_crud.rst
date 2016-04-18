Performing basic CRUD operations
================================

Sylius is using the Doctrine Common persistence interfaces. This means that every model within Sylius bundles has its own repository and object manager.

Some interfaces extend the Timestampable interface. This interface is defined in the SyliusResourceBundle to not create a dependency on Doctrine ORM.
They are however compatible with the GedmoDoctrineExtensions when using Doctrine ORM.

Retrieving resources
--------------------

Retrieving any resource from database always happens via the repository, which implements ``Sylius\Bundle\ResourceBundle\Model\RepositoryInterface``.
If you have been using Doctrine, you should already be familiar with this concept, as it extends the default Doctrine ``ObjectRepository`` interface.

Let's assume you want to load a product from database. Your product repository is always accessible via the ``sylius.repository.product`` service.

.. code-block:: php

    <?php

    public function myAction()
    {
        $repository = $this->container->get('sylius.repository.product');
    }

Retrieving many resources is as simple as calling the proper methods on the repository.

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

Every Sylius repository supports paginating products. To create a `Pagerfanta instance <https://github.com/whiteoctober/Pagerfanta>`_ use the ``createPaginator`` method.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $repository = $this->container->get('sylius.repository.product');

        $products = $repository->createPaginator();
        $products->setMaxPerPage(3);
        $products->setCurrentPage($request->query->get('page', 1));

        // Now you can returns products to template and iterate over it to get products from current page.
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

Creating a new resource object
------------------------------

To create a new resource instance, you can simply call the ``createNew()`` method on the repository.

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

Saving and removing resources
-----------------------------

To save or remove a resource, you can use any ``ObjectManager`` which is capable of managing the class.
Every model has its own manager alias, for example the ``sylius.manager.address`` is an alias to the ORM EntityManager.

Of course, it is also perfectly fine if you use the ``doctrine.orm.entity_manager`` service name or any other appropriate manager service.

.. code-block:: php

    <?php

    public function myAction()
    {
        $repository = $this->container->get('sylius.repository.address');
        $manager = $this->container->get('sylius.manager.address'); // Alias to the appropriate doctrine manager service.

        $address = $repository->createNew();

        $address
            ->setFirstname('John')
            ->setLastname('Doe')
        ;

        $manager->persist($address);
        $manager->flush(); // Save changes in database.
    }

To remove a resource, you also use the manager.

.. code-block:: php

    <?php

    public function myAction()
    {
        $repository = $this->container->get('sylius.repository.shipping_method');
        $manager = $this->container->get('sylius.manager.shipping_method');

        $shippingMethod = $repository->findOneBy(array('name' => 'DHL Express'));

        $manager->remove($shippingMethod);
        $manager->flush(); // Save changes in database.
    }
