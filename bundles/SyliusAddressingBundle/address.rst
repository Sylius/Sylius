The Address
===========

Address is very simple model you should use to store all address data in your application.

+-----------+--------------------------------+
| Attribute | Description                    |
+===========+================================+
| id        | Unique id of the address       |
+-----------+--------------------------------+
| firstname |                                |
+-----------+--------------------------------+
| lastname  |                                |
+-----------+--------------------------------+
| company   |                                |
+-----------+--------------------------------+
| country   | Reference to Country model     |
+-----------+--------------------------------+
| province  | Refernce to Province model     |
+-----------+--------------------------------+
| street    |                                |
+-----------+--------------------------------+
| city      |                                |
+-----------+--------------------------------+
| postcode  |                                |
+-----------+--------------------------------+
| createdAt | Date when address was created  |
+-----------+--------------------------------+
| updatedAt | Date of last address update    |
+-----------+--------------------------------+

Country and Province
--------------------

Every address holds reference to a Country and optionally - to one of the country Provinces. You'll learn about them in next chapter.
