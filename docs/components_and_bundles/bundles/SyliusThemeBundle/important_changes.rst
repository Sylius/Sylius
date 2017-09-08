Important changes
=================

``SyliusThemeBundle`` changes the way vanilla Symfony works a lot. Templates and translations will never behave
the same as they were.

Templates
---------

Changed loading order (priority descending):

    - App templates:
        - ``<Theme>/views`` **(NEW!)**
        - ``app/Resources/views``
    - Bundle templates:
        - ``<Theme>/<Bundle name>/views`` **(NEW!)**
        - ``app/Resources/<Bundle name>/views``
        - ``<Bundle>/Resources/views``

Translations
------------

Changed loading order (priority descending):

    - ``<Theme>/translations`` **(NEW!)**
    - ``<Theme>/<Bundle name>/translations`` **(NEW!)**
    - ``app/Resources/translations``
    - ``app/Resources/<Bundle name>/translations``
    - ``<Bundle>/Resources/translations``

Assets
------

Theme assets are installed by ``sylius:theme:assets:install`` command, which is supplementary for and should be used after ``assets:install``.

The command run with ``--symlink`` or ``--relative`` parameters creates symlinks for every installed asset file,
not for entire asset directory (eg. if ``AcmeBundle/Resources/public/asset.js`` exists, it creates symlink ``web/bundles/acme/asset.js`` 
leading to ``AcmeBundle/Resources/public/asset.js`` instead of symlink ``web/bundles/acme/`` leading to ``AcmeBundle/Resources/public/``). 
When you create a new asset or delete an existing one, it is required to rerun this command to apply changes (just as the hard copy option works).

Assetic
-------

Nothing has changed, ``ThemeBundle`` is not and will not be integrated with ``Assetic``.
