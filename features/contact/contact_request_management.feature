@contact
Feature: Contact requests management
    In order to improve customer support
    As a store owner
    I want to manage contact requests

    Background:
        Given there is default currency configured
        And there are following contact topics:
            | title               |
            | Order return        |
            | Delivery            |
            | Product information |
        And there are following contact requests:
            | topic               | firstName | lastName | email            | message                    |
            | Order return        | John      | Doe      | john@doe.com     | I want to return order     |
            | Delivery            | Joe       | Williams | joe@williams.com | What is my delivery status |
        And I am logged in as administrator

    Scenario: Browsing all contact requests
        Given I am on the dashboard page
        When I follow "Contact requests"
        Then I should be on the contact request index page
        And I should see 2 contact requests in the list
        And I should see topic with title "Order return" in the list

    Scenario: Seeing empty index of contact requests
        Given there are no contact requests
        When I am on the contact request index page
        Then I should see "There are no contact requests to display."

    Scenario: Accessing the contact request creation form
        Given I am on the dashboard page
        When I follow "Contact requests"
        And I follow "Create contact request"
        Then I should be on the contact request creation page

    Scenario: Submitting the form without the required fields fails
        Given I am on the contact request creation page
        When I press "Create"
        Then I should still be on the contact request creation page
        And I should see "Please enter first name."

    Scenario: Creating new contact request
        Given I am on the contact request creation page
        When I select "Delivery" from "Topic"
        And I fill in "First name" with "John"
        And I fill in "Last name" with "Doe"
        And I fill in "Email" with "john@example.com"
        And I fill in "Message" with "Example message"
        And I press "Create"
        Then I should be on the contact request index page
        And I should see "Contact request has been successfully created."
        And I should see contact request with email "john@example.com" in the list

    Scenario: Accessing the contact request edit form
        Given I am on the contact request index page
        When I click "edit" near "john@doe.com"
        Then I should be editing contact request with email "john@doe.com"

    Scenario: Updating the contact request
        Given I am editing contact request with email "john@doe.com"
        And I fill in "Email" with "john@example.com"
        And I press "Save changes"
        Then I should be on the contact request index page
        And I should see contact reqiest with email "john@example.com" in the list

    Scenario: Deleting a contact request
        Given I am on the contact request index page
        When I press "delete" near "john@doe.com"
        Then I should still be on the contact request index page
        And I should see "Contact request has been successfully deleted."
        And I should not see contact request with email "jonh@doe.com" in the list
