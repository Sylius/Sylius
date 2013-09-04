@account
Feature: User account addresses page
  In order to manage my addresses
  As a logged user
  I want to be able to add, edit or delete my shipping and billing addresses

  Background:
    Given I am logged in user
      And I am on my account addresses page
      And the following addresses exist:
        | sylius@example.com  | Jan Kowalski, Heine-Straße 12, 99734, Berlin, Germany  |
        | sylius@example.com  | Jan Kowalski, Fun-Straße 1, 90032, Vienna, Austria      |
        | sylius@example.com  | Jan Kowalski, Wawel 5 , 31-001, Kraków, Poland          |

  Scenario: Viewing that no address has been defined
    Given there are no address
     Then I should see "No address has been defined"

  Scenario: Viewing only my addresses
    Given the following addresses exist:
      | user                       | address                                                                  |
      | ianmurdock@example.com    | Ian Murdock, 3569 New York Avenue, CA 92801, San Francisco, USA   |
      | linustorvalds@example.com | Linus Torvalds, Väätäjänniementie 59, 00440, Helsinki, Finland    |
      | tmorel@example.com         | Théophile Morel, 17 avenue Jean Portalis, 33000, Bordeaux, France |
    Then I should see 3 addresses in the list

  Scenario: Accessing the creation address page
    Given I click "Add a new address"
     Then I should be on my account address creation page

  Scenario: Adding an address
    Given I am on my account address creation page
      And I fill in the users account address to France
      And I press "Create"
     Then I should be on my account addresses page
      And I should see "Your address has been created"
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
      And I should see 6 fields on error

  Scenario: Deleting an address
    Given I press "Delete" near "Germany"
     Then I should see 2 addresses in the list
      And I should not see "Germany"
      And I should still be on my account addresses page
      And I should see "Your address has been deleted"

  Scenario: Editing an address
    Given I press "Edit" near "Germany"
      And I fill in the users account address to France
      And I press "Edit"
     Then I should see 3 addresses in the list
      And I should not see "Germany"
      And I should see "France"
      And I should still be on my account addresses page
      And I should see "Your address has been edited"

  Scenario: Trying to edit an address with empty fields
    Given I press "Edit" near "Germany"
      And I leave "First name" empty
      And I leave "Last name" empty
      And I leave "Country" empty
      And I leave "Street" empty
      And I leave "City" empty
      And I leave "Postcode" empty
      And I press "Edit"
     Then I should see 6 fields on error

  Scenario: Viewing that no default shipping and billing addresses have been defined
    Given I am on my account addresses page
     Then I should see "No default billing address"
      And I should see "No default shipping address"

  Scenario: Setting an address as the default billing address
    Given I press "Set as default billing address" near "Austria"
     Then I should not see "No default billing address"
      And I should see "Austria" in the "billing_address" element
      And I should see "Your default billing address has been saved"

  Scenario: Setting an address as the default shipping address
    Given I press "Set as default billing address" near "Poland"
     Then I should not see "No default shipping address"
      And I should see "Poland" in the "shipping_address" element
      And I should see "Your default shipping address has been saved"
