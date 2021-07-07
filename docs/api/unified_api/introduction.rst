Introduction
============

.. warning::

    The new, unified Sylius API is still under development, that's why the whole ``ApiBundle`` is tagged with ``@experimental``.
    This means that all code from ``ApiBundle`` is excluded from :doc:`Backward Compatibility Promise </book/organization/backward-compatibility-promise>`.
    You can enable entire API by changing the flag ``sylius_api.enabled`` to ``true`` in ``app/config/packages/_sylius.yaml``.

We have decided that we should rebuild our API and use API Platform to build a truly mature, multi-purpose API
which can define a new standard for headless e-commerce backends.

We will be supporting API Platform out-of-the-box. Secondly, it means that both APIs (Admin API and Shop API) will
be deprecated. We are not dropping them right now, but they will not receive further development. In the later phase,
we should provide an upgrade path for currently working apps. Last, but not least, you can already track our progress.
All the PR’s will be aggregated `in this issue <https://github.com/Sylius/Sylius/issues/11250>`_ and the documentation
can be already found `here <http://master.demo.sylius.com/api/v2/docs>`_.
