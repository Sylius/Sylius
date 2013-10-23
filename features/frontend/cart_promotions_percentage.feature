@promotions
Feature: Checkout percentage discount promotions
    In order to handle product promotions
    As a store owner
    I want to apply promotion discounts during checkout

    Background:
        Given the following promotions exist:
          | name              | description                               |
          | 3 items           | Discount for orders with at least 3 items |
          | 300 EUR           | Discount for orders over 300 EUR          |
        And promotion "3 items" has following rules defined:
          | type       | configuration        |
          | Item count | Count: 3,Equal: true |
        And promotion "3 items" has following actions defined:
          | type                | configuration  |
          | Percentage discount | Percentage: 15 |
        And promotion "300 EUR" has following rules defined:
          | type       | configuration |
          | Item total | Amount: 300   |
        And promotion "300 EUR" has following actions defined:
          | type                | configuration |
          | Percentage discount | Percentage: 8 |
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

    Scenario: Fixed discount promotion is applied when the cart
              has the required amount
        Given I am on the store homepage
         When I add product "Woody" to cart, with quantity "3"
         Then I should be on the cart summary page
          And "Promotion total: (€30.00)" should appear on the page
          And "Grand total: €345.00" should appear on the page

    Scenario: Fixed discount promotion is not applied when the cart
              has not the required amount
        Given I am on the store homepage
         When I add product "Sarge" to cart, with quantity "8"
         Then I should be on the cart summary page
          And "Promotion total" should not appear on the page
          And "Grand total: €200.00" should appear on the page

    Scenario: Item count promotion is applied when the cart has the
              number of items required
        Given I am on the store homepage
          And I added product "Sarge" to cart, with quantity "3"
          And I added product "Etch" to cart, with quantity "1"
         When I add product "Lenny" to cart, with quantity "2"
         Then I should be on the cart summary page
          And "Promotion total: (€18.75)" should appear on the page
          And "Grand total: €106.25" should appear on the page

    Scenario: Item count promotion is not applied when the cart has
              not the number of items required
        Given I am on the store homepage
         When I add product "Etch" to cart, with quantity "8"
         Then I should be on the cart summary page
          And "Promotion total" should not appear on the page
          And "Grand total: €160.00" should appear on the page

    Scenario: Several promotions are applied when an cart fulfills
              the rules of several promotions
        Given I am on the store homepage
          And I added product "Potato" to cart, with quantity "4"
          And I added product "Buzz" to cart, with quantity "1"
         When I add product "Woody" to cart, with quantity "3"
         Then I should still be on the cart summary page
          And "Promotion total: (€385.25)" should appear on the page
          And "Grand total: €1,289.75" should appear on the page