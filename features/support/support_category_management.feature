@support
Feature: Support categories management
    In order to improve customer support
    As a store owner
    I want to manage support ticket categories

    Background:
        Given there is default currency configured
        And the following locales are defined:
            | code  |
            | en_US |
        And there is default channel configured
        And there are following support categories:
            | title               |
            | Order return        |
            | Delivery            |
            | Product information |
        And I am logged in as administrator

    Scenario: Browsing all support categories
        Given I am on the dashboard page
        When I follow "Configure Categories"
        Then I should be on the support category index page
        And I should see 3 support categories in the list
        And I should see category with title "Order return" in the list

    Scenario: Seeing empty index of support categories
        Given there are no support categories
        When I am on the support category index page
        Then I should see "There are no support categories to display"

    Scenario: Accessing the support category creation form
        Given I am on the dashboard page
        When I follow "Configure Categories"
        And I follow "Create support category"
        Then I should be on the support category creation page

    Scenario: Submitting the form without the title fails
        Given I am on the support category creation page
        When I press "Create"
        Then I should still be on the support category creation page
        And I should see "Please enter category title"

    Scenario: Creating new support category
        Given I am on the support category creation page
        When I fill in "Title" with "Product ticket"
        And I press "Create"
        Then I should be on the support category index page
        And I should see "Support category has been successfully created"
        And I should see category with title "Product ticket" in the list

    Scenario: Accessing the support category edit form
        Given I am on the support category index page
        When I click "edit" near "Order return"
        Then I should be editing support category with title "Order return"

    Scenario: Updating the support category title
        Given I am editing support category with title "Delivery"
        And I fill in "Title" with "Product delivery"
        And I press "Save changes"
        Then I should be on the support category index page
        And I should see support category with title "Product delivery" in the list

    Scenario: Deleting a support category
        Given I am on the support category index page
        When I press "delete" near "Delivery"
        Then I should still be on the support category index page
        And I should see "Support category has been successfully deleted"
        And I should not see support category with title "Delivery" in the list