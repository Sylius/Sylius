@managing_products
Feature: Accessing the variants management from the product edit page
    In order to facilitate work with the management of variants
    As an Administrator
    I want to be able to access the variants management directly from the product edit page

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Audi" configurable product
        And I am logged in as an administrator

    @no-api @ui
    Scenario: Being able to access the variants list page
        When I modify the "Audi" product
        And I go to the variants list
        Then I should be on the list of this product's variants

    @no-api @ui
    Scenario: Being able to access the variant creation page
        When I modify the "Audi" product
        And I go to the variant creation page
        Then I should be on the variant creation page for this product

    @no-api @ui
    Scenario: Being able to access the variant generation page
        Given this product has option "Model" with values "RS6" and "RS7"
        When I modify the "Audi" product
        And I go to the variant generation page
        Then I should be on the variant generation page for this product

    @no-api @ui
    Scenario: Being unable to go to the generate variants page for a product without options
        When I want to modify the "Audi" product
        Then I should not be able to go to the generate variants page
