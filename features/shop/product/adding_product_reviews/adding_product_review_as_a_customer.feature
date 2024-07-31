@adding_product_review
Feature: Adding product review as a customer
    In order to share my opinion about product with other customers
    As a Customer
    I want to be able to add product review

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Necronomicon"

    @ui @api
    Scenario: Adding product reviews as a logged in customer
        Given I am a logged in customer
        When I want to review product "Necronomicon"
        And I leave a comment "Great book for every advanced sorcerer.", titled "Scary but astonishing"
        And I rate it with 5 points
        And I add it
        Then I should be notified that my review is waiting for the acceptation
        And the "Scary but astonishing" product review of "Necronomicon" product should not be visible for customers

    @ui @no-api
    Scenario: Adding product reviews as a logged in customer with remember me option
        Given I am a logged in customer by using remember me option
        When I want to review product "Necronomicon"
        And I leave a comment "Great book for every advanced sorcerer.", titled "Scary but astonishing"
        And I rate it with 3 points
        And I add it
        Then I should be notified that my review is waiting for the acceptation
