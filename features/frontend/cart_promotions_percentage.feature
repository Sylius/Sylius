@promotions
Feature: Checkout percentage discount promotions
    In order to handle product promotions
    As a store owner
    I want to apply promotion discounts during checkout

    Background:
        Given store has default configuration
          And the following promotions exist:
            | code | name              | description                                   | not-active |
            | PR1  | 3 items           | 25% Discount for orders with at least 3 items |            |
            | PR2  | 300 EUR           | 10% Discount for orders over 300 EUR          |            |
          And promotion "3 items" has following rules defined:
            | type       | configuration        |
            # WARNING! This promotion rule works only if there are at least 3 _different_ products!
            | item_count | Count: 3,Equal: false |
          And promotion "3 items" has following benefits defined:
            | type                | configuration  |
            | percentage discount | percentage: 25 |
          And promotion "300 EUR" has following rules defined:
            | type       | configuration |
            | Item total | Amount: 300   |
          And promotion "300 EUR" has following benefits defined:
            | type                | configuration |
            | Percentage discount | percentage: 10 |
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

    Scenario Outline: Some examples should pass
        Given I have empty order
          And I add <basketContent> to the order
          And I have <activePromotions> promotions activated
         When I apply promotions
         Then I should have <appliedPromotions> discount equal <discountValue>
          And Total price should be <totalPrice>

        Examples:
            | basketContent           | activePromotions  | appliedPromotions | discountValue | totalPrice |
            | Potato:4,Buzz:1,Woody:3 | "3 items"         | "3 items"         | -418.75       | 1256.25    |
            | Potato:4,Buzz:1,Woody:3 | "300 EUR"         | "300 EUR"         | -167.50       | 1507.50    |
            | Potato:4,Buzz:1,Woody:3 | "300 EUR,3 items" | "300 EUR,3 items" | -586.25       | 1088.75    |
            | Sarge:3,Etch:1,Lenny:2  | "3 items,300 EUR" | "3 items"         | -31.25        | 93.75      |
            | Etch:4                  | "3 items"         | ""                | 0             | 80.00      |