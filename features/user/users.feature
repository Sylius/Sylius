@users
Feature: Customers management
    In order to manager customers
    As a store owner
    I want to be able to list all customers

    Background:
        Given store has default configuration
          And there are products:
            | name | price |
            | Mug  | 5.99  |
          And the following zones are defined:
            | name         | type    | members                       |
            | German lands | country | Germany, Austria, Switzerland |
            | Poland       | country | Poland                        |
          And there are following users:
            | email          | enabled | address                                                |
            | beth@foo.com   | no      | Klaus Schmitt, Heine-Straße 12, 99734, Berlin, Germany |
            | martha@foo.com | yes     | Lars Meine, Fun-Straße 1, 90032, Vienna, Austria       |
            | rick@foo.com   | no      | Klaus Schmitt, Heine-Straße 12, 99734, Berlin, Germany |
            | dale@foo.com   | yes     | Lars Meine, Fun-Straße 1, 90032, Vienna, Austria       |
          And there are following customers:
            | email          | address                                                 |
            | john@foo.com   | Klaus Schmitt, Heine-Straße 122, 99134, Berlin, Germany |
            | doe@foo.com    | Lars Meine, Fun-Straße 13, 90332, Vienna, Austria       |
          And the following orders were placed:
            | customer     | address                                        |
            | john@foo.com | Jan Kowalski, Wawel 5 , 31-001, Kraków, Poland |
            | rick@foo.com | Rick Foo, Wawel 5 , 31-001, Kraków, Poland     |
        And order #000000001 has following items:
            | product | quantity |
            | Mug     | 2        |
        And order #000000002 has following items:
            | product | quantity |
            | Mug     | 3        |
        And I am logged in as administrator

    Scenario: Seeing index of all customers
        Given I am on the dashboard page
         When I follow "Customers"
         Then I should be on the customer index page
          And I should see 7 customers in the list

    Scenario: Seeing index of unconfirmed accounts
        Given I am on the customer index page
         When I follow "unconfirmed accounts"
         Then I should still be on the customer index page
          But I should see 2 customers in the list

    Scenario: Searching for customers
        Given I am on the customer index page
         When I fill in "criteria_query" with "Klaus"
          And I press "Search"
         Then I should be on the customer index page
          And I should see 3 customers in the list

    Scenario: Accessing the customer details page from customers list
        Given I am on the customer index page
         When I click "details" near "john@foo.com"
         Then I should be on the page of customer with email "john@foo.com"
          And I should see 1 orders in the list

    Scenario: Prevent self-deletion possibility for current logged user on details page
        Given I am on the customer index page
         When I click "details" near "sylius@example.com"
         Then I should be on the page of customer with email "sylius@example.com"
          And I should not see "delete" button

    Scenario: Prevent self-deletion possibility for current logged customer on customer index page
        Given I am on the customer index page
         Then I should not see "delete" button near "sylius@example.com" in "customers" table

    Scenario: Accessing the customer creation form
        Given I am on the customer index page
         When I follow "create customer"
         Then I should be on the customer creation page

    Scenario: Submitting empty form
        Given I am on the customer creation page
         When I press "Create"
         Then I should still be on the customer creation page
          And I should see "Please enter your first name."

    Scenario: Creating customer
        Given I am on the customer creation page
         When I fill in the following:
            | First name | Saša               |
            | Last name  | Stamenković        |
            | Password   | Password           |
            | Email      | umpirsky@gmail.com |
          And I press "Create"
         Then I should be on the page of customer with email "umpirsky@gmail.com"
          And I should see "Customer has been successfully created."

    Scenario: Accessing the customer editing form
        Given I am on the page of customer with email "rick@foo.com"
         When I follow "edit"
         Then I should be editing customer with email "rick@foo.com"

    Scenario: Accessing the editing form from the list
        Given I am on the customer index page
         When I click "edit" near "rick@foo.com"
         Then I should be editing customer with email "rick@foo.com"

    Scenario: Updating the customer
        Given I am editing customer with email "rick@foo.com"
         When I fill in "Email" with "umpirsky@gmail.com"
          And I press "Save changes"
         Then I should be on the page of customer with email "umpirsky@gmail.com"
          And "Customer has been successfully updated." should appear on the page
          And "umpirsky@gmail.com" should appear on the page

    Scenario: Changing customer's password and logging in with new one
        Given I am editing customer with email "dale@foo.com"
          And I fill in "Password" with "Sylius!"
         When I press "Save changes"
          And I restart my browser
          And I log in with "dale@foo.com" and "Sylius!"
         Then I should be on the sylius homepage page
          And I should see "Logout"

    Scenario: Accessing the customer details page from customers list for deleted customer
        Given I deleted customer with email "rick@foo.com"
          And I am on the customer index page
          And I view deleted elements
         When I click "details" near "rick@foo.com"
         Then I should be on the page of customer with email "rick@foo.com"
          And I should see 1 orders in the list

    Scenario: Deleting customer
        Given I am on the page of customer with email "rick@foo.com"
         When I press "delete"
         Then I should see "Do you want to delete this item"
         When I press "delete"
         Then I should be on the customer index page
          And I should see "Customer has been successfully deleted."

    @javascript
    Scenario: Deleting customer
        Given I am on the page of customer with email "rick@foo.com"
         When I press "delete"
          And I click "delete" from the confirmation modal
         Then I should be on the customer index page
          And I should see "Customer has been successfully deleted."

    @javascript
    Scenario: Deleted customer disappears from the list
        Given I am on the page of customer with email "rick@foo.com"
         When I press "delete"
          And I click "delete" from the confirmation modal
         Then I should be on the customer index page
          And I should not see customer with email "rick@foo.com" in that list

    @javascript
    Scenario: Deleting customer from the list
        Given I am on the customer index page
         When I click "delete" near "rick@foo.com"
          And I click "delete" from the confirmation modal
         Then I should still be on the customer index page
          And "Customer has been successfully deleted." should appear on the page
          But I should not see customer with email "rick@foo.com" in that list
