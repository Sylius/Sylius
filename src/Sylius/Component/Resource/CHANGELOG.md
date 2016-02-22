CHANGELOG
=========

### v0.17.0

* Integrated Translation component

### v0.16.0

* Introduce Factory and FactoryInterface for creating new resources.
* ``createNew`` method removed from RepositoryInterface.
* Introduce InMemoryRepository for object storage using the memory.
* Extracted ``getId`` method into ResourceInterface.
* Extended RepositoryInterface by ``add`` and ``remove`` methods.
* Introduce Metadata class for easier handling of resource configurations.
* Add Registry to hold all resources in the system.
* [BC BREAK] Introduced 3 new traits: `SoftDeletableTrait`, `TimestampableTrait`, `ToggleableTrait`
