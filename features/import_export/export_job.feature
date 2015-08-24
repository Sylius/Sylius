@import_export
Feature: Export jobs viewing
    In order to see data exports from my store 
    As a store owner
    I want to be able to view export jobs

    Background:
        Given there are following export profiles configured:
            | name               | description | code        | reader   | reader_configuration                                  | writer       | writer_configuration                                |
            | UsersExportProfile | Lorem ipsum | user_export | user_orm | Rows number:10, batch_size:10,date_format:Y-m-d H:i:s | csv_writer   | Delimiter:;, Enclosure:", File path:\tmp\output.csv |
        And there are following export jobs:
            | status    | start_time          | end_time            | created_at          | updated_at          | export_profile    | 
            | completed | 2010-01-02 01:00:00 | 2010-01-02 01:00:01 | 2010-01-02 01:00:00 | 2010-01-02 01:00:01 | user_export       |
        And there is default channel configured
        And there is default currency configured
        And there are following users:
            | email          | enabled  | created_at          |
            | beth@foo.com   | yes      | 2010-01-02 12:00:00 |
            | martha@foo.com | yes      | 2010-01-02 13:00:00 |
            | rick@foo.com   | yes      | 2010-01-03 12:00:00 |
        And I am logged in as administrator

    Scenario: Viewing set export jobs for given export profile
        Given I am on the export profile index page
        When I press "details" near "UsersExportProfile"
        And I click "Jobs"
        Then I should be on the export jobs index page for profile with code "user_export"
        And I should see 1 export job in the list

    Scenario: Viewing export job details
        Given I am on the export jobs index page for profile with code "user_export"
        When I follow "details"
        Then I should see "January 2, 2010 01:00"
        And I should see "completed"

    Scenario: Accessing export profile page from its job page
        Given I am on the export jobs index page for profile with code "user_export"
        And I follow "details"
        And I click "Show export profile"
        Then I should be on the page of export profile "UsersExportProfile"
