@viewing_products
Feature: Viewing products sorted by first variant price

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "Whiskey"
        And the store has a "Jack Daniel's" configurable product
        And this product belongs to "Whiskey"
        And the product "Jack Daniel's" has "Gentleman Jack" variant priced at "$50.00"
        And the product "Jack Daniel's" has "Jack Daniel's Old No.7" variant priced at "$30.00"
        And the store has a "Johnnie Walker" configurable product
        And this product belongs to "Whiskey"
        And the product "Johnnie Walker" has "Johnnie Walker Red Label" variant priced at "$20.00"
        And the product "Johnnie Walker" has "Johnnie Walker Blue Label" variant priced at "$125.00"

    @ui
    Scenario: Sorting products by price of their first variant with ascending order
        When I browse products from taxon "Whiskey"
        And I sort products by the lowest price first
        Then the first product on the list should have name "Johnnie Walker" and price "$20.00"
        And the last product on the list should have name "Jack Daniel's" and price "$50.00"

    @ui
    Scenario: Sorting products by price of their first variant with descending order
        When I browse products from taxon "Whiskey"
        And I sort products by the highest price first
        Then the first product on the list should have name "Jack Daniel's" and price "$50.00"
        And the last product on the list should have name "Johnnie Walker" and price "$20.00"
