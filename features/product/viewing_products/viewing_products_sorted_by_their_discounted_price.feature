@viewing_products
Feature: Viewing products sorted by discounted price
    In order to see product variant sorted by discounted price
    As a Visitor
    I want to be able to see a properly sorted products by their discounted price

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And the product "Wyborowa Vodka" has "Wyborowa Vodka Exquisite" variant priced at "$30.00"
        And the product "Wyborowa Vodka" has "Wyborowa Apple" variant priced at "$20.00"
        And the "Wyborowa Apple" product variant has original price at "$35.00"
        And the store has a "Jack Daniels Whiskey" configurable product
        And the product "Jack Daniels Whiskey" has "Jack Daniels Original" variant priced at "$25.00"
        And the product "Jack Daniels Whiskey" has "Jack Daniels Red" variant priced at "$40.00"
        And the "Jack Daniels Original" product variant has original price at "$45.00"

    @api
    Scenario: Viewing a products with default variant's price sorted with discounted price
        When I browse products
        And I sort products by the lowest price first
        Then the first product on the list should have name "Wyborowa Vodka"
        And the last product on the list should have name "Jack Daniels Whiskey"

