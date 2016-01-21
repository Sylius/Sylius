@products
Feature: Product association
    In order connect products together in many contexts
    As a store owner
    I want to be able associate product with another ones

    Background:
        Given there is default currency configured
        And there is default channel configured
        And I am logged in as administrator

    Scenario: Seeing empty index of association types
        Given I am on the dashboard page
        When I am on the product association type index page
        Then I should see "There are no association types defined"

    Scenario: Seeing index of association types
        Given there are following association types:
            | name       | code |
            | Cross sell | PAs1 |
            | UP sell    | PAs2 |
        And I am on the dashboard page
        When I am on the product association type index page
        Then I should see "Cross sell"
        And I should see "Up sell"

    Scenario: Creating association type
        Given I want to create new association type
        When I create "Cross sell" association type with PAsCS code
        And I should see "Association type has been successfully created"

    Scenario: Updating association type
        Given there are following association types:
            | name       | code |
            | Cross sell | PAs1 |
        Given I am editing product association type "Cross sell"
        When I fill in "Name" with "Up sell"
        And I press "Save changes"
        Then I should see "Association type has been successfully updated"
        And "Up sell" should appear on the page
