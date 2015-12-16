Interfaces
==========

Models Interfaces
-----------------

.. _component_taxonomy_model_taxonomy-interface:

TaxonomyInterface
~~~~~~~~~~~~~~~~~

The **TaxonomyInterface** gives an object a possibility to manage Taxons.

.. note::
    This interface extends the :ref:`component_resource_model_code-aware-interface`,
    :ref:`component_translation_model_translatable-interface`,
    the :ref:`component_taxonomy_model_taxonomy-translation-interface`
    and the :ref:`component_taxonomy_model_taxons-aware-interface`. |br|
    You will find more information about that interface in `Sylius API TaxonomyInterface`_.

.. _Sylius API TaxonomyInterface: http://api.sylius.org/Sylius/Component/Taxonomy/Model/TaxonomyInterface.html

.. _component_taxonomy_model_taxon-interface:

TaxonInterface
~~~~~~~~~~~~~~

The **TaxonInterface** gives an object an ability to have Taxons assigned as children.

.. note::

    This interface extends the :ref:`component_resource_model_code-aware-interface`,
    :ref:`component_translation_model_translatable-interface`
    and the :ref:`component_translation_model_taxon-translation-interface`. |br|
    You will find more information about that interface in `Sylius API TaxonInterface`_.

.. _Sylius API TaxonInterface: http://api.sylius.org/Sylius/Component/Taxonomy/Model/TaxonInterface.html

.. _component_taxonomy_model_taxons-aware-interface:

TaxonsAwareInterface
~~~~~~~~~~~~~~~~~~~~

The **TaxonsAwareInterface** should be implemented by models that can be classified with taxons.

.. note::

    You will find more information about that interface in `Sylius API TaxonsAwareInterface`_.

.. _Sylius API TaxonsAwareInterface: http://api.sylius.org/Sylius/Component/Taxonomy/Model/TaxonsAwareInterface.html

.. _component_taxonomy_model_taxonomies-aware-interface:

TaxonomiesAwareInterface
~~~~~~~~~~~~~~~~~~~~~~~~

The **TaxonomiesAwareInterface** should be implemented by models that can be classified with taxonomies.

.. note::

    You will find more information about that interface in `Sylius API TaxonomiesAwareInterface`_.

.. _Sylius API TaxonomiesAwareInterface: http://api.sylius.org/Sylius/Component/Taxonomy/Model/TaxonomiesAwareInterface.html

.. _component_taxonomy_model_taxonomy-translation-interface:

TaxonomyTranslationInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by models that will store the **Taxonomy** translation data.

.. note::

    You will find more information about that interface in `Sylius API TaxonomyTranslationInterface`_.

.. _Sylius API TaxonomyTranslationInterface: http://api.sylius.org/Sylius/Component/Taxonomy/Model/TaxonomyTranslationInterface.html

.. _component_taxonomy_model_taxon-translation-interface:

TaxonTranslationInterface
~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by models that will store the **Taxon** translation data.

.. note::

    You will find more information about that interface in `Sylius API TaxonTranslationInterface`_.

.. _Sylius API TaxonTranslationInterface: http://api.sylius.org/Sylius/Component/Taxonomy/Model/TaxonTranslationInterface.html

Services Interfaces
-------------------

.. _component_taxonomy_repository_taxon-repository-interface:

TaxonRepositoryInterface
~~~~~~~~~~~~~~~~~~~~~~~~

In order to have a possibility to get Taxons as a list you should create a repository class, that implements this interface.

.. note::

    You will find more information about that interface in `Sylius API TaxonRepositoryInterface`_.

.. _Sylius API TaxonRepositoryInterface: http://api.sylius.org/Sylius/Component/Taxonomy/Repository/TaxonRepositoryInterface.html