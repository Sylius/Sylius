@addressing
Feature: Province unique code validation
  In order to avoid making mistakes when managing countries
  As an Administrator
  I want to be prevented from adding a new province with an existing code

  Background:
    Given the store operates in "Poland" country with province "Lodz" with "PL-LDZ" code
    And I am logged in as administrator

  @ui
  Scenario: Trying to add a new province with used code
    When I want to edit this country
    And I add the "Lublin" province with "PL-LDZ" code
    And I try to save my changes
    Then I should be notified that province code must be unique
    And this country should not have the "Lublin" province