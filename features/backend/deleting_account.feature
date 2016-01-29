@account
Feature: Deleting the account in backend
  In order to delete Customer account on demand
  As an Administrator
  I want to be able to remove Customer account details from the system

  Background:
    Given there is user "john@example.com" identified by "pswd"
    And I am logged in as administrator

  Scenario: Deleting account should not delete customer details
    When I delete the account of "john@example.com"
    Then there should be customer with email "john@example.com"
    And there should be no account with email "john@example.com"

  Scenario: A Customer with no User cannot be deleted
    Given the account of "john@example.com" was deleted
    When I access the "john@example.com" customer show page
    Then I should not see "Delete" button
