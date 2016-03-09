@legacy @channel
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
            | code | name             | gateway |
            | PM1  | Credit Card (US) | stripe  |
            | PM2  | Credit Card (EU) | adyen   |
            | PM3  | PayPal           | paypal  |
        And the following shipping methods exist:
            | code | zone | name  |
            | SM1  | USA  | FedEx |
            | SM2  | EU   | DHL   |
        And there are following channels configured:
            | code   | name       | currencies | locales      |
            | WEB-US | mystore.us | USD        | en_US        |
            | WEB-EU | mystore.eu | EUR, GBP   | fr_FR, de_DE |
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
        When I click "Edit" near "WEB-US"
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
        When I press "Delete" near "WEB-EU"
        And I click "Delete" from the confirmation modal
        Then I should still be on the channel index page
        And I should see "Channel has been successfully deleted"
        And I should not see channel with name "mystore.eu" in the list

    Scenario: Cannot add channel without code
        Given I am on the channel creation page
        And I fill in "Name" with "Mobile US"
        And I select "English (United States)" from "Locales"
        And I select "USD" from "Currencies"
        And I select "PayPal" from "Payment Methods"
        And I select "FedEx" from "Shipping Methods"
        When I press "Create"
        Then I should still be on the channel creation page
        And I should see "Please enter channel code"

    Scenario: Cannot edit channel code
        When I am editing channel "mystore.us"
        Then the code field should be disabled

    Scenario: Try add channel with existing code
        Given I am on the channel creation page
        And I fill in "Code" with "WEB-US"
        And I fill in "Name" with "Mobile US"
        And I select "English (United States)" from "Locales"
        And I select "USD" from "Currencies"
        And I select "PayPal" from "Payment Methods"
        And I select "FedEx" from "Shipping Methods"
        When I press "Create"
        Then I should still be on the channel creation page
        And I should see "Channel code has to be unique"
