@support
Feature: Support tickets management
    In order to improve customer support
    As a store owner
    I want to manage support tickets

    Background:
        Given store has default configuration
          And there are following support categories:
            | title               |
            | Order return        |
            | Delivery            |
            | Product information |
          And there are following support tickets:
            | category     | firstName | lastName | email            | message                    |
            | Order return | John      | Doe      | john@doe.com     | I want to return order     |
            | Delivery     | Joe       | Williams | joe@williams.com | What is my delivery status |
          And I am logged in as administrator

    Scenario: Browsing all support tickets
        Given I am on the dashboard page
         When I follow "Tickets"
         Then I should be on the support ticket index page
          And I should see 2 support tickets in the list
          And I should see category with category "Order return" in the list

    Scenario: Seeing empty index of support tickets
        Given there are no support tickets
         When I am on the support ticket index page
         Then I should see "There are no support tickets to display"

    Scenario: Accessing the support ticket creation form
        Given I am on the dashboard page
         When I follow "Tickets"
          And I follow "Create support ticket"
         Then I should be on the support ticket creation page

    Scenario: Submitting the form without the required fields fails
        Given I am on the support ticket creation page
         When I press "Create"
         Then I should still be on the support ticket creation page
          And I should see "Please enter your first name"

    Scenario: Creating new support ticket
        Given I am on the support ticket creation page
         When I select "Delivery" from "Category"
          And I fill in "First name" with "John"
          And I fill in "Last name" with "Doe"
          And I fill in "Email" with "john@example.com"
          And I fill in "Message" with "Example message"
          And I press "Create"
         Then I should be on the support ticket index page
          And I should see "Support ticket has been successfully created"
          And I should see support ticket with email "john@example.com" in the list

    Scenario: Accessing the support ticket edit form
        Given I am on the support ticket index page
         When I click "edit" near "john@doe.com"
         Then I should be editing support ticket with email "john@doe.com"

    Scenario: Updating the support ticket
        Given I am editing support ticket with email "john@doe.com"
          And I fill in "Email" with "john@example.com"
          And I press "Save changes"
         Then I should be on the support ticket index page
          And I should see support reqiest with email "john@example.com" in the list

    Scenario: Deleting a support ticket
        Given I am on the support ticket index page
         When I press "delete" near "john@doe.com"
         Then I should still be on the support ticket index page
          And I should see "Support ticket has been successfully deleted"
          And I should not see support ticket with email "jonh@doe.com" in the list
