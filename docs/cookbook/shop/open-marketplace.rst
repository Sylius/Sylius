How to transform Sylius into a Multi-Vendor Marketplace?
========================================================

What is a Multi-Vendor Marketplace?
-----------------------------------
A multi-vendor marketplace is a system that enables multiple vendors to sell their products
or services through a centralized website or application. This system facilitates the e-commerce
process by allowing vendors to register, create and manage their own storefronts, list products,
set prices, and receive payments, while the marketplace owner manages the platform and handles
customer service, security, and other related functions. Multi-vendor marketplaces often include 
inventory management, order tracking, and analytics features. This type of platform is commonly used for businesses of
all sizes and industries, particularly those wanting to expand their reach and increase sales and position
in the digital space.

Open Marketplace overview
-------------------------

.. image:: /_images/cookbook/open-marketplace/logo.png
    :scale: 20%
    :align: center

Since Sylius is rather an eCommerce framework than a platform,
it could be easily adjusted to different business models. One of the trending ones
in recent years is a Multi-Vendor Marketplace. `BitBag <https://bitbag.io>`_, our
leading project implementation partner built a product on top of Sylius that
transforms it into a marketplace. It is fully compatible with Symfony and Sylius
meaning you can use all Sylius knowledge, plugins, and Symfony bundles while working
with it. Moreover, the project is free and distributed under an MIT license.

What is included in the package?
--------------------------------

The project's features:

* All eCommerce-related features that could be found in Sylius-Standard
* Vendor registration and approval process
* Creating, managing, and accepting listings process
* Conversation module between vendors and admins
* Vendor order management
* Vendor shipping management
* Headless support

Installation
------------

Since the project is a bit more complex, it was developed as a standalone repository
rather than a plugin. For installation, follow the official
`Open Marketplace docs <https://github.com/BitBagCommerce/OpenMarketplace/blob/master/doc/installation.md>`_.

Demo
----

The demo can be accessed under `this link <https://demo.open-marketplace.io/>`_. There is
also a possibility to access different panels such as:

* `Vendor/shop user <https://demo.open-marketplace.io/en_US/login>`_: `camille@example.com@example.com: password`
* `Admin <https://demo.open-marketplace.io/admin>`_: `bitbag: password`

Useful links
------------

* `Official project page <https://open-marketplace.io>`_
* `Project's GitHub <https://github.com/BitBagCommerce/OpenMarketplace>`_
* `Documentation <https://github.com/BitBagCommerce/OpenMarketplace/tree/master/doc>`_
* `Project Slack Channel <https://join.slack.com/t/openmarketplacegroup/shared_invite/zt-1ks2kfsqe-w_J2uqgTMNEAYQS0xa8Q8Q>`_
