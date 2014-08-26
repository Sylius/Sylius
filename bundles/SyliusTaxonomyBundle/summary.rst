Summary
=======

Configuration Reference
-----------------------

.. code-block:: yaml

    sylius_taxonomies:
        # The driver used for persistence layer.
        driver: ~
        classes:
            taxonomy:
                model: Sylius\Component\Taxonomy\Model\Taxonomy
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\TaxonomiesBundle\Form\Type\TaxonomyType
            taxon:
                model: Sylius\Component\Taxonomy\Model\Taxon
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\TaxonomiesBundle\Form\Type\TaxonType
        validation_groups:
            taxonomy: [sylius]
            taxon: [sylius]

Tests
-----

.. code-block:: bash

    $ composer install --dev --prefer-dist
    $ bin/phpspec run -fpretty --verbose

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.