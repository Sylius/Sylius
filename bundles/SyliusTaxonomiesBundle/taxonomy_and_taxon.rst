Taxonomy and Taxons
===================

Taxonomy is a list constructed from individual Taxons. Every taxonomy has one special taxon, which serves as a root of the tree.
All taxons can have many child taxons, you can define as many of them as you need.

A good examples of taxonomies are "Categories" and "Brands". Below you can see an example tree.

.. code-block:: text

    | Categories
    |--  T-Shirts
    |    |-- Men
    |    `-- Women
    |--  Stickers
    |--  Mugs
    `--  Books

    | Brands
    |-- SuperTees
    |-- Stickypicky
    |-- Mugland
    `-- Bookmania

Here is the full list of attributes related to Taxonomy and Taxon models.

+-----------------+--------------------------------+
| Taxonomy                                         |
+-----------------+--------------------------------+
| Attribute       | Description                    |
+=================+================================+
| id              | Unique id of the taxonomy      |
+-----------------+--------------------------------+
| name            | Name of the taxonomy           |
+-----------------+--------------------------------+
| root            | First, "root" Taxon            |
+-----------------+--------------------------------+
| createdAt       | Date when taxonomy was created |
+-----------------+--------------------------------+
| updatedAt       | Date of last update            |
+-----------------+--------------------------------+

+-----------------+--------------------------------+
| Taxon                                            |
+-----------------+--------------------------------+
| Attribute       | Description                    |
+=================+================================+
| id              | Unique id of the taxon         |
+-----------------+--------------------------------+
| name            | Name of the taxon              |
+-----------------+--------------------------------+
| slug            | Urlized name                   |
+-----------------+--------------------------------+
| permalink       | Full permalink for given taxon |
+-----------------+--------------------------------+
| description     | Description of taxon           |
+-----------------+--------------------------------+
| taxonomy        | Taxonomy                       |
+-----------------+--------------------------------+
| parent          | Parent taxon                   |
+-----------------+--------------------------------+
| children        | Sub taxons                     |
+-----------------+--------------------------------+
| left            | Location within taxonomy       |
+-----------------+--------------------------------+
| right           | Location within taxonomy       |
+-----------------+--------------------------------+
| level           | How deep it is in the tree     |
+-----------------+--------------------------------+
| createdAt       | Date when taxon was created    |
+-----------------+--------------------------------+
| updatedAt       | Date of last update            |
+-----------------+--------------------------------+

Retrieving taxonomies and taxons
--------------------------------

Retrieving taxonomy from database should always happen via repository, which implements ``Sylius\Bundle\ResourceBundle\Model\RepositoryInterface``.
If you are using Doctrine, you're already familiar with this concept, as it extends the native Doctrine ``ObjectRepository`` interface.

Your taxonomy repository is always accessible via ``sylius.repository.taxonomy`` service.
Of course, ``sylius.repository.taxon`` is also available for use, but usually you obtains taxons directly from Taxonomy model.
You'll see that in further parts of this document.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $repository = $this->container->get('sylius.repository.taxonomy');
    }

Retrieving taxonomies is simpleas calling proper methods on the repository.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $repository = $this->container->get('sylius.repository.taxonomy');

        $taxonomy = $repository->find(2); // Get taxonomy with id 4, returns null if not found.
        $taxonomy = $repository->findOneBy(array('name' => 'Specials')); // Get one taxonomy by defined criteria.

        $taxonomies = $repository->findAll(); // Load all the taxonomies!
        $taxonomies = $repository->findBy(array('hidden' => true)); // Find taxonomies matching some custom criteria.
    }

Taxonomy repository also supports paginating taxonomies. To create a `Pagerfanta instance <https://github.com/whiteoctober/Pagerfanta>`_ use the ``createPaginator`` method.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $repository = $this->container->get('sylius.repository.taxonomy');

        $taxonomies = $repository->createPaginator();
        $taxonomies->setMaxPerPage(5);
        $taxonomies->setCurrentPage($request->query->get('page', 1));

       // Now you can return taxonomies to template and iterate over it to get taxonomies from current page.
    }

Paginator also can be created for specific criteria and with desired sorting.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $repository = $this->container->get('sylius.repository.taxonomy');

        $taxonomies = $repository->createPaginator(array('foo' => true), array('createdAt' => 'desc'));
        $taxonomies->setMaxPerPage(3);
        $taxonomies->setCurrentPage($request->query->get('page', 1));
    }

Creating new taxonomy object
---------------------------

To create new taxonomy instance, you can simply call ``createNew()`` method on the repository.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $repository = $this->container->get('sylius.repository.taxonomy');
        $taxonomy = $repository->createNew();
    }

.. note::

    Creating taxonomy via this factory method makes the code more testable, and allows you to change taxonomy class easily.

Saving & removing taxonomy
-------------------------

To save or remove a taxonomy, you can use any ``ObjectManager`` which manages Taxonomy. You can always access it via alias ``sylius.manager.taxonomy``.
But it's also perfectly fine if you use ``doctrine.orm.entity_manager`` or other appropriate manager service.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $repository = $this->container->get('sylius.repository.taxonomy');
        $manager = $this->container->get('sylius.manager.taxonomy'); // Alias for appropriate doctrine manager service.

        $taxonomy = $repository->createNew();

        $taxonomy
            ->setName('Foo')
        ;

        $manager->persist($taxonomy);
        $manager->flush(); // Save changes in database.
    }

To remove a taxonomy, you also use the manager.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $repository = $this->container->get('sylius.repository.taxonomy');
        $manager = $this->container->get('sylius.manager.taxonomy');

        $taxonomy = $repository->find(5);

        $manager->remove($taxonomy);
        $manager->flush(); // Save changes in database.
    }

Taxons
------

Taxons can be handled exactly the same way, but with usage of ``sylius.repository.taxon``.

Taxonomy contains methods which allow you to retrieve the child taxons. Let's look again at our example tree.

.. code-block:: text

    | Categories
    |--  T-Shirts
    |    |-- Men
    |    `-- Women
    |--  Stickers
    |--  Mugs
    `--  Books

To get a flat list of taxons under taxonomy, use the ``getTaxonsAsList`` method.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $repository = $this->container->get('sylius.repository.taxonomy');
        $taxonomy = $repository->findOneByName('Categories');

        $taxons = $taxonomy->getTaxonsAsList();
    }

``$taxons`` variable will now contain flat list (ArrayCollection) of taxons in following order: T-Shirts, Men, Women, Stickers, Mugs, Books.

If, for example, you want to render them as tree.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        $repository = $this->container->get('sylius.repository.taxonomy');
        $taxonomy = $repository->findOneByName('Categories');

        $taxons = $taxonomy->getTaxons();
    }

Now ``$taxons`` contains only the 4 main items, and you can access their children by calling ``$taxon->getChildren()``.
