Summary
=======

Configuration Reference
-----------------------

.. code-block:: yaml

    sylius_taxonomies:
        # The driver used for persistence layer.
        driver: ~
        resources:
            taxon:
                classes:
                    model:      Sylius\Component\Taxonomy\Model\Taxon
                    interface:  Sylius\Component\Taxonomy\Model\TaxonInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Component\Resource\Factory\TranslatableFactory
                    form: Sylius\Bundle\TaxonomiesBundle\Form\Type\TaxonType
                translation:
                    classes:
                        model:      Sylius\Component\Taxonomy\Model\TaxonTranslation
                        interface:  Sylius\Component\Taxonomy\Model\TaxonTranslationInterface
                        controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                        repository: ~
                        factory:    Sylius\Component\Resource\Factory\Factory
                        form: Sylius\Bundle\TaxonomiesBundle\Form\Type\TaxonTranslationType

Tests
-----

.. code-block:: bash

    $ composer install
    $ bin/phpspec run -fpretty --verbose

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.
