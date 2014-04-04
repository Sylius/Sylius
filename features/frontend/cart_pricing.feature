@checkout
Feature: Cart pricing
    In order to sell more
    As a store owner
    I want to have flexible cart pricing

    Background:
        Given there are following taxonomies defined:
            | name     |
            | Category |
          And taxonomy "Category" has following taxons:
            | Clothing > PHP T-Shirts |
          And the following zones are defined:
            | name    | type    | members        |
            | UK      | country | United Kingdom |
          And there are following tax categories:
            | name          |
            | Taxable Goods |
          And the following tax rates exist:
            | category      | zone    | name        | amount |
            | Taxable Goods | UK      | UK Tax      | 15%    |
          And the default tax zone is "UK"
          And there are following groups:
            | name                | roles             |
            | Wholesale Customers | ROLE_WHOLESALE    |
            | Retail Customers    | ROLE_RETAIL       |
          And there are following users:
            | email              | password | enabled | groups              |
            | beth@example.com   | foo      | yes     | Wholesale Customers |
            | martha@example.com | bar      | yes     | Retail Customers    |
          And the following products exist:
            | name        | price | taxons       | tax category  |
            | PHP Top     | 49.99 | PHP T-Shirts | Taxable Goods |
            | Symfony Tee | 69.00 | PHP T-Shirts | Taxable Goods |
          And product "PHP Top" has the following group based pricing:
            | group               | price |
            | Wholesale Customers | 39.49 |
            | Retail Customers    | 45.99 |
          And product "Symfony Tee" has the following volume based pricing:
            | range | price |
            | 0-9   | 69.00 |
            | 10-19 | 65.00 |
            | 20-29 | 60.00 |
            | 30+   | 55.99 |

    Scenario: Default price is used when user is not logged in
        Given I am on the store homepage
          And I follow "PHP T-Shirts"
          And I click "PHP Top"
         When I fill in "Quantity" with "2"
          And I press "Add to cart"
         Then I should be on the cart summary page
          And "Tax total: €15.00" should appear on the page
          But "Grand total: €114.98" should appear on the page

    Scenario: Wholesale customers have the lower price
        Given I log in with "beth@example.com" and "foo"
         When I add product "PHP Top" to cart, with quantity "4"
         Then I should be on the cart summary page
          And "Tax total: €23.69" should appear on the page
          And "Grand total: €181.65" should appear on the page

    Scenario: Retail customers get the higher price than wholesalers
        Given I log in with "martha@example.com" and "bar"
         When I add product "PHP Top" to cart, with quantity "3"
         Then I should be on the cart summary page
          And "Tax total: €23.69" should appear on the page
          And "Grand total: €158.66" should appear on the page

    Scenario: Price is calculated based on the quantity
        Given I am on the store homepage
         When I add product "Symfony Tee" to cart, with quantity "11"
         Then I should be on the cart summary page
          And "Tax total: €107.25" should appear on the page
          And "Grand total: €882.25" should appear on the page

    Scenario: Lower price is given for higher quantity
        Given I am on the store homepage
         When I add product "Symfony Tee" to cart, with quantity "25"
         Then I should be on the cart summary page
          And "Tax total: €225" should appear on the page
          And "Grand total: €1725" should appear on the page
