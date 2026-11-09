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

    @no-api @ui
    Scenario: An anonymous visitor after an administrator logged in does not inherit the back office
        When I open administration dashboard
        Then I should be logged in
        When I log out
        And I try to open administration dashboard
        Then I should be on login page
        And I should not be logged in

    @no-api @ui
    Scenario: A customer account cannot access the administration panel after an administrator was active
        When I open administration dashboard
        Then I should be logged in
        When I log out
        And I specify the username as "ted@example.com"
        And I specify the password as "bear"
        And I log in
        Then I should be notified about bad credentials
        And I should not be logged in
        When I specify the username as "ned@stark.com"
        And I specify the password as "winterfell"
        And I log in
        Then I should be logged in

    @no-api @ui
    Scenario: Shop traffic on multiple channels does not break back office access
        When I open administration dashboard
        Then I should be logged in
        When I browse the "Poland" channel
        And I browse the "United States" channel
        When I open administration dashboard
        Then I should be logged in

    @no-api @ui
    Scenario: A locale added during process runtime shows up in translatable forms
        When I want to create a new translatable entity
        Then I should not be able to translate it in "Polish (Poland)"
        Given the store has locale "Polish (Poland)"
        When I want to create a new translatable entity
        Then I should be able to translate it in "Polish (Poland)"
