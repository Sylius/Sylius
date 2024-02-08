.. index::
   single: Architecture

Architecture Overview
=====================

Before we dive separately into every Sylius concept, you need to have an overview of how our main application is structured.

Architectural drivers
---------------------

All architectural decisions need to be backed by a valid reason. The fundamental signposts we use to take such choices, are explained
in :doc:`Architectural Drivers section </book/architecture/drivers>`.

Specific decisions we make during the development are often explained using Architectural Decision Records. They're stored
in the `main Sylius repository <https://github.com/Sylius/Sylius/tree/1.11/adr>`_ for better visibility.

Architecture
------------

On the below image you can see the symbolic representation of Sylius architecture.

.. image:: ../../_images/architecture_overview.png
    :align: center
    :scale: 50%

|

Keep on reading this chapter to learn more about each of its parts: Shop, Admin, API, Core, Components and Bundles.

Division into Components, Bundles, Platform
-------------------------------------------

You already know that Sylius is built from components and Symfony bundles, which are integration layers with the framework.
All bundles share the same conventions for naming things and the way of data persistence.

Components
~~~~~~~~~~

Every single component of Sylius can be used standalone. Taking the ``Taxation`` component as an example,
its only responsibility is to calculate taxes, it does not matter whether these will be taxes for products or anything else, it is fully decoupled.
In order to let the Taxation component operate on your objects you need to have them implementing the ``TaxableInterface``.
Since then they can have taxes calculated.
Such approach is true for every component of Sylius.
Besides components that are strictly connected to the e-commerce needs, we have plenty of components that are more general. For instance Attribute, Mailer, Locale etc.

All the components are packages available via `Packagist <https://packagist.org/>`_.

:doc:`Read more about the Components </components_and_bundles/components/index>`.

Bundles
~~~~~~~

These are the Symfony Bundles - therefore if you are a Symfony Developer, and you would like to use the Taxation component in your system,
but you do not want to spend time on configuring forms or services in the container. You can include the ``TaxationBundle`` in your application
with minimal or even no configuration to have access to all the services, models, configure tax rates, tax categories and use that for any taxes you will need.

:doc:`Read more about the Bundles </components_and_bundles/bundles/index>`.

Platform
~~~~~~~~

This is a fullstack Symfony Application, based on Symfony Standard. Sylius Platform gives you the classic, quite feature rich webshop.
Before you start using Sylius you will need to decide whether you will need a full platform with all the features we provide, or maybe you will use decoupled bundles and components
to build something very custom, maybe smaller, with different features.
But of course the platform itself is highly flexible and can be easily customized to meet all business requirements you may have.

.. _division-into-core-shop-admin-api:

Division into Core, Admin, Shop, Api
------------------------------------

Core
~~~~

The Core is another component that integrates all the other components. This is the place where for example the ``ProductVariant`` finally learns that it has a ``TaxCategory``.
The Core component is where the ``ProductVariant`` implements the ``TaxableInterface`` and other interfaces that are useful for its operation.
Sylius has here a fully integrated concept of everything that is needed to run a webshop.
To get to know more about concepts applied in Sylius - keep on reading :doc:`The Book </book/index>`.

Admin
~~~~~

In every system with the security layer the functionalities of system administration need to be restricted to only some users with a certain role - Administrator.
This is the responsibility of our ``AdminBundle`` although if you do not need it, you can turn it off. Views have been built using the `SemanticUI <https://semantic-ui.com/>`_.

Shop
~~~~

Our ``ShopBundle`` is basically a standard B2C interface for everything that happens in the system.
It is made mainly of yaml configurations and templates.
Also here views have been built using the `SemanticUI <https://semantic-ui.com/>`_.

API
~~~

When we created our API based on API Platform framework we have done everything to offer API as easy as possible to use by developer.
The most important features of our API:

    * All operations are grouped by `shop` and `admin` context (two prefixes)
    * Developers can enable or disable entire API by changing single parameter (check :doc:`this </book/api/introduction>` chapter)
    * We create all endpoints implementing the REST principles and we are using http verbs (POST, GET, PUT, PATCH, DELETE)
    * Returned responses contain minimal information (developer should extend serialization if need more data)
    * Entire business logic is separated from API - if it necessary we dispatch command instead mixing API logic with business logic
