@contact
Feature: Contact requesting
    In order to get help from customer support
    As a visitor
    I want to send message to store owner

    Background:
        Given there is default currency configured
        And there are following contact topics:
            | title               |
            | Order return        |
            | Delivery            |
            | Product information |
        And I am logged in as administrator

    Scenario: Submitting the form without the required fields fails
        Given I am on the contact page
        When I press "Send"
        Then I should still be on the contact page
        And I should see "Please enter first name."

    Scenario: Submitting contact form
        Given I am on the contact page
        When I fill in "First name" with "John"
        And I fill in "Last name" with "Doe"
        And I fill in "Email" with "john@doe.com"
        And I fill in "Message" with "Hello!"
        And I select "Delivery" from "Topic"
        And I press "Send"
        Then I should be on the contact page
        And I should see "Contact has been successfully sent."