@import_export
Feature: Executing import command
    In order to import data from my store
    As a store owner
    I want to be able to run jobs based on defined import profiles

    Background:
        Given there are following import profiles configured:
            | name                 | description | code           | reader        | reader configuration                                | writer      | writer configuration    |
            | UsersImportProfile   | Lorem ipsum | user_import    | csv_reader    | Delimiter:;,Enclosure:",File:/tmp/user.csv,Batch:15 | user_orm    | date_format:Y-m-d H:i:s |

    @using_file
    Scenario: Running import command
        Given there are following users in a file "/tmp/user.csv":
            | email          | enabled  | created_at          |
            | beth@foo.com   | yes      | 2010-01-02 12:00:00 |
            | martha@foo.com | yes      | 2010-01-02 13:00:00 |
            | rick@foo.com   | yes      | 2010-01-03 12:00:00 |
         When The file at path "/tmp/user.csv" exists
          And I run "sylius:import user_import" command in less then "30" seconds
         Then the command should finish successfully
          And I should see "Command executed successfully!" in a terminal
          And I should have 3 users in a database
          And I should find 1 "completed" job for this "import" profile in database

    Scenario: Running import command without import code defined
         When I run "sylius:import" command in less then "30" seconds
         Then the command should finish unsuccessfully
          And I should see "Not enough arguments" in error message

    Scenario: Running import command without import code defined
         When I run "sylius:import another_user_import" command in less then "30" seconds
         Then the command should finish unsuccessfully
          And I should see "There is no import profile with given code" in error message
