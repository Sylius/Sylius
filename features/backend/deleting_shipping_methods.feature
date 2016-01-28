@shipping
Feature: Deleting a shipping method
  In order to remove not used or invalid shipping methods
  As an Administrator
  I want to be able to delete a shipping method

  Background:
    Given store has a shipping method "DHL Express"
    And there is a shipment using it
    And store has a shipping method "UPS Ground"
    And I am logged in as administrator

  Scenario: Being unable to delete a shipping method which is in use
    When I try to delete shipping method "DHL Express"
    Then I should be notified that it is in use
    And shipping method "DHL Express" should not be removed

  Scenario: Successfully deleting a shipping method when it's not used
    When I try to delete shipping method "UPS Ground"
    Then it should be successfully removed
