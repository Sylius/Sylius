@legacy @taxation
Feature: Tax rates
    In order to apply proper taxation to my merchandise
    As a store owner
    I want to be able to configure tax rates

    Background:
        Given store has default configuration
        And the following zones are defined:
            | name         | type    | members                 |
            | UK + Germany | country | United Kingdom, Germany |
            | USA          | country | United States           |
        And there are following tax categories:
            | code | name        |
            | TC1  | Clothing    |
            | TC2  | Electronics |
            | TC3  | Food        |
        And the following tax rates exist:
            | code | category    | zone         | name                  | amount |
            | TR1  | Clothing    | UK + Germany | UK+DE Clothing Tax    | 20%    |
            | TR2  | Electronics | UK + Germany | UK+DE Electronics Tax | 23%    |
            | TR3  | Clothing    | USA          | US Clothing Tax       | 8%     |
            | TR4  | Electronics | USA          | US Electronics Tax    | 10%    |
        And I am logged in as administrator

    Scenario: Seeing index of all tax rates
        Given I am on the dashboard page
        When I follow "Tax rates"
        Then I should be on the tax rate index page
        And I should see 4 tax rates in the list

    Scenario: Seeing empty index of tax rates
        Given there are no tax rates
        And I am on the tax rate index page
        Then I should see "There are no tax rates configured"

    Scenario: Accessing the tax rate creation form
        Given I am on the dashboard page
        When I follow "Tax rates"
        And I follow "Create tax rate"
        Then I should be on the tax rate creation page

    Scenario: Submitting invalid form without name
        Given I am on the tax rate creation page
        When I press "Create"
        Then I should still be on the tax rate creation page
        And I should see "Please enter tax rate name"

    Scenario: Trying to create tax leaving the amount field blank
        Given I am on the tax rate creation page
        When I fill in "Code" with "TR5"
        And I fill in "Name" with "US Food Tax"
        And I leave "Amount" empty
        And I press "Create"
        Then I should still be on the tax rate creation page
        And I should see "Please enter tax rate amount"

    Scenario: Creating new tax rate
        Given I am on the tax rate creation page
        When I fill in "Code" with "TR5"
        And I fill in "Name" with "US Food Tax"
        And I fill in "Amount" with "30"
        And I select "USA" from "Zone"
        When I press "Create"
        Then I should be on the page of tax rate "US Food Tax"
        And I should see "Tax rate has been successfully created"

    Scenario: Creating tax rate included in price
        Given I am on the tax rate creation page
        When I fill in "Code" with "TR5"
        And I fill in "Name" with "EU VAT"
        And I fill in "Amount" with "19"
        And I select "UK + Germany" from "Zone"
        And I check "Included in price?"
        When I press "Create"
        Then I should be on the page of tax rate "EU VAT"
        And I should see "Tax rate has been successfully created"

    Scenario: Created tax rates appear in the list
        Given I created 18% tax "Food Tax" with code "TR5" for category "Food" with zone "USA"
        And I go to the tax rate index page
        Then I should see 5 tax rates in the list
        And I should see tax rate with name "Food Tax" in that list

    Scenario: Accessing the tax rate editing form
        Given I am on the page of tax rate "US Clothing Tax"
        And I follow "Edit"
        Then I should be editing tax rate "US Clothing Tax"

    Scenario: Accessing the editing form from the list
        Given I am on the tax rate index page
        And I click "Edit" near "US Clothing Tax"
        Then I should be editing tax rate "US Clothing Tax"

    Scenario: Updating the tax rate
        Given I am on the page of tax rate "UK+DE Clothing Tax"
        And I follow "Edit"
        When I fill in "Name" with "General Tax"
        And I press "Save changes"
        Then I should be on the page of tax rate "General Tax"

    Scenario: Deleting tax rate
        Given I am on the page of tax rate "US Clothing Tax"
        When I press "Delete"
        Then I should see "Do you want to delete this item"
        When I press "Delete"
        Then I should be on the tax rate index page
        And I should see "Tax rate has been successfully deleted"

    @javascript
    Scenario: Deleting tax rate
        Given I am on the page of tax rate "US Clothing Tax"
        When I press "Delete"
        And I click "Delete" from the confirmation modal
        Then I should be on the tax rate index page
        And I should see "Tax rate has been successfully deleted"

    @javascript
    Scenario: Deleted tax rate disappears from the list
        Given I am on the page of tax rate "US Electronics Tax"
        When I press "Delete"
        And I click "Delete" from the confirmation modal
        Then I should be on the tax rate index page
        And I should not see tax rate with name "US Electronics Tax" in that list

    @javascript
    Scenario: Deleting tax rate from the list
        Given I am on the tax rate index page
        When I click "Delete" near "US Electronics Tax"
        And I click "Delete" from the confirmation modal
        Then I should still be on the tax rate index page
        And "Tax rate has been successfully deleted" should appear on the page
        But I should not see tax rate with name "US Electronics Tax" in that list

    Scenario: Accessing tax rate details page via list
        Given I am on the tax rate index page
        When I click "Details" near "US Electronics Tax"
        Then I should be on the page of tax rate "US Electronics Tax"

    Scenario: Displaying the tax rate amount on details page
        Given I am on the page of tax rate "US Clothing Tax"
        Then I should see "8%"

    Scenario: Cannot update tax rate code
        When I am editing tax rate "UK+DE Clothing Tax"
        Then the code field should be disabled

    Scenario: Try add tax rate  with existing code
        Given I am on the tax rate creation page
        When I fill in "Code" with "TR1"
        And I fill in "Name" with "US Food Tax"
        And I fill in "Amount" with "30"
        And I press "Create"
        Then I should still be on the tax rate creation page
        And I should see "The tax rate with given code already exists"

    Scenario: Trying to create tax rate leaving the code field blank
        Given I am on the tax rate creation page
        When I fill in "Name" with "US Food Tax"
        And I fill in "Amount" with "30"
        And I press "Create"
        Then I should still be on the tax rate creation page
        And I should see "Please enter tax rate code"
