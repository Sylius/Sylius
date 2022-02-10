# Catalog promotions validation

* Status: accepted
* Date: 2022-02-09

## Context and Problem Statement

Catalog Promotions, as the new feature in Sylius 1.11, is developed in API-first manner, but of course provides
UI functionality as well. The challenge, that we faced during the implementation was the validation, and either it should
be unified for API and UI or separated.

## Decision Drivers

Catalog Promotions' validation should:
* be easy to customize and extend
* work on both API and UI
* validate both business scenarios and syntactical correctness

## Considered Options

### Unified validation in custom validators

This is the first approach to validation we had. The concept was, to have
[custom validator for each action and scope](https://github.com/Sylius/Sylius/blob/b5458fa31aadaf699e39b0cc106d5efd25144823/src/Sylius/Bundle/CoreBundle/Validator/CatalogPromotionAction/FixedDiscountActionValidator.php).
The correct validation is chosen based on the key provided in `sylius.catalog_promotion.action_validator` tag. These custom
validators should contain both business and syntactical validation (as the latter is not configured anywhere else).

* Good, because custom validators are unified for API and UI, which results in the lack of duplications
* Bad, because business and syntactical validation is mixed, even though these are different type of validations
* Bad, because it resulted in some problems in validation messages display (Catalog Promotions' forms are quite complicated)

### Unified business validation and separated syntactical validation

Variation of the previous option, which still uses custom validators but only for business (or functional) validation.
Syntactical validation is delivered separately for UI and API.

* Good, because syntactical and business validation is separated, as it should be
* Good, because UI validation on forms is less problematic still being extendable and customizable
* Semi-bad, because it results in _apparent_ duplication of logic
* Bad, because there is no easy way to syntactically validate requests in ApiPlatform, so it needs to be done in validators anyway

## Decision Outcome

Chosen option: **"Unified business validation and separated syntactical validation"**

Syntactical and functional validation are two different types of validation. They should be separated, especially as they
can be approached differently (from the technical point of view) regarding the interface used (UI or API). Moreover, this
separation gives us better control over how requests/forms are validated (e.g. some values passed in forms does not have
to be validated in Sylius, as they're already protected in used Symfony types). Output of this decisions results in following
structure of validators:

* `src/Sylius/Bundle/PromotionBundle/Validator/CatalogPromotionAction` - base business validation of catalog promotion actions
not related to the concepts from other bundles (currently empty, only interface)
* `src/Sylius/Bundle/CoreBundle/Validator/CatalogPromotionAction` - business validation of catalog promotion actions related
to the concepts from other bundles (e.g. product variants)
* `src/Sylius/Bundle/CoreBundle/Validator/CatalogPromotionScope` - business validation of catalog promotion scopes related
to the concepts from other bundles (e.g. taxons)
* `src/Sylius/Bundle/ApiBundle/Validator/CatalogPromotion` - syntactical validation of API requests; validators here usually
decorate validators from the **CoreBundle**

Syntactical validation of forms data shall be done on each specific form (e.g. those from `src/Sylius/Bundle/CoreBundle/Form/Type/CatalogPromotionScope`).

## References

* [1st approach](https://github.com/Sylius/Sylius/pull/13174)
* [Chosen approach](https://github.com/Sylius/Sylius/pull/13620)
