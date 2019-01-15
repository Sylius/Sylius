.. rst-class:: outdated

Summary
=======

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

Configuration Reference
-----------------------

.. code-block:: yaml

    sylius_taxonomy:
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
                    form: Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonType
                translation:
                    classes:
                        model:      Sylius\Component\Taxonomy\Model\TaxonTranslation
                        interface:  Sylius\Component\Taxonomy\Model\TaxonTranslationInterface
                        controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                        repository: ~
                        factory:    Sylius\Component\Resource\Factory\Factory
                        form: Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonTranslationType

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.
