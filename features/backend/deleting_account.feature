@ui-user
Feature: Deleting the account in backend
  In order to delete Customer account on demand
  As an Administrator
  I want to be able to remove Customer account details from the system

    Background:
      Given there is user "theodore@example.com" identified by "pswd"
      And there is an administrator account
      And I am logged in as administrator

    @javascript
    Scenario: Deleting account should not delete customer details
      When I delete the account of "theodore@example.com"
      Then there should be customer with email "theodore@example.com"
      And there should be no account with email "theodore@example.com"

    @javascript
    Scenario: A customer with no user cannot be deleted
      Given the account of "theodore@example.com" was deleted
      When I access the "theodore@example.com" customer show page
      Then I should not see "Delete" button
