@legacy @pricing
Feature: Group based pricing
    In order to have different contracts
    As a store owner
    I want to configure prices per customer group

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
        And the default tax zone is "UK"
        And there are following groups:
            | name                |
            | Wholesale Customers |
            | Retail Customers    |
        And there are following users:
            | email              | password | enabled | groups              |
            | beth@example.com   | foo1     | yes     | Wholesale Customers |
            | martha@example.com | bar1     | yes     | Retail Customers    |
        And the following products exist:
            | name        | price | taxons       | tax category  |
            | PHP Top     | 49.99 | PHP T-Shirts | Taxable Goods |
            | Symfony Tee | 69.00 | PHP T-Shirts | Taxable Goods |
        And product "PHP Top" has the following group based pricing:
            | group               | price |
            | Wholesale Customers | 39.49 |
            | Retail Customers    | 45.99 |
        And all products are assigned to the default channel
        And the default channel has following configuration:
            | taxon    |
            | Category |

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
        Given I log in with "beth@example.com" and "foo1"
        When I add product "PHP Top" to cart, with quantity "4"
        Then I should be on the cart summary page
        And "Tax total: €23.69" should appear on the page
        And "Grand total: €181.65" should appear on the page

    Scenario: Retail customers get the higher price than wholesalers
        Given I log in with "martha@example.com" and "bar1"
        When I add product "PHP Top" to cart, with quantity "3"
        Then I should be on the cart summary page
        And "Tax total: €20.70" should appear on the page
        And "Grand total: €158.67" should appear on the page

    Scenario: Prices are calculated accordingly after security checkout step
        Given I add product "PHP Top" to cart, with quantity "4"
        And I go to the checkout start page
        And I fill in the following:
            | Email    | beth@example.com |
            | Password | foo1             |
        And I press "Login"
        When I go to the cart summary page
        Then "Tax total: €23.69" should appear on the page
        And "Grand total: €181.65" should appear on the page
