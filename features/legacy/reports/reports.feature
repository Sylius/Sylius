@legacy @report
Feature: Reports
    In order to see my store statistics
    As a store owner
    I want to be able to manage reports

    Background:
        Given store has default configuration
        And there are following reports configured:
            | code             | name           | description | renderer              | renderer_configuration                                       | data_fetcher                          | data_fetcher_configuration                   |
            | table_report     | TableReport    | Lorem ipsum | sylius_renderer_table | Template:SyliusReportBundle:Table:default.html.twig          | sylius_data_fetcher_user_registration | Period:day,Start:2010-01-01,End:2010-04-01   |
            | sales_report     | SalesReport    | Lorem ipsum | sylius_renderer_table | Template:SyliusReportBundle:Table:default.html.twig          | sylius_data_fetcher_sales_total       | Period:day,Start:2010-01-01,End:2010-01-05   |
            | bar_chart_report | BarChartReport | Lorem ipsum | sylius_renderer_chart | Type:bar,Template:SyliusReportBundle:Chart:default.html.twig | sylius_data_fetcher_user_registration | Period:month,Start:2010-01-01,End:2010-04-01 |
        And there are following users:
            | email          | enabled | created at          |
            | beth@foo.com   | yes     | 2010-01-02 12:00:00 |
            | martha@foo.com | yes     | 2010-01-02 13:00:00 |
            | rick@foo.com   | yes     | 2010-01-03 12:00:00 |
        And I am logged in as administrator

    Scenario: Seeing created reports it the list
        Given I am on the dashboard page
        When I follow "Reports"
        Then I should see 3 reports in the list

    Scenario: Adding new report with default options
        Given I am on the report creation page
        When I fill in the following:
            | Code        | report1           |
            | Name        | Report1           |
            | Description | Lorem ipsum dolor |
        And I press "Create"
        Then I should be on the page of report "Report1"
        And I should see "Report has been successfully created"
        And I should see 1 result in the list

    Scenario: Adding new report with custom end date option
        Given I am on the report creation page
        When I fill in the following:
            | Code        | report2           |
            | Name        | Report2           |
            | Description | Lorem ipsum dolor |
        And I select "2010" from "sylius_report_dataFetcherConfiguration_start_year"
        And I select "2010" from "sylius_report_dataFetcherConfiguration_end_year"
        And I select "Jan" from "sylius_report_dataFetcherConfiguration_start_month"
        And I select "Jan" from "sylius_report_dataFetcherConfiguration_end_month"
        And I select "1" from "sylius_report_dataFetcherConfiguration_start_day"
        And I select "3" from "sylius_report_dataFetcherConfiguration_end_day"
        And I press "Create"
        Then I should be on the page of report "Report2"
        And I should see "Report has been successfully created"
        And I should see 2 results in the list

    Scenario: Adding new report with some custom options
        Given I am on the report creation page
        When I fill in the following:
            | Code        | report3           |
            | Name        | Report3           |
            | Description | Lorem ipsum dolor |
        And I select "2010" from "sylius_report_dataFetcherConfiguration_start_year"
        And I select "2010" from "sylius_report_dataFetcherConfiguration_end_year"
        And I select "Jan" from "sylius_report_dataFetcherConfiguration_start_month"
        And I select "Jan" from "sylius_report_dataFetcherConfiguration_end_month"
        And I select "1" from "sylius_report_dataFetcherConfiguration_start_day"
        And I select "3" from "sylius_report_dataFetcherConfiguration_end_day"
        And I select "month" from "Time period"
        And I press "Create"
        Then I should be on the page of report "Report3"
        And I should see "Report has been successfully created"
        And I should see 1 results in the list

    Scenario: Prevent adding new report with the same code that has been used before
        Given I am on the report creation page
        When I fill in the following:
            | Code        | table_report      |
            | Name        | Report1           |
            | Description | Lorem ipsum dolor |
        And I press "Create"
        Then I should still be on the report creation page
        And I should see "This code is already in use"

    Scenario: Prevent adding new report with multiple-word code
        Given I am on the report creation page
        When I fill in the following:
            | Code        | table report      |
            | Name        | Report1           |
            | Description | Lorem ipsum dolor |
        And I press "Create"
        Then I should still be on the report creation page
        And I should see "Report code should be a single word"

    Scenario: Accessing report details page from list
        Given I am on the report index page
        When I press "Details" near "TableReport"
        Then I should be on the page of report "TableReport"

    Scenario: Accessing report edit form from list
        Given I am on the report index page
        When I press "Edit" near "TableReport"
        Then I should be editing report with name "TableReport"

    Scenario: Accessing report with chart renderer details page
        Given I am on the report index page
        When I press "Details" near "BarChartReport"
        Then I should be on the page of report "BarChartReport"
        And "canvas" should appear on the page

    Scenario: Accessing report edit form from details page
        Given I am on the page of report "TableReport"
        When I click "Edit"
        Then I should be editing report with name "TableReport"

    Scenario: Updating the report
        Given I am editing report "TableReport"
        When I fill in "Name" with "TableReportEdited"
        And I fill in "Description" with "Lorem ipsum dolor"
        And I press "Save changes"
        Then I should see "Report has been successfully updated"
        And "reportEdited" should appear on the page
        And "Lorem ipsum dolor" should appear on the page

    Scenario: Deleting report from index page
        Given I am on the report index page
        When I press "Delete" near "TableReport"
        Then I should be on the report index page
        And I should see "Report has been successfully deleted"
        And I should not see report with name "TableReport" in that list

    Scenario: Deleting report from details page
        Given I am on the page of report "TableReport"
        When I press "Delete"
        Then I should be on the report index page
        And I should see "Report has been successfully deleted"
        And I should not see report with name "TableReport" in that list

    Scenario: Cannot update report code
        When I am editing report "TableReport"
        Then the code field should be disabled

    Scenario: Try adding new report without code
        Given I am on the report creation page
        When I fill in the following:
            | Name        | Report1           |
            | Description | Lorem ipsum dolor |
        And I press "Create"
        Then I should still be on the report creation page
        And I should see "Report code cannot be blank"

    Scenario: Getting the correct sum with exchange rate
        Given the following zones are defined:
            | name        | type    | members                 |
            | Scandinavia | country | Norway, Sweden, Finland |
            | France      | country | France                  |
        And there are following shipping categories:
            | code | name    |
            | SC1  | Regular |
            | SC2  | Heavy   |
        And the following shipping methods exist:
            | code | category | zone        | name |
            | SM1  | Regular  | Scandinavia | DHL  |
            | SM2  | Heavy    | France      | UPS  |
        And the following products exist:
            | name | price | sku |
            | Mug  | 10    | 456 |
            | Book | 22    | 948 |
        And the following orders exist:
            | customer                | shipment                 | address                                                           | currency | exchange_rate |
            | sylius@example.com      | UPS, shipped, DTBHH380HG | Théophile Morel, 17 avenue Jean Portalis, 33000, Bordeaux, France | EUR      | 1.00000       |
            | linustorvalds@linux.com | DHL, shipped, DTBHH380HH | Linus Torvalds, Väätäjänniementie 59, 00440, Helsinki, Finland    | USD      | 0.5           |
            | sylius@example.com      | UPS, shipped, DTBHH380HI | Théophile Morel, 17 avenue Jean Portalis, 33000, Bordeaux, France | GBP      | 1.25          |
        And order #000000001 has following items:
            | product | quantity |
            | Mug     | 2        |
            | Book    | 1        |
        And order #000000002 has following items:
            | product | quantity |
            | Mug     | 2        |
            | Book    | 1        |
        And order #000000003 has following items:
            | product | quantity |
            | Mug     | 2        |
            | Book    | 1        |
        And order #000000001 will be completed on "2010-01-01"
        And order #000000002 will be completed on "2010-01-02"
        And order #000000003 will be completed on "2010-01-02"
        When I am on the page of report "SalesReport"
        Then the report row for date "2010-01-01" will have a total amount of "42"
        Then the report row for date "2010-01-02" will have a total amount of "73.50"
