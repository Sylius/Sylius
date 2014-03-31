Git
===

This document explains some conventions and specificities in the way we manage
the Sylius code with Git.

Pull Requests
-------------

Whenever a pull request is merged, all the information contained in the pull
request is saved in the repository.

You can easily spot pull request merges as the commit message always follows
this pattern:

.. code-block:: text

    merged branch USER_NAME/BRANCH_NAME (PR #1111)

The PR reference allows you to have a look at the original pull request on
GitHub: https://github.com/Sylius/Sylius/pull/1111.
Often, this can help understand what the changes were about and the
reasoning behind the changes.
