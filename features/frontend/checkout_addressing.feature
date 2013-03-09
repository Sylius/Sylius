Feature: Checkout addressing
    In order to select billing and shipping addresses
    As a visitor
    I want to proceed through addressing checkout step

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
          And I am logged in user
          And there are following countries:
            | name           |
            | USA            |
            | United Kingdom |
            | Poland         |
            | Germany        |

    Scenario: Filling the shipping address
        Given I added product "PHP Top" to cart
         When I go to the checkout start page
          And I fill in the shipping address to United Kingdom
          And I press "Continue"
         Then I should be on the checkout shipping step

    Scenario: Using different billing address
        Given I added product "PHP Top" to cart
         When I go to the checkout start page
          And I fill in the shipping address to Germany
          But I check "Use different address for billing?"
          And I fill in the billing address to USA
          And I press "Continue"
         Then I should be on the checkout shipping step
