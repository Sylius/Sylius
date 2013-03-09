CHANGELOG
=========

### v0.1.0

* Initial development release.
* Added documentation.
* All money values are stored as integers.
* Add basic validation schema.

### 03-02-2013

* ``SellableInterface`` was introduced.
* ``sylius_sales`` prefix was cut to simpler version - just ``sylius``.
* ``OrderBuilderInterface`` has been changed. Now it serves as a real builder for order.

### 01-01-2013

* ``OrderBuilderInterface::finalize`` method was removed. Please rely on events instead.
