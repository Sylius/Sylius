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
                    model:      Sylius\Component\Taxonomy\Model\Taxonomy
                    interface:  Sylius\Component\Taxonomy\Model\TaxonomyInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Component\Resource\Factory\Factory
                    form:
                        default: Sylius\Bundle\TaxonomiesBundle\Form\Type\TaxonomyType
                        choice:  Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType
                validation_groups:
                    default: [ sylius ]
                translation:
                    classes:
                        model:      Sylius\Component\Taxonomy\Model\TaxonomyTranslation
                        interface:  Sylius\Component\Taxonomy\Model\TaxonomyTranslationInterface
                        controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                        repository: ~
                        factory:    Sylius\Component\Resource\Factory\Factory
                        form:
                            default: Sylius\Bundle\TaxonomiesBundle\Form\Type\TaxonomyTranslationType
                    validation_groups:
                        default: [ sylius ]
                    fields:
                        default: [ name ]
            taxon:
                classes:
                    model:      Sylius\Component\Taxonomy\Model\Taxon
                    interface:  Sylius\Component\Taxonomy\Model\TaxonInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Component\Resource\Factory\Factory
                    form:
                        default: Sylius\Bundle\TaxonomiesBundle\Form\Type\TaxonType
                validation_groups:
                    default: [ sylius ]
                translation:
                    classes:
                        model:      Sylius\Component\Taxonomy\Model\TaxonTranslation
                        interface:  Sylius\Component\Taxonomy\Model\TaxonTranslationInterface
                        controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                        repository: ~
                        factory:    Sylius\Component\Resource\Factory\Factory
                        form:
                            default: Sylius\Bundle\TaxonomiesBundle\Form\Type\TaxonTranslationType
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
