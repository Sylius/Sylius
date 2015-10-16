@promotions
Feature: Checkout fixed discount promotions
    In order to handle product promotions
    As a store owner
    I want to apply promotion discounts during checkout

    Background:
        Given store has default configuration
          And the following countries exist:
            | name    |
            | Germany |
            | Poland  |
          And there are following taxonomies defined:
            | name     |
            | Category |
          And taxonomy "Category" has following taxons:
            | Clothing > Ubuntu T-Shirts |
            | Clothing > Debian T-Shirts |
          And the following products exist:
            | name    | price | taxons          |
            | Buzz    | 500   | Debian T-Shirts |
            | Potato  | 200   | Debian T-Shirts |
            | Woody   | 125   | Debian T-Shirts |
            | Sarge   | 25    | Debian T-Shirts |
            | Etch    | 20    | Debian T-Shirts |
            | Lenny   | 15    | Debian T-Shirts |
            | Ubu     | 200   | Ubuntu T-Shirts |
          And the following promotions exist:
            | code | name                | description                                              | not-active |
            | P1   | 3 items             | 15 EUR Discount for orders with at least 3 items         | true       |
            | P2   | 300 EUR             | 40 EUR Discount for orders over 300 EUR                  | true       |
            | P3   | Shipping to Germany | 40 EUR Discount for orders with shipping country Germany | true       |
            | P4   | Ubuntu T-Shirts     | 40 EUR Discount for Ubuntu T-Shirts                      | true       |
            | P5   | 3rd order           | 10 EUR Discount for 3rd order                            | true       |
          And all products are assigned to the default channel
          And all promotions are assigned to the default channel
          And promotion "3 items" has following rules defined:
            | type       | configuration        |
            | Item count | Count: 3,Equal: true |
          And promotion "3 items" has following benefits defined:
            | type           | configuration |
            | Fixed discount | Amount: 15    |
          And promotion "300 EUR" has following rules defined:
            | type       | configuration |
            | Item total | Amount: 300   |
          And promotion "300 EUR" has following benefits defined:
            | type           | configuration |
            | Fixed discount | Amount: 40    |
          And promotion "Shipping to Germany" has following rules defined:
            | type             | configuration |
            | Shipping country | Country: Germany |
          And promotion "Shipping to Germany" has following benefits defined:
            | type           | configuration |
            | Fixed discount | Amount: 40    |
          And promotion "Ubuntu T-Shirts" has following rules defined:
            | type     | configuration                      |
            | Taxonomy | Taxons: Ubuntu T-Shirts,Exclude: 0 |
          And promotion "Ubuntu T-Shirts" has following benefits defined:
            | type           | configuration |
            | Fixed discount | Amount: 40    |
          And promotion "3rd order" has following rules defined:
            | type      | configuration |
            | Nth order | Nth: 3        |
          And promotion "3rd order" has following benefits defined:
            | type           | configuration |
            | Fixed discount | Amount: 10    |
          And promotion "3 items" has following filters defined:
            | type                  | configuration |
            | most_expensive_filter |               |
#          And promotion "300 EUR" has following filters defined:
#            | type                  | configuration |
#            | most_expensive_filter |               |
          And I am logged in as user "klaus@example.com"

    Scenario Outline: Some examples should pass
        Given I have empty order
          And I add <basketContent> to the order
          And I have <activePromotions> promotions activated
         When I apply promotions
         Then I should have <discountName> discount equal <discountValue>
          And Total price should be <totalPrice>

Examples:
| basketContent          | activePromotions  | discountName                                       | discountValue | totalPrice |
#| Woody:3                | "300 EUR"         | "40 dis on order"          | -40.00        | 335.00     |
#| Sarge:8                | "300 EUR"         | ""                                                 |               | 200.00     |
#| Sarge:3,Etch:1,Lenny:2 | "3 items"         | "15 dis on order" | -15.00        | 110.00     |
#| Etch:8                 | "3 items"         | ""                                                 |               | 160.00     |
#| Ubu:1                  | "Ubuntu T-Shirts" | "40 EUR Discount for Ubuntu T-Shirts"              | -40.00        | 160.00     |
#| Lenny:1                | "Ubuntu T-Shirts" | ""                                                 |               | 15.00      |
| Potato:4,Woody:3,Buzz:1| "300 EUR,3 items" | "40 dis on order,15 dis on order"    |  -55.00  | 1620.00     |

    Scenario Outline: Examples with shipping to option should pass
        Given I have empty order
        And I add <basketContent> to the order
        And I have <activePromotions> promotions activated
        And Order is shipped to <shippingTo>
        When I apply promotions
        Then I should have <discountName> discount equal <discountValue>
        And Total price should be <totalPrice>
    Examples:
| basketContent | shippingTo | activePromotions     | discountName                                               | discountValue | totalPrice |
| Lenny:5       | "Germany"  |"Shipping to Germany" | "40 EUR Discount for orders with shipping country Germany" | -40.00        | 35.00      |
| Lenny:5       | "Poland"   |"Shipping to Germany" | ""                                                         |               | 75.00      |

    Scenario: Nth order promotion is applied when user have enough orders before
        Given I have "3rd order" promotions activated
        And the following zones are defined:
          | name         | type    | members                       |
          | German lands | country | Germany, Austria, Switzerland |
        And there are following tax categories:
          | code | name    |
          | TC1  | General |
        And there are products:
          | name          | price | tax category |
          | Mug           | 5.99  | General      |
          | Sticker       | 10.00 | General      |
        And the following tax rates exist:
          | code | category | zone         | name | amount |
          | TR1  | General  | German lands | VAT  | 23     |
        And the following orders were placed:
          | customer          | address                                                |
          | klaus@example.com | Klaus Schmitt, Heine-Straße 12, 99734, Berlin, Germany |
          | klaus@example.com | Klaus Schmitt, Heine-Straße 12, 99734, Berlin, Germany |
        And order #000000001 has following items:
          | product | quantity |
          | Mug     | 2        |
        And order #000000002 has following items:
          | product | quantity |
          | Mug     | 1        |
          | Sticker | 4        |
          And I am on the store homepage
         When I add product "Lenny" to cart, with quantity "1"
          And I go to the checkout start page
          And I fill in the shipping address to Poland
          And I press "Continue"
          And I go to the cart summary page
          And "Promotion total: -€10.00" should appear on the page
          And "Grand total: €5.00" should appear on the page

    Scenario: Nth order promotion is not applied when user have no orders before
        Given I have "3rd order" promotions activated
          And I am on the store homepage
         When I add product "Lenny" to cart, with quantity "1"
         Then I should be on the cart summary page
          And "Promotion total" should not appear on the page
          And "Grand total: €15.00" should appear on the page

    Scenario: Several promotions are applied when an cart fulfills
              the rules of several promotions
        Given I have "300 EUR,3 items" promotions activated
        Given I am on the store homepage
        And I added product "Potato" to cart, with quantity "4"
        And I added product "Buzz" to cart, with quantity "1"
        And I added product "Woody" to cart, with quantity "3"
        Then I should still be on the cart summary page
        And "Promotion total: -€55.00" should appear on the page
        And "Grand total: €1,620.00" should appear on the page
