@support
Feature: Support topics management
    In order to improve customer support
    As a store owner
    I want to manage support request topics

    Background:
        Given there is default currency configured
        And the following locales are defined:
            | code  |
            | en_US |
        And there is default channel configured
        And there are following support topics:
            | title               |
            | Order return        |
            | Delivery            |
            | Product information |
        And I am logged in as administrator

    Scenario: Browsing all support topics
        Given I am on the dashboard page
        When I follow "Configure Topics"
        Then I should be on the support topic index page
        And I should see 3 support topics in the list
        And I should see topic with title "Order return" in the list

    Scenario: Seeing empty index of support topics
        Given there are no support topics
        When I am on the support topic index page
        Then I should see "There are no support topics to display"

    Scenario: Accessing the support topic creation form
        Given I am on the dashboard page
        When I follow "Configure Topics"
        And I follow "Create support topic"
        Then I should be on the support topic creation page

    Scenario: Submitting the form without the title fails
        Given I am on the support topic creation page
        When I press "Create"
        Then I should still be on the support topic creation page
        And I should see "Please enter topic title"

    Scenario: Creating new support topic
        Given I am on the support topic creation page
        When I fill in "Title" with "Product request"
        And I press "Create"
        Then I should be on the support topic index page
        And I should see "Support topic has been successfully created"
        And I should see topic with title "Product request" in the list

    Scenario: Accessing the support topic edit form
        Given I am on the support topic index page
        When I click "edit" near "Order return"
        Then I should be editing support topic with title "Order return"

    Scenario: Updating the support topic title
        Given I am editing support topic with title "Delivery"
        And I fill in "Title" with "Product delivery"
        And I press "Save changes"
        Then I should be on the support topic index page
        And I should see support topic with title "Product delivery" in the list

    Scenario: Deleting a support topic
        Given I am on the support topic index page
        When I press "delete" near "Delivery"
        Then I should still be on the support topic index page
        And I should see "Support topic has been successfully deleted"
        And I should not see support topic with title "Delivery" in the list