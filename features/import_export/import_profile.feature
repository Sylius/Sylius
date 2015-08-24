@import_export
Feature: Import profiles
    In order to import data from my store 
    As a store owner
    I want to be able to manage import profiles

    Background:
        Given there are following import profiles configured:
            | name                 | description | code           | reader        | reader configuration                                | writer      | writer configuration    |
            | UsersImportProfile   | Lorem ipsum | user_import    | csv_reader    | Delimiter:;,Enclosure:",File:/tmp/user.csv,Batch:15 | user_orm    | date_format:Y-m-d H:i:s |
        And there is default currency configured
        And there is default channel configured
        And I am logged in as administrator

    Scenario: Seeing created import profile at the list
        Given I am on the dashboard page
         When I follow "Import Profile"
         Then I should see 1 import profiles in the list

    Scenario: Adding new import profile with default options
        Given I am on the import profile creation page
         When I fill in the following:
            | Name        | ImportProfile1    | 
            | Description | Lorem ipsum dolor |
            | Code        | import_profile1   |
            | Rows number | 10                |
            | Delimiter   | ;                 |
            | Enclosure   | "                 |
            | File        | \tmp\file.csv     |
          And I press "Create"
         Then I should be on the page of import profile "ImportProfile1"
          And I should see "Import profile has been successfully created."

    Scenario: Adding new import profile with custom options
        Given I am on the import profile creation page
         When I fill in the following:
            | Name        | ImportProfile2    | 
            | Description | Lorem ipsum dolor |
            | Code        | import_profile    |
            | Enclosure   | "                 |
            | Date format |  Y.m.d            |
            | File path   | \tmp\file.csv     |
          And I select "csv_reader" from "Reader"
          And I fill in "Rows number" with "1000"
          And I fill in "Delimiter" with "*"
          And I press "Create"
         Then I should be on the page of import profile "ImportProfile2"
          And I should see "Import profile has been successfully created."
          And I should see "1000"
          And I should see "Y.m.d"

    Scenario: Prevent adding new import profile with the same code that has been used before
        Given I am on the import profile creation page
         When I fill in the following:
            | Name        | ImportProfile3    | 
            | Description | Lorem ipsum dolor |
            | Code        | user_import       |
            | Rows number | 10                |
            | Delimiter   | ;                 |
            | Enclosure   | "                 |
            | File path   | \tmp\file.csv     |
          And I press "Create"
         Then I should still be on the import profile creation page
          And I should see "This code is already in use."

    Scenario: Prevent adding new user import profile without date format defined
        Given I am on the import profile creation page
         When I fill in the following:
            | Name        | ImportProfile3    |
            | Description | Lorem ipsum dolor |
            | Code        | user_import3      |
            | Delimiter   | ;                 |
            | Date format |                   |
            | Enclosure   | "                 |
            | File path   | \tmp\file.csv     |
          And I press "Create"
         Then I should still be on the import profile creation page
          And I should see "This value should not be blank."

    Scenario: Accessing import profile details page from list
        Given I am on the import profile index page
         When I press "details" near "UsersImportProfile"
         Then I should be on the page of import profile "UsersImportProfile"
  
    Scenario: Accessing import profile edit form from list
        Given I am on the import profile index page
         When I press "edit" near "UsersImportProfile"
         Then I should be editing import profile with name "UsersImportProfile"

    Scenario: Accessing import profile edit form from details page
        Given I am on the page of import profile "UsersImportProfile"
         When I click "edit"
         Then I should be editing import profile with name "UsersImportProfile"

    Scenario: Updating the import profile
        Given I am editing import profile "UsersImportProfile"
         When I fill in "Name" with "UsersImportProfile2"
          And I fill in "Description" with "Lorem ipsum dolor"
          And I fill in "File path" with "\tmp\output2.csv"
          And I press "Save changes"
         Then I should see "Import profile has been successfully updated."
          And I should see "UsersImportProfile2"
          And I should see "Lorem ipsum dolor"

    Scenario: Deleting import profile from index page
        Given I am on the import profile index page
         When I press "delete" near "UsersImportProfile"
         Then I should be on the import profile index page
          And I should see "Import profile has been successfully deleted."
          And I should see "There are no profiles to display."

    Scenario: Deleting import profile from details page
        Given I am on the page of import profile "UsersImportProfile"
         When I press "delete"
         Then I should be on the import profile index page
          And I should see "Import profile has been successfully deleted."
          And I should see "There are no profiles to display."

    @using_file
    Scenario: Executing import from browser
        Given I am on the page of import profile "UsersImportProfile"
          And there are following users in a file "/tmp/user.csv":
            | email          | enabled  | created_at          |
            | beth@foo.com   | yes      | 2010-01-02 12:00:00 |
            | martha@foo.com | yes      | 2010-01-02 13:00:00 |
            | rick@foo.com   | yes      | 2010-01-03 12:00:00 |
         When I follow "Import"
         Then Import job for import profile "UsersImportProfile" should be created
          And I should be on its details page
          And I should see "completed"
          And I should have 4 users in a database
