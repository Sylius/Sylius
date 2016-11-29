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
                    form:
                        default: Sylius\Bundle\TaxonomiesBundle\Form\Type\TaxonType
                        from_identifier: Sylius\Bundle\ResourceBundle\Form\Type\ResourceFromIdentifierType
                        to_identifier: Sylius\Bundle\ResourceBundle\Form\Type\ResourceToHiddenIdentifierType
                        to_hidden_identifier: Sylius\Bundle\ResourceBundle\Form\Type\ResourceToIdentifierType
                validation_groups:
                    default: [ sylius ]
                    from_identifier: [ sylius ]
                    to_identifier: [ sylius ]
                    to_hidden_identifier: [ sylius ]
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
