@managing_countries
Feature: Post code validation
    In order to avoid making mistakes when managing a post code
    As an Administrator
    I want to be prevented from adding invalid entities

    Background:
        Given the store has country "United Kingdom"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Trying to add a new post code without specifying its value
        When I want to create a new post code in country "United Kingdom"
        And I name the post code "Scotland"
        But I do not specify the post code value
        And I try to save changes
        Then I should be notified that "postCode" should not be blank
        And post code with name "Scotland" should not be added in this country

    @ui @javascript
    Scenario: Trying to add a new post code without specifying its name
        When I want to create a new post code in country "United Kingdom"
        And I specify the post code value as "123"
        But I do not name the post code
        And I try to save changes
        Then I should be notified that "postCode" should have a name
        And province with code "123" should not be added in this country

    @ui @javascript
    Scenario: Trying to insert a non-numeric post code
        When I want to create a new post code in country "United Kingdom"
        And I specify the post code value as "123a"
        But I name the post code "hello"
        And I try to save changes
        Then I should be notified that "postCode" has to be numerical
        And province with code "123" should not be added in this country
