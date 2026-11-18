@worker @worker:admin @mink:workerphp
Feature: Back office isolation between consecutive requests
    In order to administer the shop all day
    As an Administrator
    I want a form I open now not to carry rules left by the form before it

    The back office reuses form types across requests. A form type that appends
    to its own property instead of building a fresh value - UserType did exactly
    that with validation_groups - keeps the addition for every later request in
    the same process. Creating an administrator then demands a password from
    every edit that follows.

    Background:
        Given the store operates on a channel named "Poland"
        And the store operates on another channel named "United States"
        And there is a user "ted@example.com" identified by "bear"
        And there is an administrator "ned@stark.com" identified by "winterfell"
        And I am logged in as an administrator

    @no-api @ui
    Scenario: Editing an administrator without a password still works after one was created
        When I want to create a new administrator
        And I specify its name as "jon"
        And I specify its email as "jon@stark.com"
        And I specify its password as "bastardofwinterfell"
        And I add it
        Then I should be notified that it has been successfully created
        When I want to edit this administrator
        And I do not specify its password
        And I save my changes
        Then I should be notified that it has been successfully edited
