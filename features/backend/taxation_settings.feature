@taxation
Feature: Taxation settings
    In order configure my store taxation system
    As a store owner
    I want to be able to edit taxation configuration

    Background:
        Given I am logged in as administrator
          And the following zones are defined:
            | name         | type    | members                       |
            | German lands | country | Germany, Austria, Switzerland |
            | USA          | country | United States                 |

    Scenario: Accessing the settings form
        Given I am on the dashboard page
         When I follow "Taxation settings"
         Then I should be on the taxation settings page

    Scenario: Saving the configuration
        Given I am on the taxation settings page
         When I press "Save changes"
         Then I should still be on the taxation settings page
          And I should see "Settings have been successfully updated."

    Scenario: Editing the default tax zone
        Given I am on the taxation settings page
         When I select "USA" from "Default tax zone"
          And I press "Save changes"
         Then I should still be on the taxation settings page
          And I should see "Settings have been successfully updated."
