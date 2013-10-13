@checkout
Feature: Cart taxation
    In order to handle product taxation
    As a store owner
    I want to apply taxes during checkout

    Background:
        Given there are following taxonomies defined:
            | name     |
            | Category |
          And taxonomy "Category" has following taxons:
            | Clothing > PHP T-Shirts |
          And the following zones are defined:
            | name  | type    | members        |
            | UK      | country | United Kingdom |
            | Germany | country | Germany        |
          And there are following tax categories:
            | name          |
            | Taxable Goods |
          And the following tax rates exist:
            | category      | zone    | name        | amount |
            | Taxable Goods | UK      | UK Tax      | 15%    |
            | Taxable Goods | Germany | Germany VAT | 23%    |
          And the following products exist:
            | name    | price | taxons       | tax category  |
            | PHP Top | 50    | PHP T-Shirts | Taxable Goods |

    Scenario: No taxes are applied for unknown billing address
              when default tax zone is not configured
        Given I am on the store homepage
          And I follow "PHP T-Shirts"
          And I click "PHP Top"
         When I fill in "Quantity" with "2"
          And I press "Add to cart"
         Then I should be on the cart summary page
          And "Tax total: €0.00" should appear on the page
          But "Grand total: €100.00" should appear on the page

    Scenario: Correct taxes are applied for uknown billing address
              when default tax zone is configured
        Given the default tax zone is "UK"
          And I am on the store homepage
          And I follow "PHP T-Shirts"
          And I click "PHP Top"
         When I fill in "Quantity" with "2"
          And I press "Add to cart"
         Then I should be on the cart summary page
          And "Tax total: €15.00" should appear on the page
          And "Grand total: €115.00" should appear on the page

    Scenario: Correct taxes are applied for known billing address
        Given I am logged in user
          And I added product "PHP Top" to cart
          And I go to the checkout start page
          And I fill in the shipping address to Germany
          And I press "Continue"
         When I go to the cart summary page
         Then "Tax total: €11.50" should appear on the page
          And "Grand total: €61.50" should appear on the page
