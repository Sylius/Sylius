@support
Feature: Support ticketing
    In order to get help from customer support
    As a visitor
    I want to send message to store owner

    Background:
        Given store has default configuration
          And there are following support categories:
            | title               |
            | Order return        |
            | Delivery            |
            | Product information |
          And I am logged in as administrator

    Scenario: Submitting the form without the required fields fails
        Given I am on the support page
         When I press "Send"
         Then I should still be on the support page
          And I should see "Please enter your first name"

    Scenario: Submitting support form
        Given I am on the support page
         When I fill in "First name" with "John"
          And I fill in "Last name" with "Doe"
          And I fill in "Email" with "john@doe.com"
          And I fill in "Message" with "Hello!"
          And I select "Delivery" from "Category"
          And I press "Send"
         Then I should be on the support page
          And I should see "Support ticket has been successfully created"
