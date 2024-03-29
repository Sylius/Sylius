Submitting a Patch
==================

Patches are the best way to provide a bug fix or to propose enhancements to
Sylius.

Step 1: Setup your Environment
------------------------------

Install the Software Stack
~~~~~~~~~~~~~~~~~~~~~~~~~~

Before working on Sylius, set a Symfony friendly environment up with the following
software:

* Git
* PHP version 8.1 or above
* MySQL

Configure Git
~~~~~~~~~~~~~

Set your user information up with your real name and a working email address:

.. code-block:: bash

    git config --global user.name "Your Name"
    git config --global user.email "you@example.com"

.. tip::

    If you are new to Git, you are highly recommended to read the excellent and
    free `ProGit`_ book.

.. tip::

    If your IDE creates configuration files inside the directory of the project,
    you can use global ``.gitignore`` file (for all projects) or
    ``.git/info/exclude`` file (per project) to ignore them. See
    `Github's documentation`_.

.. tip::

    Windows users: when installing Git, the installer will ask what to do with
    line endings, and will suggest replacing all LF with CRLF. This is the wrong
    setting if you wish to contribute to Sylius. Selecting the as-is method is
    your best choice, as Git will convert your line feeds to the ones in the
    repository. If you have already installed Git, you can check the value of
    this setting by typing:

    .. code-block:: bash

        git config core.autocrlf

    This will return either "false", "input" or "true"; "true" and "false" being
    the wrong values. Change it to "input" by typing:

    .. code-block:: bash

        git config --global core.autocrlf input

    Replace --global by --local if you want to set it only for the active
    repository

Get the Sylius Source Code
~~~~~~~~~~~~~~~~~~~~~~~~~~

Get the Sylius source code:

* Create a `GitHub`_ account and sign in;

* Fork the `Sylius repository`_ (click on the "Fork" button);

* After the "forking action" has completed, clone your fork locally
  (this will create a ``Sylius`` directory):

.. code-block:: bash

      git clone git@github.com:USERNAME/Sylius.git

* Add the upstream repository as a remote:

.. code-block:: bash

      cd sylius
      git remote add upstream git://github.com/Sylius/Sylius.git

Step 2: Work on your Patch
--------------------------

The License
~~~~~~~~~~~

Before you start, you must know that all patches you are going to submit
must be released under the *MIT license*, unless explicitly specified in your
commits.

Choose the right Base Branch
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Before starting to work on a patch, you must determine on which branch you need to work. It will be:

* ``{lowest_bugfix_version}``, if you are fixing a bug for an existing feature or want to make a change that falls into the list of acceptable changes in patch versions
* ``{future_version}``, if you are adding a new feature.

.. note::

    All bug fixes merged into the ``{lowest_bugfix_version}`` maintenance branch are also merged into ``{future_version}`` on a regular basis.

Create a Topic Branch
~~~~~~~~~~~~~~~~~~~~~

Each time you want to work on a patch for a bug or on an enhancement, create a
topic branch, starting from the previously chosen base branch:

.. code-block:: bash

    git switch upstream/{future_version} -c BRANCH_NAME

.. tip::

    Use a descriptive name for your branch (``issue_XXX`` where ``XXX`` is the
    GitHub issue number is a good convention for bug fixes).

The above checkout command automatically switches the code to the newly created
branch (check the branch you are working on with ``git branch``).

Work on your Patch
~~~~~~~~~~~~~~~~~~

Work on the code as much as you want and commit as much as you want; but keep
in mind the following:

* Practice :doc:`BDD </bdd/index>`, which is the development methodology we use at Sylius;

* Follow :doc:`coding standards <standards>` (use ``git diff --check`` to check for
  trailing spaces -- also read the tip below);

* Do atomic and logically separate commits (use the power of ``git rebase`` to
  have a clean and logical history);

* Squash irrelevant commits that are just about fixing coding standards or
  fixing typos in your own code;

* Never fix coding standards in some existing code as it makes the code review
  more difficult (submit CS fixes as a separate patch);

* In addition to this "code" pull request, you must also update the documentation when appropriate.
  See more in :doc:`contributing documentation </book/contributing/documentation/overview>` section.

* Write good commit messages (see the tip below).

.. tip::

    A good commit message is composed of a summary (the first line),
    optionally followed by a blank line and a more detailed description. The
    summary should start with the Component you are working on in square
    brackets (``[Cart]``, ``[Taxation]``, ...). Use a
    verb (``fixed ...``, ``added ...``, ...) to start the summary and **don't
    add a period at the end**.

Prepare your Patch for Submission
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

When your patch is not about a bug fix (when you add a new feature or change
an existing one for instance), it must also include the following:

* An explanation of the changes in the relevant ``CHANGELOG`` file(s) (the
  ``[BC BREAK]`` or the ``[DEPRECATION]`` prefix must be used when relevant);

* An explanation on how to upgrade an existing application in the relevant
  ``UPGRADE`` file(s) if the changes break backward compatibility or if you
  deprecate something that will ultimately break backward compatibility.

Step 3: Submit your Patch
-------------------------

Whenever you feel that your patch is ready for submission, follow the
following steps.

Rebase your Patch
~~~~~~~~~~~~~~~~~

Before submitting your patch, update your branch (needed if it takes you a
while to finish your changes):

If you are basing on the ``{future_version}`` branch:

.. code-block:: bash

    git checkout BRANCH_NAME # to make sure you're on the right branch
    git rebase upstream/{future_version}

If you are basing on the ``{lowest_bugfix_version}`` branch:

