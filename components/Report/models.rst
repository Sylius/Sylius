Models
======

The Report
-----------

Report is the main model in SyliusReportComponent. This simple class represents every unique report in the system.
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
| description               | Description of your gorgeous report                |
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

    This model implements ``ReportInterface``


The Data
-----------

Data model represents report data in a uniform form.

+--------------+-------------------------------------+
| Attribute    | Description                         |
+==============+=====================================+
| labels       | Array of labels that describe data  |
+--------------+-------------------------------------+
| data         | Array of values with data           |
+--------------+-------------------------------------+


DataFetcherInterface
----------------------

To characterize data fetcher object, its class needs to implement the ``DataFetcherInterface``.

+-------------------------------+---------------------------------------------+
| Method                        | Description                                 |
+===============================+=============================================+
| fetch(array $configuration)   | Returns data, based on given configuration  |
+-------------------------------+---------------------------------------------+
| getType()                     | Returns type of data fetcher object         |
+-------------------------------+---------------------------------------------+

RendererInterface
----------------------

To characterize renderer object, its class needs to implement the ``RendererInterface``.

+-----------------------------------------------+----------------------------------------------------------+
| Method                                        | Description                                              |
+===============================================+==========================================================+
| render(ReportInterface $report, Data $data)   | Renders given data and report with proper configuration  |
+-----------------------------------------------+----------------------------------------------------------+
| getType()                                     | Returns type of renderer object                          |
+-----------------------------------------------+----------------------------------------------------------+