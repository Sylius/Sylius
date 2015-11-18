Models
======

.. _component_report_model_report:

Report
------

Report is the main model in **SyliusReportComponent**. This simple class represents every unique report in the system.
The default model contains the following attributes with appropriate setters and getters.

+---------------------------+----------------------------------------------------+
| Attribute                 | Description                                        |
+===========================+====================================================+
| id                        | Unique id of the report                            |
+---------------------------+----------------------------------------------------+
| code                      | Unique code of the report                          |
+---------------------------+----------------------------------------------------+
| name                      | Name of the report                                 |
+---------------------------+----------------------------------------------------+
| description               | Description of your report                         |
+---------------------------+----------------------------------------------------+
| renderer                  | Name of the renderer that visualize report data    |
+---------------------------+----------------------------------------------------+
| rendererConfiguration     | Configuration of used renderer                     |
+---------------------------+----------------------------------------------------+
| dataFetcher               | Name of the dataFetcher that provides report data  |
+---------------------------+----------------------------------------------------+
| dataFetcherConfiguration  | Configuration of used data fetcher                 |
+---------------------------+----------------------------------------------------+

.. note::
    This model implements the :ref:`component_report_model_report-interface`
    For more detailed information go to `Sylius API Report`_.

.. _Sylius API Report: http://api.sylius.org/Sylius/Component/Report/Model/Report.html

.. _component_report_data-fetcher_data:

Data
----

Data model represents report data in a uniform form.

+--------------+-------------------------------------+
| Attribute    | Description                         |
+==============+=====================================+
| labels       | Array of labels that describe data  |
+--------------+-------------------------------------+
| data         | Array of values with data           |
+--------------+-------------------------------------+

.. note::
    For more detailed information go to `Sylius API Data`_.

.. _Sylius API Data: http://api.sylius.org/Sylius/Component/Report/DataFetcher/Data.html
