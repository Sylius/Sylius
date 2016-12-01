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
        Then I should be notified that prices in all channels must be defined for the 1st variant
        And I should see 0 variants in the list

    @ui
    Scenario: Generating a product variant without code
        Given I want to generate new variants for this product
        When I specify that the 1st variant costs "$90" in "United States" channel
        And I try to generate it
        Then I should be notified that code is required for the 1st variant
        And I should see 0 variants in the list

    @ui
    Scenario: Generation a product variants without specific required fields for second variant
        Given I want to generate new variants for this product
        When I specify that the 1st variant is identified by "WYBOROWA_ORANGE" code
        And I specify that the 1st variant costs "$90" in "United States" channel
        And I try to generate it
        Then I should be notified that code is required for the 2st variant
        Then I should be notified that prices in all channels must be defined for the 2st variant
        And I should see 0 variants in the list

    @ui
    Scenario: Generation a product variants with the same code
        Given I want to generate new variants for this product
        When I specify that the 1st variant is identified by "WYBOROWA_TASTE" code
        And I specify that the 1st variant costs "$90" in "United States" channel
        And I specify that the 2st variant is identified by "WYBOROWA_TASTE" code
        And I specify that the 2st variant costs "$90" in "United States" channel
        And I try to generate it
        Then I should be notified that variant code must be unique within this product for the 1st variant
        And I should be notified that variant code must be unique within this product for the 2st variant
        And I should see 0 variants in the list

    @ui
    Scenario: Generation a product variants without specific required fields for second variant
        Given I want to generate new variants for this product
        When I do not specify any information about variants
        And I try to generate it
        Then I should be notified that code is required for the 1st variant
        And I should be notified that prices in all channels must be defined for the 1st variant
        And I should be notified that code is required for the 2st variant
        And I should be notified that prices in all channels must be defined for the 2st variant
        And I should see 0 variants in the list
