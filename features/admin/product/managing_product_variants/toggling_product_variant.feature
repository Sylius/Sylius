@managing_product_variants
Feature: Toggling the product variant
    In order to stop or resume the sale of some product variants
    As an Administrator
    I want to toggle the product variant

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And the product "Wyborowa Vodka" has a "Wyborowa Vodka Exquisite" variant priced at "$40.00"
        And I am logged in as an administrator

    @api @ui
    Scenario: Disabling the product variant
        Given the "Wyborowa Vodka Exquisite" product variant is enabled
        When I want to modify the "Wyborowa Vodka Exquisite" product variant
        And I disable it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this variant should be disabled

    @api @ui
    Scenario: Enabling the product variant
        Given the "Wyborowa Vodka Exquisite" product variant is disabled
        When I want to modify the "Wyborowa Vodka Exquisite" product variant
        And I enable it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this variant should be enabled
