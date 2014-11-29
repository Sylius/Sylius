LocaleContextInterface
======================

The ``LocaleContext`` allows you to manage the currently used locale, it needs to implement the ``LocaleContextInterface``.

+----------------------+-------------------------------------+----------------------------+
| Method               | Description                         | Returned value             |
+======================+=====================================+============================+
| getDefaultLocale()   | Get the default locale              | string                     |
+----------------------+-------------------------------------+----------------------------+
| getLocale()          | Get the currently active locale     | string                     |
+----------------------+-------------------------------------+----------------------------+
| setLocale($locale)   | Set the currently active locale     | Void                       |
+----------------------+-------------------------------------+----------------------------+
