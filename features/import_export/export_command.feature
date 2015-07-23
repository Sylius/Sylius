@importexport
Feature: Export command
    In order to export data from my store
    As a store owner
    I want to be able to run jobs based on defined export profiles

    Background:
        Given there are following export profiles configured:
          | name               | description | code        | reader   | reader_configuration                  | writer     | writer_configuration                         |
          | UsersExportProfile | Lorem ipsum | user_export | user_orm | batch_size:10,date_format:Y-m-d H:i:s | csv_writer | Delimiter:;,Enclosure:",File:/tmp/output.csv |
        And there is default currency configured
        And there is default channel configured
        And there are following users:
          | email          | enabled  | created_at          |
          | beth@foo.com   | yes      | 2010-01-02 12:00:00 |
          | martha@foo.com | yes      | 2010-01-02 13:00:00 |
          | rick@foo.com   | yes      | 2010-01-03 12:00:00 |
        And I am logged in as administrator

    @scenarioWithFile
    Scenario: Running export command
         When I run "sylius:export user_export" command in less then "300" seconds
         Then the command should finish successfully
          And I should see "Command executed successfully!" in terminal
          And I should find "/tmp/output.csv" file
          And this file should contains 4 rows
          And the file data should be valid
          And I should find 1 "completed" job for this "export" profile in database

    Scenario: Running export command without export code defined
         When I run "sylius:export" command in less then "30" seconds
         Then the command should finish unsuccessfully
          And I should see  "Not enough arguments" in error message

    Scenario: Running export command without export code defined
         When I run "sylius:export another_user_export" command in less then "30" seconds
         Then the command should finish unsuccessfully
          And I should see  "There is no export profile with given code" in error message
