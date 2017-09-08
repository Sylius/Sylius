Vision & Strategy
=================

Vision & strategy is defined by the Project Lead, Core Team and Community members.

If you would like to suggest new tool, process, feel free to submit a PR to this section of the documentation.

GitHub
------

We use GitHub as the main tool to organize the community work and everything that happens around Sylius.
Releases, bug reports, feature requests, roadmap management, etc. happens on this platform.

If you are not sure about your issue, please use Slack to discuss it with the fellow community members before opening it on GitHub.

Milestones
~~~~~~~~~~

We use version milestones to define the roadmap. We open milestones only for major and minor releases.
Depending on the current focus, appropriate issues are assigned to proper milestones.

By issues worthy a milestone assignment, we understand:

* New features, which are crucial for the upcoming release;
* Bugs, which are blocking the upcoming release;
* RFC/Discussions, which should be addressed before releasing new major/minor version.

Learn more about the :doc:`/contributing/organization/release-process`.

Efforts
~~~~~~~

Larger tasks, which consist of multiple issues are organized as Efforts.

Every Effort should have a main Effort Issue opened on GitHub, which contains all details about particular task. It should also have an Effort Lead(s) assigned.

Individual tasks that push the effort forward shall be created as separate issues, but always referenced in the Effort Issue.

An example of Efforts: "Optimization of the Checkout Process", "Integrate All The Things" (better foundation for integrations) or whatever creative name you can come up with.

Anyone can kickstart a new Effort, but first one should discuss it with the Core Team Members (on Slack) to get proper guidance.

Labels (Issue Types)
~~~~~~~~~~~~~~~~~~~~

* **Bug** - Confirmed bugs, something is not working as expected;
* **Potential Bug** - Bug reports. When confirmed/reproduced, should become a confirmed bug;
* **Bug Fix** - PRs, which fix bugs;
* **Feature Request** - New feature proposals;
* **New Feature** - PRs, which implement new features;
* **RFC** - Discussions about potential changes or new features;
* **Documentation Request** - New documentation request;
* **BC Break** - Patches, which break backwards compatibility;
* **Minor** - Typo fixes, spelling, grammar, commas and other valuable, although not crucial changes;
* **Maintenance** - Travis configurations, READMEs, releases, etc.;
* **Optimization** - Patches which optimize the performance of the platform;
* **Environment** - Environment (OS, databases, libraries, etc.) specific issues;
* **UI** - User Interface related issues;
* **Translations** - Everything related to transalting the UI;
* **Easy Pick** - Bugs and feature proposals, which are relatively simple to implement for newcomers;
* **Critical** - Issues, which are critical and should be fixed ASAP;
* **Effort** - Reserved for the main Effort Issues.

Pull Request Checklist
~~~~~~~~~~~~~~~~~~~~~~

Before any PR is merged, the following things need to be confirmed:

1. Changes can be included in the upcoming release;
2. PR has been approved by at least 1 fellow Core Team member;
3. PR adheres to the PR template and contains the MIT license;
4. PR includes relevant documentation updates;
5. PR contains appropriate UPGRADE file updates if necessary;
6. PR is properly labeled and milestone is assigned if needed;
7. All required checks are passing. It is green!

Certain PRs can only be merged by the Project Lead:

* BC Breaks;
* Introducing new components, bundles or high level architecture layers;
* Renaming existing components;
* If in doubt, ask your friendly neighborhood Project Lead.
