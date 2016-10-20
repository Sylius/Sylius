@adding_product_review
Feature: Review validation
    In order to avoid making mistakes while submitting a new product review
    As a Visitor
    I want to be prevented from submitting invalid review

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Necronomicon"

    @ui @javascript
    Scenario: Adding a product review without specifying a rate
        Given I want to review product "Necronomicon"
        When I leave a comment "This book made me sad, but plot was fine.", titled "Not good, not bad" as "bartholomew@heaven.com"
        But I do not rate it
        Then I should be notified that I must check review rating

    @ui @javascript
    Scenario: Adding a product review without specifying a title
        Given I want to review product "Necronomicon"
        When I leave a comment "This book made me sad, but plot was fine." as "bartholomew@heaven.com"
        And I rate it with 3 points
        Then I should be notified that I title is required
