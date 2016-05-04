Interfaces
==========

Model Interfaces
----------------

.. _component_report_model_report-interface:

ReportInterface
~~~~~~~~~~~~~~~

This interface should be implemented by model representing a single Report.

.. note::
    This interface extends :ref:`component_resource_model_code-aware-interface`.

    For more detailed information go to `Sylius API ReportInterface`_.

.. _Sylius API ReportInterface: http://api.sylius.org/Sylius/Component/Report/Model/ReportInterface.html

Service Interfaces
------------------

.. _component_report_data-fetcher_data-fetcher-interface:

DataFetcherInterface
~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by service representing data fetcher. Responsible for providing specific data for a report, based on its configuration.

.. note::
    For more detailed information go to `Sylius API DataFetcherInterface`_.

.. _Sylius API DataFetcherInterface: http://api.sylius.org/Sylius/Component/Report/DataFetcher/DataFetcherInterface.html

.. _component_report_renderer_renderer-interface:

RendererInterface
~~~~~~~~~~~~~~~~~

This interface should be implemented by service representing data renderer, which renders the data in a specific output format. Examples would be "graph", "table", "csv".

.. note::
    For more detailed information go to `Sylius API RendererInterface`_.

.. _Sylius API RendererInterface: http://api.sylius.org/Sylius/Component/Report/Renderer/RendererInterface.html

.. _component_report_data-fetcher_delegating-data-fetcher-interface:

DelegatingDataFetcherInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by service responsible for delegating data fetching.

.. note::
    For more detailed information go to `Sylius API DelegatingDataFetcherInterface`_.

.. _Sylius API DelegatingDataFetcherInterface: http://api.sylius.org/Sylius/Component/Report/DataFetcher/DelegatingDataFetcherInterface.html

.. _component_report_renderer_delegating-renderer-interface:

DelegatingRendererInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by service responsible for delegating data rendering.

.. note::
    For more detailed information go to `Sylius API DelegatingRendererInterface`_.

.. _Sylius API DelegatingRendererInterface: http://api.sylius.org/Sylius/Component/Report/Renderer/DelegatingRendererInterface.html
