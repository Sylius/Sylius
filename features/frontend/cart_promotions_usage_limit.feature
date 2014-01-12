@promotions
Feature: Checkout usage limited promotions
    In order to handle product promotions
    As a store owner
    I want to apply promotion discounts during checkout

    Background:
        Given the following promotions exist:
          | name                             | description                                            | usage limit | used |
          | 50% off over 200 EUR             | First 3 orders over 200 EUR have 50% discount!         | 3           | 0    |
          | Free order with at least 3 items | First order with at least 3 items has 100% discount!   | 1           | 1    |
        And promotion "50% off over 200 EUR" has following rules defined:
          | type       | configuration |
          | Item total | Amount: 200   |
        And promotion "50% off over 200 EUR" has following actions defined:
          | type                | configuration  |
          | Percentage discount | Percentage: 50 |
        And promotion "Free order with at least 3 items" has following rules defined:
          | type       | configuration        |
          | Item count | Count: 3,Equal: true |
        And promotion "Free order with at least 3 items" has following actions defined:
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

    Scenario: Promotion with usage limit is applied when the
              number of usage is not reached
        Given I am on the store homepage
         When I add product "Buzz" to cart, with quantity "2"
         Then I should be on the cart summary page
          And "Promotion total: (€500.00)" should appear on the page
          And "Grand total: €500.00" should appear on the page

    Scenario: Promotion with usage limit is not applied when the
              number of usage is reached
        Given I am on the store homepage
          And I added product "Sarge" to cart, with quantity "3"
          And I added product "Etch" to cart, with quantity "1"
         When I add product "Lenny" to cart, with quantity "2"
         Then I should be on the cart summary page
          And "Promotion total" should not appear on the page
          And "Grand total: €125.00" should appear on the page