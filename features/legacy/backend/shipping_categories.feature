@legacy @shipping
Feature: Shipping categories
    In order to limit products to certain shipping methods
    As a store owner
    I want to be able to manage shipping categories

    Background:
        Given store has default configuration
        And there are following shipping categories:
            | code | name    |
            | SC1  | Regular |
            | SC2  | Heavy   |
        And I am logged in as administrator

    Scenario: Seeing index of all shipping categories
        Given I am on the dashboard page
        When I follow "Shipping categories"
        Then I should be on the shipping category index page
        And I should see 2 shipping categories in the list

    Scenario: Seeing empty index of shipping categories
        Given there are no shipping categories
        And I am on the shipping category index page
        Then I should see "There are no shipping categories configured"

    Scenario: Accessing the shipping category creation form
        Given I am on the dashboard page
        When I follow "Shipping categories"
        And I follow "Create shipping category"
        Then I should be on the shipping category creation page

    Scenario: Submitting invalid form without name
        Given I am on the shipping category creation page
        When I fill in "Code" with "SC1"
        And I press "Create"
        Then I should still be on the shipping category creation page
        And I should see "Please enter shipping category name"

    Scenario: Creating new shipping category
        Given I am on the shipping category creation page
        When I fill in "Name" with "Light"
        And I fill in "Code" with "SC32"
        And I press "Create"
        Then I should be on the shipping category index page
        And I should see "Shipping category has been successfully created"

    Scenario: Created shipping categories appear in the list
        Given I created shipping category "Light" with code "SC3"
        And I go to the shipping category index page
        Then I should see 3 shipping categories in the list
        And I should see shipping category with name "Light" in that list

    Scenario: Accessing the editing form from the list
        Given I am on the shipping category index page
        When I click "Edit" near "Heavy"
        Then I should be editing shipping category "Heavy"

    Scenario: Updating the shipping category
        Given I am editing shipping category "Heavy"
        When I fill in "Name" with "Light"
        And I press "Save changes"
        Then I should be on the shipping category index page
        And I should see "Shipping category has been successfully updated"

    Scenario: Cannot update shipping category code
        When I am editing shipping category "Heavy"
        Then the code field should be disabled

    Scenario: Try add shipping category with existing code
        Given I am on the shipping category creation page
        When I fill in "Name" with "Middle"
        And I fill in "Code" with "SC1"
        And I press "Create"
        Then I should still be on the shipping category creation page
        And I should see "The shipping category with given code already exists"

    Scenario: Submitting invalid form without code
        Given I am on the shipping category creation page
        When I fill in "Name" with "Middle"
        And I press "Create"
        Then I should still be on the shipping category creation page
        And I should see "Please enter shipping category code"

    @javascript
    Scenario: Deleting shipping category from list
        Given I am on the shipping category index page
        When I click "Delete" near "Heavy"
        And I click "Delete" from the confirmation modal
        Then I should be on the shipping category index page
        And I should see "Shipping category has been successfully deleted"
        And I should not see shipping category with name "Heavy" in that list
