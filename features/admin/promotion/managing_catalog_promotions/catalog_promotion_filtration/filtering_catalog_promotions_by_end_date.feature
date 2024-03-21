@managing_catalog_promotions
Feature: Filtering catalog promotions by end date
    In order to see catalog promotions from an end date range
    As an Administrator
    I want to be able to filter catalog promotions on the list

    Background:
        Given the store operates on a single channel
        And there is a catalog promotion with "winter-sale" code and "Winter sale" name
        And this catalog promotion operates between "2021-12-20" and "2021-12-30"
        And there is a catalog promotion with "spring-sale" code and "Spring sale" name
        And this catalog promotion operates between "2022-04-01" and "2022-05-01"
        And there is a catalog promotion with "surprise-sale" code and "Surprise sale" name
        And this catalog promotion operates between "2021-07-01" and "2022-05-04"
        And I am logged in as an administrator

    @ui @api
    Scenario: Filtering catalog promotions from end date
        When I browse catalog promotions
        And I filter by end date from "2022-05-01"
        Then I should see a catalog promotion with name "Surprise sale"
        And I should see a catalog promotion with name "Spring sale"
        But I should not see a catalog promotion with name "Winter sale"

    @ui @api
    Scenario: Filtering catalog promotions up to end date
        When I browse catalog promotions
        And I filter by end date up to "2022-05-01"
        Then I should see a catalog promotion with name "Spring sale"
        And I should see a catalog promotion with name "Winter sale"
        But I should not see a catalog promotion with name "Surprise sale"

    @ui @api
    Scenario: Filtering catalog promotions in an end date range
        When I browse catalog promotions
        And I filter by end date from "2022-04-02" up to "2022-05-03"
        Then I should see a catalog promotion with name "Spring sale"
        But I should not see a catalog promotion with name "Surprise sale"
        And I should not see a catalog promotion with name "Winter sale"
