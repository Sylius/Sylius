Basic Usage
===========

.. code-block:: php

    <?php

    use Sylius\Component\Taxonomy\Model\Taxon;
    use Sylius\Component\Taxonomy\Model\Taxonomy;

    // Let's assume we want to begin creating new taxonomy in our system
    // therefore we think of a new taxon that will be a root for us.
    $taxon = new Taxon();

    // And later on we create a taxonomy with our taxon as a root.
    $taxonomy = new Taxonomy($taxon);

    // Before we can start using the newly created taxonomy, we have to define its locales.
    $taxonomy->setFallbackLocale('en');
    $taxonomy->setCurrentLocale('en');
    $taxonomy->setName('Root');

    $taxon->getName(); //will return 'Root'
