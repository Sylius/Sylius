The Sylius API
==============

.. warning::

    The new, unified Sylius API is still under development.

We have decided that we should rebuild our API and use API Platform to build a truly mature, multi-purpose API
which can define a new standard for headless e-commerce backends.

We will be supporting API Platform out-of-the-box. Secondly, it means that both APIs will be deprecated. We are not
dropping them right now, but they will not receive further development. In the later phase, we should provide
an upgrade path for currently working apps. Last, but not least, you can already tracktrace our progress. All the PR’s
will be aggregated `in this issue <https://github.com/Sylius/Sylius/issues/11250>`_ and the documentation can be already found
`here <http://master.demo.sylius.com/new-api/docs>`_.

The Admin API
-------------

This part of the documentation is about the old Admin API for the Sylius platform.

.. toctree::
    :hidden:

    admin_api/index

.. include:: /api/admin_api/map.rst.inc
