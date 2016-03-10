@legacy @search
Feature: Search products
    In order to be able to find products
    As a visitor
    I want to be able to search the products

    Background:
        Given store has default configuration
        And there are following taxons defined:
            | code | name     |
            | RTX1 | Category |
        And taxon "Category" has following children:
            | Clothing[TX1] > T-Shirts[TX2]     |
            | Clothing[TX1] > PHP T-Shirts[TX3] |
            | Clothing[TX1] > Gloves[TX4]       |
        And the following products exist:
            | name             | price | taxons       | description             |
            | Super T-Shirt    | 19.99 | T-Shirts     | super black t-shirt     |
            | Black T-Shirt    | 18.99 | T-Shirts     | black t-shirt           |
            | Sylius Tee       | 12.99 | PHP T-Shirts | a very nice php t-shirt |
            | Symfony T-Shirt  | 15.00 | PHP T-Shirts | symfony t-shirt         |
            | Doctrine T-Shirt | 15.00 | PHP T-Shirts | doctrine t-shirt        |
        And all products are assigned to the default channel
        And the default channel has following configuration:
            | taxon    |
            | Category |
        And I populate the index

    Scenario: Search homepage is accessible
        Given I am on homepage
        Then I should see "Login"
        And the response status code should be 200

    Scenario: Search for a product
        Given I am on homepage
        When I fill in "search" with "black"
        And I press "search-button"
        Then I should be on "/search/"
        And I should see "black"
        And I should see "T-Shirts (2)"
        And I should see "€0.00 to €20.00 (2)"

    Scenario: Apply filters to a search result
        Given I am on homepage
        When I fill in "search" with "black"
        And I press "search-button"
        Then I should be on "/search/"
        And I should see "black"
        When I select the "price-0"
        And I press "Filter"
        Then I should see "€0.00 to €20.00 (2)"

    Scenario: Get facets for a taxon
        Given I am on homepage
        And I follow "T-Shirts"
        Then I should see there 2 products
        And I should see "€0.00 to €20.00 (2)"

    Scenario: Apply filters on a taxon
        Given I am on homepage
        And I follow "T-Shirts"
        Then I should see there 2 products
        And I should see "€0.00 to €20.00 (2)"
        When I select the "price-0"
        And I press "Filter"
        Then I should see "€0.00 to €20.00 (2)"

    Scenario: If I search for something that does not exists I should see a no products message
        Given I am on homepage
        When I fill in "search" with "crazy frog"
        And I press "search-button"
        Then I should be on "/search/"
        And I should see "There are no products to display"
