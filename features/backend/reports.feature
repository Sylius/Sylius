Feature: Reports
  In order to measure sale parameters
  As a store owner
  I want to be able to manage reports

  Background:
    Given I am logged in as administrator
      And the following reports exist:
        | name                 |
        | Monthly orders (CSV) |
        | Yearly orders (CSV)  |
      And report "Monthly orders (CSV)" has following fetcher defined:
        | type  | configuration |
        | order | group: m      |
      And report "Monthly orders (CSV)" has following renderer defined:
        | type | configuration              |
        | csv  | delimeter: ;, enclosure: " |
      And report "Yearly orders (CSV)" has following fetcher defined:
        | type  | configuration |
        | order | group: y      |
      And report "Yearly orders (CSV)" has following renderer defined:
        | type | configuration              |
        | csv  | delimeter: ;, enclosure: " |

  Scenario: Seeing index of all reports
    Given I am on the dashboard page
     When I follow "Reports"
     Then I should be on the report index page
      And I should see 2 reports in the list

  Scenario: Seeing empty index of reports
    Given there are no reports
     When I am on the report index page
     Then I should see "There are no reports to display"

  Scenario: Accessing the report creation form
    Given I am on the dashboard page
     When I follow "Reports"
      And I follow "create report"
     Then I should be on the report creation page

  Scenario: Submitting invalid form without name
    Given I am on the report creation page
     When I press "Create"
     Then I should still be on the report creation page
      And I should see "Please enter report name."

  @javascript
  Scenario: Creating new weekly orders csv report
    Given I am on the report creation page
     When I fill in "Name" with "Weekly orders (CSV)"
      And I select "Order" from "Data fetcher"
      And I select "Week" from "Group by"
      And I select "CSV" from "Renderer"
      And I fill in "Delimeter" with ";"
      And I fill in "Enclosure" with "'"
      And I press "Create"
     Then I should be on the page of report "Weekly orders (CSV)"
      And I should see "Report has been successfully created."

  @javascript
  Scenario: Created reports appear in the list
    Given I am on the report creation page
     When I fill in "Name" with "Weekly orders (CSV)"
      And I select "Order" from "Data fetcher"
      And I select "Week" from "Group by"
      And I select "CSV" from "Renderer"
      And I fill in "Delimeter" with ";"
      And I fill in "Enclosure" with "'"
      And I press "Create"
     When I go to the report index page
     Then I should see 3 reports in the list
      And I should see report with name "Weekly orders (CSV)" in that list

  Scenario: Accessing the report editing form
    Given I am on the page of report "Monthly orders (CSV)"
     When I follow "edit"
     Then I should be editing report "Monthly orders (CSV)"

  Scenario: Accessing the editing form from the list
    Given I am on the report index page
     When I click "edit" near "Monthly orders (CSV)"
     Then I should be editing report "Monthly orders (CSV)"

  Scenario: Updating the report
    Given I am editing report "Monthly orders (CSV)"
     When I fill in "Name" with "Monthly CSV orders"
      And I press "Save changes"
     Then I should be on the page of report "Monthly CSV orders"

  Scenario: Deleting report
    Given I am on the page of report "Monthly orders (CSV)"
     When I press "delete"
     Then I should be on the report index page
      And I should see "Report has been successfully deleted."

  Scenario: Deleted report disappears from the list
    Given I am on the page of report "Monthly orders (CSV)"
     When I press "delete"
     Then I should be on the report index page
      And I should not see report with name "Monthly orders (CSV)" in that list

  Scenario: Deleting report via list
    Given I am on the report index page
     When I click "delete" near "Monthly orders (CSV)"
     Then I should be on the report index page
      And I should see "Report has been successfully deleted."
