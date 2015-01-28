Models
======

The Report
-----------

Report is the main model in SyliusReportComponent. This simple class represents every unique report in the system.
The default interface contains the following attributes with appropriate setters and getters.

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

Data is supporting model in SyliusReportBundle. That contains data provided by data fetcher and labels used to clarify report display.

+--------------+------------------------------------+
| Attribute    | Description                        |
+==============+====================================+
| labels       | Labels that describe fetched data  |
+--------------+------------------------------------+
| data         | Data fetcher from database         |
+--------------+------------------------------------+


DataFetcherInterface
----------------------

To characterize data fetcher object, its class needs to implement the ``DataFetcherInterface``.

+-------------------------------+---------------------------------------+
| Method                        | Description                           |
+===============================+=======================================+
| fetch(array $configuration)   | Fetch data from database              |
+-------------------------------+---------------------------------------+
| getType()                     | Return type of data fetcher object    |
+-------------------------------+---------------------------------------+

RendererInterface
----------------------

To characterize renderer object, its class needs to implement the ``RendererInterface``.

+-----------------------------------------------+----------------------------------------------------------+
| Method                                        | Description                                              |
+===============================================+==========================================================+
| render(ReportInterface $report, Data $data)   | Render given data and report with propor configuration   |
+-----------------------------------------------+----------------------------------------------------------+
| getType()                                     | Returns type of renderer object                          |
+-----------------------------------------------+----------------------------------------------------------+