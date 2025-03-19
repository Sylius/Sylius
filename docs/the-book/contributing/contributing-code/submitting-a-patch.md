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

# Submitting a Patch

Patches are the best way to submit bug fixes or propose enhancements to Sylius. Here’s a step-by-step guide.

## Step 1: Set Up Your Environment

1. **Install Required Software**
   * **Git**
   * **PHP** (version 8.1 or above)
   * **MySQL**
2.  **Configure Git**

    *   Set up your name and email:

        ```bash
        git config --global user.name "Your Name"
        git config --global user.email "you@example.com"
        ```

    **Windows Users**: During Git setup, select the “as-is” option for line endings to avoid issues. You can verify and correct this by running:

    ```bash
    git config core.autocrlf input
    ```
3. **Get the Sylius Source Code**
   * **Fork** the Sylius repository on GitHub.
   *   Clone your fork:

       ```bash
       git clone git@github.com:YOUR_USERNAME/Sylius.git
       ```
   *   Add the main Sylius repository as an upstream remote:

       ```bash
       cd Sylius
       git remote add upstream git://github.com/Sylius/Sylius.git
       ```

{% hint style="info" %}
New to Git? Check out the free [_ProGit_](http://git-scm.com/book) book for guidance.
{% endhint %}

## Step 2: Develop Your Patch

1. **Choose the Base Branch**
   * **1.14** for bug fixes or minor changes.
   * **2.0** for new features.
2.  **Create a Topic Branch**

    *   Start your work on a dedicated branch, based on the chosen branch:

        ```bash
        git switch upstream/2.0 -c feature_branch
        ```

    **Tip**: Use descriptive names for branches (e.g., `issue_123` for a fix related to issue #123).
3. **Develop Your Patch**
   * Follow **BDD** and **coding standards** (check for trailing spaces with `git diff --check`).
   * Keep commits **atomic** and **logically organized**.
   * **Squash** minor fix commits (e.g., typo corrections).
   * Avoid changing coding standards in existing files (submit those as separate patches).
4. **Write Clear Commit Messages**
   * Format: `[Component] Fixed issue ...`
   * Include a **summary** on the first line, followed by details if needed.

## Step 3: Prepare for Submission

1. **Rebase Your Patch**
   *   Before submitting, ensure your branch is up-to-date:

       ```bash
       git checkout feature_branch
       git rebase upstream/2.0  # or upstream/1.14 for bug fixes
       ```
   *   Resolve any conflicts and continue the rebase:

       ```bash
       git add resolved_file
       git rebase --continue
       ```
2. **Push Your Branch**
   *   Push your changes to your fork:

       ```bash
       git push --force-with-lease origin feature_branch
       ```
3. **Create a Pull Request**
   * Open a pull request on the Sylius GitHub repository.
   * **Title**: Include relevant components (e.g., `[Cart] Fixed bug ...`).
   *   **Checklist**: Include the following table in the PR description:

       ```
       | Q               | A
       | --------------- | -----
       | Branch?         | {lowest_bugfix_version} or {future_version}
       | Bug fix?        | no/yes
       | New feature?    | no/yes
       | BC breaks?      | no/yes
       | Deprecations?   | no/yes
       | Related tickets | fixes #X, partially #Y, mentioned in #Z
       | License         | MIT
       ```

{% hint style="info" %}
If your PR is unfinished, add `[WIP]` to the title and list any tasks still in progress.
{% endhint %}

## Step 4: Make Requested Changes

After submitting, you may receive feedback. Here’s how to make adjustments:

1. **Rebase with Your Base Branch**
   *   Rebase instead of merging, and push again:

       ```bash
       git rebase -f upstream/2.0
       git push --force-with-lease origin feature_branch
       ```
2. **Squash Commits**
   *   To squash commits, rebase interactively:

       ```bash
       git rebase -i upstream/2.0
       ```
   *   Replace `pick` with `squash` or `s` for all but the first commit, then save and push:

       ```bash
       git push --force-with-lease origin feature_branch
       ```
