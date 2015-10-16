@promotions
Feature: Checkout promotions with multiple rules and benefits
    In order to handle product promotions
    As a store owner
    I want to apply promotion discounts during checkout

    Background:
        Given store has default configuration
          And the following promotions exist:
            | code | name              | description                                            |
            | P1   | 150 EUR / 2 items | Discount for orders over 150 EUR with at least 2 items |
          And promotion "150 EUR / 2 items" has following rules defined:
            | type       | configuration        |
            | Item total | Amount: 150          |
            | Item count | Count: 2,Equal: true |
          And promotion "150 EUR / 2 items" has following benefits defined:
            | type                | configuration |
            | Percentage discount | Percentage: 5 |
            | Fixed discount      | Amount: 20    |
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
             Then I should have <discountName> discount equal <discountValue>
              And Total price should be <totalPrice>

        Examples:
        | basketContent   | activePromotions    | discountName | discountValue  | totalPrice |
        | Sarge:7,Lenny:2 | "150 EUR / 2 items" | "150 EUR / 2 items"  | -50.25 | 154.75    |
        | Buzz:1,Potato:1 | "150 EUR / 2 items" | "150 EUR / 2 items"  | -75.00 | 625.00    |

#    Scenario: Several discounts are applied when a promotion has several
#              actions and the cart fulfills all the rules
#        Given I am on the store homepage
#          And I added product "Sarge" to cart, with quantity "5"
#         When I add product "Lenny" to cart, with quantity "2"
#         Then I should be on the cart summary page
#          And "Promotion total: -€27.75" should appear on the page
#          And "Grand total: €127.25" should appear on the page

    Scenario: Promotion is not applied when one of the cart does not
              fulfills one of the rule
        Given I am on the store homepage
         When I add product "Sarge" to cart, with quantity "7"
         Then I should be on the cart summary page
          And "Promotion total" should not appear on the page
          And "Grand total: €175.00" should appear on the page
