@legacy @promotion
Feature: Checkout limited time promotions
    In order to handle product promotions
    As a store owner
    I want to apply promotion discounts during checkout

    Background:
        Given store has default configuration
        And the following promotions exist:
            | code | name     | description                   | starts     | ends       |
            | P1   | Decade   | 20 EUR off for this decade    | 2013-01-01 | 2023-01-01 |
            | P2   | Too late | too late to get this discount |            | 2013-01-01 |
            | P3   | Too soon | too soon to get this discount | 2023-01-01 |            |
        And promotion "Decade" has following actions defined:
            | type                 | configuration |
            | Order fixed discount | Amount: 20    |
        And promotion "Too late" has following actions defined:
            | type                 | configuration |
            | Order fixed discount | Amount: 30    |
        And promotion "Too soon" has following actions defined:
            | type                 | configuration |
            | Order fixed discount | Amount: 40    |
        And there are following taxons defined:
            | code | name     |
            | RTX1 | Category |
        And taxon "Category" has following children:
            | Clothing[TX1] > Debian T-Shirts[TX2] |
        And the following products exist:
            | name   | price | taxons          |
            | Buzz   | 500   | Debian T-Shirts |
            | Potato | 200   | Debian T-Shirts |
            | Woody  | 125   | Debian T-Shirts |
            | Sarge  | 25    | Debian T-Shirts |
            | Etch   | 20    | Debian T-Shirts |
            | Lenny  | 15    | Debian T-Shirts |
        And all products are assigned to the default channel
        And all promotions are assigned to the default channel

    Scenario: Promotion is applied when the order date corresponds
            with promotion dates
        Given I am on the store homepage
        When I added product "Sarge" to cart, with quantity "8"
        Then I should be on the cart summary page
        And "Promotion total: -€20.00" should appear on the page
        And "Grand total: €180.00" should appear on the page
