Coding Standards
================

When contributing code to Sylius, you must follow its coding standards.

Sylius follows the standards defined in the `PSR-0`_, `PSR-1`_ and `PSR-2`_
documents.

Here is a short example containing most features described below:

.. code-block:: html+php

    <?php

    /*
     * This file is part of the Sylius package.
     *
     * (c) Paweł Jędrzejewski
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code.
     */

    namespace Acme;

    /**
     * Coding standards demonstration.
     */
    class FooBar
    {
        const SOME_CONST = 42;

        private $fooBar;

        /**
         * @param string $dummy Some argument description
         */
        public function __construct($dummy)
        {
            $this->fooBar = $this->transformText($dummy);
        }

        /**
         * @param string $dummy Some argument description
         * @param array  $options
         *
         * @return string|null Transformed input
         *
         * @throws \RuntimeException
         */
        private function transformText($dummy, array $options = array())
        {
            $mergedOptions = array_merge(
                array(
                    'some_default'    => 'values',
                    'another_default' => 'more values',
                ),
                $options
            );

            if (true === $dummy) {
                return;
            }

            if ('string' === $dummy) {
                if ('values' === $mergedOptions['some_default']) {
                    return substr($dummy, 0, 5);
                }

                return ucwords($dummy);
            }

            throw new \RuntimeException(sprintf('Unrecognized dummy option "%s"', $dummy));
        }
    }

Structure
---------

* Add a single space after each comma delimiter;

* Add a single space around operators (``===``, ``&&``, ...);

* Add a comma after each array item in a multi-line array, even after the
  last one;

* Add a blank line before ``return`` statements, unless the return is alone
  inside a statement-group (like an ``if`` statement);

* Use braces to indicate control structure body regardless of the number of
  statements it contains;

* Define one class per file - this does not apply to private helper classes
  that are not intended to be instantiated from the outside and thus are not
  concerned by the `PSR-0`_ standard;

* Declare class properties before methods;

* Declare public methods first, then protected ones and finally private ones;

* Use parentheses when instantiating classes regardless of the number of
  arguments the constructor has;

* Exception message strings should be concatenated using ``sprintf``.

Naming Conventions
------------------

* Use camelCase, not underscores, for variable, function and method
  names, arguments;

* Use underscores for option names and parameter names;

* Use namespaces for all classes;

* Prefix abstract classes with ``Abstract``.

* Suffix interfaces with ``Interface``;

* Suffix traits with ``Trait``;

* Suffix exceptions with ``Exception``;

* Use alphanumeric characters and underscores for file names;

* Don't forget to look at the more verbose :doc:`conventions` document for
  more subjective naming considerations.

.. _service-naming-conventions:

Service Naming Conventions
~~~~~~~~~~~~~~~~~~~~~~~~~~

* A service name contains groups, separated by dots;
* All Sylius services use ``sylius`` as first group;
* Use lowercase letters for service and parameter names;
* A group name uses the underscore notation;
* Each service has a corresponding parameter containing the class name,
  following the ``service_name.class`` convention.

Documentation
-------------

* Add PHPDoc blocks for all classes, methods, and functions;

* Omit the ``@return`` tag if the method does not return anything;

* The ``@package`` and ``@subpackage`` annotations are not used.

License
-------

* Sylius is released under the MIT license, and the license block has to be
  present at the top of every PHP file, before the namespace.

.. _`PSR-0`: http://www.php-fig.org/psr/psr-0/
.. _`PSR-1`: http://www.php-fig.org/psr/psr-1/
.. _`PSR-2`: http://www.php-fig.org/psr/psr-2/
