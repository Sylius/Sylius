Vision & Strategy
=================

Vision & strategy is defined by the Project Leader, Core Team and Community members.

If you would like to suggest new tool, process, feel free to submit a PR to this section of the documentation.

GitHub
------

We use GitHub as the main tool to organize the community work and everything that happens around Sylius.
Releases, bug reports, feature requests, roadmap management, etc. happens on this platform.

If you are not sure about your issue, please use Slack to discuss it with the fellow community members before opening it on GitHub.

Milestones
~~~~~~~~~~

Since stable Sylius release, we use milestones to mark the lowest branch an issue or a PR applies to.
For example, if a bug report is marked with 1.0 milestone, the related bugfix PR should be opened against
1.0 branch. Then, after this PR is merged, it would be released in the next 1.0.x release.

Learn more about the :doc:`/contributing/organization/release-process`.

Labels (Issue Types)
~~~~~~~~~~~~~~~~~~~~

* **Admin** - AdminBundle related issues and PRs.
* **API** - AdminApiBundle related issues and PRs.
* **BC Break** - PRs introducing BC breaks (do not even try to merge).
* **Behat** - Issues and PRs aimed at improving Behat usage.
* **Bug** - Confirmed bugs or bugfixes.
* **Critical** - Issues and PRs, which are critical and should be fixed ASAP.
* **Documentation** - Documentation related issues and PRs - requests, fixes, proposals.
* **DX** - Issues and PRs aimed at improving Developer eXperience.
* **Easy Pick** - Bugs and feature proposals, which are relatively simple to implement for newcomers.
* **Enhancement** - Minor issues and PRs improving the current solutions (optimizations, typo fixes, etc.).
* **Environment** - Environment (OS, databases, libraries, etc.) specific issues.
* **Feature** - New feature proposals.
* **Help Wanted** - Issues needing help and clarification.
* **Maintenance** - Travis configurations, READMEs, releases, etc.
* **Potential Bug** - Bug reports, should become a *Bug* after confirming it.
* **RFC** - Discussions about potential changes or new features.
* **Shop** - ShopBundle related issues and PRs.
* **Symfony 4.0** - Symfony 4.0 related issues and PRs.
* **Stale** - Issues and PRs with no recent activity, about to be closed soon.
* **UX** - Issues and PRs aimed at improving User eXperience.

Pull Request Checklist
~~~~~~~~~~~~~~~~~~~~~~

Before any PR is merged, the following things need to be confirmed:

1. Changes can be included in the upcoming release.
2. PR has been approved by at least 1 fellow Core Team member.
3. PR adheres to the PR template and contains the MIT license.
4. PR includes relevant documentation updates.
5. PR contains appropriate UPGRADE file updates if necessary.
6. PR is properly labeled and milestone is assigned if needed.
7. All required checks are passing. It is green!

Certain PRs can only be merged by the Project Lead:

* BC Breaks
* Introducing new components, bundles or high level architecture layers
* Renaming existing components
* If in doubt, ask your friendly neighborhood Project Lead
