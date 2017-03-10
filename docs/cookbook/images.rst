How to resize images?
=====================

In Sylius we are using the `LiipImagineBundle <http://symfony.com/doc/current/bundles/LiipImagineBundle/index.html>`_
for handling images.

.. tip::

    You will find a reference to the types of filters in the LiipImagineBundle `in their documentation <http://symfony.com/doc/current/bundles/LiipImagineBundle/filters.html>`_.

There are three places in the Sylius platform where the configuration for images can be found:

* `AdminBundle <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/AdminBundle/Resources/config/app/config.yml>`_
* `ShopBundle <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/ShopBundle/Resources/config/app/config.yml>`_
* `CoreBundle <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/CoreBundle/Resources/config/app/config.yml>`_

These configs provide you with a set of filters for resizing images to **thumbnails**.

+-----------------------------------------+------------------+
| ``sylius_admin_product_tiny_thumbnail`` | size: [64, 64]   |
+-----------------------------------------+------------------+
| ``sylius_admin_product_thumbnail``      | size: [50, 50]   |
+-----------------------------------------+------------------+
| ``sylius_shop_product_tiny_thumbnail``  | size: [64, 64]   |
+-----------------------------------------+------------------+
| ``sylius_shop_product_small_thumbnail`` | size: [150, 112] |
+-----------------------------------------+------------------+
| ``sylius_shop_product_thumbnail``       | size: [260, 260] |
+-----------------------------------------+------------------+
| ``sylius_shop_product_large_thumbnail`` | size: [550, 412] |
+-----------------------------------------+------------------+
| ``sylius_small``                        | size: [120, 90]  |
+-----------------------------------------+------------------+
| ``sylius_medium``                       | size: [240, 180] |
+-----------------------------------------+------------------+
| ``sylius_large``                        | size: [640, 480] |
+-----------------------------------------+------------------+

How to resize images with filters?
----------------------------------

Knowing that you have filters out of the box you need to also know how to use them with images in **Twig** templates.

The ``imagine_filter('name')`` is a twig filter. This is how you would get an image path for on object ``item`` with a thumbnail applied:

.. code-block:: twig

    <img src="{{ object.path|imagine_filter('sylius_small') }}" />

.. note::

    Sylius stores images on entities by saving a ``path`` to the file in the database. 
    The imagine_filter root path is ``/web/media/image``.

How to add custom image resizing filters?
-----------------------------------------

If the filters we have in Sylius by deafult are not suitable for your needs, you can easily add your own.

All you need to do is to configure new filter in the ``app/config/config.yml`` file.
For example you can create a filter for advertisement banners:

.. code-block:: yaml

    # app/config/config.yml
    liip_imagine:
        filter_sets:
            advert_banner:
                filters:
                    thumbnail: { size: [800, 200], mode: inset }

**How to use your new filter in Twig?**

.. code-block:: twig

    <img src="{{ banner.path|imagine_filter('advert_banner') }}" />

Learn more
----------

* `The LiipImagineBundle documentation <http://symfony.com/doc/current/bundles/LiipImagineBundle/index.html>`_
