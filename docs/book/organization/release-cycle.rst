The Release Cycle
=================

This document explains the **release cycle** of the Sylius project (i.e. the
code & documentation hosted on the main ``Sylius/Sylius`` `Git repository`_).

Sylius manages its releases through a *time-based model* and follows the
`Semantic Versioning`_ strategy:

* A new Sylius minor version (e.g. 1.1, 1.2, etc.) comes out *at least every four months*.
* A new Sylius patch version (e.g. 1.0.1, 1.0.2, etc.) comes out *at least every three weeks*.

New Sylius minor releases will drop unsupported PHP versions.

Development
-----------

The full development period for any minor version is divided into two phases:

* **Development**: *First 5/6 of the time intended for the release* to add new features and to enhance existing ones.

* **Stabilization**: *Last 1/6 of the time intended for the release* to fix bugs, prepare the release, and wait
  for the whole Sylius ecosystem (third-party libraries, plugins, and projects using Sylius) to catch up.

During both periods, any new feature can be reverted if it won't be
finished in time or won't be stable enough to be included in the coming release.

Maintenance
-----------

Each minor Sylius version is maintained for a fixed period of time after its release.
This maintenance is divided into:

* *Bug fixes and security fixes*: During this period, being *eight months* long,
  all issues can be fixed. The end of this period is referenced as being the
  *end of maintenance* of a release.

* *Security fixes only*: During this period, being *sixteen months* long,
  only security related issues can be fixed. The end of this period is referenced
  as being the *end of life* of a release.

Past Releases
-------------

+---------+--------------------+--------------------+--------------------+-----------------------+
| Version | Release date       | End of maintenance | End of life        | Status                |
+=========+====================+====================+====================+=======================+
| 1.0     | Sep 13, 2017       | May 13, 2018       | Jan 13, 2019       | Not supported         |
+---------+--------------------+--------------------+--------------------+-----------------------+
| 1.1     | Feb 12, 2018       | Oct 12, 2018       | Jun 12, 2019       | Not supported         |
+---------+--------------------+--------------------+--------------------+-----------------------+
| 1.2     | Jun 13, 2018       | Feb 13, 2019       | Oct 13, 2019       | Not supported         |
+---------+--------------------+--------------------+--------------------+-----------------------+
| 1.3     | Oct 1, 2018        | Jun 1, 2019        | Feb 1, 2020        | Not supported         |
+---------+--------------------+--------------------+--------------------+-----------------------+
| 1.4     | Feb 4, 2019        | Oct 4, 2019        | Jun 4, 2020        | Not supported         |
+---------+--------------------+--------------------+--------------------+-----------------------+
| 1.5     | May 10, 2019       | Jan 10, 2020       | Sep 10, 2020       | Not supported         |
+---------+--------------------+--------------------+--------------------+-----------------------+
| 1.6     | Aug 29, 2019       | Apr 29, 2020       | Dec 29, 2020       | Not supported         |
+---------+--------------------+--------------------+--------------------+-----------------------+
| 1.7     | Mar 2, 2020        | Nov 16, 2020       | Jul 16, 2021       | Security support only |
+---------+--------------------+--------------------+--------------------+-----------------------+
| 1.8     | Sep 14, 2020       | May 14, 2021       | Jan 14, 2022       | Security support only |
+---------+--------------------+--------------------+--------------------+-----------------------+
| 1.9     | Mar 1, 2021        | Nov 1, 2021        | Jul 1, 2022        | Fully supported       |
+---------+--------------------+--------------------+--------------------+-----------------------+
| 1.10    | Jun 29, 2021       | Mar 29, 2022       | Nov 29, 2022       | Fully supported       |
+---------+--------------------+--------------------+--------------------+-----------------------+

Future Releases
---------------

+---------+----------------------+------------------------+--------------------+
| Version | Development starts   | Stabilization starts   | Release date       |
+=========+======================+========================+====================+
| 1.11    | Jun 29, 2021         |                        |                    |
+---------+----------------------+------------------------+--------------------+

Backward Compatibility
----------------------

All Sylius releases have to comply with our :doc:`Backward Compatibility Promise <backward-compatibility-promise>`.

Whenever keeping backward compatibility is not possible, the feature, the
enhancement or the bug fix will be scheduled for the next major version.

.. _Git repository: https://github.com/Sylius/Sylius
.. _Semantic Versioning: http://semver.org/
