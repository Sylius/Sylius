The Release Process
===================

This document explains the **release process** of the Sylius project (i.e. the
code & documentation hosted on the main ``Sylius/Sylius`` `Git repository`_).

Sylius manages its releases through a *time-based model* and follows the
`Semantic Versioning`_ strategy:

* A new Sylius minor version (e.g. 1.1, 1.2, etc.) comes out every *four months*
* A new Sylius patch version (e.g. 1.0.1, 1.0.2, etc.) comes out every *two weeks*

Development
-----------

The full development period for any minor version lasts four months and
is divided into two phases:

* **Development**: *Three months* to add new features and to enhance existing
  ones;

* **Stabilization**: *One month* to fix bugs, prepare the release, and wait
  for the whole Sylius ecosystem (third-party libraries, plugins, and
  projects using Sylius) to catch up.

During the development period, any new feature can be reverted if it won't be
finished in time or if it won't be stable enough to be included in the coming release.

Maintenance
-----------

Each Sylius version is maintained for a fixed period of time.
This maintenance is divided into:

* *Bug fixes and security fixes*: During this period, being *eight months* long,
  all issues can be fixed. The end of this period is referenced as being the
  *end of maintenance* of a release.

* *Security fixes only*: During this period, being *sixteen months* long,
  only security related issues can be fixed. The end of this period is referenced
  as being the *end of life* of a release.

Backward Compatibility
----------------------

All Sylius releases have to comply with our `Backward Compatibility Promise`_.

Whenever keeping backward compatibility is not possible, the feature, the
enhancement or the bug fix will be scheduled for the next major version.

.. _Git repository: https://github.com/Sylius/Sylius
.. _Semantic Versioning: http://semver.org/
.. _Backward Compatibility Promise: http://docs.sylius.org/en/latest/contributing/code/bc.html
