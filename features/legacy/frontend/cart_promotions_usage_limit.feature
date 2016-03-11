@legacy @promotion
Feature: Checkout usage limited promotions
    In order to handle product promotions
    As a store owner
    I want to apply promotion discounts during checkout

    Background:
        Given store has default configuration
        And the following promotions exist:
            | code | name                             | description                                          | usage limit | used |
            | P1   | 25% off over 200 EUR             | First order over 200 EUR have 25% discount!          | 1           | 0    |
            | P2   | Free order with at least 3 items | First order with at least 3 items has 100% discount! | 1           | 1    |
        And promotion "25% off over 200 EUR" has following rules defined:
            | type       | configuration |
            | Item total | Amount: 200   |
        And promotion "25% off over 200 EUR" has following actions defined:
            | type                      | configuration  |
            | Order percentage discount | Percentage: 25 |
        And promotion "Free order with at least 3 items" has following rules defined:
            | type          | configuration |
            | Cart quantity | Count: 3      |
        And promotion "Free order with at least 3 items" has following actions defined:
            | type                      | configuration   |
            | Order percentage discount | Percentage: 100 |
        And there are following taxons defined:
            | code | name            |
            | TX1  | Debian T-Shirts |
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

    Scenario: Promotion with usage limit is not applied when the
            number of usage is reached
        Given I am on the store homepage
        When I add product "Etch" to cart, with quantity "3"
        Then I should be on the cart summary page
        And "Promotion total" should not appear on the page
        And "Grand total: €60.00" should appear on the page

    Scenario: Promotion with usage limit is applied when the
            number of usage is not reached
        Given I am on the store homepage
        And I added product "Woody" to cart, with quantity "3"
        Then I should be on the cart summary page
        And "Promotion total: -€93.75" should appear on the page
        And "Grand total: €281.25" should appear on the page
