How to disable admin version notifications?
===========================================

By default Sylius sends checks from the admin whether you are running the latest version. In case you are not
running the latest version, a notification will be shown in the admin panel (top right).

.. note::

    In order to inform you about the newest Sylius releases and for us to be aware of shops based on Sylius,
    we are using an internal statistical service called **GUS**.
    The only data that is collected and stored in the database of GUS are hostname, user agent, locale,
    app environment (test, dev or prod), current Sylius version and the date of last contact.

This guide will instruct you how to disable this check & notification.

How to disable notifications?
-----------------------------

Add the following configuration to ``config/packages/sylius_admin.yaml``.

.. code-block:: yaml

    sylius_admin:
        notifications:
            enabled: false
