@pricing
Feature: Volume based pricing
    In order to sell higher quantities
    As a store owner
    I want to configure volume based pricing

    Background:
        Given there is default currency configured
          And there are following taxonomies defined:
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
          And the following products exist:
            | name        | price | taxons       | tax category  |
            | Symfony Tee | 69.00 | PHP T-Shirts | Taxable Goods |
          And product "Symfony Tee" has the following volume based pricing:
            | range | price |
            | 0-9   | 69.00 |
            | 10-19 | 65.00 |
            | 20-29 | 60.00 |
            | 30+   | 55.99 |

    Scenario: Price is calculated based on the quantity
        Given I am on the store homepage
         When I add product "Symfony Tee" to cart, with quantity "11"
         Then I should be on the cart summary page
          And "Tax total: €107.25" should appear on the page
          And "Grand total: €822.25" should appear on the page

    Scenario: Lower price is given for higher quantity
        Given I am on the store homepage
         When I add product "Symfony Tee" to cart, with quantity "25"
         Then I should be on the cart summary page
          And "Tax total: €225.00" should appear on the page
          And "Grand total: €1,725.00" should appear on the page
