Overview
========

.. note::

    If you're still using Gulp consider switching to Webpack (as it became our default build tool) using :doc:`our guide </cookbook/frontend/migrating-to-webpack-1-12-or-later>`.

Requirements
------------

We recommend using Node.js ``16.x`` as it is the current LTS version. However, Sylius frontend is also compatible with Node.js ``14.x`` and ``18.x``.

In Sylius, we use ``yarn`` as a package manager, but there are no obstacles to using ``npm``.

Stack overview
--------------

Sylius frontend is based on the following technologies:

* Semantic UI
* jQuery
* Webpack

Of course, it is not a complete list of packages we use, but these are the most important. To see all packages used in Sylius check the `package.json <https://github.com/Sylius/Sylius/blob/1.12/package.json>`_ file.

Webpack vs Gulp
---------------

For a long time, the Gulp was the default build tool for Sylius. Since version 1.12, Gulp has been replaced by Webpack. Gulp's configs are still present due to compatibility with previous versions, but we do not recommend using them anymore.

Webpack Encore
--------------

To improve the experience of using Webpack with Sylius, we use the Webpack Encore package made by the Symfony team. `You can read more about Encore in the official Symfony documentation <https://symfony.com/doc/current/frontend.html#webpack-encore>`_.

Assets structure
----------------

We provide the following assets directory structure:

.. code-block:: bash

    <project_root>
    ├── assets
    │   ├── admin <- all admin-related assets should be placed here, they are only included when you are in the admin panel
    │   │   ├── entry.js <- entry point for admin assets, do not remove nor rename it unless you know what you do
    │   ├── shop <- all shop-related assets should be placed here, they are only included when you are in the shop
    │   │   ├── entry.js <- entry point for shop assets, do not remove nor rename it unless you know what you do

When you want to add e.g. SCSS files or images your structure might look like this:

.. code-block:: bash

    <project_root>
    ├── assets
    │   ├── admin
    │   │   ├── entry.js
    │   ├── shop
    │   │   ├── styles
    │   │   │   ├── app.scss
    │   │   ├── images
    │   │   │   ├── logo.png
    │   │   ├── entry.js

If you want to know how to import and manage those assets take a look at our :doc:`/book/frontend/managing-assets` guide.
