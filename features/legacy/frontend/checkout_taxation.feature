@legacy @checkout
Feature: Checkout taxation
    In order to handle product taxation
    As a store owner
    I want to apply taxes during checkout

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
            | code | category      | zone | name   | amount |
            | TR1  | Taxable Goods | UK   | UK Tax | 15%    |
        And the following products exist:
            | name    | price | taxons       | tax category  |
            | PHP Top | 250   | PHP T-Shirts | Taxable Goods |
        And the following shipping methods exist:
            | code | zone | name        | tax category  | calculator | configuration |
            | SM1  | UK   | DHL Express | Taxable Goods | Flat rate  | Amount: 5000  |
        And the following payment methods exist:
            | code | name    | gateway | enabled |
            | PM1  | Offline | offline | yes     |
        And all products are assigned to the default channel
        And the default channel has following configuration:
            | taxon    | payment | shipping    |
            | Category | Offline | DHL Express |
        And I am logged in user
        And I added product "PHP Top" to cart
        And I go to the checkout start page

    Scenario: Placing the order
        Given I fill in the shipping address to United Kingdom
        And I press "Continue"
        And I select the "DHL Express" radio button
        And I press "Continue"
        And I select the "Offline" radio button
        When I press "Continue"
        Then I should be on the checkout finalize step
        And I should see "Shipping total: €57.50"
        And "Tax total: €45.00" should appear on the page
