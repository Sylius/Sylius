.. index::
    single: System Requirements

System Requirements
===================

Here you will find the list of system requirements that have to be adhered to be able to use **Sylius**.
First of all have a look at the `requirements for running Symfony <http://symfony.com/doc/current/reference/requirements.html>`_.

Read about the `LAMP stack <https://en.wikipedia.org/wiki/LAMP_(software_bundle)>`_ and the `MAMP stack <https://en.wikipedia.org/wiki/MAMP>`_.

Operating Systems
-----------------

The recommended operating systems for running Sylius are the Unix systems - **Linux, MacOS**.

Web server and configuration
----------------------------

In the production environment we do recommend using Apache web server ≥ 2.2.

While developing the recommended way to work with your Symfony application is to use PHP's built-in web server.

`Go there <http://symfony.com/doc/current/cookbook/configuration/web_server_configuration.html>`_ to see the full reference to the web server configuration.

PHP required modules and configuration
--------------------------------------

**PHP version**:

+---------------+-----------------------+
| PHP           | ^5.6|^7.0             |
+---------------+-----------------------+

**PHP extensions**:

+-------------+---------------------------+
| `gd`_       | No specific configuration |
+-------------+---------------------------+
| `exif`_     | No specific configuration |
+-------------+---------------------------+
| `fileinfo`_ | No specific configuration |
+-------------+---------------------------+
| `intl`_     | No specific configuration |
+-------------+---------------------------+

**PHP configuration settings**:

+---------------+-----------------------+
| memory_limit  | ≥1024M                |
+---------------+-----------------------+
| date.timezone | Europe/Warsaw         |
+---------------+-----------------------+

.. warning::

    Use your local timezone, for example America/Los_Angeles or Europe/Berlin. See http://www.php.net/timezones for the list of all available timezones.

Database
--------

By default, the database connection is pre-configured to work with a following MySQL configuration:

+---------------+-----------------------+
| MySQL         | 5.x                   |
+---------------+-----------------------+

.. note::

    Of course you may use any other RDBMS for instance PostgreSQL.

Access rights
-------------

Most of the application folders and files require only read access, but a few folders need also the write access for the Apache user:

* var/cache
* var/logs
* web/media

You can read how to set these permissions in the `Symfony - setting up permissions <http://symfony.com/doc/current/book/installation.html#book-installation-permissions>`_ section.

.. _`gd`: http://php.net/manual/en/book.fileinfo.php
.. _`exif`: http://php.net/manual/en/book.exif.php
.. _`fileinfo`: http://php.net/manual/en/book.fileinfo.php
.. _`intl`: http://php.net/manual/en/book.intl.php
