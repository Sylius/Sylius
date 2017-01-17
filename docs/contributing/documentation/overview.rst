Contributing to the Documentation
=================================

The documentation is as important as the code. It follows the exact same principles:
DRY, tests, ease of maintenance, extensibility, optimization, and refactoring
just to name a few. And of course, documentation has bugs, typos, hard to read
tutorials, and many more.

Contributing
------------

Before contributing, you need to become familiar with the :doc:`markup
language <format>` used by the documentation.

The Sylius documentation is hosted on GitHub, in the main repository:

.. code-block:: text

    https://github.com/Sylius/Sylius

If you want to submit a patch, `fork`_ the official repository on GitHub and
then clone your fork to you local destination:

.. code-block:: bash

    $ git clone git@github.com:YOUR_USERNAME/Sylius.git

Under the name ``origin`` you will have from now on the access to your fork.
Add also the main repository as the ``upstream`` remote.

.. code-block:: bash

    $ git remote add upstream git@github.com:Sylius/Sylius.git


The ``master`` branch holds the documentation for the development branch of the code.

Create a dedicated branch for your changes (for organization):

.. code-block:: bash

    $ git checkout -b docs/improving_foo_and_bar

You can now make your changes directly to this branch and commit them.
Remember to name your commits descriptively, keep them possibly small, with just unitary changes (such that change something only in one part of the docs, not everywhere).

When you're done, push this branch to *your* GitHub fork and initiate a pull request.

Your pull request will be reviewed, you will be asked to apply fixes if necessary and then it will be merged into the main repository.


Testing Documentation
~~~~~~~~~~~~~~~~~~~~~

To test the documentation before a commit:

* Install `pip`_, the Python package manager,

* Download the documentation requirements,

.. code-block:: bash

    $ pip install -r requirements.txt
    # This makes sure that the version of Sphinx you'll get is >=1.4.2!

* Install `Sphinx`_,

.. code-block:: bash

    $ pip install Sphinx

* In the ``docs`` directory run ``sphinx-build -b html . build`` and view the generated HTML files in the ``build`` directory.

Creating a Pull Request
~~~~~~~~~~~~~~~~~~~~~~~

Following the example, the pull request will be from your
``improving_foo_and_bar`` branch to the ``Sylius`` ``master`` branch by default.

GitHub covers the topic of `pull requests`_ in detail.

.. note::

    The Sylius documentation is licensed under a Creative Commons
    Attribution-Share Alike 3.0 Unported :doc:`License <license>`.

.. warning::

    You should always prefix the PR name with a ``[Documentation]`` tag!

You can prefix the title of your pull request in a few cases:

* ``[WIP]`` (Work in Progress) is used when you are not yet finished with your
  pull request, but you would like it to be reviewed. The pull request won't
  be merged until you say it is ready.

* ``[ComponentName]`` if you are contributing docs that regard on of :doc:`the Sylius Components </components/general/index>`.

* ``[BundleName]`` when you add documentation of :doc:`the Sylius Bundles </bundles/index>`.

* ``[Behat]`` if you modify something in the :doc:`the Behat guide </behat/index>`.

* ``[API]`` when you are contributing docs to :doc:`the API guide </api/index>`.

For instance if your pull request is about documentation of some feature of the Resource bundle, but it is still a work in progress
it should look like : ``[WIP][Documentation][ResourceBundle] Arbitrary feature documentation``.

.. _doc-contributing-pr-format:

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

The easiest contributions you can make is reporting issues: a typo, a grammar
mistake, a bug in a code example, a missing explanation, and so on.

Steps:

* Submit a new issue in the `GitHub tracker`_;
* *(optional)* Submit a patch.

.. _`fork`:                       https://help.github.com/articles/fork-a-repo
.. _`pull requests`:              https://help.github.com/articles/using-pull-requests
.. _`pip`:                        https://pip.pypa.io/en/stable/installing/
.. _`Sphinx`:                     http://www.sphinx-doc.org/en/stable/
.. _`Github tracker`:             https://github.com/Sylius/Sylius/issues/new
