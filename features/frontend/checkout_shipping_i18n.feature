@i18n
Feature: Checkout shipping in preferred language
    In order to understand shipping methods
    As a visitor
    I want to be able to see shipping methods in my preferred language

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
            | Germany        |
          And the following shipping methods exist:
            | zone         | name       | calculator | configuration | enabled |
            | USA          | FedEx      | Flat rate  | Amount: 6500  | yes     |
            | UK + Germany | UPS Ground | Flat rate  | Amount: 20000 | yes     |
          And all products are assigned to the default channel
          And there are following locales configured:
            | code  |
            | en_US |
            | de_DE |
          And the shipping method translations exist
            | shipping_method | name        | locale |
            | UPS Ground      | UPS Land    | de     |
          And I am logged in user
          And I added product "PHP Top" to cart

    Scenario: Seeing shipping method in my preferred language
        Given I go to the checkout start page
          And I fill in the shipping address to Germany
         When I press "Continue"
          And I change the locale to "German"
         Then I should be on the checkout shipping step
          And I should see "UPS Land"
          And I should not see "UPS Ground"

