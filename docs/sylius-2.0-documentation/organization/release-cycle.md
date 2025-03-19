---
layout:
  title:
    visible: true
  description:
    visible: false
  tableOfContents:
    visible: true
  outline:
    visible: true
  pagination:
    visible: true
---

# Release Cycle

This document explains the **release cycle** of the Sylius project (i.e. the code & documentation hosted on the main `Sylius/Sylius` [repository](https://github.com/Sylius/Sylius)).

Sylius follows the [Semantic Versioning](https://semver.org/) strategy:

* A new Sylius patch version (e.g. 1.14.1, 1.14.2, etc.) comes out usually _every 3 to 6 weeks_, depending on the number of bug fixes developed
* A new Sylius minor version (e.g. 1.14, 2.1, 2.2, etc.) is released depending on various factors (see below), usually _every 3 to 6 months_
* A new Sylius major version (e.g. 2.0, 3.0, etc.) is also released depending on various factors, usually _every 2 years_

New Sylius minor releases will drop unsupported PHP versions.

### Scope-based vs time-based

The Sylius **release cycle** is **loosely** time-based (contrary to the [Symfony release cycle](https://symfony.com/releases)). Based on the experience from over 10 minor versions, we decided that time is not the only reason on which we should rely when planning the new Sylius’ version. Therefore, each new minor release of Sylius takes into consideration:

* what we would like to include in it (features, improvements, fixes)
* when we would like to release it (based on the Team capacity, estimated amount of work, and experience from previous minor releases development)

{% hint style="info" %}
The natural consequence of such a decision is uncertainty regarding the exact time of the next minor version release. We try to estimate it as precisely as possible, but sometimes delays cannot be avoided. We believe that releasing a **good** product is more important than releasing it **fast** 🤖
{% endhint %}

### Development

The full development period for any minor version is divided into two phases:

* **Development**: _The first 5/6 of the time intended for the release_ to add new features and to enhance existing ones.
* **Stabilization**: _The last 1/6 of the time intended for the release_ is to fix bugs, prepare the release, and wait for the whole Sylius ecosystem (third-party libraries, plugins, and projects using Sylius) to catch up.

During both periods, any new feature can be reverted if it won’t be finished in time or won’t be stable enough to be included in the coming release.

### Maintenance

Each minor Sylius version is maintained for a fixed time after its release. This maintenance is divided into:

* _Bug fixes and security fixes_: During this period all issues can be fixed. The end of this period is referenced as being the _**end of maintenance**_ of a release.
* _Security fixes only_: During this period, only security-related issues can be fixed. The end of this period is referenced as being the _**end of support**_ of a release.

### Planned releases

| Version | Development starts | Stabilization starts | Release date |
| ------- | ------------------ | -------------------- | ------------ |
| 2.1     | November, 2024     | Q1/Q2 2025           | Q2/Q3 2025   |

### Supported versions

<table><thead><tr><th width="89">Version</th><th>Release date</th><th>End of maintenance</th><th>End of support</th><th>Status</th></tr></thead><tbody><tr><td>2.0</td><td>Nov 12, 2024</td><td>August 2025</td><td>February 2026</td><td>Fully supported</td></tr><tr><td><strong>1.14 (LTS)</strong></td><td><strong>Nov 12, 2024</strong></td><td><strong>December 2025</strong></td><td><strong>December 2026</strong></td><td><strong>Fully supported</strong></td></tr><tr><td>1.13</td><td>Apr 23, 2024</td><td>January 2025</td><td>April 2025</td><td>Fully supported</td></tr></tbody></table>

### Unsupported versions

| Version | Release date | End of maintenance | End of support |
| ------- | ------------ | ------------------ | -------------- |
| 1.12    | Oct 31, 2022 | Jun 30, 2024       | Dec 31, 2024   |
| 1.11    | Feb 14, 2022 | Jan 31, 2023       | Oct 31, 2023   |
| 1.10    | Jun 29, 2021 | May 14, 2022       | Jan 14, 2023   |
| 1.9     | Mar 1, 2021  | Nov 1, 2021        | Jul 1, 2022    |
| 1.8     | Sep 14, 2020 | May 14, 2021       | Jan 14, 2022   |
| 1.7     | Mar 2, 2020  | Nov 16, 2020       | Jul 16, 2021   |
| 1.6     | Aug 29, 2019 | Apr 29, 2020       | Dec 29, 2020   |
| 1.5     | May 10, 2019 | Jan 10, 2020       | Sep 10, 2020   |
| 1.4     | Feb 4, 2019  | Oct 4, 2019        | Jun 4, 2020    |
| 1.3     | Oct 1, 2018  | Jun 1, 2019        | Feb 1, 2020    |
| 1.2     | Jun 13, 2018 | Feb 13, 2019       | Oct 13, 2019   |
| 1.1     | Feb 12, 2018 | Oct 12, 2018       | Jun 12, 2019   |
| 1.0     | Sep 13, 2017 | May 13, 2018       | Jan 13, 2019   |

### Backward Compatibility

All Sylius releases have to comply with our [Backward Compatibility Promise](backwards-compatibility-promise.md).

Whenever keeping backward compatibility is not possible, the feature, the enhancement, or the bug fix will be scheduled for the next major version.
