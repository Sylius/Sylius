@channels
Feature: Channel management
    In order to sell through different entry points
    As a store owner
    I want to configure channels

    Background:
        Given store has default configuration
          And the following zones are defined:
            | name | type    | members         |
            | USA  | country | United States   |
            | EU   | country | Germany, France |
          And there are following currencies configured:
            | code |
            | USD  |
            | EUR  |
          And there are following locales configured:
            | code  |
            | en_US |
            | fr_FR |
            | de_DE |
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
            | code   | name       | currencies | locales      | enabled |
            | WEB-US | mystore.us | USD        | en_US        | true    |
            | WEB-EU | mystore.eu | EUR, GBP   | fr_FR, de_DE | false   |
          And channel "WEB-US" has following configuration:
            | shipping | payment                  |
            | FedEx    | Credit Card (US), PayPal |
          And channel "WEB-EU" has following configuration:
            | shipping | payment                  |
            | DHL      | Credit Card (EU), PayPal |
          And I am logged in as administrator

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
          And I should see "Channel has been successfully created"

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

    Scenario: Enabling channel
        Given there is a disabled channel "WEB-VE"
          And I am on the channel index page
         When I click "Enable" near "WEB-VE"
         Then I should see enabled channel with name "WEB-VE" in the list
          And I should see "Channel has been successfully enabled"

    Scenario: Disabling channel
        Given there is an enabled channel "WEB-VE"
          And I am on the channel index page
         When I click "Disable" near "WEB-VE"
         Then I should see disabled channel with name "WEB-VE" in the list
          And I should see "Channel has been successfully disabled"
