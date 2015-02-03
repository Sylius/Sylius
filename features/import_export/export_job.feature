@importexport
Feature: Export jobs
    In order to see data exports from my store 
    As a store owner
    I want to be able to view export jobs

    Background:
        Given there are following export profiles configured:
            | name                 | description | code           | reader         | reader configuration      | writer       | writer configuration                                |
            | ProductExportProfile | Lorem ipsum | product_export | product_reader | Rows number:10            | csv_writer   | Delimiter:;,Enclosure:",File path:\tmp\output.csv   |
            | UsersExportProfile   | Lorem ipsum | user_export    | user_reader    | Rows number:10            | excel_writer | File path:\tmp\output.csv                           |
        And there are following export jobs set:
            | status    | startTime           | endTime             | createdAt           | updatedAt           | exportProfileCode | 
            | completed | 2010-01-01 01:00:00 | 2010-01-01 01:00:01 | 2010-01-01 01:00:00 | 2010-01-01 01:00:01 | product_export    |
            | completed | 2010-01-01 01:01:00 | 2010-01-01 01:01:01 | 2010-01-01 01:01:00 | 2010-01-01 01:01:01 | product_export    |
            | completed | 2010-01-02 01:00:00 | 2010-01-02 01:00:01 | 2010-01-02 01:00:00 | 2010-01-02 01:00:01 | user_export       |
        And there is default currency configured
        And there are following users:
            | email          | enabled  | created at          |
            | beth@foo.com   | yes      | 2010-01-02 12:00:00 |
            | martha@foo.com | yes      | 2010-01-02 13:00:00 |
            | rick@foo.com   | yes      | 2010-01-03 12:00:00 |
        And I am logged in as administrator


    Scenario: Seeing set export jobs for given export profile
        Given I am on the export profile index page
         When I press "details" near "ProductExportProfile"
          And I click "Jobs"
         Then I should be on the export jobs index page for profile with code "product_export"
          And I should see 2 export jobs in the list