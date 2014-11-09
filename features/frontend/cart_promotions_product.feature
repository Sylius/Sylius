@promotions
Feature: Checkout product promotion
    In order to handle product promotions
    As a store owner
    I want to apply promotion discounts during checkout

    Background:
        Given the following products exist:
          | name   | price |
          | Buzz   | 500   |
          | Potato | 200   |
          | Etch   | 20    |
          | Woody  | 125   |
          | Sarge  | 25    |
          | Lenny  | 15    |
          | Ubu    | 200   |
        And the following promotions exist:
          | name                | description                               |
          | Free product: Lenny | Almost free product over 100 euro         |
          | Free product: Sarge | Free product Sarge when buying only Woody |
        And promotion "Free product: Lenny" has following rules defined:
          | type       | configuration |
          | Item total | Amount: 100   |
        And promotion "Free product: Lenny" has following actions defined:
          | type        | configuration                          |
          | Add product | Variant: Lenny, Quantity: 1, Price: 10 |
        And promotion "Free product: Sarge" has following rules defined:
          | type                    | configuration                              |
          | Contain product variant | Variant: Woody, Only: true, Exclude: false |
        And promotion "Free product: Sarge" has following actions defined:
          | type        | configuration                         |
          | Add product | Variant: Sarge, Quantity: 1, Price: 0 |
        And there is default currency configured

    Scenario: Free product is not applied when the cart
              has not the required amount
        Given I am on the store homepage
         When I add product "Etch" to cart, with quantity "1"
         Then I should be on the cart summary page
          And "Lenny" should not appear on the page
          And "Grand total: €20.00" should appear on the page

    Scenario: Free product is applied when the cart has required item
        Given I am on the store homepage
         When I add product "Woody" to cart, with quantity "1"
         Then I should be on the cart summary page
          And "Lenny" should appear on the page
          And "Grand total: €610.00" should appear on the page

    Scenario: Free product is not applied when the cart has required
              item but also other products
        Given I am on the store homepage
         When I add product "Woody" to cart, with quantity "1"
          And I add product "Ubu" to cart, with quantity "1"
         Then I should be on the cart summary page
          And "Sarge" should not appear on the page
          And "Grand total: €325.00" should appear on the page
