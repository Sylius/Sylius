CHANGELOG
=========

### v0.17.0

* Integrated TranslationBundle.

### v0.16.0

* Introduce Factory for all resources.
* New ``DriverInterface`` for resources.
* Reworked all listeners to use new Registry.
* Configuration tree has changed slightly, added ``validation_groups``, ``form`` and ``translation`` support.
* It generates forms for non-Sylius resources if form type classes specified in configuration.

### v0.10.0

* Twig extension was renamed from `SyliusResourceExtension` into `ResourceExtension`,
  also the service name was changed from `sylius.twig.resource` to `sylius.twig.extension.resource`.

### v0.9.0

* Release before the components extraction.
