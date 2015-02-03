@importexport
Feature: Export profiles
    In order to export data from my store 
    As a store owner
    I want to be able to manage export profiles

    Background:
        Given there are following export profiles configured:
            | name                 | description | code           | reader         | reader configuration      | writer       | writer configuration                                |
            | ProductExportProfile | Lorem ipsum | product_export | product_reader | Rows number:10            | csv_writer   | Delimiter:;,Enclosure:",File path:\tmp\output.csv   |
            | UsersExportProfile   | Lorem ipsum | user_export    | user_reader    | Rows number:10            | excel_writer | File path:\tmp\output.csv                           |
        And there is default currency configured
        And there are following users:
            | email          | enabled  | created at          |
            | beth@foo.com   | yes      | 2010-01-02 12:00:00 |
            | martha@foo.com | yes      | 2010-01-02 13:00:00 |
            | rick@foo.com   | yes      | 2010-01-03 12:00:00 |
        And I am logged in as administrator

    Scenario: Seeing created export profile at the list
        Given I am on the dashboard page
         When I follow "Export"
         Then I should see 2 export profiles in the list

    Scenario: Adding new export profile with default options
        Given I am on the export profile creation page
         When I fill in the following:
            | Name        | ExportProfile1    | 
            | Description | Lorem ipsum dolor |
            | Code        | export_profile    |
            | Rows number | 10                |
            | Delimiter   | ;                 |
            | Enclosure   | "                 |
            | File path   | \tmp\file.csv     |
          And I press "Create"
         Then I should be on the page of export profile "ExportProfile1"
          And I should see "Export profile has been successfully created."

    Scenario: Adding new export profile with custom options
        Given I am on the export profile creation page
         When I fill in the following:
            | Name        | ExportProfile2    | 
            | Description | Lorem ipsum dolor |
            | Code        | export_profile    |
            | Enclosure   | "                 |
            | File path   | \tmp\file.csv     |
          And I select "product_reader" from "Reader"
          And I fill in "Rows number" with "1000"
          And I fill in "Delimiter" with "*"
          And I press "Create"
         Then I should be on the page of export profile "ExportProfile2"
          And I should see "Export profile has been successfully created."
          And I should see "1000"

    Scenario: Prevent adding new export profile with the same code that has been used before
        Given I am on the export profile creation page
         When I fill in the following:
            | Name        | ExportProfile3    | 
            | Description | Lorem ipsum dolor |
            | Code        | product_export    |
            | Rows number | 10                |
            | Delimiter   | ;                 |
            | Enclosure   | "                 |
            | File path   | \tmp\file.csv     |
          And I press "Create"
         Then I should still be on the export profile creation page
          And I should see "This code is already in use."

    Scenario: Accessing export profile details page from list
        Given I am on the export profile index page
         When I press "details" near "ProductExportProfile"
         Then I should be on the page of export profile "ProductExportProfile"
  
    Scenario: Accessing export profile edit form from list
        Given I am on the export profile index page
         When I press "edit" near "ProductExportProfile"
         Then I should be editing export profile with name "ProductExportProfile"

    Scenario: Accessing export profile edit form from details page
        Given I am on the page of export profile "ProductExportProfile"
         When I click "edit"
         Then I should be editing export profile with name "ProductExportProfile"

    Scenario: Updating the export profile
        Given I am editing export profile "ProductExportProfile"
         When I fill in "Name" with "ProductExportProfile2"
          And I fill in "Description" with "Lorem ipsum dolor"
          And I fill in "File path" with "\tmp\output2.csv"
          And I press "Save changes"
         Then I should see "Export profile has been successfully updated."
          And "ProductExportProfile2" should appear on the page
          And "Lorem ipsum dolor" should appear on the page

    Scenario: Deleting export profile from index page
        Given I am on the export profile index page
         When I press "delete" near "ProductExportProfile"
         Then I should be on the export profile index page
          And I should see "Export profile has been successfully deleted."
          And I should not see export profile with name "ProductExportProfile" in that list

    Scenario: Deleting export profile from details page
        Given I am on the page of export profile "ProductExportProfile"
         When I press "delete"
         Then I should be on the export profile index page
          And I should see "Export profile has been successfully deleted."
          And I should not see export profile with name "ProductExportProfile" in that list
