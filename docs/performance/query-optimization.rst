Query optimization
==================

A huge performance boost is achieved by optimizing queries.
For example, let's take a look at the customers' list.

By default, we don't really need to fetch every single field from a customer entity to create a list of 10 customers.
All we need, is to load the `id` and the field we are sorting by (for pagination purposes).

Once these 10 customers are fetched, we can create 10 queries to fetch data for them.
As it can slow down small databases (a lot of unnecessary queries), this allows tables with millions of records to be loaded in less than a second.

It seems to be a bit complicated, but `Pagerfanta\Adapter\DoctrineORMAdapter` allow us to achieve it easily.

The second argument of `Pagerfanta\Adapter\DoctrineORMAdapter` is `fetchJoinCollection`, which is set to `false` by default.
Changing it to `true`, forces the database to fetch additional data once we get sorted results.

With 3 000 000 customers this method allows you to load page up to 70% faster.

.. warning::

    This solution may slow down loading page with small tables as it will create additional database queries.

This solution is turned on by default on `SyliusGridBundle` since version 1.10.
For the earlier versions, the `Pagerfanta\Adapter\DoctrineORMAdapter` has to be adjusted manually,
by overriding the `Sylius\Bundle\GridBundle\Doctrine\ORM\DataSource` class.
