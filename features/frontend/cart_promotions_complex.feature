@checkout
Feature: Checkout promotions with multiple rules and actions
    In order to handle product promotions
    As a store owner
    I want to apply promotion discounts during checkout

    Background:
        Given the following promotions exist:
          | name              | description                                            |
          | 150 EUR / 2 items | Discount for orders over 150 EUR with at least 2 items |
        And promotion "150 EUR / 2 items" has following rules defined:
          | type       | configuration        |
          | Item total | Amount: 150          |
          | Item count | Count: 2,Equal: true |
        And promotion "150 EUR / 2 items" has following actions defined:
          | type                | configuration |
          | Fixed discount      | Amount: 20    |
          | Percentage discount | Percentage: 5 |
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

    # promotion with several actions and rules (150 EUR / 2 items)
    Scenario: Several discounts are applied when a promotion has several
              actions and the cart fulfills all the rules
        Given I am on the store homepage
          And I follow "Debian T-Shirts"
          And I click "Sarge"
          And I fill in "Quantity" with "5"
          And I press "Add to cart"
          And I follow "Debian T-Shirts"
          And I click "Lenny"
         When I fill in "Quantity" with "2"
          And I press "Add to cart"
         Then I should be on the cart summary page
          And "Promotion total: (€27.75)" should appear on the page
          # 5*25 + 2*15 = 155 - 155*5% - 20 = 127.25
          And "Grand total: €127.25" should appear on the page

    Scenario: Promotion is not applied when one of the cart does not
              fulfills one of the rule
      Given I am on the store homepage
        And I follow "Debian T-Shirts"
        And I click "Sarge"
       When I fill in "Quantity" with "7"
        And I press "Add to cart"
       Then I should be on the cart summary page
        And "Promotion total: €0.00" should appear on the page
        And "Grand total: €175.00" should appear on the page