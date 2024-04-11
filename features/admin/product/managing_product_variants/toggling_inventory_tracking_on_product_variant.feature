@managing_product_variants
Feature: Toggling the inventory tracking
    In order to have the inventory tracked in my shop
    As an Administrator
    I want to toggle the inventory tracking

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And the product "Wyborowa Vodka" has a "Wyborowa Vodka Exquisite" variant priced at "$40.00"
        And I am logged in as an administrator

    @api @ui
    Scenario: Disabling inventory tracking for the product variant
        Given the "Wyborowa Vodka Exquisite" product variant is tracked by the inventory
        When I want to modify the "Wyborowa Vodka Exquisite" product variant
        And I disable its inventory tracking
        And I save my changes
        Then I should be notified that it has been successfully edited
        And inventory of this variant should not be tracked

    @api @ui
    Scenario: Enabling inventory tracking for the product variant
        When I want to modify the "Wyborowa Vodka Exquisite" product variant
        And I enable its inventory tracking
        And I save my changes
        Then I should be notified that it has been successfully edited
        And inventory of this variant should be tracked
