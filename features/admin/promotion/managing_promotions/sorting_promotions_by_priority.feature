@managing_promotions
Feature: Sorting listed promotions by priority
    In order to change the order by which promotions are used
    As an Administrator
    I want to sort promotions by their priority

    Background:
        Given the store operates on a single channel in "United States"
        And there is a promotion "Honour Harambe" with priority 2
        And there is a promotion "Gimme An Owl" with priority 1
        And there is a promotion "Pugs For Everyone" with priority 0
        And I am logged in as an administrator

    @ui
    Scenario: Promotions are sorted by priority in descending order by default
        When I want to browse promotions
        Then I should see 3 promotions on the list
        And the first promotion on the list should have name "Honour Harambe"
        And the last promotion on the list should have name "Pugs For Everyone"

    @ui
    Scenario: Promotion's default priority is 0 which puts it at the bottom of the list
        Given there is a promotion "Flying Pigs"
        When I want to browse promotions
        Then I should see 4 promotions on the list
        And the last promotion on the list should have name "Flying Pigs"

    @ui
    Scenario: Promotion added with priority -1 is set at the top of the list
        Given there is a promotion "Flying Pigs" with priority -1
        When I want to browse promotions
        Then I should see 4 promotions on the list
        And the first promotion on the list should have name "Flying Pigs"
