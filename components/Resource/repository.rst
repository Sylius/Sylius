Repository insterfaces
======================

RepositoryInterface
-------------------

This inferface will ask you to implement two methods to your repositories:

+----------------------------------------------------------------+--------------------------------------------+------------------+
| Method                                                         | Description                                | Returned value   |
+================================================================+============================================+==================+
| createNew()                                                    | Create a new  instance of your resource    | mixed            |
+----------------------------------------------------------------+--------------------------------------------+------------------+
| createPaginator(array $criteria = null, array $orderBy = null) | Get paginated collection of your resources | mixed            |
+----------------------------------------------------------------+--------------------------------------------+------------------+
