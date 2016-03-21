.. index::
   single: Products

Products
========

**Product** model represents unique products in your Sylius store. Every product can have different variations or attributes and has following values:

* *name* - The full name of the product
* *slug* - Urlized version of the name
* *description* - Description of the product
* *shortDescription* - Simple description of the product for lists and banners
* *metaDescription* - Description for search engines
* *metaKeywords* - SEO keywords
* *createdAt* - Date of creation
* *updateAt* - Timestamp of the most recent update

Options
-------

In many cases, you will have product with different variations. The simplest example would be a T-Shirt available in different sizes and colors.
In order to automatically generate appropriate variants, you need to define options.

Every option type is represented by **ProductOption** and references multiple **ProductOptionValue** entities.

* Size
    * S
    * M
    * L
    * XL
    * XXL
* Color
    * Red
    * Green
    * Blue

Variants
--------

**ProductVariant** represents a unique combination of product options and can have its own pricing configuration, inventory tracking etc.

You are also able to use product variations system without the options at all.

Master Variant
``````````````

Each product has a master variant, which tracks the same information as any other variant. It exists to simplify the internal Sylius logic. Whenever a product is created, a master variant for that product will be created too.

Attributes
----------

Attributes allow you to define product specific values.

Prototypes
----------

...

Images
------

...

Final Thoughts
--------------

...

Learn more
----------

* ...
