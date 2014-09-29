@contact
Feature: Contact topics management
    In order to improve customer support
    As a store owner
    I want to manage contact request topics

    Background:
        Given there is default currency configured
        And there are following contact topics:
            | title               |
            | Order return        |
            | Delivery            |
            | Product information |
        And I am logged in as administrator

    Scenario: Browsing all contact topics
        Given I am on the dashboard page
        When I follow "Contact topics"
        Then I should be on the contact topic index page
        And I should see 3 contact topics in the list
        And I should see topic with title "Order return" in the list

    Scenario: Seeing empty index of contact topics
        Given there are no contact topics
        When I am on the contact topic index page
        Then I should see "There are no contact topics to display."

    Scenario: Accessing the contact topic creation form
        Given I am on the dashboard page
        When I follow "Contact topics"
        And I follow "Create contact topic"
        Then I should be on the contact topic creation page

    Scenario: Submitting the form without the title fails
        Given I am on the contact topic creation page
        When I press "Create"
        Then I should still be on the contact topic creation page
        And I should see "Please enter title."

    Scenario: Creating new contact topic
        Given I am on the contact topic creation page
        When I fill in "Title" with "Product request"
        And I press "Create"
        Then I should be on the contact topic index page
        And I should see "Contact topic has been successfully created."
        And I should see topic with title "Product request" in the list

    Scenario: Accessing the contact topic edit form
        Given I am on the contact topic index page
        When I click "edit" near "Order return"
        Then I should be editing contact topic with title "Order return"

    Scenario: Updating the contact topic title
        Given I am editing contact topic with title "Delivery"
        And I fill in "Title" with "Product delivery"
        And I press "Save changes"
        Then I should be on the contact topic index page
        And I should see contact topic with title "Product delivery" in the list

    Scenario: Deleting a contact topic
        Given I am on the contact topic index page
        When I press "delete" near "Delivery"
        Then I should still be on the contact topic index page
        And I should see "Contact topic has been successfully deleted."
        And I should not see contact topic with title "Delivery" in the list