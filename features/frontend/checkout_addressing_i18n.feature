@i18n
Feature: Checkout addressing in preferred language
    In order to select the correct shipping country
    As a visitor
    I want to see the country list in my preferred language

    Background:
        Given store has default configuration
          And there are following taxonomies defined:
            | name     |
            | Category |
          And taxonomy "Category" has following taxons:
            | Clothing > PHP T-Shirts |
          And the following products exist:
            | name          | price | taxons       |
            | PHP Top       | 5.99  | PHP T-Shirts |
          And the following zones are defined:
            | name         | type    | members                 |
            | UK + Germany | country | United Kingdom, Germany |
            | USA          | country | United States           |
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
          And all products are assigned to the default channel
          And there are following locales configured:
            | code  | enabled |
            | en_US | yes     |
            | de_DE | yes     |

    Scenario: Seeing country in preferred language
        Given I am not logged in
          And I added product "PHP Top" to cart
         When I go to the checkout start page
          And I fill in guest email with "example@example.com"
          And I press "Proceed with your order"
          And I change the locale to "German"
         Then I select "Deutschland" from "Land"
