@cart
Feature: Cart
    In order to do shopping comfortably
    As a visitor
    I want my cart to be maintained after I log in

    Background:
          Given there are following users:
              | email       | password | enabled |
              | bar@foo.com | foo1     | yes     |
          And there are following taxonomies defined:
              | name     |
              | Category |
          And taxonomy "Category" has following taxons:
              | Clothing > PHP T-Shirts |
          And the following products exist:
              | name    | price | taxons       |
              | PHP Top | 85    | PHP T-Shirts |
          And there is default currency configured
          And there is default channel configured
          And channel "DEFAULT-WEB" has following configuration:
              | taxonomy |
              | Category |
          And all products assigned to "DEFAULT-WEB" channel

    Scenario: The cart is maintained after user log in
        Given I am on the store homepage
          And I follow "PHP T-Shirts"
          And I click "PHP Top"
          And I press "Add to cart"
          And I follow "Login"
          And I fill in the following:
              | Email    | bar@foo.com |
              | Password | foo1        |
          And I press "Login"
          And I follow "View cart"
         Then I should be on the cart summary page
          And I should see 1 item in the list

    Scenario: The cart is maintained after user registration
        Given I am on the store homepage
          And I follow "PHP T-Shirts"
          And I click "PHP Top"
          And I press "Add to cart"
          And I follow "Register"
         When I fill in the following:
              | First name   | John        |
              | Last name    | Doe         |
              | Email        | foo@bar.com |
              | Password     | bar1        |
              | Verification | bar1        |
          And I press "Register"
          And I follow "View cart"
         Then I should be on the cart summary page
          And I should see 1 item in the list
