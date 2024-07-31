@adding_product_review
Feature: Product review validation
    In order to avoid making mistakes while submitting a new product review
    As a Visitor
    I want to be prevented from submitting invalid review

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Necronomicon"

    @ui @api
    Scenario: Trying to add a product review without specifying a rate
        When I want to review product "Necronomicon"
        And I leave a comment "This book made me sad, but plot was fine.", titled "Not good, not bad" as "bartholomew@heaven.com"
        But I do not rate it
        And I try to add it
        Then I should be notified that I must check review rating

    @ui @api
    Scenario: Trying to add a product review without specifying a title
        When I want to review product "Necronomicon"
        And I leave a comment "This book made me sad, but plot was fine." as "bartholomew@heaven.com"
        And I rate it with 3 points
        And I try to add it
        Then I should be notified that title is required

    @ui @api
    Scenario: Trying to add a product review with too short title
        When I want to review product "Necronomicon"
        And I leave a comment "This book made me sad, but plot was fine.", titled "X" as "bartholomew@heaven.com"
        And I rate it with 3 points
        And I try to add it
        Then I should be notified that title must have at least 2 characters

    @ui @api
    Scenario: Trying to add a product review with too long title
        When I want to review product "Necronomicon"
        And I leave a comment "This book made me sad, but plot was fine." as "bartholomew@heaven.com"
        And I title it with very long title
        And I rate it with 3 points
        And I try to add it
        Then I should be notified that title must have at most 255 characters

    @ui @api
    Scenario: Trying to add a product review without specifying a comment
        When I want to review product "Necronomicon"
        And I leave a review titled "Not good, not bad" as "bartholomew@heaven.com"
        And I rate it with 3 points
        And I try to add it
        Then I should be notified that comment is required

    @ui @api
    Scenario: Trying to add a product review without specifying an author email
        When I want to review product "Necronomicon"
        And I leave a comment "Not good, not bad", titled "Not good, not bad"
        And I rate it with 3 points
        And I try to add it
        Then I should be notified that I must enter my email

    @ui @api
    Scenario: Trying to add a product review with specifying already registered author email
        Given there is a customer account "sam@winchester.com" identified by "familybusiness"
        When I want to review product "Necronomicon"
        And I leave a comment "Really good book, with many important info.", titled "Usefull" as "sam@winchester.com"
        And I rate it with 4 points
        And I try to add it
        Then I should be notified that this email is already registered

    @api
    Scenario: Trying to add a product review with an out-of-range rate
        When I want to review product "Necronomicon"
        And I leave a comment "This book made me sad, but plot was fine.", titled "Not good, not bad" as "example@example.com"
        And I rate it with 6 points
        And I try to add it
        Then I should be notified that rating must be between 1 and 5
