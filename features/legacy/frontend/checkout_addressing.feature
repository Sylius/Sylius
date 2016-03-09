@legacy @checkout
Feature: Checkout addressing
    In order to select billing and shipping addresses
    As a visitor
    I want to proceed through addressing checkout step

    Background:
        Given store has default configuration
        And there are following taxons defined:
            | code | name     |
            | RTX1 | Category |
        And taxon "Category" has following children:
            | Clothing[TX1] > PHP T-Shirts[TX2] |
        And the following products exist:
            | name    | price | taxons       |
            | PHP Top | 5.99  | PHP T-Shirts |
        And there are following users:
            | email            | password | enabled |
            | john@example.com | foo1     | yes     |
            | rick@example.com | bar1     | yes     |
        And the following zones are defined:
            | name         | type    | members                 |
            | UK + Germany | country | United Kingdom, Germany |
            | USA          | country | United States           |
        And there are following countries:
            | name           |
            | United States  |
            | United Kingdom |
            | Poland         |
            | Germany        |
        And the following shipping methods exist:
            | code | zone         | name        | calculator | configuration |
            | SM1  | UK + Germany | DHL Express | Flat rate  | Amount: 5000  |
            | SM2  | USA          | FedEx       | Flat rate  | Amount: 6500  |
        And all products are assigned to the default channel

    Scenario: Filling the shipping address
        Given I am logged in user
        And I added product "PHP Top" to cart
        When I go to the checkout start page
        And I fill in the shipping address to United Kingdom
        And I press "Continue"
        Then I should be on the checkout shipping step

    Scenario: Filling the shipping address as guest
        Given I am not logged in
        And I added product "PHP Top" to cart
        When I go to the checkout start page
        And I fill in guest email with "example@example.com"
        And I press "Proceed with your order"
        And I fill in the shipping address to United Kingdom
        And I press "Continue"
        Then I should be on the checkout shipping step

    Scenario: Using different billing address
        Given I am logged in user
        And I added product "PHP Top" to cart
        When I go to the checkout start page
        And I fill in the shipping address to Germany
        But I check "Use different address for billing?"
        And I fill in the billing address to United States
        And I press "Continue"
        Then I should be on the checkout shipping step

    Scenario: Using different billing address as guest
        Given I am not logged in
        And I added product "PHP Top" to cart
        When I go to the checkout start page
        And I fill in guest email with "example@example.com"
        And I press "Proceed with your order"
        And I fill in the shipping address to Germany
        But I check "Use different address for billing?"
        And I fill in the billing address to United States
        And I press "Continue"
        Then I should be on the checkout shipping step

    Scenario: Validating shipping country is entered
        Given I am not logged in
        And I added product "PHP Top" to cart
        When I go to the checkout start page
        And I fill in guest email with "example@example.com"
        And I press "Proceed with your order"
        And I press "Continue"
        Then I should see "Please select country"
