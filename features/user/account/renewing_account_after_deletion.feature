@account
  Feature: Registering account again after it has been deleted
    In order to set up a new account after I deleted it from the system
    As a Visitor
    I want to be able to register again with the same e-mail

  Background:
    Given there was account of "john@example.com"
    And there was an order made by this customer
    But his account was deleted

    Scenario: Registering again after my account deletion
      When I try to register again with email "john@example.com”
      Then I should be successfully registered

    Scenario: Maintaining orders history after my account renewal
      When I register with email "john@example.com"
      Then I should have 1 order in my orders history
