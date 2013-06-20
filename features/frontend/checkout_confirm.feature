Feature: Order confirmation
    In order to buy products
    As a visitor
    I want to be able to confirm placed order

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
            | name        | gateway | enabled |
            | Credit Card | stripe  | yes     |

    Scenario: Placing the order
        Given I am logged in user
          And I added product "PHP Top" to cart
          And I go to the checkout start page
          And I fill in the shipping address to United Kingdom
          And I press "Continue"
          And I select the "DHL Express" radio button
          And I press "Continue"
          And I select the "Credit Card" radio button
          And I press "Continue"
         When I click "Place order"
         Then an email should have been sent to "email@foo.com"

    Scenario: Confirm order
        Given I am logged in user
          And I added product "PHP Top" to cart
          And I go to the checkout start page
          And I fill in the shipping address to United Kingdom
          And I press "Continue"
          And I select the "DHL Express" radio button
          And I press "Continue"
          And I select the "Credit Card" radio button
          And I press "Continue"
         When I click "Place order"
          And I click on order confirmation link
         Then I should see "Your order has been confirmed."
