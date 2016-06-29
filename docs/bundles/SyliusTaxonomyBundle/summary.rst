Summary
=======

Configuration Reference
-----------------------

.. code-block:: yaml

    sylius_taxonomies:
        # The driver used for persistence layer.
        driver: ~
        resources:
            taxonomy:
                classes:
                    model:      Sylius\Taxonomy\Model\Taxonomy
                    interface:  Sylius\Taxonomy\Model\TaxonomyInterface
                    controller: Sylius\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Resource\Factory\Factory
                    form:
                        default: Sylius\TaxonomiesBundle\Form\Type\TaxonomyType
                        choice:  Sylius\ResourceBundle\Form\Type\ResourceChoiceType
                validation_groups:
                    default: [ sylius ]
                translation:
                    classes:
                        model:      Sylius\Taxonomy\Model\TaxonomyTranslation
                        interface:  Sylius\Taxonomy\Model\TaxonomyTranslationInterface
                        controller: Sylius\ResourceBundle\Controller\ResourceController
                        repository: ~
                        factory:    Sylius\Resource\Factory\Factory
                        form:
                            default: Sylius\TaxonomiesBundle\Form\Type\TaxonomyTranslationType
                    validation_groups:
                        default: [ sylius ]
                    fields:
                        default: [ name ]
            taxon:
                classes:
                    model:      Sylius\Taxonomy\Model\Taxon
                    interface:  Sylius\Taxonomy\Model\TaxonInterface
                    controller: Sylius\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Resource\Factory\Factory
                    form:
                        default: Sylius\TaxonomiesBundle\Form\Type\TaxonType
                validation_groups:
                    default: [ sylius ]
                translation:
                    classes:
                        model:      Sylius\Taxonomy\Model\TaxonTranslation
                        interface:  Sylius\Taxonomy\Model\TaxonTranslationInterface
                        controller: Sylius\ResourceBundle\Controller\ResourceController
                        repository: ~
                        factory:    Sylius\Resource\Factory\Factory
                        form:
                            default: Sylius\TaxonomiesBundle\Form\Type\TaxonTranslationType
                    validation_groups:
                        default: [ sylius ]
                    fields:
                        default: [ name ]

Tests
-----

.. code-block:: bash

    $ composer install
    $ bin/phpspec run -fpretty --verbose

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.
