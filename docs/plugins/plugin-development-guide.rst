Plugin Development Guide
========================

Sylius plugins are one of the most powerful ways to extend Sylius functionality. They're not bounded by Sylius release cycle and can be
developed quicker and more effectively. They also allow sharing our (developers) work in an open-source community, which is not possible with
regular application customizations.

BDD methodology says the most accurate way to explain some process is using an example.
With respect to that rule, let's create some simple first plugin together!


Idea
----

The most important thing is a concept. You should be aware, that not every customization should be made as a plugin for Sylius.
If you:

* share the common logic between multiple projects
* think provided feature could be useful for the whole Sylius community and want to share it for free or sell it

then you should definitely consider the creation of a plugin. On the other hand, if:

* your feature is specific for your project
* you don't want to share your work in the community (maybe **yet**)

then don't be afraid to make a regular Sylius customization.

For needs of this tutorial, we would implement a simple plugin, making possible to mark product variant **available at demand**.


How to start?
-------------

The first step is to create a new plugin using our ``PluginSkeleton``.

.. code-block:: bash

    $ composer create-project sylius/plugin-skeleton IronManSyliusProductOnDemandPlugin

.. note::

    Remember about naming convention! Sylius plugin should start with your vendor name, followed by ``Sylius`` prefix and with ``Plugin`` suffix at the end.
    Let's say your vendor name is **IronMan**
