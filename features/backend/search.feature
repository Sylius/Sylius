@search_orm_only
Feature: Orm indexer event listener
    In order to have a consistent indexing when orm is enabled
    As a administrator
    I want to update the index when a product change occurs

    Background:
        Given there is default currency configured
        And I am logged in as administrator
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

    Scenario: Viewing the dashboard at website root
        Given I am on the dashboard page
         Then I should see "Administration dashboard"

    Scenario: Creating simple product and indexing it
        Given I am on the product creation page
         When I fill in the following:
            | Name        | Index test product      |
            | Description | Interesting description |
            | Price       | 29.99                   |
          And I press "Create"
         Then I should be on the page of product "Index test product"
          And I should see "Product has been successfully created."
          And I should find an indexed entry for "Interesting description"

    Scenario: Updating the product description and update the index accordingly
        Given I am editing product "Sylius Tee"
         When I fill in "Description" with "Another description"
          And I press "Save changes"
         Then I should be on the page of product "Sylius Tee"
          And I should see "Another description"
          And I should find an indexed entry for "Another description"

    Scenario: Deleting product should delete the index as well
        Given I am on the page of product "Sylius Tee"
         When I press "delete"
         Then I should be on the product index page
          And I should see "Product has been successfully deleted."
          And I should not find an indexed entry for "a very nice php t-shirt"