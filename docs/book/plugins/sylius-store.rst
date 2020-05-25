Sylius Store
============

As the Sylius is an open-source project, it has an awesome community of users and developers.
Therefore our ecosystem flourishes with plugins created outside of our organization. These plugins can be listed in our
`Sylius Store <https://sylius.com/plugins/>`_ or even become officially approved by us when they meet specific requirements.

How to have a Plugin listed on Sylius Store?
--------------------------------------------

Since Sylius is an open-source platform, there is a precise flow for the plugin to become officially adopted by the community.

**1.** Develop the plugin using :doc:`the official Plugin Development guide </book/plugins/creating-plugin>`.

**2.** Remember about the tests and code quality! Check out :ref:`book_plugins_technical_requirements` for more details.

.. warning::

    Beware! Your plugin needs to have at least one tag (even if it's `0.1`). We're not putting plugins in `dev-master` version
    into the Sylius Store.

**3.** Send it to the project maintainers. The preferred way is to use `plugin request submit form <https://store.sylius.com/submit>`_.

**4.** One of our Plugin Curators will contact you with the feedback regarding your plugin's code quality, test suite,
and general feeling. They will also ask you to provide some changes in the code (if needed) to make this plugin visible in the Sylius Store.

**5.** Wait for your Plugin to be featured in `the list of plugins <https://sylius.com/plugins/>`_ on the Sylius website.

.. _book_plugins_technical_requirements:

Technical requirements
----------------------

Below you can find a list of requirements that your plugin needs to fulfill to pass the Sylius Core Team review. Try to follow
them and your plugin's approval process will be faster and more efficient!

Must have
#########

Every plugin must fulfill these requirements to be listed on the Sylius Store. We're happy to accept new extensions to our platform,
but it's also crucial for us to keep their high standards.

**Name of the plugin:**

* Does the name clearly say what kind of feature the plugin provides?
* Does the plugin name contain the vendor's name?
* Are all configuration roots and all classes appropriately named?
* Generally, does the plugin fulfills :ref:`naming conventions <book_plugins_creating_plugin_naming_conventions>`?

**Documentation:**

* Does the plugin contain a description of its features?
* Is there a description of the plugin installation?
* Does the documentation consist of any screenshots (if the plugin provides any visual content)?

**Installation:**

* Is it possible to install the plugin on a fresh Sylius-Standard application with no problems?
* Is every step needed for installation and configuration explained in the documentation? Are there any assumptions that could be confusing for less experienced developers?

**Coding standards:**

* Does the code apply at least `PSR-1 <https://www.php-fig.org/psr/psr-1/>`_?

Should have
###########

If you want your plugin to be officially approved by Sylius Core Team, there are more things to do. Only plugins with the
highest standards of code and properly developed test suite could get the "Approved by Sylius" mark, which makes them more
visible in the community and ensures users about their quality.
When Sylius approve a plugin, you can recognize it also by this badge below in its readme file:

.. image:: ../../_images/approved_plugin.png
    :scale: 30%

|

**Coding standards:**

* Does the code follow SOLID principles?
* Are conventions in the code consistent with each other (are the same conventions used for the same concepts)?
* Does the code apply some other `PSR's <https://www.php-fig.org/psr/>`_?

**Tests:**

* Are there any unit tests for the plugin's classes? They can be written in PHPSpec, PHPUnit or any other working and reliable unit testing library
* Does the unit tests cover at least the most crucial classes in the plugin (those which contain important business logic)?
* Does the plugin include some functional/acceptance tests (written in Behat/PHPUnit or similar tool)?
* Are the core features of the plugin described and tests by them?
* Do the functional/acceptance tests describe most of the application business-related features?

**Continuous integration:**

* Is there any CI tool used in the plugin's repository (CircleCI, Travis CI, Jenkins)?
