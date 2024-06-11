@managing_catalog_promotions
Feature: Filtering product variants
    In order to quickly find product variants
    As an Administrator
    I want to be able to filter product variants in the list

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "Clothes"
        And the store has a "T-Shirt" configurable product
        And this product belongs to "Clothes"
        And this product has "PHP T-Shirt" variant priced at "$20.00"
        And this product has "Sylius T-Shirt" variant priced at "$15.00"
        And there is a catalog promotion "T-Shirt promotion" that reduces price by "20%" and applies on "T-Shirt" product
        And I am logged in as an administrator

    @ui @todo-api
    Scenario: Filtering product variants by code
        Given I am browsing variants affected by catalog promotion "T-Shirt promotion"
        When I filter by code containing "PHP"
        Then there should be 1 product variant on the list
        And it should be the "PHP T-Shirt" product variant

    @ui @todo-api
    Scenario: Filtering product variants by name
        Given I am browsing variants affected by catalog promotion "T-Shirt promotion"
        When I filter by name containing "Sylius"
        Then there should be 1 product variant on the list
        And it should be the "Sylius T-Shirt" product variant
