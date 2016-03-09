@legacy @pricing
Feature: Standard pricing
    In order to sell products with simple pricing
    As a store owner
    I want to configure flat price for items

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
        And the following products exist:
            | name        | price | taxons       | tax category  |
            | PHP Top     | 49.99 | PHP T-Shirts | Taxable Goods |
            | Symfony Tee | 69.00 | PHP T-Shirts | Taxable Goods |
        And all products are assigned to the default channel
        And the default channel has following configuration:
            | taxon    |
            | Category |

    Scenario: Flat price is calculated for products
        Given I am on the store homepage
        And I follow "PHP T-Shirts"
        And I click "PHP Top"
        When I fill in "Quantity" with "2"
        And I press "Add to cart"
        Then I should be on the cart summary page
        And "Tax total: €15.00" should appear on the page
        But "Grand total: €114.98" should appear on the page
