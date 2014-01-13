@promotions
Feature: Checkout fixed discount promotions
    In order to handle product promotions
    As a store owner
    I want to apply promotion discounts during checkout

    Background:
        Given I am logged in as user "klaus@example.com"
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
          | name                | description                                       |
          | 3 items             | Discount for orders with at least 3 items         |
          | 300 EUR             | Discount for orders over 300 EUR                  |
          | Shipping to Germany | Discount for orders with shipping country Germany |
          | Ubuntu T-Shirts     | Discount for Ubuntu T-Shirts                      |
          | 3rd order           | Discount for 3rd order                            |
        And promotion "3 items" has following rules defined:
          | type       | configuration        |
          | Item count | Count: 3,Equal: true |
        And promotion "3 items" has following actions defined:
          | type           | configuration |
          | Fixed discount | Amount: 15    |
        And promotion "300 EUR" has following rules defined:
          | type       | configuration |
          | Item total | Amount: 300   |
        And promotion "300 EUR" has following actions defined:
          | type           | configuration |
          | Fixed discount | Amount: 40    |
        And promotion "Shipping to Germany" has following rules defined:
          | type       | configuration |
          | Shipping country | Country: Germany |
        And promotion "Shipping to Germany" has following actions defined:
          | type           | configuration |
          | Fixed discount | Amount: 40    |
        And promotion "Ubuntu T-Shirts" has following rules defined:
          | type     | configuration          |
          | Taxonomy | Taxons: Ubuntu T-Shirts,Exclude: 0 |
        And promotion "Ubuntu T-Shirts" has following actions defined:
          | type           | configuration |
          | Fixed discount | Amount: 40    |
        And promotion "3rd order" has following rules defined:
          | type      | configuration |
          | Nth order | Nth: 3        |
        And promotion "3rd order" has following actions defined:
          | type           | configuration |
          | Fixed discount | Amount: 10    |

    Scenario: Fixed discount promotion is applied when the cart
              has the required amount
        Given I am on the store homepage
         When I add product "Woody" to cart, with quantity "3"
         Then I should be on the cart summary page
          And "Promotion total: (€40.00)" should appear on the page
          And "Grand total: €335.00" should appear on the page

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
          And "Promotion total: (€15.00)" should appear on the page
          And "Grand total: €110.00" should appear on the page

    Scenario: Item count promotion is not applied when the cart has
              not the number of items required
        Given I am on the store homepage
         When I add product "Etch" to cart, with quantity "8"
         Then I should be on the cart summary page
          And "Promotion total" should not appear on the page
          And "Grand total: €160.00" should appear on the page

    Scenario: Shipping country promotion is applied when shipping country match
        Given I am on the store homepage
         When I add product "Lenny" to cart, with quantity "5"
          And I go to the checkout start page
          And I fill in the shipping address to Germany
          And I press "Continue"
          And I go to the cart summary page
         Then "Promotion total: (€40.00)" should appear on the page
          And "Grand total: €35.00" should appear on the page

    Scenario: Shipping country promotion is not applied when shipping country does not match
        Given I am on the store homepage
         When I add product "Lenny" to cart, with quantity "5"
          And I go to the checkout start page
          And I fill in the shipping address to Poland
          And I press "Continue"
          And I go to the cart summary page
          And "Promotion total" should not appear on the page
          And "Grand total: €75.00" should appear on the page

    Scenario: Ubuntu T-Shirts promotion is applied when the cart containes Ubuntu T-Shirts
        Given I am on the store homepage
         When I add product "Ubu" to cart, with quantity "1"
         Then I should be on the cart summary page
          And "Promotion total: (€40.00)" should appear on the page
          And "Grand total: €160.00" should appear on the page

    Scenario: Ubuntu T-Shirts promotion is not applied when the cart does not contain Ubuntu T-Shirts
        Given I am on the store homepage
         When I add product "Lenny" to cart, with quantity "1"
         Then I should be on the cart summary page
          And "Promotion total" should not appear on the page
          And "Grand total: €15.00" should appear on the page

    Scenario: Nth order promotion is applied when user have enough orders before
        Given the following zones are defined:
          | name         | type    | members                       |
          | German lands | country | Germany, Austria, Switzerland |
        And there are following tax categories:
          | name    |
          | General |
        And there are products:
          | name          | price | tax category |
          | Mug           | 5.99  | General      |
          | Sticker       | 10.00 | General      |
        And the following tax rates exist:
          | category | zone         | name | amount |
          | General  | German lands | VAT  | 23     |
        And the following orders were placed:
          | user              | address                                                |
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
          And "Promotion total: (€10.00)" should appear on the page
          And "Grand total: €5.00" should appear on the page

    Scenario: Nth order promotion is not applied when user have no orders before
        Given I am on the store homepage
         When I add product "Lenny" to cart, with quantity "1"
         Then I should be on the cart summary page
          And "Promotion total" should not appear on the page
          And "Grand total: €15.00" should appear on the page

    Scenario: Several promotions are applied when an cart fulfills
              the rules of several promotions
        Given I am on the store homepage
          And I added product "Potato" to cart, with quantity "4"
          And I added product "Buzz" to cart, with quantity "1"
         When I add product "Woody" to cart, with quantity "3"
         Then I should still be on the cart summary page
          And "Promotion total: (€55.00)" should appear on the page
          And "Grand total: €1,620.00" should appear on the page