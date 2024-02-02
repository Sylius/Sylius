@managing_catalog_promotions
Feature: Filtering catalog promotions by search
    In order to quickly find promotions with a specific name or code
    As an Administrator
    I want to be able to filter catalog promotions on the list

    Background:
        Given the store operates on a single channel
        And there is a catalog promotion with "winter-sale-1" code and "Winter sale" name
        And there is a catalog promotion with "hunter-sale-2" code and "Hunter sale" name
        And there is a catalog promotion with "surprise-sale-12" code and "Surprise sale" name
        And I am logged in as an administrator

    @ui @api
    Scenario: Filtering catalog promotions by full name
        When I browse catalog promotions
        And I search by "Surprise sale" name
        Then I should see a catalog promotion with name "Surprise sale"
        But I should not see a catalog promotion with name "Hunter sale"
        And I should not see a catalog promotion with name "Winter sale"

    @ui @api
    Scenario: Filtering catalog promotions by partial name
        When I browse catalog promotions
        And I search by "ter sale" name
        Then I should see a catalog promotion with name "Hunter sale"
        And I should see a catalog promotion with name "Winter sale"
        But I should not see a catalog promotion with name "Surprise sale"

    @ui @api
    Scenario: Filtering catalog promotions by full code
        When I browse catalog promotions
        And I search by "surprise-sale" code
        Then I should see a catalog promotion with name "Surprise sale"
        But I should not see a catalog promotion with name "Hunter sale"
        And I should not see a catalog promotion with name "Winter sale"

    @ui @api
    Scenario: Filtering catalog promotions by partial code
        When I browse catalog promotions
        And I search by "sale-1" code
        Then I should see a catalog promotion with name "Surprise sale"
        And I should see a catalog promotion with name "Winter sale"
        But I should not see a catalog promotion with name "Hunter sale"
