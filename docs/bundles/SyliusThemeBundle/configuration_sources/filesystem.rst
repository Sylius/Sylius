Filesystem configuration source
===============================

**Filesystem** configuration source loads theme definitions from files placed under specified directories.

By default it seeks for ``composer.json`` files that exists under ``%kernel.root_dir%/themes`` directory, which
usually is resolved to ``app/themes``.

Configuration reference
-----------------------

.. code-block:: yaml

    sylius_theme:
        sources:
            filesystem:
                enabled: false
                filename: composer.json
                directories:
                    - "%kernel.root_dir%/themes"

.. note::
    Like every other source, ``filesystem`` is disabled if not specified otherwise. To enable it and use
    the default configuration, use the following configuration:

    .. code-block:: yaml

        sylius_theme:
            sources:
                filesystem: ~
