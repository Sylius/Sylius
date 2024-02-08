@viewing_products
Feature: Viewing different discounted price for different product variants
    In order to see product variant discounted price
    As a Visitor
    I want to be able to see a proper discounted price for each product variant

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And the product "Wyborowa Vodka" has "Wyborowa Vodka Exquisite" variant priced at "$40.00"
        And the product "Wyborowa Vodka" has "Wyborowa Apple" variant priced at "$12.55"
        And the "Wyborowa Apple" product variant has original price at "$20.00"
        And the product "Wyborowa Vodka" has "Wyborowa Pear" variant priced at "$30.00"
        And the "Wyborowa Pear" product variant has original price at "$20.00"

    @ui @api
    Scenario: Viewing a detailed page with default variant's price without discount
        When I view product "Wyborowa Vodka"
        Then the product price should be "$40.00"
        And I should not see any original price

    @ui @javascript @api
    Scenario: Viewing a detailed page with product's discount price for different variant
        When I view product "Wyborowa Vodka"
        And I select "Wyborowa Apple" variant
        Then the product variant price should be "$12.55"
        And the product original price should be "$20.00"

    @ui @javascript @no-api
    Scenario: Not seeing the discount when a variant's original price is lower than current price
        When I view product "Wyborowa Vodka"
        And I select "Wyborowa Pear" variant
        Then the product variant price should be "$30.00"
        And I should not see any original price
