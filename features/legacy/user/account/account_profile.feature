@legacy @account
Feature: User account profile edition
    In order to manage my personal information
    As a logged user
    I want to be able to edit my name and my email

    Background:
        Given store has default configuration
        And I am logged in user
        And I am on my account homepage

    Scenario: Viewing my personal information page
        Given I follow "My personal information"
        Then I should be on my account profile edition page

    Scenario: Editing my information with a blank email
        Given I am on my account profile edition page
        When I leave "Email" field blank
        And I fill in "First name" with "John"
        And I fill in "Last name" with "Doe"
        And I press "Save changes"
        Then I should still be on my account profile edition page
        And I should see "Please enter your email"

    Scenario: Editing my information with a blank first name
        Given I am on my account profile edition page
        When I fill in "Email" with "username@example.com"
        And I leave "First name" field blank
        And I fill in "Last name" with "Doe"
        And I press "Save changes"
        Then I should still be on my account profile edition page
        And I should see "Please enter your first name"

    Scenario: Editing my information with a blank last name
        Given I am on my account profile edition page
        When I fill in "Email" with "username@example.com"
        And I fill in "First name" with "John"
        And I leave "Last name" field blank
        And I press "Save changes"
        Then I should still be on my account profile edition page
        And I should see "Please enter your last name"

    Scenario: Editing my information with an invalid email
        Given I am on my account profile edition page
        When I fill in "Email" with "wrongemail"
        And I fill in "First name" with "John"
        And I fill in "Last name" with "Doe"
        And I press "Save changes"
        Then I should still be on my account profile edition page
        And I should see "This email is invalid"

    Scenario: Successfully editing my personal information
        Given I am on my account profile edition page
        When I fill in "Email" with "johndoe@example.com"
        And I fill in "First name" with "John"
        And I fill in "Last name" with "Doe"
        And I press "Save changes"
        Then I should be on my account profile page
        And I should see "Customer has been successfully updated"
