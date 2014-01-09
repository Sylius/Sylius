@account
Feature: User account addresses page
  In order to manage my addresses
  As a logged user
  I want to be able to add, edit or delete my shipping and billing addresses

  Background:
    Given I am logged in user
    And the following countries exist:
      | name    |
      | Germany |
      | Austria |
      | Poland  |
      | Finland |
      | USA     |
    And the following addresses exist:
      | user                | address                                               |
      | sylius@example.com  | Jan Kowalski, Heine-Straße 12, 99734, Berlin, Germany |
      | sylius@example.com  | Jan Kowalski, Fun-Straße 1, 90032, Vienna, Austria    |
      | sylius@example.com  | Jan Kowalski, Wawel 5 , 31-001, Kraków, Poland        |
    And I am on my account addresses page

  Scenario: Viewing my account addresses page
    Given I am on my account homepage
    And I follow "My address book"
    Then I should be on my account addresses page

  Scenario: Viewing that no address has been defined
    Given there are no addresses
      And I am on my account addresses page
     Then I should see "You have created no address yet"

  Scenario: Viewing only my addresses
    Given the following addresses exist:
      | user                       | address                                                             |
      | ianmurdock@example.com   | Ian Murdock, 3569 New York Avenue, CA 92801, San Francisco, USA   |
      | linustorvalds@example.com | Linus Torvalds, Väätäjänniementie 59, 00440, Helsinki, Finland    |
    Then I should see 3 addresses in the list

  Scenario: Accessing the creation address page
    Given I click "Create a new address"
     Then I should be on my account address creation page

  Scenario: Adding an address
    Given I am on my account address creation page
      And I fill in the users account address to Finland
      And I press "Create"
     Then I should be on my account addresses page
      And I should see "Address has been successfully created"
      And I should see 4 addresses in the list

  Scenario: Trying to add an address with empty fields
    Given I am on my account address creation page
      And I leave "First name" empty
      And I leave "Last name" empty
      And I leave "Country" empty
      And I leave "Street" empty
      And I leave "City" empty
      And I leave "Postcode" empty
      And I press "Create"
     Then I should still be on my account address creation page
      And I should see 6 validation errors

  Scenario: Deleting an address
    Given I press "Delete" near "GERMANY"
     Then I should see "Do you want to delete this item"
     When I press "delete"
     Then I should see 2 addresses in the list
      And I should not see "GERMANY"
      And I should still be on my account addresses page
      And I should see "Address has been successfully deleted"

  Scenario: Editing an address
    Given I press "Edit" near "GERMANY"
      And I fill in the users account address to Finland
      And I press "Save changes"
     Then I should see 3 addresses in the list
      And I should not see "GERMANY"
      And I should see "FINLAND"
      And I should still be on my account addresses page
      And I should see "Address has been successfully updated"

  Scenario: Trying to edit an address with empty fields
    Given I press "Edit" near "GERMANY"
      And I leave "First name" empty
      And I leave "Last name" empty
      And I leave "Country" empty
      And I leave "Street" empty
      And I leave "City" empty
      And I leave "Postcode" empty
      And I press "Save changes"
     Then I should see 6 validation errors

  Scenario: Viewing that no default shipping and billing addresses have been defined
    Given I am on my account addresses page
     Then I should see "No default billing address"
      And I should see "No default shipping address"

  Scenario: Setting an address as the default billing address
    Given I press "Set as default billing address" near "AUSTRIA"
     Then I should not see "No default billing address"
      And I should see a "#defaultBillingAddress" element near "AUSTRIA"
      And I should see "The address has been successfully set as your default billing address"

  Scenario: Setting an address as the default shipping address
    Given I press "Set as default shipping address" near "POLAND"
     Then I should not see "No default shipping address"
      And I should see a "#defaultShippingAddress" element near "POLAND"
      And I should see "The address has been successfully set as your default shipping address"
