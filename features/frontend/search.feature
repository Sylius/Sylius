@search
Feature: Search products
    In order to be able to find products
    As a visitor
    I want to be able to search the products

    Background:
        Given there is default currency configured
        And there are following taxonomies defined:
            | name     |
            | Category |
        And taxonomy "Category" has following taxons:
            | Clothing > T-Shirts     |
            | Clothing > PHP T-Shirts |
            | Clothing > Gloves       |
        And the following products exist:
            | name             | price | taxons       | description             |
            | Super T-Shirt    | 19.99 | T-Shirts     | super black t-shirt     |
            | Black T-Shirt    | 18.99 | T-Shirts     | black t-shirt           |
            | Sylius Tee       | 12.99 | PHP T-Shirts | a very nice php t-shirt |
            | Symfony T-Shirt  | 15.00 | PHP T-Shirts | symfony t-shirt         |
            | Doctrine T-Shirt | 15.00 | PHP T-Shirts | doctrine t-shirt        |
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

