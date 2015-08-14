@metadata
Feature: Metadata management
  In order to manage metadata on my store
  As a store owner
  I want to have easy and intuitive access to managing metadata

  Background:
    Given there is default channel configured
      And I am logged in as administrator
      And the following locales are defined:
        | code  |
        | en_US |
        | pl_PL |

  Scenario: Accessing default metadata edit form
    Given I am on the metadata index page
     When I click "Customize"
     Then I should be on the metadata edit page
      And I should see default metadata edit form