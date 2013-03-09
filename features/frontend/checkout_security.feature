Feature: Checkout security
    In order to authenticate
    As a visitor
    I want to login or register during checkout

    Background:
        Given there are following taxonomies defined:
            | name     |
            | Category |
          And taxonomy "Category" has following taxons:
            | Clothing > PHP T-Shirts |
          And the following products exist:
            | name          | price | taxons       |
            | PHP Top       | 5.99  | PHP T-Shirts |
          And there are following users:
            | username | password | enabled |
            | john     | foo      | yes     |
            | rick     | bar      | yes     |

    Scenario: Trying to sign in with bad credentials
              during the checkout
        Given I added product "PHP Top" to cart
          And I go to the checkout start page
         When I fill in the following:
            | Username | john     |
            | Password | habababa |
          And I press "Login"
         Then I should see "Bad credentials"

    Scenario: Signing in during the checkout
        Given I added product "PHP Top" to cart
          And I go to the checkout start page
         When I fill in the following:
            | Username | john |
            | Password | foo  |
          And I press "Login"
         Then I should be redirected to the checkout addressing step

    Scenario: Creating account during the checkout
        Given I added product "PHP Top" to cart
          And I go to the checkout start page
         When I fill in the following:
            | Username     | mike             |
            | Email        | mike@example.com |
            | Password     | mikepass         |
            | Verification | mikepass         |
          And I press "Register"
         Then I should be redirected to the checkout addressing step
