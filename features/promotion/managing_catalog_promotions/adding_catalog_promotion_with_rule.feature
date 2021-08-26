@managing_catalog_promotions
Feature: Adding catalog promotion with a rule
    In order to set up a catalog promotion for chosen part of catalog
    As an Administrator
    I want to be able to set a rule for a catalog promotion

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "T-Shirt" configurable product
        And this product has "PHP T-Shirt" variant priced at "$20.00"
        And this product has "Kotlin T-Shirt" variant priced at "$40.00"
        And this product has "Python T-Shirt" variant priced at "$40.00"
        And I am logged in as an administrator

    @api
    Scenario: Creating catalog promotion for chosen product variants
        When I want to create new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I add the "contains variants" rule configured with "PHP T-Shirt" variant and "Kotlin T-Shirt" variant
        And I add it
        Then there should be 1 new catalog promotion on the list
        And it should have "winter_sale" code and "Winter sale" name
