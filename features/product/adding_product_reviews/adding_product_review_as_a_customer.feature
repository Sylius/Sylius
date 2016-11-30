@adding_product_review
Feature: Adding product review as a customer
    In order to share my opinion about product with other customers
    As a Customer
    I want to be able to add product review

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Necronomicon"
        And I am a logged in customer

    @ui @javascript
    Scenario: Adding product reviews as a logged in customer
        Given I want to review product "Necronomicon"
        When I leave a comment "Great book for every advanced sorcerer.", titled "Scary but astonishing"
        And I rate it with 5 points
        And I add it
        Then I should be notified that my review is waiting for the acceptation
