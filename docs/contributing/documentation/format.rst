Documentation Format
====================

The Sylius documentation uses the `reStructuredText`_ as its markup language and
`Sphinx`_ for building the output (HTML, PDF, ...).

reStructuredText
----------------

reStructuredText "is an easy-to-read, what-you-see-is-what-you-get plaintext
markup syntax and parser system".

You can learn more about its syntax by reading existing Sylius `documents`_
or by reading the `reStructuredText Primer`_ on the Sphinx website.

If you are familiar with Markdown, be careful as things are sometimes very
similar but different:

* Lists starts at the beginning of a line (no indentation is allowed);
* Inline code blocks use double-ticks (````like this````).

Sphinx
------

Sphinx is a build system that adds some nice tools to create documentation
from the reStructuredText documents. As such, it adds new directives and
interpreted text roles to standard reST `markup`_.

Syntax Highlighting
~~~~~~~~~~~~~~~~~~~

All code examples uses PHP as the default highlighted language. You can change
it with the ``code-block`` directive:

.. code-block:: rst

    .. code-block:: yaml

        { foo: bar, bar: { foo: bar, bar: baz } }

If your PHP code begins with ``<?php``, then you need to use ``html+php`` as
the highlighted pseudo-language:

.. code-block:: rst

    .. code-block:: html+php

        <?php echo $this->foobar(); ?>

.. note::

    A list of supported languages is available on the `Pygments website`_.

.. _docs-configuration-blocks:

Configuration Blocks
~~~~~~~~~~~~~~~~~~~~

Whenever you show a configuration, you must use the ``configuration-block``
directive to show the configuration in all supported configuration formats
(``PHP``, ``YAML``, and ``XML``)

.. code-block:: rst

    .. configuration-block::

        .. code-block:: yaml

            # Configuration in YAML

        .. code-block:: xml

            <!-- Configuration in XML //-->

        .. code-block:: php

            // Configuration in PHP

The previous reST snippet renders as follow:

.. configuration-block::

    .. code-block:: yaml

        # Configuration in YAML

    .. code-block:: xml

        <!-- Configuration in XML //-->

    .. code-block:: php

        // Configuration in PHP

The current list of supported formats are the following:

+-----------------+-------------+
| Markup format   | Displayed   |
+=================+=============+
| html            | HTML        |
+-----------------+-------------+
| xml             | XML         |
+-----------------+-------------+
| php             | PHP         |
+-----------------+-------------+
| yaml            | YAML        |
+-----------------+-------------+
| json            | JSON        |
+-----------------+-------------+
| jinja           | Twig        |
+-----------------+-------------+
| html+jinja      | Twig        |
+-----------------+-------------+
| html+php        | PHP         |
+-----------------+-------------+
| ini             | INI         |
+-----------------+-------------+
| php-annotations | Annotations |
+-----------------+-------------+

Adding Links
~~~~~~~~~~~~

To add links to other pages in the documents use the following syntax:

.. code-block:: rst

    :doc:`/path/to/page`

Using the path and filename of the page without the extension, for example:

.. code-block:: rst

    :doc:`/book/architecture`

    :doc:`/bundles/SyliusAddressingBundle/installation`

The link's text will be the main heading of the document linked to. You can
also specify an alternative text for the link:

.. code-block:: rst

    :doc:`Simple CRUD </bundles/SyliusResourceBundle/installation>`

You can also link to pages outside of the documentation, for instance to the `Github`_.

.. code-block:: rst

    `Github`_ //it is an intext link.


At the bottom of the document in which you are using your link add a reference:

.. code-block:: rst

    .. _`Github`: http://www.github.com // with a url to your desired destination.

.. _`documents`:               https://github.com/Sylius/Sylius/tree/master/docs
.. _`reStructuredText Primer`: http://www.sphinx-doc.org/en/stable/rest.html
.. _`markup`:                  http://www.sphinx-doc.org/en/stable/markup/
.. _`Pygments website`:                  http://pygments.org/languages/
.. _`Github`:                  http://www.github.com
