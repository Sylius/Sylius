@viewing_products
Feature: Viewing products with multiple variants sorted by price
    In order to change the order in which products are displayed
    As a Customer
    I want to be able to sort products with multiple variants by the price of their first variant

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "Alcohols"
        And the store has a "Wyborowa Vodka" configurable product
        And this product belongs to "Alcohols"
        And the product "Wyborowa Vodka" has "Wyborowa Vodka Exquisite" variant priced at "$40.00"
        And the product "Wyborowa Vodka" has "Wyborowa Apple" variant priced at "$12.55"
        And the store has a "Jack Daniel's" configurable product
        And this product belongs to "Alcohols"
        And the product "Jack Daniel's" has "Jack Daniel's Old No.7" variant priced at "$30.00"
        And the product "Jack Daniel's" has "Gentleman Jack" variant priced at "$50.00"
        And the store has a "Johnnie Walker" configurable product
        And this product belongs to "Alcohols"
        And the product "Johnnie Walker" has "Johnnie Walker Red Label" variant priced at "$20.00"
        And the product "Johnnie Walker" has "Johnnie Walker Black Label" variant priced at "$25.00"

    @ui
    Scenario: Sorting products by price of their first variant with ascending order
        When I browse products from taxon "Alcohols"
        And I sort products by the lowest price first
        Then I should see 3 products in the list
        And the first product on the list should have name "Johnnie Walker"
        And the last product on the list should have name "Wyborowa Vodka"

    @ui
    Scenario: Sorting products by price of their first variant with descending order
        When I browse products from taxon "Alcohols"
        And I sort products by the highest price first
        Then I should see 3 products in the list
        And the first product on the list should have name "Wyborowa Vodka"
        And the last product on the list should have name "Johnnie Walker"
