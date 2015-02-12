@channels
Feature: Channel management
    In order to sell through different entry points
    As a store owner
    I want to configure channels

    Background:
        Given there is default currency configured
          And there is default channel configured
          And I am logged in as administrator
          And the following zones are defined:
            | name | type    | members                         |
            | USA  | country | USA                             |
            | EU   | country | Germany, United Kingdom, France |
          And there are following currencies configured:
            | code | exchange rate | enabled |
            | USD  | 0.76496       | yes     |
            | GBP  | 1.16998       | yes     |
            | EUR  | 1.00000       | yes     |
          And there are following locales configured:
            | code  | activated |
            | en_US | yes       |
            | en_GB | yes       |
            | fr_FR | yes       |
            | de_DE | yes       |
          And the following payment methods exist:
            | name             | gateway |
            | Credit Card (US) | stripe  |
            | Credit Card (EU) | adyen   |
            | PayPal           | paypal  |
          And the following shipping methods exist:
            | zone | name  |
            | USA  | FedEx |
            | EU   | DHL   |
          And there are following channels configured:
            | code   | name       | currencies | locales             |
            | WEB-US | mystore.us | EUR, GBP   | en_US               |
            | WEB-EU | mystore.eu | USD        | en_GB, fr_FR, de_DE |
          And channel "WEB-US" has following configuration:
            | shipping | payment                  |
            | FedEx    | Credit Card (US), PayPal |
          And channel "WEB-EU" has following configuration:
            | shipping | payment                  |
            | DHL      | Credit Card (EU), PayPal |

    Scenario: Browsing all configured channels
        Given I am on the dashboard page
         When I follow "Channels"
         Then I should be on the channel index page
          And I should see 3 channels in the list

    Scenario: Channel codes are visible in the grid
        Given I am on the dashboard page
         When I follow "Channels"
         Then I should be on the channel index page
          And I should see channel with code "WEB-US" in the list

    Scenario: Seeing empty index of channels
        Given there are no channels
         When I am on the channel index page
         Then I should see "There are no channels configured."

    Scenario: Accessing the channel creation form
        Given I am on the dashboard page
         When I follow "Channels"
          And I follow "Add channel"
         Then I should be on the channel creation page

    Scenario: Creating new channel
        Given I am on the channel creation page
          And I fill in "Code" with "MOBILE-US"
          And I fill in "Name" with "Mobile US"
          And I select "English (United States)" from "Locales"
          And I select "USD" from "Currencies"
          And I select "PayPal" from "Payment Methods"
          And I select "FedEx" from "Shipping Methods"
         When I press "Create"
         Then I should be on the channel index page
          And I should see "Channel has been successfully created."

    Scenario: Accessing the channel edit form
        Given I am on the channel index page
         When I click "edit" near "WEB-US"
         Then I should be editing channel with code "WEB-US"

    Scenario: Updating the channel
        Given I am editing channel with code "WEB-US"
          And I fill in "Name" with "mystore.com"
          And I press "Save changes"
         Then I should be on the channel index page
          And I should see channel with name "mystore.com" in the list

    @javascript
    Scenario: Deleting a channel
        Given I am on the channel index page
         When I press "delete" near "WEB-EU"
          And I confirm the deletion action
         Then I should still be on the channel index page
          And I should see "Channel has been successfully deleted."
          And I should not see channel with name "mystore.eu" in the list
