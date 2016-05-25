@managing_product_variants
Feature: Adding a new product variant
    In order to sell different variations of a single product
    As an Administrator
    I want to add a new product variant to the shop

    Background:
        Given the store is available in "English (United States)"
        And the store has a base currency "US Dollar"
        And the store has a "Wyborowa Vodka" configurable product
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new product variant
        Given I want to create a new variant of this product
        When I specify its code as "VODKA_WYBOROWA_PREMIUM"
        And I name it "Wyborowa Vodka Exquisite"
        And I set its price to "$100.00"
        And I add it
        Then I should be notified that it has been successfully created
        And the "Wyborowa Vodka Exquisite" variant of the "Wyborowa Vodka" product should appear in the shop
