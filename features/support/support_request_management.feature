@support
Feature: Support requests management
    In order to improve customer support
    As a store owner
    I want to manage support requests

    Background:
        Given there is default currency configured
        And there is default channel configured
        And there are following support topics:
            | title               |
            | Order return        |
            | Delivery            |
            | Product information |
        And there are following support requests:
            | topic               | firstName | lastName | email            | message                    |
            | Order return        | John      | Doe      | john@doe.com     | I want to return order     |
            | Delivery            | Joe       | Williams | joe@williams.com | What is my delivery status |
        And I am logged in as administrator

    Scenario: Browsing all support requests
        Given I am on the dashboard page
        When I follow "Requests"
        Then I should be on the support request index page
        And I should see 2 support requests in the list
        And I should see topic with topic "Order return" in the list

    Scenario: Seeing empty index of support requests
        Given there are no support requests
        When I am on the support request index page
        Then I should see "There are no support requests to display"

    Scenario: Accessing the support request creation form
        Given I am on the dashboard page
        When I follow "Requests"
        And I follow "Create support request"
        Then I should be on the support request creation page

    Scenario: Submitting the form without the required fields fails
        Given I am on the support request creation page
        When I press "Create"
        Then I should still be on the support request creation page
        And I should see "Please enter your first name"

    Scenario: Creating new support request
        Given I am on the support request creation page
        When I select "Delivery" from "Topic"
        And I fill in "First name" with "John"
        And I fill in "Last name" with "Doe"
        And I fill in "Email" with "john@example.com"
        And I fill in "Message" with "Example message"
        And I press "Create"
        Then I should be on the support request index page
        And I should see "Support request has been successfully created"
        And I should see support request with email "john@example.com" in the list

    Scenario: Accessing the support request edit form
        Given I am on the support request index page
        When I click "edit" near "john@doe.com"
        Then I should be editing support request with email "john@doe.com"

    Scenario: Updating the support request
        Given I am editing support request with email "john@doe.com"
        And I fill in "Email" with "john@example.com"
        And I press "Save changes"
        Then I should be on the support request index page
        And I should see support reqiest with email "john@example.com" in the list

    Scenario: Deleting a support request
        Given I am on the support request index page
        When I press "delete" near "john@doe.com"
        Then I should still be on the support request index page
        And I should see "Support request has been successfully deleted"
        And I should not see support request with email "jonh@doe.com" in the list
