@managing_product_variants
Feature: Generating product variant generation
    In order to avoid making mistakes when generating variants
    As an Administrator
    I want to be prevented from generating it without specifying required fields

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And this product has option "Taste" with values "Orange" and "Melon"
        And I am logged in as an administrator

    @ui
    Scenario: Generating a product variant without price
        Given I want to generate new variants for this product
        When I specify that the 1st variant is identified by "WYBOROWA_ORANGE" code
        And I try to generate it
        Then I should be notified that price is required for the 1st variant
        And I should see 0 variants in the list

    @ui
    Scenario: Generating a product variant without code
        Given I want to generate new variants for this product
        When I specify that the 1st variant costs "$90"
        And I try to generate it
        Then I should be notified that code is required for the 1st variant
        And I should see 0 variants in the list
