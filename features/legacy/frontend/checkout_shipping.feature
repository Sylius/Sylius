@legacy @checkout
Feature: Checkout shipping
    In order to select shipping method
    As a visitor
    I want to be able to use checkout shipping step

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
            | code | zone         | name          | calculator | configuration | enabled |
            | SM1  | UK + Germany | DHL Express   | Flat rate  | Amount: 5000  | yes     |
            | SM2  | USA          | FedEx         | Flat rate  | Amount: 6500  | yes     |
            | SM3  | USA          | FedEx Premium | Flat rate  | Amount: 10000 | yes     |
            | SM4  | UK + Germany | UPS Ground    | Flat rate  | Amount: 20000 | no      |
        And the following payment methods exist:
            | code | name    | gateway | enabled |
            | PM1  | Offline | offline | yes     |
        And all products are assigned to the default channel
        And the default channel has following configuration:
            | taxon    | payment | shipping                                      |
            | Category | Offline | DHL Express, FedEx, FedEx Premium, UPS Ground |
        And I am logged in user
        And I added product "PHP Top" to cart

    Scenario: Only enabled and available methods are displayed to user for zone
            depending on the shipping address zone
        Given I go to the checkout start page
        And I fill in the shipping address to United Kingdom
        When I press "Continue"
        Then I should be on the checkout shipping step
        And I should see "DHL Express"
        But I should not see "FedEx"
        And I should not see "UPS Ground"

    Scenario: Shipping price is displayed when selecting the shipping method
        Given I go to the checkout start page
        And I fill in the shipping address to United Kingdom
        When I press "Continue"
        Then I should be on the checkout shipping step
        And I should see "DHL Express"
        And I should see "€50"

    Scenario: Listing methods for another zone
        Given I go to the checkout start page
        And I fill in the shipping address to United States
        When I press "Continue"
        Then I should be on the checkout shipping step
        And I should not see "DHL Express"
        But I should see "FedEx"
        And "FedEx Premium" should appear on the page

    Scenario: Selecting one of shipping methods
        Given I go to the checkout start page
        And I fill in the shipping address to United States
        And I press "Continue"
        When I select the "FedEx" radio button
        And I press "Continue"
        Then I should be on the checkout payment step

    Scenario: Trying to continue without selecting any method
        Given I go to the checkout start page
        And I fill in the shipping address to United States
        And I press "Continue"
        When I press "Continue"
        Then I should see "Please select shipping method"

    Scenario: Shipping costs affect the order total
        Given I go to the checkout start page
        And I fill in the shipping address to United States
        And I press "Continue"
        And I select the "FedEx" radio button
        And I press "Continue"
        And I select the "Offline" radio button
        When I press "Continue"
        Then I should be on the checkout finalize step
        And "Shipping total: €65.00" should appear on the page
        And "Total: €70.99" should appear on the page

    Scenario: Selecting shipping address that not match any shop shipping zones
        Given I go to the checkout start page
        And I fill in the shipping address to Poland
        When I press "Continue"
        Then I should be on the checkout addressing step
        And "We're sorry" should appear on the page

    Scenario: Returning to shipping method page
        Given I go to the checkout start page
        And I fill in the shipping address to United States
        And I press "Continue"
        When I select the "FedEx" radio button
        And I press "Continue"
        And I click "Back"
        Then I should see "FedEx"
