@legacy @checkout
Feature: Tax included in price
    In order to handle product taxation
    As a store owner
    I want to apply taxes during checkout

    Background:
        Given store has default configuration
        And there are following taxons defined:
            | code | name     |
            | RTX1 | Category |
        And taxon "Category" has following children:
            | Clothing[TX1] > T-Shirts[TX2]     |
            | Clothing[TX1] > PHP T-Shirts[TX3] |
        And the following zones are defined:
            | name    | type    | members |
            | Germany | country | Germany |
        And there are following tax categories:
            | code | name          |
            | TC1  | Taxable Goods |
        And the following tax rates exist:
            | code | category      | zone    | name        | amount | included in price? |
            | TR1  | Taxable Goods | Germany | Germany VAT | 23%    | yes                |
        And the following products exist:
            | name    | price | taxons       | tax category  |
            | PHP Top | 85    | PHP T-Shirts | Taxable Goods |
        And all products are assigned to the default channel
        And the default channel has following configuration:
            | taxon    |
            | Category |
        And the default tax zone is "Germany"

    Scenario: Correct amounts are displayed for inclusive taxes
        Given I am on the store homepage
        And I follow "PHP T-Shirts"
        And I click "PHP Top"
        When I fill in "Quantity" with "3"
        And I press "Add to cart"
        Then I should be on the cart summary page
        And "Tax total: €47.68" should appear on the page
        And "Grand total: €255.00" should appear on the page
