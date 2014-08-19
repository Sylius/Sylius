Models
======

The Product
-----------

Product is the main model in SyliusProductComponent. This simple class represents every unique product in the catalog.
The default interface contains the following attributes with appropriate setters and getters.

+-----------------+----------------------------------------------------+
| Attribute       | Description                                        |
+=================+====================================================+
| id              | Unique id of the product                           |
+-----------------+----------------------------------------------------+
| name            | Name of the product                                |
+-----------------+----------------------------------------------------+
| slug            | SEO slug, by default created from the name         |
+-----------------+----------------------------------------------------+
| description     | Description of your great product                  |
+-----------------+----------------------------------------------------+
| availableOn     | Date when product becomes available in catalog     |
+-----------------+----------------------------------------------------+
| metaDescription | Description for search engines                     |
+-----------------+----------------------------------------------------+
| metaKeywords    | Comma separated list of keywords for product (SEO) |
+-----------------+----------------------------------------------------+
| createdAt       | Date when product was created                      |
+-----------------+----------------------------------------------------+
| updatedAt       | Date of last product update                        |
+-----------------+----------------------------------------------------+
| deletedAt       | Date of deletion from catalog                      |
+-----------------+----------------------------------------------------+

Product Properties
------------------

Except products, you can also define Properties (think Attributes) and define their values on each product.
Default property model has following structure.

+--------------+-------------------------------------------+
| Attribute    | Description                               |
+==============+===========================================+
| id           | Unique id of the property                 |
+--------------+-------------------------------------------+
| name         | Name of the property ("T-Shirt Material") |
+--------------+-------------------------------------------+
| presentation | Pretty name visible for user ("Material") |
+--------------+-------------------------------------------+
| type         | Property type                             |
+--------------+-------------------------------------------+
| createdAt    | Date when property was created            |
+--------------+-------------------------------------------+
| updatedAt    | Date of last property update              |
+--------------+-------------------------------------------+

Currently there are several different property types are available, a proper form widget (Symfony Form type) will be rendered
on product form for entering the value.

+------------+
| Type       |
+============+
| text       |
+------------+
| number     |
+------------+
| percentage |
+------------+
| checkbox   |
+------------+
| choice     |
+------------+