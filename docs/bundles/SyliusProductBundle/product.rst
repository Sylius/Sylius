The Product
===========

Retrieving products
-------------------

Retrieving a product from the database should always happen via repository, which always implements ``Sylius\Bundle\ResourceBundle\Model\RepositoryInterface``.
If you are using Doctrine, you're already familiar with this concept, as it extends the native Doctrine ``ObjectRepository`` interface.

Your product repository is always accessible via the ``sylius.repository.product`` service.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $repository = $this->container->get('sylius.repository.product');
    }

Retrieving products is simple as calling proper methods on the repository.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $repository = $this->container->get('sylius.repository.product');

        $product = $repository->find(4); // Get product with id 4, returns null if not found.
        $product = $repository->findOneBy(array('slug' => 'my-super-product')); // Get one product by defined criteria.

        $products = $repository->findAll(); // Load all the products!
        $products = $repository->findBy(array('special' => true)); // Find products matching some custom criteria.
    }

Product repository also supports paginating products. To create a `Pagerfanta instance <https://github.com/whiteoctober/Pagerfanta>`_ use the ``createPaginator`` method.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $repository = $this->container->get('sylius.repository.product');

        $products = $repository->createPaginator();
        $products->setMaxPerPage(3);
        $products->setCurrentPage($request->query->get('page', 1));

       // Now you can return products to the template and iterate over it to get products from the current page.
    }

The paginator also can be created for specific criteria and with desired sorting.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $repository = $this->container->get('sylius.repository.product');

        $products = $repository->createPaginator(array('foo' => true), array('createdAt' => 'desc'));
        $products->setMaxPerPage(3);
        $products->setCurrentPage($request->query->get('page', 1));
    }

Creating new product object
---------------------------

To create new product instance, you can simply call ``createNew()`` method on the factory.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $factory = $this->container->get('sylius.factory.product');
        $product = $factory->createNew();
    }

.. note::

    Creating a product via this factory method makes the code more testable, and allows you to change the product class easily.

Saving & removing product
-------------------------

To save or remove a product, you can use any ``ObjectManager`` which manages Product. You can always access it via alias ``sylius.manager.product``.
But it's also perfectly fine if you use ``doctrine.orm.entity_manager`` or other appropriate manager service.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $factory = $this->container->get('sylius.factory.product');
        $manager = $this->container->get('sylius.manager.product'); // Alias for appropriate doctrine manager service.

        $product = $factory->createNew();

        $product
            ->setName('Foo')
            ->setDescription('Nice product')
        ;

        $manager->persist($product);
        $manager->flush(); // Save changes in database.
    }

To remove a product, you also use the manager.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $repository = $this->container->get('sylius.repository.product');
        $manager = $this->container->get('sylius.manager.product');

        $product = $repository->find(1);

        $manager->remove($product);
        $manager->flush(); // Save changes in database.
    }

Properties
----------

A product can also have a set of defined Properties (:doc:`Attributes </book/products/attributes>`), you'll learn about them in next chapter of this documentation.
