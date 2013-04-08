Summary
=======

Configuration reference
-----------------------

.. code-block:: yaml

    sylius_taxonomies:
        driver: ~ # The driver used for persistence layer.
        engine: twig # Templating engine to use by default.
        classes:
            taxonomy:
                model: ~ # The taxonomy model class.
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~ # Taxonomy repository class.
                form: Sylius\Bundle\TaxonomiesBundle\Form\Type\TaxonomyType # Taxonomy form type class name.
            taxon:
                model: ~ # The taxon model class.
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~ # Taxon repository class.
                form: Sylius\Bundle\TaxonomiesBundle\Form\Type\TaxonType # Taxon form type class name.

`phpspec2 <http://phpspec.net>`_ examples
-----------------------------------------

.. code-block:: bash

    $ composer install --dev --prefer-dist
    $ bin/phpspec run -f pretty

Working examples
----------------

If you want to see working implementation, try out the `Sylius application <http://github.com/Sylius/Sylius>`_.

There is also an example that shows how to integrate this bundle into `Symfony Standard Edition <https://github.com/umpirsky/symfony-standard/tree/sylius/taxonomies-bundle>`_.

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/SyliusAssortmentBundle/issues>`_.
If you have found bug, please create an issue.
