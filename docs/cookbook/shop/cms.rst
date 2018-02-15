How to manage content in Sylius?
=================================

Why do you need content management system?
------------------------------------------

Content management is one of the most important business aspects of modern eCommerce apps.
Providing store updates like new blog pages, banners and promotion images is responsible for building the conversion rate
either for new and existing clients.

Content management in Sylius
----------------------------

Sylius standard app does not come with a content management system. Our community has taken care of it.
As Sylius does have a convenient dev oriented plugin environment, the developers from `BitBag <https://bitbag.shop>`_ decided to develop
their flexible CMS module. You can find it `here <https://github.com/BitBagCommerce/SyliusCmsPlugin>`_.

.. tip::

    The whole plugin has its own `demo page <https://cms.bitbag.shop/>`_ with specific use cases. You can access
    the `admin panel <https://cms.bitbag.shop/admin/>`_
    with ``login: sylius, password: sylius`` credentials.

Inside the plugin, you will find:

* HTML, image and text blocks you can place in each Twig template
* Page resources
* Sections which you can use to create a blog, customer information, etc.
* FAQ module

A very handy feature of this plugin is that you can customize it for your specific needs like you do with each :doc:`Sylius model </customization/model>`.

Installation & usage
--------------------

Find out more about how to install the plugin on `GitHub <https://github.com/BitBagCommerce/SyliusCmsPlugin>`_ in the README file.

Learn more
----------

* :doc:`How to create a plugin for Sylius? </plugins/creating-plugin>`
* `BitBag plugins <https://github.com/BitBagCommerce>`_
* `FriendsOfSylius plugins <https://github.com/FriendsOfSylius/SyliusGoose>`_
