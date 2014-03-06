Contributing to the Documentation
=================================

Documentation is as important as code. It follows the exact same principles:
DRY, tests, ease of maintenance, extensibility, optimization, and refactoring
just to name a few. And of course, documentation has bugs, typos, hard to read
tutorials, and more.

Contributing
------------

Before contributing, you need to become familiar with the :doc:`markup
language <format>` used by the documentation.

The Sylius documentation is hosted on GitHub:

.. code-block:: text

    https://github.com/Sylius/Sylius-Docs

If you want to submit a patch, `fork`_ the official repository on GitHub and
then clone your fork:

.. code-block:: bash

    $ git clone git://github.com/YOURUSERNAME/Sylius-Docs.git

The ``master`` branch holds the documentation for the development branch of the code.

Create a dedicated branch for your changes (for organization):

.. code-block:: bash

    $ git checkout -b improving_foo_and_bar

You can now make your changes directly to this branch and commit them. When
you're done, push this branch to *your* GitHub fork and initiate a pull request.

Creating a Pull Request
~~~~~~~~~~~~~~~~~~~~~~~

Following the example, the pull request will default to be between your
``improving_foo_and_bar`` branch and the ``Sylius-Docs`` ``master`` branch.

GitHub covers the topic of `pull requests`_ in detail.

.. note::

    The Sylius documentation is licensed under a Creative Commons
    Attribution-Share Alike 3.0 Unported :doc:`License <license>`.

You can also prefix the title of your pull request in a few cases:

* ``[WIP]`` (Work in Progress) is used when you are not yet finished with your
  pull request, but you would like it to be reviewed. The pull request won't
  be merged until you say it is ready.

* ``[WCM]`` (Waiting Code Merge) is used when you're documenting a new feature
  or change that hasn't been accepted yet into the core code. The pull request
  will not be merged until it is merged in the core code (or closed if the
  change is rejected).

.. _doc-contributing-pr-format:

Pull Request Format
~~~~~~~~~~~~~~~~~~~

Unless you're fixing some minor typos, the pull request description **must**
include the following checklist to ensure that contributions may be reviewed
without needless feedback loops and that your contributions can be included
into the documentation as quickly as possible:

.. code-block:: text

    | Q             | A
    | ------------- | ---
    | Doc fix?      | [yes|no]
    | New docs?     | [yes|no] (PR # on Sylius/Sylius if applicable)
    | Fixed tickets | [comma separated list of tickets fixed by the PR]

An example submission could now look as follows:

.. code-block:: text

    | Q             | A
    | ------------- | ---
    | Doc fix?      | yes
    | New docs?     | yes (Sylius/Sylius#1250)
    | Fixed tickets | #1075

.. tip::

    Online documentation is rebuilt on every code-push to github.

Documenting new Features or Behavior Changes
--------------------------------------------

If you're documenting a brand new feature or a change that's been made in
Sylius, you should precede your description of the change with a ``.. versionadded:: 1.X``
tag and a short description:

.. code-block:: text

    .. versionadded:: 1.1
        The ``getProductDiscount`` method was introduced in Sylius 1.1.

Standards
---------

All documentation in the Sylius Documentation should follow
:doc:`the documentation standards <standards>`.

Reporting an Issue
------------------

The most easy contribution you can make is reporting issues: a typo, a grammar
mistake, a bug in a code example, a missing explanation, and so on.

Steps:

* Submit new issue in the GitHub tracker;
* *(optional)* Submit a patch.

Translating
-----------

Read the dedicated :doc:`document <translations>`.

.. _`fork`:                       https://help.github.com/articles/fork-a-repo
.. _`pull requests`:              https://help.github.com/articles/using-pull-requests
