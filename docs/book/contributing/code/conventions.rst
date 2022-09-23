Conventions
===========

This document describes conventions used in the Sylius to make it more consistent and predictable. Any new code
delivered to Sylius should comply with the following conventions, we also encourage you to use them in your own projects
based on Sylius.

Naming
------

* Use ``camelCase`` for:

    * PHP variables,
    * method names (except PHPSpec and PHPUnit),
    * arguments,
    * Twig templates;

* Use ``snake_case`` for:

    * configuration parameters,
    * Twig template variables,
    * fixture fields,
    * YAML files,
    * XML files,
    * Behat feature files,
    * PHPSpec method names,
    * PHPUnit method names;

* Use ``SCREAMING_SNAKE_CASE`` for:

    * constants,
    * environment variables;

* Use ``UpperCamelCase`` for:

    * classes names,
    * interfaces names,
    * traits names,
    * other PHP files names;

* Prefix abstract classes with `Abstract`,
* Suffix interfaces with  `Interface`,
* Suffix traits with `Trait`,
* Use a command name  with the `Handler` suffix to create a name of command handler,
* Suffix exceptions with `Exception`,
* Suffix PHPSpec classes with `Spec`,
* Suffix PHPUnit tests with `Test`,
* Prefix Twig templates that are partial blocks with `_`,
* Use fully qualified class name (FQCN) of an interface as a service name of newly created service or FQCN of class
  if there are multiple implementations of a given interface unless it is inconsistent with the current scope of Sylius.

Template Events
---------------

* Priorities of blocks should be increased by a step of 10.
* A block between two existing blocks should use the priority that is in the middle point between the two blocks. For
  example if you want to add a block between two blocks with priorities 10 and 20, you should use 15 as a priority.
* If you want to add a block at the beginning of a template, you should use a priority of 5.
