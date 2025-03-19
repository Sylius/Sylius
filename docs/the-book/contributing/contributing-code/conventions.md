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

# Conventions

This document describes conventions used in the Sylius to make it more consistent and predictable. Any new code delivered to Sylius should comply with the following conventions, we also encourage you to use them in your own projects based on Sylius.

### Naming

* Use `camelCase` for:
  * PHP variables,
  * method names (except PHPSpec and PHPUnit),
  * arguments,
  * Twig templates;
* Use `snake_case` for:
  * configuration parameters,&#x20;
  * Twig template variables,&#x20;
  * fixture fields,&#x20;
  * YAML files,&#x20;
  * XML files,&#x20;
  * Behat feature files,&#x20;
  * PHPSpec method names,&#x20;
  * PHPUnit method names;
* Use `SCREAMING_SNAKE_CASE` for:
  * constants,
  * environment variables;
* Use `UpperCamelCase` for:
  * classes names,
  * interfaces names,
  * traits names,
  * other PHP file names;
* Prefix abstract classes with _Abstract_,
* Suffix interfaces with _Interface_,
* Suffix traits with _Trait_,
* Use a command name with the _Handler_ suffix to create a name of the command handler,
* Suffix exceptions with _Exception_,
* Suffix PHPSpec classes with _Spec_,
* Suffix PHPUnit tests with _Test_,
* Prefix Twig templates that are partial blocks with _\__,
* Use the fully qualified class name (FQCN) of an interface as a service name of a newly created service or FQCN of a class if there are multiple implementations of a given interface unless it is inconsistent with the current scope of Sylius.\
