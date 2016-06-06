@legacy @review
Feature: Reviews
    In orders to know customer's opinions about my products
    As a store owner
    I want to be able to manage reviews

    Background:
        Given there are following users:
            | email          |
            | beth@foo.com   |
            | martha@foo.com |
            | rick@foo.com   |
        And the following products exist:
            | name            | price | average rating |
            | Black T-Shirt   | 18.99 | 4              |
            | Sylius Tee      | 12.99 | 5              |
            | Symfony T-Shirt | 15.00 | 1              |
        And there are following reviews:
            | title      | rating | comment               | author         | product         | subject type | status |
            | Really bad | 1      | Lorem ipsum dolor sit | beth@foo.com   | Symfony T-Shirt | product      |        |
            | Very good  | 4      | Lorem ipsum dolor sit | martha@foo.com | Black T-Shirt   | product      |        |
            | Awesome    | 5      | Lorem ipsum dolor sit | rick@foo.com   | Sylius Tee      | product      | new    |
        And there is default currency configured
        And there is default channel configured
        And I am logged in as administrator

    Scenario: Seeing created reviews in the list
        Given I am on the dashboard page
        When I follow "Product reviews"
        Then I should see 3 reviews in the list

    Scenario: Accessing review edit page from the list
        Given I am on the product review index page
        When I press "Edit" near "Very good"
        Then I should be editing product review with title "Very good"

    Scenario: Accessing review edit page from details page
        Given I am on the page of product review with title "Very good"
        When I follow "Edit"
        Then I should be editing product review with title "Very good"

    Scenario: Updating review
        Given I am editing product review with title "Very good"
        When I fill in "Title" with "Very, very good"
        And I press "Save changes"
        Then I should be on the page of product review with title "Very, very good"

    @javascript
    Scenario: Removing review from the list
        Given I am on the product review index page
        When I press "Delete" near "Awesome"
        And I click "Delete" from the confirmation modal
        Then I should still be on the product review index page
        And I should see "Review has been successfully deleted."

    @javascript
    Scenario: Removing review from details page
        Given I am on the page of product review with title "Awesome"
        When I press "Delete"
        And I click "Delete" from the confirmation modal
        Then I should be on the product review index page
        And I should see "Review has been successfully deleted."

    Scenario: Showing review details
        Given I am on the product review index page
        When I click "Details" near "Awesome"
        Then I should be on the page of product review with title "Awesome"
        And I should see "Awesome"
        And I should see "rick@foo.com"
        And I should see "Lorem ipsum dolor"

    Scenario: Accepting review from the list
        Given I am on the product review index page
        When I press "Accept" near "Awesome"
        Then I should see "Product review has been successfully updated."
        When I press "Details" near "Awesome"
        Then I should see "accepted"

    Scenario: Accepting review from details page
        Given I am on the page of product review with title "Awesome"
        When I press "Accept"
        Then I should see "Product review has been successfully updated."
        And I should see "accepted"
        And I should not see "Reject" button
        And I should not see "Accept" button

    Scenario: Rejecting review from the list
        Given I am on the product review index page
        When I press "Reject" near "Awesome"
        And I press "Details" near "Awesome"
        Then I should see "rejected"

    Scenario: Rejecting review from details page
        Given I am on the page of product review with title "Awesome"
        When I press "Reject"
        Then I should see "Product review has been successfully updated."
        And I should see "rejected"
        And I should not see "Reject" button
        And I should not see "Accept" button

    Scenario: Seeing reviews list unchanged after user deletion
        Given user "beth@foo.com" has been deleted
        And I am on the product review index page
        Then I should see 3 reviews in the list
        And I should see "Really bad"

    Scenario: Seeing review list after product deletion
        Given product "Symfony T-Shirt" has been deleted
        And I am on the product review index page
        Then I should see 2 reviews in the list
        And I should not see "Really bad"
