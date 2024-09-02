@managing_countries
Feature: Managing provinces of a country
    In order to add or remove provinces in existing countries
    As an Administrator
    I want to be able to edit a country and its provinces

    Background:
        Given the store has country "United Kingdom"
        And I am logged in as an administrator

    @ui @mink:chromedriver @api
    Scenario: Adding a province to an existing country
        When I want to edit this country
        And I add the "Scotland" province with "GB-SCT" code
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this country should have the "Scotland" province

    @ui @mink:chromedriver @api
    Scenario: Removing a province from an existing country
        Given this country has the "Northern Ireland" province with "GB-NIR" code
        When I want to edit this country
        And I delete the "Northern Ireland" province of this country
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this country should not have the "Northern Ireland" province

    @ui @mink:chromedriver @api
    Scenario: Removing a province that is a zone member should not be possible
        Given this country has the "Northern Ireland" province with "GB-NIR" code
        And this country also has the "Scotland" province with "GB-SCT" code
        And this country also has the "England" province with "GB-ENG" code
        And the store has a zone "Great Britain" with code "GB"
        And it has the "Northern Ireland" province member
        And it also has the "Scotland" province member
        And it also has the "England" province member
        When I want to edit this country
        And I delete the "Northern Ireland" province of this country
        And I also delete the "Scotland" province of this country
        And I save my changes
        Then I should be notified that provinces that are in use cannot be deleted
        And this country should still have the "Northern Ireland" province
        And this country should still have the "Scotland" province
        And this country should still have the "England" province

    @ui @mink:chromedriver @api
    Scenario: Removing a province that is not a zone member anymore should be possible
        Given this country has the "Northern Ireland" province with "GB-NIR" code
        And this country also has the "Scotland" province with "GB-SCT" code
        And this country also has the "England" province with "GB-ENG" code
        And the store has a zone "Great Britain" with code "GB"
        And it has the "Northern Ireland" province member
        And it also has the "Scotland" province member
        And it also has the "England" province member
        And the "England" province member has been removed from this zone
        When I am editing this country
        And I delete the "England" province of this country
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this country should not have the "England" province
        And this country should still have the "Northern Ireland" and "Scotland" provinces

    @ui @mink:chromedriver @api
    Scenario: Removing and adding a new province to an existing country
        Given this country has the "Northern Ireland" province with "GB-NIR" code
        When I want to edit this country
        And I delete the "Northern Ireland" province of this country
        And I add the "Scotland" province with "GB-SCT" code
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this country should not have the "Northern Ireland" province
        And this country should have the "Scotland" province

    @ui @mink:chromedriver @api
    Scenario: Adding a province with an austrian province code
        When I want to edit this country
        And I add the "Wien" province with "AT-9" code
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this country should have the "Wien" province
