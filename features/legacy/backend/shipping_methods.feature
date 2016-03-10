@legacy @shipping
Feature: Shipping methods
    In order to apply proper shipping to my merchandise
    As a store owner
    I want to be able to configure shipping methods

    Background:
        Given store has default configuration
        And the following zones are defined:
            | name         | type    | members                 |
            | UK + Germany | country | United Kingdom, Germany |
            | USA          | country | United States           |
        And there are following shipping categories:
            | code | name    |
            | CS1  | Regular |
            | CS2  | Heavy   |
        And the following shipping methods exist:
            | code | category | zone         | name         |
            | SM1  | Regular  | USA          | FedEx        |
            | SM2  | Heavy    | UK + Germany | DHL          |
            | SM3  |          | UK + Germany | DHL Express  |
            | SM4  |          | USA          | TurboPackage |
        And shipping method "DHL Express" has following rules defined:
            | type       | configuration |
            | Item total | Amount: 10000 |
        And shipping method "TurboPackage" has following rules defined:
            | type   | configuration     |
            | Weight | Min: 10, Max: 500 |
        And I am logged in as administrator

    Scenario: Seeing index of all shipping methods
        Given I am on the dashboard page
        When I follow "Shipping methods"
        Then I should be on the shipping method index page
        And I should see 4 shipping methods in the list

    Scenario: Seeing empty index of shipping methods
        Given there are no shipping methods
        When I am on the shipping method index page
        Then I should see "There are no shipping methods configured"

    Scenario: Accessing the shipping method creation form
        Given I am on the dashboard page
        When I follow "Shipping methods"
        And I follow "Create shipping method"
        Then I should be on the shipping method creation page

    Scenario: Submitting invalid form without name
        Given I am on the shipping method creation page
        When I press "Create"
        Then I should still be on the shipping method creation page
        And I should see "Please enter shipping method name"

    @javascript
    Scenario: Creating new shipping method for specific zone
        Given I am on the shipping method creation page
        When I fill in "Name" with "FedEx World Shipping"
        And I fill in "Code" with "SM5"
        And I select "USA" from "Zone"
        And I select "Flat rate per unit" from "Calculator"
        And I fill in "Amount" with "10"
        And I press "Create"
        Then I should be on the page of shipping method "FedEx World Shipping"
        And I should see "Shipping method has been successfully created"
        And I should see "USA"

    @javascript
    Scenario: Creating new shipping method with flat rate per unit
        Given I am on the shipping method creation page
        When I fill in "Name" with "FedEx World Shipping"
        And I fill in "Code" with "SM6"
        And I select "USA" from "Zone"
        And I select "Flat rate per unit" from "Calculator"
        And I fill in "Amount" with "10"
        And I press "Create"
        Then I should be on the page of shipping method "FedEx World Shipping"
        And I should see "Shipping method has been successfully created"

    @javascript
    Scenario: Creating new shipping method with flat rate per shipment
        Given I am on the shipping method creation page
        When I fill in "Name" with "FedEx World Shipping"
        And I fill in "Code" with "SM7"
        And I select "Flat rate per shipment" from "Calculator"
        And I fill in "Amount" with "10"
        And I press "Create"
        Then I should be on the page of shipping method "FedEx World Shipping"
        And I should see "Shipping method has been successfully created"

    @javascript
    Scenario: Creating new shipping method with flexible rate
        Given I am on the shipping method creation page
        When I fill in "Name" with "FedEx World Shipping"
        And I fill in "Code" with "SM7"
        And I select "Flexible rate" from "Calculator"
        And I fill in "First unit cost" with "100"
        And I fill in "Additional unit cost" with "10"
        And I fill in "Limit additional units" with "5"
        And I press "Create"
        Then I should be on the page of shipping method "FedEx World Shipping"
        And I should see "Shipping method has been successfully created"

    Scenario: Created shipping methods appear in the list
        Given I created shipping method "FedEx World Shipping" with code "SM7" and zone "USA"
        And I go to the shipping method index page
        Then I should see 5 shipping methods in the list
        And I should see shipping method with name "FedEx World Shipping" in that list

    Scenario: Accessing the shipping method editing form
        Given I am on the page of shipping method "DHL"
        When I follow "Edit"
        Then I should be editing shipping method "DHL"

    Scenario: Accessing the editing form from the list
        Given I am on the shipping method index page
        When I click "Edit" near "FedEx"
        Then I should be editing shipping method "FedEx"

    @javascript
    Scenario: Updating the shipping method with js modal
        Given I am editing shipping method "FedEx"
        When I fill in "Name" with "General Shipping"
        And I press "Save changes"
        Then I should be on the page of shipping method "General Shipping"

    Scenario: Enabling shipping method
        Given there is a disabled shipping method "UPS" with code "SM7" and zone "USA"
        And I am on the shipping method index page
        When I click "Enable" near "UPS"
        Then I should see enabled shipping method with name "UPS" in the list
        And I should see "Shipping method has been successfully enabled"

    Scenario: Disabling shipping method
        Given there is an enabled shipping method "UPS" with code "SM7" and zone "USA"
        And I am on the shipping method index page
        When I click "Disable" near "UPS"
        Then I should see disabled shipping method with name "UPS" in the list
        And I should see "Shipping method has been successfully disabled"

    Scenario: Cannot update shipping method code
        When I am editing shipping method "FedEx"
        Then the code field should be disabled

    Scenario: Try add shipping method with existing code
        Given I am on the shipping method creation page
        When I fill in "Name" with "MegaPackage"
        And I fill in "Code" with "SM1"
        And I press "Create"
        Then I should still be on the shipping method creation page
        And I should see "The shipping method with given code already exists"

    Scenario: Submitting invalid form without code
        Given I am on the shipping method creation page
        When I fill in "Name" with "MegaPackage"
        And I press "Create"
        Then I should still be on the shipping method creation page
        And I should see "Please enter shipping method code"
