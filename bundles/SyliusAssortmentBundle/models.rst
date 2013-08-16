The Object Model
================

Here is a quick reference of what the default models can do for you.

Product
-------

By default a product is represented with a `name`, and a `description`. There is a `slug` which is auto-populated using doctrine sluggable extension,
and can be used to generate seo friendly routes. The date `availableOn` can be used to control when a particular product will become visible.
There are also metadata `metaKeywords`, `metaDescription` and dates `createdAt`, `updatedAt`, `deletedAt` auto populated using doctrine timestampable extension.

CustomizableProduct
-------------------

Products can have variants with different colors, styles, sizes, or a combination of more such options.
`CustomizableProduct` provides access to 3 collections: `variants`, `options` and `properties`.
Each of this represents a collection of objects described below, and turns product into a pretty flexible structure.
Speaking of variants, `CustomizableProduct` have ``->getMasterVariant()`` method which gets default variant from variants
collection. Each product has one master variant.

Variant
-------

Products that can have different sizes, styles, colors, and so on, can be modeled as variants of the product.
A product can have zero or more variants. A variant has a `master` flag which defines it as a master (default) variant, a `sku` - stock-keeping unit,
a `presentation` string, a reference to a product, a collection of options, and dates similar to `Product`.

Property and ProductProperty
----------------------------

Property is represented with a `name`, and a `presentation`. Name is the internal name and can be used in the backend to identify a property, while presentation
can be used in the front end. There are several property types: boolean, string, number and choice.
Property is applied to product using `ProductProperty` model, which references `Property`, `Product` and gives access to an actual property value.

Option and OptionValue
----------------------

Similar as property, option has a `name`, and a `presentation`. It holds collection of option values represented by the `OptionValue` model.
`OptionValue` value holds an actual value. For example you can have options like color, size, and so on, and values would be red, blue, white
for color option and S, M, L, XL, XXL for size, and so on. Based on options you can have different product variants.

Prototype
---------

A prototype is a kind of template for related products. You can create a product very easy based on a desired prototype associated with certain
properties and option types. This is a useful time-saving measure and helps to get consistency within groups of similar products.
We will explain how to do this with the `PrototypeBuilder` service in the next chapter.
