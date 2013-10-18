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
