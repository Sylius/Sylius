@checkout
Feature: Checkout starting
    In order to buy products
    As a visitor
    I want to be able to use checkout process

    Background:
        Given there are following taxonomies defined:
            | name     |
            | Category |
          And taxonomy "Category" has following taxons:
            | Clothing > T-Shirts     |
            | Clothing > PHP T-Shirts |
          And there are following options:
            | name          | presentation | values           |
            | T-Shirt color | Color        | Red, Blue, Green |
          And the following products exist:
            | name          | price | options       | taxons       | variants selection |
            | Super T-Shirt | 20.00 | T-Shirt color | T-Shirts     | match              |
            | PHP Top       | 5.99  |               | PHP T-Shirts |                    |
          And product "Super T-Shirt" is available in all variations

    Scenario: There is no checkout for empty cart
        Given I am on the store homepage
         When I follow "View cart"
         Then I should be on the cart summary page
          And I should see "Your cart is empty"
          But I should not see "Checkout"

    Scenario: There is checkout button for filled cart
        Given I added product "PHP Top" to cart
         When I go to the cart summary page
         Then I should see 1 cart item in the list
         And I should see "Checkout"

    Scenario: Accessing checkout via cart
        Given I added product "PHP Top" to cart
         When I go to the cart summary page
          And I follow "Checkout"
         Then I should be on the checkout security step

    Scenario: Logged in users are starting checkout
              from the addressing step
        Given I am logged in user
          And I added product "PHP Top" to cart
         When I go to the checkout start page
         Then I should be redirected to the checkout addressing step

    Scenario: Not logged in users need to authenticate or register
              new account in the store
        Given I added product "PHP Top" to cart
         When I go to the checkout start page
         Then I should be redirected to the checkout security step
          And I should see "Existing Customer"
          And I should see "New Customer"
