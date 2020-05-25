@viewing_products
Feature: Viewing different price for different product variants
    In order to see product variant price
    As a Visitor
    I want to be able to see a proper price for each product variant

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And the product "Wyborowa Vodka" has "Wyborowa Lemon" variant priced at "$12.55"
        And the product "Wyborowa Vodka" has "Wyborowa Vodka Exquisite" variant priced at "$40.00"
        And the product "Wyborowa Vodka" has "Wyborowa Apple" variant priced at "$12.55"
        And the "Wyborowa Lemon" product variant is disabled

    @ui
    Scenario: Viewing a detailed page with default variant's price
        When I view product "Wyborowa Vodka"
        Then the product price should be "$40.00"

    @ui @javascript
    Scenario: Viewing a detailed page with product's price for different variant
        When I view product "Wyborowa Vodka"
        And I select "Wyborowa Apple" variant
        Then the product price should be "$12.55"
