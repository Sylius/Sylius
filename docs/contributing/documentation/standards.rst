Documentation Standards
=======================

In order to help the reader as much as possible and to create code examples that
look and feel familiar, you should follow these standards.

Sphinx
------

* The following characters are chosen for different heading levels: level 1
  is ``=``, level 2 ``-``, level 3 ``~``, level 4 ``.`` and level 5 ``"``;
* Each line should break approximately after the first word that crosses the
  72nd character (so most lines end up being 72-78 characters);
* The ``::`` shorthand is *preferred* over ``.. code-block:: php`` to begin a PHP
  code block (read `the Sphinx documentation`_ to see when you should use the
  shorthand);
* Inline hyperlinks are **not** used. Separate the link and their target
  definition, which you add on the bottom of the page;
* Inline markup should be closed on the same line as the open-string;

Example
~~~~~~~

.. code-block:: text

    Example
    =======

    When you are working on the docs, you should follow the
    `Sylius Documentation`_ standards.

    Level 2
    -------

    A PHP example would be::

        echo 'Hello World';

    Level 3
    ~~~~~~~

    .. code-block:: php

        echo 'You cannot use the :: shortcut here';

    .. _`Sylius Documentation`: http://docs.sylius.org/en/latest/contributing/documentation/standards.html

Code Examples
-------------

* The code follows the :doc:`Sylius Coding Standards </contributing/code/standards>`
  as well as the `Twig Coding Standards`_;
* To avoid horizontal scrolling on code blocks, we prefer to break a line
  correctly if it crosses the 85th character, which in many IDEs is signalised by a vertical line;
* When you fold one or more lines of code, place ``...`` in a comment at the point
  of the fold. These comments are:

.. code-block:: text

      // ... (php),
      # ... (yaml/bash),
      {# ... #} (twig)
      <!-- ... --> (xml/html),
      ; ... (ini),
      ... (text)

* When you fold a part of a line, e.g. a variable value, put ``...`` (without comment)
  at the place of the fold;
* Description of the folded code: (optional)
  If you fold several lines: the description of the fold can be placed after the ``...``
  If you fold only part of a line: the description can be placed before the line;
* If useful to the reader, a PHP code example should start with the namespace
  declaration;
* When referencing classes, be sure to show the ``use`` statements at the
  top of your code block. You don't need to show *all* ``use`` statements
  in every example, just show what is actually being used in the code block;
* If useful, a ``codeblock`` should begin with a comment containing the filename
  of the file in the code block. Don't place a blank line after this comment,
  unless the next line is also a comment;
* You should put a ``$`` in front of every bash line.

Formats
~~~~~~~

Configuration examples should show recommended formats using
:ref:`configuration blocks <docs-configuration-blocks>`. The recommended formats
(and their orders) are:

* **Configuration** (including services and routing): YAML
* **Validation**: XML
* **Doctrine Mapping**: XML

Example
~~~~~~~

.. code-block:: php

    // src/Foo/Bar.php
    namespace Foo;

    use Acme\Demo\Cat;
    // ...

    class Bar
    {
        // ...

        public function foo($bar)
        {
            // set foo with a value of bar
            $foo = ...;

            $cat = new Cat($foo);

            // ... check if $bar has the correct value

            return $cat->baz($bar, ...);
        }
    }

.. caution::

    In YAML you should put a space after ``{`` and before ``}`` (e.g. ``{ _controller: ... }``),
    but this should not be done in Twig (e.g.  ``{'hello' : 'value'}``).

Language Standards
------------------

* For sections, use the following capitalization rules:
  `Capitalization of the first word, and all other words, except for closed-class words`_:

    The Vitamins are in my Fresh California Raisins

* Do not use `Serial (Oxford) Commas`_;
* You should use a form of *you* instead of *we* (i.e. avoid the first person
  point of view: use the second instead);
* When referencing a hypothetical person, such as "a user with a session cookie", gender-neutral
  pronouns (they/their/them) should be used. For example, instead of:

     * he or she, use they
     * him or her, use them
     * his or her, use their
     * his or hers, use theirs
     * himself or herself, use themselves

.. _`the Sphinx documentation`: http://sphinx-doc.org/rest.html#source-code
.. _`Twig Coding Standards`: http://twig.sensiolabs.org/doc/coding_standards.html
.. _`Capitalization of the first word, and all other words, except for closed-class words`: http://en.wikipedia.org/wiki/Letter_case#Headings_and_publication_titles
.. _`Serial (Oxford) Commas`: http://en.wikipedia.org/wiki/Serial_comma
