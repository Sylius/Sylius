Theme configuration reference
=============================

.. code-block:: json

    {
        "name": "vendor/sylius-theme",
        "title": "Great Sylius theme!",
        "description": "Optional description",
        "authors": [
            [
                "name": "Kamil Kokot",
                "email": "kamil@kokot.me",
                "homepage": "http://kamil.kokot.me",
                "role": "Developer
            ]
        ],
        "parents": [
            "vendor/common-sylius-theme",
            "another-vendor/not-so-cool-looking-sylius-theme"
        ]
    }

.. warning::

    Theme configuration was meant to be mixed with the one from Composer. Fields ``name``, ``description`` and
    ``authors`` are shared between these by default. To use different values for Composer & ThemeBundle,
    have a look below.

Composer integration
--------------------

.. code-block:: json

    {
        "name": "vendor/sylius-theme", # Used by both Composer and ThemeBundle
        "type": "sylius-theme", # Used only by Composer while installing themes as dependencies in the root package
        "description": "Composer package description", # Used only by Composer; ThemeBundle will use the other one
        "authors": [ # Used by both Composer and ThemeBundle
            [
                "name": "Kamil Kokot"
            ]
        ],
        "extra": {
            "sylius-theme": {
                "description": "Theme description", # Used by ThemeBundle, overrides the Composer one
                "parents": {
                    "vendor/other-sylius-theme"
                }
            }
        }
    }

.. note::

    By configuring Composer package along with theme we do not have to duplicate fields like ``name`` or ``authors``,
    but we are free to overwrite them in any time, just like the ``description`` field in example below.
    The theme configuration is complementary to the Composer configuration and results in perfectly valid ``composer.json``.
