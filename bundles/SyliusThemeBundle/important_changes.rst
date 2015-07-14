Important changes
=================

``SyliusThemeBundle`` changes the way vanilla Symfony2 works a lot. Templates, translations and assets will never behave
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

Changed the way command ``assets:install`` works.

Since now, ``assets:install`` with ``--symlink`` or ``--relative`` parameters, creates symlinks for every installed asset file, 
not for entire asset directory (eg. if ``AcmeBundle/Resources/public/asset.js`` exists, it creates symlink ``web/bundles/acme/asset.js`` 
leading to ``AcmeBundle/Resources/public/asset.js`` instead of symlink ``web/bundles/acme/`` leading to ``AcmeBundle/Resources/public/``). 
When you create new asset or delete existing one, it is required to rerun this command to apply changes (just as the hard copy option works).

Assetic
-------

Nothing has changed, ``ThemeBundle`` is not integrated with ``Assetic`` (for now).