# Transition from PHPSpec to PHPUnit

* Status: Accepted
* Date: 2025-04-07

## Context and Problem Statement

Sylius has historically used PHPSpec for unit testing, following a behavior-driven development (BDD) approach. However, 
maintaining PHPSpec alongside PHPUnit (which is already used for integration and functional tests) has introduced challenges 
in terms of maintainability, tooling, and onboarding new contributors.

To ensure long-term sustainability and align with modern PHP development practices, we need to evaluate whether 
continuing with PHPSpec is beneficial or if a transition to PHPUnit for unit testing is a better approach.

## Decision Drivers

* **Consistency** – how well each approach integrates with the existing Sylius testing strategy.

* **Maintainability** – the long-term viability of each testing framework and the effort required to support it.

* **Developer Experience** – the learning curve, IDE support, and available tooling for each approach.

* **Flexibility** – the ability to write and structure tests in a way that supports the evolving architecture of Sylius.

* **Community and Ecosystem** – the adoption, support, and future development of each tool within the broader PHP ecosystem.

## Considered Options

### Option 1: Keep using PHPSpec for unit tests

* **Good**, because maintains a strict BDD approach, enforcing specification-driven design.
* **Good**, because no need for immediate refactoring of existing tests.
* **Bad**, because PHPSpec adoption has been declining, making long-term support uncertain.
* **Bad**, because new contributors unfamiliar with PHPSpec face a steeper learning curve.
* **Bad**, because requires maintaining two separate testing frameworks, increasing complexity.

### Option 2: Migrate unit tests from PHPSpec to PHPUnit

* **Good**, because aligns Sylius with the broader PHP ecosystem, making contribution easier.
* **Good**, because reduces tooling complexity by removing a niche framework.
* **Good**, because provides more flexibility in writing unit tests.
* **Good**, because of better support from IDEs, static analysis tools, and CI/CD pipelines.
* **Good**, because is directory agnostic, meaning tests can be structured more freely without being tightly coupled 
  to a specific directory structure.
* **Bad**, because requires refactoring existing PHPSpec tests to PHPUnit.
* **Bad**, because developers accustomed to PHPSpec need to adapt to a different testing approach.

### Option 3: Use a hybrid approach (PHPSpec for unit tests, PHPUnit for integration and functional tests)

* **Good**, because retains the benefits of PHPSpec for specification-driven development.
* **Good**, because avoids immediate refactoring of all unit tests.
* **Bad**, because maintains complexity by requiring knowledge of two different testing frameworks.
* **Bad**, because does not resolve the long-term viability concerns of PHPSpec.

## Decision Outcome

**Chosen option**: **Option 2: Migrate unit tests from PHPSpec to PHPUnit**, because it simplifies our testing strategy, 
improves maintainability, and ensures Sylius remains aligned with the PHP ecosystem.
