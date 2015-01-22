@reports
Feature: Reports
    In order to see my store statistics
    As a store owner
    I want to be able to manage reports

    Background:
        Given there are following reports configured:
            | name             | description | renderer | renderer configuration                | data fetcher      | data fetcher configuration                       |
            | TableReport      | Lorem ipsum | table    | Template: default.html.twig           | user_registration | Period: day,Start: 2010-01-01,End: 2010-04-01    |
            | BarChartRenderer | Lorem ipsum | chart    | Type: bar,Template: default.html.twig | user_registration | Period: month,Start: 2010-01-01,End: 2010-04-01  |
        And there is default currency configured
        And there are following users:
            | email          | enabled  | created_at          |
            | beth@foo.com   | yes      | 2010-01-01 12:00:00 |
            | martha@foo.com | yes      | 2010-01-01 13:00:00 |
            | rick@foo.com   | yes      | 2010-01-02 12:00:00 |
        And I am logged in as administrator

    Scenario: Seeing created reports it the list
        Given I am on the dashboard page
         When I follow "Reports"
         Then I should see 2 reports in the list

    Scenario: Adding new report with default options
        Given I am on the report creation page
         When I fill in the following:
            | Name        | Report1           | 
            | Description | Lorem ipsum dolor |
          And I press "Create"
         Then I should be on the page of report "Report1"
          And I should see "Report has been successfully created."
          And I should see "There is no data to display"

    # Scenario: Adding new report with custom options
    #     Given I am on the report creation page
    #      When I fill in the following:
    #         | Name         | Report2           |
    #         | Description  | Lorem ipsum dolor |
    #       And I select "2010" from "sylius_report_dataFetcherConfiguration_end_year"
    #       And I select "1" from "sylius_report_dataFetcherConfiguration_end_month"
    #       And I select "5" from "sylius_report_dataFetcherConfiguration_end_day"
    #       And I press "Create" 
    #      Then I should be on the page of report "Report2"
    #       And I should see "Report has been successfully created."
    #       And I should see 1 results in the list

    Scenario: Accessing report details page from list
        Given I am on the report index page
         When I press "details" near "TableReport"
         Then I should be on the page of report "TableReport"

    Scenario: Deleting report from index page
        Given I am on the report index page
         When I press "delete" near "TableReport"
         Then I should be on the report index page
          And I should see "Report has been successfully deleted."
          And I should not see product with name "TableReport" in that list

    Scenario: Deleting report from details page
        Given I am on the page of report "TableReport"
         When I press "delete"
         Then I should be on the report index page
          And I should see "Report has been successfully deleted."
          And I should not see product with name "TableReport" in that list