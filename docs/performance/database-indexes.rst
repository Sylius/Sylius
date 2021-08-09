Database indexes
================

Indexing tables allows you to decrease fetching time from database.

As an example lets take a look on customers list

Default index page is sorted by registration date, to create table index on this page all you need to do is one database query:

.. code-block:: mysql

    CREATE INDEX sylius_customer_created_at_index ON sylius_customer (created_at DESC);

Using this solution you can increase speed of customer listing by around 10%.
