The Release Process
===================

This document explains the **release process** of the Sylius project (i.e. the
code & documentation hosted on the main ``Sylius/Sylius`` `Git repository`_).

Sylius manages its releases through a *time-based model* and follows the
`Semantic Versioning`_ strategy:

* A new Sylius minor version (e.g. 1.1, 2.4, 3.2) comes out every *six months*:
* A new Sylius major version (e.g., 2.0, 3.0) comes out every *two years* and
  it's released at the same time of the last minor version of the previous major
  version.

Development
-----------

The full development period for any major or minor version lasts six months and
is divided into two phases:

* **Development**: *Four months* to add new features and to enhance existing
  ones;

* **Stabilization**: *Two months* to fix bugs, prepare the release, and wait
  for the whole Sylius ecosystem (third-party libraries, plugins, and
  projects using Sylius) to catch up.

During the development phase, any new feature can be reverted if it won't be
finished in time or if it won't be stable enough to be included in the current
final release.

Maintenance
-----------

Each Sylius version is maintained for a fixed period of time, depending on the
type of the release. This maintenance is divided into:

* *Bug fixes and security fixes*: During this period, all issues can be fixed.
  The end of this period is referenced as being the *end of maintenance* of a
  release.

* *Security fixes only*: During this period, only security related issues can
  be fixed. The end of this period is referenced as being the *end of life* of
  a release.

Sylius Versions
---------------

Standard Versions
~~~~~~~~~~~~~~~~~

A **Standard Minor Version** is maintained for an *eight month* period for bug
fixes, and for a *fourteen month* period for security issue fixes.

Long Term Support Versions
~~~~~~~~~~~~~~~~~~~~~~~~~~

Every two years, a new **Long Term Support Version** (usually abbreviated as "LTS")
is published. Each LTS version is supported for a *three year* period for bug
fixes, and for a *four year* period for security issue fixes.

Backward Compatibility
----------------------

Our Backward Compatibility Promise is very
strict and allows developers to upgrade with confidence from one minor version
of Sylius to the next one.

Whenever keeping backward compatibility is not possible, the feature, the
enhancement or the bug fix will be scheduled for the next major version.

.. _Semantic Versioning: http://semver.org/
.. _Git repository: https://github.com/Sylius/Sylius
