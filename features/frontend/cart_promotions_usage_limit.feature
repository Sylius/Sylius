@promotions
Feature: Checkout usage limited promotions
    In order to handle product promotions
    As a store owner
    I want to apply promotion discounts during checkout

    # TODO: This feature doesn't make any sense!!!
    # TODO: It's talking about 'usage limit' but the only test here relating to limits is item quantity
    # TODO: and this doesn't really do what we expected and is tested on another feature.Scenario:
    # TODO: This one should be rewritten to relate to CUSTOMER USAGE
    Background:
        Given store has default configuration
          And the following promotions exist:
            | code | name                             | description                                          | usage limit | used |
            | P1   | 25% off over 200 EUR             | First order over 200 EUR have 25% discount!          | 1           | 0    |
            | P2   | Free order with at least 3 items | First order with at least 3 items has 100% discount! | 1           | 1    |
          And promotion "25% off over 200 EUR" has following rules defined:
            | type       | configuration |
            | Item total | Amount: 200   |
          And promotion "25% off over 200 EUR" has following benefits defined:
            | type                | configuration  |
            | Percentage discount | Percentage: 25 |
          And promotion "Free order with at least 3 items" has following rules defined:
            | type       | configuration        |
            | Item count | Count: 3,Equal: true |
          And promotion "Free order with at least 3 items" has following benefits defined:
            | type                | configuration   |
            | Percentage discount | Percentage: 100 |
          And there are following taxonomies defined:
            | name     |
            | Category |
          And taxonomy "Category" has following taxons:
            | Clothing > Debian T-Shirts |
          And the following products exist:
            | name    | price | taxons          |
            | Buzz    | 500   | Debian T-Shirts |
            | Potato  | 200   | Debian T-Shirts |
            | Woody   | 125   | Debian T-Shirts |
            | Sarge   | 25    | Debian T-Shirts |
            | Etch    | 20    | Debian T-Shirts |
            | Lenny   | 15    | Debian T-Shirts |
          And all products are assigned to the default channel
          And all promotions are assigned to the default channel

    Scenario Outline: Promotions should be applied or not based on usage limit
        Given I have empty order
          And I add <basketContent> to the order
          And I have <activePromotions> promotions activated
         When I apply promotions
         Then I should have <appliedPromotions> discount equal <discountValue>
          And Total price should be <totalPrice>

        Examples:
            | basketContent | activePromotions                   | appliedPromotions      | discountValue | totalPrice |
            | Woody:3       | "25% off over 200 EUR"             | "25% off over 200 EUR" | -93.75        | 281.25     |
            | Etch:3        | "Free order with at least 3 items" | ""                     |               | 60         |
