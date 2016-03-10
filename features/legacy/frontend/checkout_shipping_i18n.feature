@legacy @i18n
Feature: Checkout shipping in preferred language
    In order to understand shipping methods
    As a visitor
    I want to be able to see shipping methods in my preferred language

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
            | name    | type    | members       |
            | Germany | country | Germany       |
            | USA     | country | United States |
        And there are following countries:
            | name          |
            | United States |
            | Germany       |
        And the following shipping methods exist:
            | code | zone    | name       |
            | SM1  | USA     | FedEx      |
            | SM2  | Germany | UPS Ground |
        And all products are assigned to the default channel
        And there are following locales configured and assigned to the default channel:
            | code  |
            | en_US |
            | de_DE |
        And the shipping method translations exist:
            | shipping method | name     | locale |
            | UPS Ground      | UPS Land | de_DE  |
        And the default channel has following configuration:
            | taxon    | shipping          |
            | Category | FedEx, UPS Ground |
        And I am logged in user
        And I added product "PHP Top" to cart

    Scenario: Seeing shipping method in my preferred language
        Given I go to the checkout start page
        And I fill in the shipping address to Germany
        When I press "Continue"
        And I change the locale to "German (Germany)"
        Then I should be on the checkout shipping step
        And I should see "UPS Land"
        And I should not see "UPS Ground"
