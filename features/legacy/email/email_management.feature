@legacy @emails
Feature: Managing emails
    In order to easily customize my marketing emails
    As a store owner
    I want to be able to configure them in backend

    Background:
        Given store has default configuration
        And there are following emails configured:
            | code                  | subject                      | enabled |
            | user_confirmation     | Welcome!                     | yes     |
            | order_confirmation    | Thank you for your order!    | no      |
            | shipment_confirmation | Your shipment is on the way! | yes     |
        And I am logged in as administrator

    Scenario: Seeing index of all emails
        Given I am on the dashboard page
        When I follow "Emails"
        Then I should be on the email index page
        And I should see 3 emails in the list

    Scenario: Seeing empty index of emails
        Given there are no emails
        When I am on the email index page
        Then I should see "There are no emails configured"

    Scenario: Accessing the email adding form
        Given I am on the dashboard page
        When I follow "Emails"
        And I follow "Add new Email"
        Then I should be on the email creation page

    Scenario: Creating new email
        Given I am on the email creation page
        When I fill in "Code" with "promotion_coupon"
        And I fill in "Subject" with "You get a coupon!"
        And I press "Create"
        Then I should be editing email with code "promotion_coupon"
        And I should see "Email has been successfully created"

    Scenario: Email codes have to be unique
        Given I am on the email creation page
        When I fill in "Code" with "order_confirmation"
        And I press "Create"
        Then I should still be on the email creation page
        And I should see "Email code must be unique"

    Scenario: Updating the email
        Given I am on the email index page
        And I click "Edit" near "order_confirmation"
        When I fill in "Subject" with "A great purchase!"
        And I press "Save changes"
        Then I should see "Email has been successfully updated"

    Scenario: Deleting email from list
        Given I am on the email index page
        When I click "Delete" near "order_confirmation"
        Then I should be on the email index page
        And I should see "Email has been successfully deleted"
        And I should not see email with code "order_confirmation" in that list

    Scenario: Email code is required
        Given I am on the email creation page
        When I fill in "Subject" with "You get a coupon!"
        And I press "Create"
        Then I should still be on the email creation page
        And I should see "Please enter email code"

    Scenario: Cannot update email code
        When I am editing email with code "user_confirmation"
        Then the code field should be disabled
