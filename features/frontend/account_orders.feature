Feature: User account orders page
  In order to follow my orders
  As a logged user
  I want to be able to track my pending orders and get an invoice for my delivered orders

  Background:
    Given I am logged in user
    And I am on my account homepage

  Scenario: Viewing my personal information page
    Given I follow "My orders / my invoices"
    Then I should be on my account orders page