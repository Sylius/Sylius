Configuration reference
=======================

.. code-block:: yaml

    sylius_taxonomies:
        driver: ~ # The driver used for persistence layer.
        classes:
            taxonomy:
                model: Sylius\Bundle\TaxonomiesBundle\Model\Taxonomy
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~ # Taxonomy repository class.
                form: Sylius\Bundle\TaxonomiesBundle\Form\Type\TaxonomyType # Taxonomy form type class name.
            taxon:
                model: Sylius\Bundle\TaxonomiesBundle\Model\Taxon
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~ # Taxon repository class.
                form: Sylius\Bundle\TaxonomiesBundle\Form\Type\TaxonType # Taxon form type class name.
        validation_groups:
            taxonomy: [sylius] # Taxonomy validation groups.
            taxon: [sylius] # Taxon validation groups.
