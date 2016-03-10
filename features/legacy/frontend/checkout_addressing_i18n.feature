@legacy @i18n
Feature: Checkout addressing in preferred language
    In order to select the correct shipping country
    As a visitor
    I want to see the country list in my preferred language

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
            | code | zone    | name        |
            | SM1  | Germany | DHL Express |
            | SM2  | USA     | FedEx       |
        And all products are assigned to the default channel
        And there are following locales configured and assigned to the default channel:
            | code  |
            | en_US |
            | de_DE |

    Scenario: Seeing country in preferred language
        Given I am not logged in
        And I added product "PHP Top" to cart
        When I go to the checkout start page
        And I fill in guest email with "example@example.com"
        And I press "Proceed with your order"
        And I change the locale to "German (Germany)"
        Then I select "Deutschland" from "Land"
