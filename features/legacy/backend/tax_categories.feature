@legacy @taxation
Feature: Tax categories
    In order to apply different tax rates to my products
    As a store owner
    I want to be able to manage tax categories

    Background:
        Given store has default configuration
        And there are following tax categories:
            | code | name        |
            | TC1  | Clothing    |
            | TC2  | Electronics |
        And I am logged in as administrator

    Scenario: Seeing index of all tax categories
        Given I am on the dashboard page
        When I follow "Taxation categories"
        Then I should be on the tax category index page
        And I should see 2 tax categories in the list

    Scenario: Seeing empty index of tax categories
        Given there are no tax categories
        When I am on the tax category index page
        Then I should see "There are no tax categories configured"

    Scenario: Accessing the tax category creation form
        Given I am on the dashboard page
        When I follow "Taxation categories"
        And I follow "Create tax category"
        Then I should be on the tax category creation page

    Scenario: Submitting form without specifying the name
        Given I am on the tax category creation page
        When I press "Create"
        Then I should still be on the tax category creation page
        And I should see "Please enter tax category name"

    Scenario: Creating new tax category
        Given I am on the tax category creation page
        When I fill in "Code" with "TC3"
        And I fill in "Name" with "Taxable goods"
        And I press "Create"
        Then I should be on the tax category index page
        And I should see "Tax category has been successfully created"

    Scenario: Created tax categories appear in the list
        Given I created tax category "Food" with code "TC3"
        When I go to the tax category index page
        Then I should see 3 tax categories in the list
        And I should see tax category with name "Food" in that list

    Scenario: Accessing the editing form from the list
        Given I am on the tax category index page
        When I click "Edit" near "Clothing"
        Then I should be editing tax category "Clothing"

    Scenario: Updating the tax category
        Given I am editing tax category "Clothing"
        When I fill in "Name" with "Clothing & Accessories"
        And I press "Save changes"
        Then I should be on the tax category index page
        And I should see "Tax category has been successfully updated"

    @javascript
    Scenario: Deleting tax category
        Given I am on the tax category index page
        When I click "Delete" near "Clothing"
        And I click "Delete" from the confirmation modal
        Then I should be on the tax category index page
        And I should see "Tax category has been successfully deleted"

    @javascript
    Scenario: Deleted tax category disappears from the list
        Given I am on the tax category index page
        When I click "Delete" near "Clothing"
        And I click "Delete" from the confirmation modal
        Then I should be on the tax category index page
        And I should not see tax category with name "Clothing" in that list

    Scenario: Cannot update tax category code
        When I am editing tax category "Clothing"
        Then the code field should be disabled

    Scenario: Try add tax category  with existing code
        Given I am on the tax category creation page
        When I fill in "Code" with "TC1"
        And I fill in "Name" with "Computers"
        And I press "Create"
        Then I should still be on the tax category creation page
        And I should see "The tax category with given code already exists"

    Scenario: Trying to create tax category leaving the code field blank
        Given I am on the tax category creation page
        When I fill in "Name" with "Computers"
        And I press "Create"
        Then I should still be on the tax category creation page
        And I should see "Please enter tax category code"
