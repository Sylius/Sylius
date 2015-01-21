@reports
Feature: Reports
    In order to see my store statistics
    As a store owner
    I want to be able to manage reports

    Background:
        Given there are following reports configured:
            | id | name             | description | renderer type | data fetcher type |
            | 1  | TableReport      | Lorem ipsum | table         | user_registration |
            | 2  | BarChartRenderer | Lorem ipsum | chart - line  | user_registration |
        And I am logged in as administrator

    Scenario: Seeing created reports listed
        Given I am on the dashboard page
         When I follow "Reports"
         Then I should see 2 reports in the list