@managing_string_blocks
Feature: Browsing string blocks
    In order to see all string blocks in the store
    As an Administrator
    I want to browse string blocks

    Background:
        Given the store has string block "summer-sale-info"
        And I am logged in as an administrator

    @ui
    Scenario: Browsing string blocks in store
        When I browse string blocks of the store
        Then I should see 1 string blocks in the list
        And I should see the string block "summer-sale-info" in the list
