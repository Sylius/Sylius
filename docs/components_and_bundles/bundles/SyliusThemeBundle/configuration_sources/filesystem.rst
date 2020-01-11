Filesystem configuration source
===============================

**Filesystem** configuration source loads theme definitions from files placed under specified directories.

By default it seeks for ``composer.json`` files that exists under ``%kernel.project_dir%/themes`` directory, which
usually is resolved to ``themes``.

Configuration reference
-----------------------

.. code-block:: yaml

    sylius_theme:
        sources:
            filesystem:
                enabled: false
                filename: composer.json
                scan_depth: null
                directories:
                    - "%kernel.project_dir%/themes"

.. note::
    Like every other source, ``filesystem`` is disabled if not specified otherwise. To enable it and use
    the default configuration, use the following configuration:

    .. code-block:: yaml

        sylius_theme:
            sources:
                filesystem: ~

.. tip::

    Scanning for the configuration file inside themes directories is recursive with unlimited directory depth by default,
    which can result in slow performance when a lot of files are placed inside themes (e.g. a `node_modules` folder).
    Define the optional `scan_depth` (integer) setting to the configuration to restrict scanning for the theme configuration
    file to a specific depth.
