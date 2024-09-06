@managing_catalog_promotions
Feature: Filtering catalog promotions by status
    In order to see catalog promotions with a specific status
    As an Administrator
    I want to be able to filter catalog promotions on the list

    Background:
        Given the store operates on a channel named "Web-US"
        And there is a catalog promotion with "winter-sale" code and "Winter sale" name
        And this catalog promotion is active
        And there is a catalog promotion with "spring-sale" code and "Spring sale" name
        And this catalog promotion is active
        And there is a catalog promotion with "surprise-sale" code and "Surprise sale" name
        And this catalog promotion is disabled
        And there is a catalog promotion with "special-sale" code and "Special sale" name
        And this catalog promotion is disabled
        And I am logged in as an administrator

    @ui @api
    Scenario: Filtering enabled catalog promotions
        When I browse catalog promotions
        And I filter enabled catalog promotions
        Then I should see a catalog promotion with name "Spring sale"
        And I should see a catalog promotion with name "Winter sale"
        But I should not see a catalog promotion with name "Surprise sale"
        And I should not see a catalog promotion with name "Special sale"

    @ui @api
    Scenario: Filtering active catalog promotions
        When I browse catalog promotions
        And I filter by active state
        Then I should see a catalog promotion with name "Spring sale"
        And I should see a catalog promotion with name "Winter sale"
        But I should not see a catalog promotion with name "Surprise sale"
        And I should not see a catalog promotion with name "Special sale"

    @ui @api
    Scenario: Filtering inactive catalog promotions
        When I browse catalog promotions
        And I filter by inactive state
        Then I should see a catalog promotion with name "Surprise sale"
        And I should see a catalog promotion with name "Special sale"
        But I should not see a catalog promotion with name "Winter sale"
        And I should not see a catalog promotion with name "Spring sale"
