@managing_tax_rates
Feature: Filtering tax rates by start date
    In order to see tax rates from a start date range
    As an Administrator
    I want to be able to filter tax rates on the list

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "2022 tax rate" tax rate of 50% for "Clothes" within the "US" zone with dates between "2022-01-01" and "2022-12-31"
        And the store has "2023 tax rate" tax rate of 15% for "Clothes" within the "US" zone with dates between "2023-01-01" and "2023-12-31"
        And the store has "3 weeks tax rate" tax rate of 25% for "Clothes" within the "US" zone with dates between "2022-12-24" and "2023-01-15"
        And I am logged in as an administrator

    @ui @api
    Scenario: Filtering tax rates from start date
        When I browse tax rates
        And I filter tax rates by start date from "2022-12-26"
        Then I should not see a tax rate with name "2022 tax rate"
        And I should not see a tax rate with name "3 weeks tax rate"
        But I should see the tax rate "2023 tax rate" in the list

    @ui @api
    Scenario: Filtering catalog promotions up to start date
        When I browse tax rates
        And I filter tax rates by start date up to "2022-12-22"
        Then I should not see a tax rate with name "2023 tax rate"
        And I should not see a tax rate with name "3 weeks tax rate"
        But I should see the tax rate "2022 tax rate" in the list

    @ui @api
    Scenario: Filtering catalog promotions in a start date range
        When I browse tax rates
        And I filter tax rates by start date from "2022-08-20" up to "2022-12-26"
        Then I should not see a tax rate with name "2023 tax rate"
        And I should not see a tax rate with name "2022 tax rate"
        But I should see the tax rate "3 weeks tax rate" in the list
