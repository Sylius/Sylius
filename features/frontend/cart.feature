Feature: Cart
    In order to select products for purchase
    As a visitor
    I want to be able to add products to cart

    Background:
        Given there are following taxonomies defined:
            | name     |
            | Category |
          And taxonomy "Category" has following taxons:
            | Clothing > T-Shirts     |
            | Clothing > PHP T-Shirts |
            | Clothing > Gloves       |
          And the following products exist:
            | name             | price | taxons       |
            | Super T-Shirt    | 19.99 | T-Shirts     |
            | Black T-Shirt    | 18.99 | T-Shirts     |
            | Sylius Tee       | 12.99 | PHP T-Shirts |
            | Symfony T-Shirt  | 15.00 | PHP T-Shirts |
            | Doctrine T-Shirt | 15.00 | PHP T-Shirts |

    Scenario: Adding simple product to cart via list
        Given I am on the store homepage
          And I follow "T-Shirts"
         When I press "Add to cart"
         Then I should be on the cart summary page
          And I should see "Item has been added to cart."
