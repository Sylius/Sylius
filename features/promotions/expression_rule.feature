@promotions
Feature: Promotion rules based on expressions
    In order to handle product promotions
    As a store owner
    I want to apply promotion discounts during checkout

    Background:
        Given there are following taxonomies defined:
            | name     |
            | Category |
          And taxonomy "Category" has following taxons:
            | Clothing > Ubuntu T-Shirts |
          And the following products exist:
            | name | price | taxons          |
            | Ubu  | 42    | Ubuntu T-Shirts |
          And the following promotions exist:
            | name        | description                                |
            | opensky.com | Discount for users from opensky.com domain |
          And promotion "opensky.com" has following rules defined:
            | type       | configuration                                                                 |
            | Expression | expr: user.getEmail() matches "/[_a-z0-9-]+(\.[_a-z0-9-]+)*@(?i)opensky.com/" |
          And promotion "opensky.com" has following actions defined:
            | type           | configuration |
            | Fixed discount | Amount: 20    |

    Scenario: Fixed discount promotion is applied when the order fulfills
              the requirment stored in expression
        Given I am logged in as user "john@opensky.com"
         When I add product "Ubu" to cart, with quantity "2"
         Then I should be on the cart summary page
          And "Promotion total: -€20.00" should appear on the page
          And "Grand total: €64.00" should appear on the page

    Scenario: Fixed discount promotion is not applied when the cart
              is not accepted by the expression
        Given I am logged in as user "rick@example.com"
         When I add product "Ubu" to cart, with quantity "3"
         Then I should be on the cart summary page
          And "Promotion total" should not appear on the page
          And "Grand total: €126.00" should appear on the page
