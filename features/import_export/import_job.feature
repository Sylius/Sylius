@import_export
Feature: Import jobs
    In order to see data imports from my store 
    As a store owner
    I want to be able to view import jobs

    Background:
        Given there are following import profiles configured:
            | name                 | description | code           | reader        | reader configuration                              | writer      | writer configuration |
            | UsersImportProfile   | Lorem ipsum | user_import    | csv_reader    | Delimiter:;,Enclosure:",File path:\tmp\output.csv | user_orm    | Rows number:10       |
        And there are following import jobs:
            | status    | start_time          | end_time            | created_at          | updated_at          | import_profile    |
            | completed | 2010-01-02 01:00:00 | 2010-01-02 01:00:01 | 2010-01-02 01:00:00 | 2010-01-02 01:00:01 | user_import       |
        And there is default currency configured
        And there is default channel configured
        And I am logged in as administrator

    Scenario: Viewing set import jobs for given import profile
        Given I am on the import profile index page
        When I press "details" near "UsersImportProfile"
        And I click "Jobs"
        Then I should be on the import jobs index page for profile with code "user_import"
        And I should see 1 import job in the list

    Scenario: Viewing import job details
        Given I am on the import jobs index page for profile with code "user_import"
        And I follow "details"
        Then I should see "January 2, 2010 01:00"
        And I should see "completed"

    Scenario: Accessing import profile page from its job page
        Given I am on the import jobs index page for profile with code "user_import"
        And I follow "details"
        And I click "Show import profile"
        Then I should be on the page of import profile "UsersImportProfile"
