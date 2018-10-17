How to disable admin version notifications?
===========================================

By default Sylius sends checks from the admin whether you are running the latest version. In case you are not
running the latest version, a notification will be shown in the admin panel (top right).

This guide will instruct you how to disable this check & notification.

How to disable notifications?
-----------------------------

Add the following configuration to ``app/config/config.yml``.

.. code-block:: yaml

    sylius_admin:
        notifications:
            enabled: false