.. code-block:: bash

    git checkout BRANCH_NAME # to make sure you're on the right branch
    git rebase upstream/{lowest_bugfix_version}

When doing the ``rebase`` command, you might have to fix merge conflicts.
``git status`` will show you the *unmerged* files. Resolve all the conflicts,
then continue the rebase:

.. code-block:: bash

    git add ... # add resolved files
    git rebase --continue

Push your branch remotely:

.. code-block:: bash

    git push --force-with-lease origin BRANCH_NAME

Make a Pull Request
~~~~~~~~~~~~~~~~~~~

.. warning::

    Please remember that bug fixes must be submitted against the ``{lowest_bugfix_version}`` branch,
    but features and deprecations against the ``{future_version}`` branch. Just accordingly to which branch you chose as the base branch before.

You can now make a pull request on the ``Sylius/Sylius`` GitHub repository.

To ease the core team work, always include the modified components in your
pull request message, like in:

.. code-block:: text

    [Cart] Fixed something
    [Taxation] [Addressing] Added something

The pull request description must include the following checklist at the top
to ensure that contributions may be reviewed without needless feedback
loops and that your contributions can be included into Sylius as quickly as
possible:

.. code-block:: text

    | Q               | A
    | --------------- | -----
    | Branch?         | {lowest_bugfix_version} or {future_version}
    | Bug fix?        | no/yes
    | New feature?    | no/yes
    | BC breaks?      | no/yes
    | Deprecations?   | no/yes
    | Related tickets | fixes #X, partially #Y, mentioned in #Z
    | License         | MIT

An example submission could now look as follows:

.. code-block:: text

    | Q               | A
    | --------------- | -----
    | Branch?         | {lowest_bugfix_version}
    | Bug fix?        | yes
    | New feature?    | no
    | BC breaks?      | no
    | Deprecations?   | no
    | Related tickets | fixes #12
    | License         | MIT

The whole table must be included (do **not** remove lines that you think are
not relevant).

Some answers to the questions trigger some more requirements:

 * If you answer yes to "Bug fix?", check if the bug is already listed in the
   Sylius issues and reference it/them in "Related tickets";

 * If you answer yes to "New feature?", you should submit a pull request to the
   documentation;

 * If you answer yes to "BC breaks?", the patch must contain updates to the
   relevant ``CHANGELOG`` and ``UPGRADE`` files;

 * If you answer yes to "Deprecations?", the patch must contain updates to the
   relevant ``CHANGELOG`` and ``UPGRADE`` files;

If some of the previous requirements are not met, create a todo-list and add
relevant items:

.. code-block:: text

    - [ ] Fix the specs as they have not been updated yet
    - [ ] Submit changes to the documentation
    - [ ] Document the BC breaks

If the code is not finished yet because you don't have time to finish it or
because you want early feedback on your work, add an item to todo-list:

.. code-block:: text

    - [ ] Finish the feature
    - [ ] Gather feedback for my changes

As long as you have items in the todo-list, please prefix the pull request
title with "[WIP]".

In the pull request description, give as much details as possible about your
changes (don't hesitate to give code examples to illustrate your points). If
your pull request is about adding a new feature or modifying an existing one,
explain the rationale for the changes. The pull request description helps the
code review.

Rework your Patch
~~~~~~~~~~~~~~~~~

Based on the feedback on the pull request, you might need to rework your
patch. Before re-submitting the patch, rebase with your base branch (``{future_version}`` or ``{lowest_bugfix_version}``), don't merge; and force the push to the origin:

.. code-block:: bash

    git rebase -f upstream/{future_version}
    git push --force-with-lease origin BRANCH_NAME

or

.. code-block:: bash

    git rebase -f upstream/{lowest_bugfix_version}
    git push --force-with-lease origin BRANCH_NAME

.. note::

    When doing a ``push --force-with-lease``, always specify the branch name explicitly
    to avoid messing other branches in the repo (``--force-with-lease`` tells Git that
    you really want to mess with things so do it carefully).

Often, Sylius team members will ask you to "squash" your commits. This means you will
convert many commits to one commit. To do this, use the rebase command:

.. code-block:: bash

    git rebase -i upstream/{future_version}
    git push --force-with-lease origin BRANCH_NAME

or

.. code-block:: bash

    git rebase -i upstream/{lowest_bugfix_version}
    git push --force-with-lease origin BRANCH_NAME

After you type this command, an editor will popup showing a list of commits:

.. code-block:: text

    pick 1a31be6 first commit
    pick 7fc64b4 second commit
    pick 7d33018 third commit

To squash all commits into the first one, remove the word ``pick`` before the
second and the last commits, and replace it by the word ``squash`` or just
``s``. When you save, Git will start rebasing, and if successful, will ask
you to edit the commit message, which by default is a listing of the commit
messages of all the commits. When you are finished, execute the push command.

.. _ProGit:                                http://git-scm.com/book
.. _GitHub:                                https://github.com/signup/free
.. _`GitHub's Documentation`:              https://help.github.com/articles/ignoring-files
.. _`Sylius repository`:                   https://github.com/Sylius/Sylius
.. _travis-ci.org:                         https://travis-ci.org/
.. _`travis-ci.org status icon`:           http://about.travis-ci.org/docs/user/status-images/
.. _`travis-ci.org Getting Started Guide`: http://about.travis-ci.org/docs/user/getting-started/
.. _`documentation repository`:            https://github.com/Sylius/Sylius-Docs
.. _`PSR-1`:                               http://www.php-fig.org/psr/psr-1/
.. _`PSR-2`:                               http://www.php-fig.org/psr/psr-2/
