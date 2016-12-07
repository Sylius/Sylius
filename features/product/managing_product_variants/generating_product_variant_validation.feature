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
    Scenario: Generating a product's variant without price
        When I want to generate new variants for this product
        And I specify that the 1st variant is identified by "WYBOROWA_ORANGE" code
        And I try to generate it
        Then I should be notified that prices in all channels must be defined for the 1st variant
        And I should not see any variants in the list

    @ui
    Scenario: Generating a product's variant without code
        When I want to generate new variants for this product
        And I specify that the 1st variant costs "$90" in "United States" channel
        And I try to generate it
        Then I should be notified that code is required for the 1st variant
        And I should not see any variants in the list

    @ui
    Scenario: Generating product's variants without specific required fields for second variant
        When I want to generate new variants for this product
        And I specify that the 1st variant is identified by "WYBOROWA_ORANGE" code
        And I specify that the 1st variant costs "$90" in "United States" channel
        And I try to generate it
        Then I should be notified that code is required for the 2nd variant
        Then I should be notified that prices in all channels must be defined for the 2nd variant
        And I should not see any variants in the list

    @ui
    Scenario: Generating product's variants with the same code
        When I want to generate new variants for this product
        And I specify that the 1st variant is identified by "WYBOROWA_TASTE" code
        And I specify that the 1st variant costs "$90" in "United States" channel
        And I specify that the 2nd variant is identified by "WYBOROWA_TASTE" code
        And I specify that the 2nd variant costs "$90" in "United States" channel
        And I try to generate it
        Then I should be notified that variant code must be unique within this product for the 1st variant
        And I should be notified that variant code must be unique within this product for the 2nd variant
        And I should not see any variants in the list

    @ui
    Scenario: Generating product's variants without specific required fields for second variant
        When I want to generate new variants for this product
        And I do not specify any information about variants
        And I try to generate it
        Then I should be notified that code is required for the 1st variant
        And I should be notified that prices in all channels must be defined for the 1st variant
        And I should be notified that code is required for the 2nd variant
        And I should be notified that prices in all channels must be defined for the 2nd variant
        And I should not see any variants in the list
