# Feature Coverage in API

* Status: accepted
* Date: 2020-03-03

## Context and Problem Statement

We want our API to cover all the functionalities that are currently implemented in the UI.

## Decision Drivers

* All the functionalities implemented for API should be tested
* Tracking whether a feature has been covered in the API or not should be easy

## Considered Options

### Using Behat for the feature coverage

Behat allows us to run the same Gherkin scenarios within one or more context. Currently, most of our scenarios are
run within UI context (tagged with `@ui`). Adding `@api` tag to those allows to run the scenarios within API context. 

* Good, because we can track the coverage easily by comparing scenarios tagged with `@ui` and `@api`
* Bad, because we don't have any tooling to test API within Behat ecosystem

### Using PHPUnit for the feature coverage

API Platform recommends to use PHPUnit in order to cover the functionalities with tests.

* Good, because there is already a recommended tool to test the API within PHPUnit
* Bad, because it is not easy to track features covered with API compared with Behat
* Bad, because it creates another system to test business features

## Decision Outcome

Chosen *Using Behat for the feature coverage*, because it's the only option, that meets all the decision drivers criteria.

We will gradually add `@api` tag to the scenarios currently tagged with `@ui` and then implement the API contexts.
As a consequence, we will have to create a testing tool to use it in Behat contexts.

## References

* The initial implementation and references to further PRs with improvements: [[API] Product options (with values) creation and index](https://github.com/Sylius/Sylius/pull/11136)
