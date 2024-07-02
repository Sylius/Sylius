.. rst-class:: plus-doc

Upgrading Sylius Plus
=====================

Sylius regularly releases new versions of Sylius Plus modules.
Each release comes with an ``UPGRADE.md`` file, which is meant to help in the upgrading process.

1. **Update the version constraint of your Sylius Plus modules by modifying the ``composer.json`` file:**

    .. code-block:: yaml

        {
            "require": {
                "sylius/plus-marketplace-suite-plugin": "^2.6"
            }
        }

2. **Upgrade dependencies by running a Composer command:**

    .. code-block:: bash

        composer update sylius/plus-marketplace-suite-plugin --with-all-dependencies

    If this does not help, it is a matter of debugging the conflicting versions and working out how your ``composer.json``
    should look after the upgrade.
    You can check what version is installed by running ``composer show sylius/plus-marketplace-suite-plugin`` command.

3. **Follow the instructions found in the ``UPGRADE.md`` file for a given minor release.**

   .. note::

      As Sylius Plus modules are private repositories their UPGRADE files (and CHANGELOGs) have been exposed in a separate public
      repository which can be found here: `<https://github.com/Sylius/PlusInformationCenter>`_


.. image:: ../../_images/sylius_plus/banner.png
   :align: center
   :target: https://sylius.com/plus/?utm_source=docs
