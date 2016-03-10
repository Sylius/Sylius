@legacy @checkout
Feature: Checkout inventory
    In order to manage my inventory
    As a store owner
    I need to see inventory changes after checkout

    Background:
        Given store has default configuration
        And there are following taxons defined:
            | code | name     |
            | RTX1 | Category |
        And taxon "Category" has following children:
            | Clothing[TX1] > PHP T-Shirts[TX2] |
        And the following products exist:
            | name          | price | taxons       | quantity |
            | PHP Top       | 5.99  | PHP T-Shirts | 14       |
            | Sylius Hoodie | 19.99 | PHP T-Shirts | 8        |
        And the following zones are defined:
            | name | type    | members        |
            | UK   | country | United Kingdom |
        And the following shipping methods exist:
            | code | zone | name        |
            | SM1  | UK   | DHL Express |
        And the following payment methods exist:
            | code | name        | gateway | enabled |
            | PM1  | Credit Card | offline | yes     |
        And all products are assigned to the default channel
        And the default channel has following configuration:
            | taxon    | payment     | shipping    |
            | Category | Credit Card | DHL Express |
        And I am logged in as administrator

    Scenario: Inventory is updated after buying products
        Given I added product "PHP Top" to cart, with quantity "4"
        And I added product "Sylius Hoodie" to cart, with quantity "5"
        And I go to the checkout start page
        And I finish the checkout process
        When I go to the inventory index page
        Then I should see stock level "10" for "PHP Top"
        And I should see stock level "3" for "Sylius Hoodie"

    Scenario: When buying only one product, other quantities remain unchanged
        And I added product "Sylius Hoodie" to cart, with quantity "3"
        And I go to the checkout start page
        And I finish the checkout process
        When I go to the inventory index page
        Then I should see stock level "14" for "PHP Top"
        And I should see stock level "5" for "Sylius Hoodie"

    Scenario: Buying out all products
        And I added product "Sylius Hoodie" to cart, with quantity "8"
        And I go to the checkout start page
        And I finish the checkout process
        When I go to the inventory index page
        Then I should see stock level "0" for "Sylius Hoodie"

    Scenario: Ordering more than available with enabled backorders
        And I added product "PHP Top" to cart, with quantity "20"
        And I go to the checkout start page
        And I finish the checkout process
        When I go to the inventory index page
        Then I should see stock level "0" for "PHP Top"
