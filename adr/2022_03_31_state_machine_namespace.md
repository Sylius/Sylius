# [short title of solved problem and solution]

* Status: proposed
* Date: 2022-03-31

## Context and Problem Statement

During the implementation of Headless Checkout [Pull Request](https://github.com/Sylius/Sylius/pull/13793) we encountered
discussion whether newly created states related classes should be moved from CoreBundle to ApiBundle. The classes
were created there at first, because there are other Checkout State classes. This situation showed us that classes like
`Sylius\Component\Core\OrderCheckoutStates` and State Machine are more related to the UI than to the business logic.

## Decision Drivers

* Purely architectural

## Considered Options

### Option 1 - State related classes in API and UI Bundle

Headless Checkout classes will be stored in ApiBundle and current Order Checkout classes will be stored in UiBundle.

* Good, because we will stop treat states/presentation related classes as core
* Bad, because we have to move Order Checkout classes that probably will create BC breaks

### Option 2 - State related classes in CoreBundle

Both Headless and Order Checkout classes will be stored in CoreBundle.

* Good, because all checkout classes will be close to each other
* Good, we avoid BC breaks
* Bad, because presentation driven classes will be stored in CoreBundle

## Decision Outcome

Chosen option: Option 1 - State related classes in API and UI Bundle

Short term perspective says that the Option 2 is more suitable, yet long term perspective shows that with Sylius 2.0 and
maybe later modularization the UI Bundle will be deprecated, therefore all classes related to current order checkout in Core Bundle
will be deprecated too.
