.. rst-class:: plus-doc

Upgrading Sylius Plus
=====================

Sylius regularly releases new versions, usually every two weeks.
Each release comes with an ``UPGRADE.md`` file, which is meant to help in the upgrading process.

1. **Update the Sylius Plus version constraint by modifying the ``composer.json`` file:**

    .. code-block:: yaml

        {
            "require": {
                "sylius/plus": "^1.0.0@beta"
            }
        }

2. **Upgrade dependencies by running a Composer command:**

    .. code-block:: bash

        composer update sylius/plus --with-all-dependencies

    If this does not help, it is a matter of debugging the conflicting versions and working out how your ``composer.json``
    should look after the upgrade.
    You can check what version of Sylius is installed by running ``composer show sylius/plus`` command.

3. **Follow the instructions found in the ``UPGRADE.md`` file for a given minor release.**

   .. note::

      As Sylius Plus is a private repository its README files (and CHANGELOG) have been exposed in a separate public
      repository which can be found here: `<https://github.com/Sylius/PlusInformationCenter>`_


.. image:: ../../_images/sylius_plus/banner.png
   :align: center
   :target: https://sylius.com/plus/?utm_source=docs
