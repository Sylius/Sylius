.. rst-class:: outdated

Interfaces
==========

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

Models Interfaces
-----------------

.. _component_taxonomy_model_taxon-interface:

TaxonInterface
~~~~~~~~~~~~~~

The **TaxonInterface** gives an object an ability to have Taxons assigned as children.

.. note::

    This interface extends the `CodeAwareInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/CodeAwareInterface.php>`_,
    `TranslatableInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/TranslatableInterface.php>`_
    and the :ref:`component_taxonomy_model_taxon-translation-interface`.

.. _component_taxonomy_model_taxons-aware-interface:

TaxonsAwareInterface
~~~~~~~~~~~~~~~~~~~~

The **TaxonsAwareInterface** should be implemented by models that can be classified with taxons.

.. _component_taxonomy_model_taxon-translation-interface:

TaxonTranslationInterface
~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by models that will store the **Taxon** translation data.

Services Interfaces
-------------------

.. _component_taxonomy_repository_taxon-repository-interface:

TaxonRepositoryInterface
~~~~~~~~~~~~~~~~~~~~~~~~

In order to have a possibility to get Taxons as a list you should create a repository class, that implements this interface.
