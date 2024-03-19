@managing_tax_rates
Feature: Filtering tax rates by end date
    In order to see tax rates from an end date range
    As an Administrator
    I want to be able to filter tax rates on the list

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "2022 tax rate" tax rate of 50% for "Clothes" within the "US" zone with dates between "2022-01-01" and "2022-12-31"
        And the store has "2023 tax rate" tax rate of 15% for "Clothes" within the "US" zone with dates between "2023-01-01" and "2023-12-31"
        And the store has "3 weeks tax rate" tax rate of 25% for "Clothes" within the "US" zone with dates between "2022-12-24" and "2023-01-15"
        And I am logged in as an administrator

    @ui @api
    Scenario: Filtering tax rates from end date
        When I browse tax rates
        And I filter tax rates by end date from "2022-12-26"
        Then I should see the tax rate "2023 tax rate" in the list
        And I should see the tax rate "2022 tax rate" in the list
        And I should see the tax rate "3 weeks tax rate" in the list

    @ui @api
    Scenario: Filtering tax rates up to end date
        When I browse tax rates
        And I filter tax rates by end date up to "2022-12-31"
        Then I should not see a tax rate with name "2023 tax rate"
        And I should not see a tax rate with name "3 weeks tax rate"
        But I should see the tax rate "2022 tax rate" in the list

    @ui @api
    Scenario: Filtering tax rates in a end date range
        When I browse tax rates
        And I filter tax rates by end date from "2023-01-02" up to "2023-01-31"
        Then I should not see a tax rate with name "2023 tax rate"
        And I should not see a tax rate with name "2022 tax rate"
        But I should see the tax rate "3 weeks tax rate" in the list
