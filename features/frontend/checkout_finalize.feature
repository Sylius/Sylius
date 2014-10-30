@checkout
Feature: Checkout finalization
    In order to buy products
    As a visitor
    I want to be able to complete the checkout process

    Background:
        Given there are following taxonomies defined:
            | name     |
            | Category |
          And taxonomy "Category" has following taxons:
            | Clothing > PHP T-Shirts |
          And the following products exist:
            | name          | price | taxons       |
            | PHP Top       | 5.99  | PHP T-Shirts |
          And the following zones are defined:
            | name  | type    | members        |
            | UK    | country | United Kingdom |
          And the following shipping methods exist:
            | zone | name        |
            | UK   | DHL Express |
          And the following payment methods exist:
            | name  | gateway | enabled |
            | Dummy | dummy   | yes     |
          And there is default currency configured

    Scenario: Placing the order
        Given I am logged in user
          And I added product "PHP Top" to cart
          And I go to the checkout start page
          And I fill in the shipping address to United Kingdom
          And I press "Continue"
          And I select the "DHL Express" radio button
          And I press "Continue"
          And I select the "Dummy" radio button
          And I press "Continue"
         When I click "Place order"
         Then I should be on the store homepage
          And I should see "Thank you for your order!"
          And I am on my account orders page
          And I should see "All your orders"
          And I should see 1 orders in the list
          And I should see "000000001"

    Scenario: Placing the order as Guest without email address
        Given I am not logged in
          And I added product "PHP Top" to cart
          And I go to the checkout start page
         When I press "Proceed with your order"
         Then I should see "This value should not be blank."

    Scenario: Placing the order as Guest with invalid email address
        Given I am not logged in
          And I added product "PHP Top" to cart
          And I go to the checkout start page
         And I fill in "sylius_checkout_guest[email]" with "example"
          And I press "Proceed with your order"
         Then I should see "This value is not a valid email address."

    Scenario: Placing the order as Guest
        Given I am not logged in
          And I added product "PHP Top" to cart
          And I go to the checkout start page
          And I fill in "sylius_checkout_guest[email]" with "example@example.com"
          And I press "Proceed with your order"
          And I fill in the shipping address to United Kingdom
          And I press "Continue"
          And I select the "DHL Express" radio button
          And I press "Continue"
          And I select the "Dummy" radio button
          And I press "Continue"
         When I click "Place order"
         Then I should be on the store homepage
          And I should see "Thank you for your order!"