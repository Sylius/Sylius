Archetype
=========

Handling of dynamic attributes and options on PHP models is a common task for most of modern business applications.
Additionally when an object has certain attributes and options, similar objects are likely to be built from the same set
of attributes and/or options.

This Sylius Archetype component makes it easier to define types of objects that have these attributes and options and
attach them when creating a new object. This is called an _archetype_ and a model can be defined as an archetype by
implementing a simple interface.

.. toctree::
   :maxdepth: 2

   installation
   basic_usage
   models
   interfaces
