@legacy @pricing
Feature: Volume based pricing
    In order to sell higher quantities
    As a store owner
    I want to configure volume based pricing

    Background:
        Given store has default configuration
        And there are following taxons defined:
            | code | name     |
            | RTX1 | Category |
        And taxon "Category" has following children:
            | Clothing[TX1] > PHP T-Shirts[TX2] |
        And the following zones are defined:
            | name | type    | members        |
            | UK   | country | United Kingdom |
        And there are following tax categories:
            | code | name          |
            | TC1  | Taxable Goods |
        And the following tax rates exist:
            | code | category      | zone | name       | amount |
            | TR1  | Taxable Goods | UK   | TR1 UK Tax | 15%    |
        And the default tax zone is "UK"
        And the following products exist:
            | name        | price | taxons       | tax category  |
            | Symfony Tee | 70.00 | PHP T-Shirts | Taxable Goods |
        And product "Symfony Tee" has the following volume based pricing:
            | range | price |
            | 1-9   | 69.00 |
            | 10-19 | 65.00 |
            | 20-29 | 60.00 |
            | 30+   | 55.99 |
        And all products are assigned to the default channel

    Scenario: Volume-based pricing has priority over price attribute
        Given I am on the store homepage
        When I add product "Symfony Tee" to cart, with quantity "1"
        Then I should be on the cart summary page
        And "Tax total: €10.35" should appear on the page
        And "Grand total: €79.35" should appear on the page

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

    Scenario: Lowest price is given for highest quantity and above
        Given I am on the store homepage
        When I add product "Symfony Tee" to cart, with quantity "100"
        Then I should be on the cart summary page
        And "Tax total: €839.85" should appear on the page
        And "Grand total: €6,438.85" should appear on the page
