Taxons
======

Taxons
------

Retrieving taxons from database should always happen via repository, which implements ``Sylius\Bundle\ResourceBundle\Model\RepositoryInterface``.
If you are using Doctrine, you're already familiar with this concept, as it extends the native Doctrine ``ObjectRepository`` interface.

Your taxon repository is always accessible via ``sylius.repository.taxon`` service.

Taxon contains methods which allow you to retrieve the child taxons. Let's look at our example tree.

.. code-block:: text

    | Categories
    |--  T-Shirts
    |    |-- Men
    |    `-- Women
    |--  Stickers
    |--  Mugs
    `--  Books

To get a collection of child taxons under taxon, use the ``findChildren`` method.

.. code-block:: php

    <?php

    public function myAction(Request $request)
    {
        // Find the parent taxon
        $taxonRepository = $this->container->get('sylius.repository.taxon');
        $taxon = $taxonRepository->findOneByName('Categories');

        $taxonRepository = $this->container->get('sylius.repository.taxon');
        $taxons = $taxonRepository->findChildren($taxon);
    }

``$taxons`` variable will now contain a list (ArrayCollection) of taxons in following order: T-Shirts, Men, Women, Stickers, Mugs, Books.
