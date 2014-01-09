@checkout
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
            | email             | password | enabled |
            | john@example.com  | foo      | yes     |
            | rick@example.com  | bar      | yes     |
          And the following zones are defined:
            | name         | type    | members                 |
            | UK + Germany | country | United Kingdom, Germany |
            | USA          | country | USA                     |
          And there are following countries:
            | name           |
            | USA            |
            | United Kingdom |
            | Poland         |
            | Germany        |
          And the following shipping methods exist:
            | zone         | name          | calculator | configuration |
            | UK + Germany | DHL Express   | Flat rate  | Amount: 5000  |
            | USA          | FedEx         | Flat rate  | Amount: 6500  |
          And I am logged in user

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
