Documentation Format
====================

The Sylius documentation uses `reStructuredText`_ as its markup language and
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
from reStructuredText documents. As such, it adds new directives and
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

The link text will be the main heading of the document linked to. You can
also specify alternative text for the link:

.. code-block:: rst

    :doc:`Simple CRUD </bundles/SyliusResourceBundle/installation>`

Testing Documentation
~~~~~~~~~~~~~~~~~~~~~

To test documentation before a commit:

* Install `Sphinx`_;

* Run the `Sphinx quick setup`_;

* Install the Sphinx extensions (see below);

* Run ``make html`` and view the generated HTML in the ``build`` directory.

Installing the Sphinx extensions
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

* Download the extension from the `source`_ repository

* Copy the ``sensio`` directory to the ``_exts`` folder under your source
  folder (where ``conf.py`` is located)

* Add the following to the ``conf.py`` file:

.. code-block:: py

    # ...
    sys.path.append(os.path.abspath('_exts'))

    # adding PhpLexer
    from sphinx.highlighting import lexers
    from pygments.lexers.web import PhpLexer

    # ...
    # add the extensions to the list of extensions
    extensions = [..., 'sensio.sphinx.refinclude', 'sensio.sphinx.configurationblock', 'sensio.sphinx.phpcode']

    # enable highlighting for PHP code not between ``<?php ... ?>`` by default
    lexers['php'] = PhpLexer(startinline=True)
    lexers['php-annotations'] = PhpLexer(startinline=True)
    lexers['php-standalone'] = PhpLexer(startinline=True)
    lexers['php-symfony'] = PhpLexer(startinline=True)

    # use PHP as the primary domain
    primary_domain = 'php'

    # set URL for API links
    api_url = 'http://api.sylius.org/master/%s'

.. _reStructuredText:        http://docutils.sourceforge.net/rst.html
.. _Sphinx:                  http://sphinx-doc.org/
.. _documents:               https://github.com/Sylius/Sylius-Docs
.. _reStructuredText Primer: http://sphinx-doc.org/rest.html
.. _markup:                  http://sphinx-doc.org/markup/
.. _Pygments website:        http://pygments.org/languages/
.. _source:                  https://github.com/fabpot/sphinx-php
.. _Sphinx quick setup:      http://sphinx-doc.org/tutorial.html#setting-up-the-documentation-sources
