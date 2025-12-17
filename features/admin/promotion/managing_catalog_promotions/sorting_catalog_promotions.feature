@managing_catalog_promotions
Feature: Sorting listed catalog promotion
    In order to change the order by which catalog promotions are displayed
    As an Administrator
    I want to sort catalog promotions

    Background:
        Given the store operates on a single channel in "United States"
        And there is a catalog promotion with "a" code and "A" name
        And the catalog promotion "A" starts at "10-01-2023"
        And this catalog promotion is disabled
        And there is a catalog promotion with "not-b" code and "B" name
        And the catalog promotion "B" ended "10-02-2023"
        And there is a catalog promotion with "c" code and "C" name
        And the catalog promotion "C" operates between "01-01-2023" and "01-03-2023"
        And its priority is 2
        And I am logged in as an administrator

    @api @ui
    Scenario: Catalog promotions are sorted by ascending code by default
        When I browse catalog promotions
        Then I should see 3 catalog promotions on the list
        And the first catalog promotion should have code "a"

    @api @ui
    Scenario: Changing the code sorting order to descending
        When I browse catalog promotions
        And I sort catalog promotions by descending code
        Then I should see 3 catalog promotions on the list
        And the first catalog promotion should have code "not-b"

    @api @ui
    Scenario: Sorting catalog promotions by name in ascending order
        When I browse catalog promotions
        And I sort catalog promotions by ascending name
        Then I should see 3 catalog promotions on the list
        And the first catalog promotion should have code "a"

    @api @ui
    Scenario: Sorting catalog promotion by name in descending order
        When I browse catalog promotions
        And I sort catalog promotions by descending name
        Then I should see 3 catalog promotions on the list
        And the first catalog promotion should have code "c"

    @api @ui @no-postgres
    Scenario: Sorting catalog promotion by start date in ascending order
        When I browse catalog promotions
        And I sort catalog promotions by ascending "start date"
        Then I should see 3 catalog promotions on the list
        And the first catalog promotion should have code "not-b"

    @api @ui @no-postgres
    Scenario: Sorting catalog promotion by start date in descending order
        When I browse catalog promotions
        And I sort catalog promotions by descending "start date"
        Then I should see 3 catalog promotions on the list
        And the first catalog promotion should have code "a"

    @api @ui @no-postgres
    Scenario: Sorting catalog promotion by end date in ascending order
        When I browse catalog promotions
        And I sort catalog promotions by ascending "end date"
        Then I should see 3 catalog promotions on the list
        And the first catalog promotion should have code "a"

    @api @ui @no-postgres
    Scenario: Sorting catalog promotion by end date in descending order
        When I browse catalog promotions
        And I sort catalog promotions by descending "end date"
        Then I should see 3 catalog promotions on the list
        And the first catalog promotion should have code "c"

    @api @ui
    Scenario: Sorting catalog promotion by priority in ascending order
        When I browse catalog promotions
        And I sort catalog promotions by ascending priority
        Then I should see 3 catalog promotions on the list
        And the first catalog promotion should have code "not-b"

    @api @ui
    Scenario: Sorting catalog promotion by priority in descending order
        When I browse catalog promotions
        And I sort catalog promotions by descending priority
        Then I should see 3 catalog promotions on the list
        And the first catalog promotion should have code "c"
