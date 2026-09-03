@adding_product_review
Feature: Adding product review with multiline comment
    In order to share my detailed opinion about product with other customers
    As a Customer
    I want to be able to add product review with a detailed multiline comment

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Necronomicon"

    @ui @api
    Scenario: Adding product reviews as a logged in customer
        Given I am a logged in customer
        When I want to review product "Necronomicon"
        And I leave a comment "Great book for every advanced sorcerer.\nWarning: may summon demons. Five stars anyway.", titled "Scary but astonishing"
        And I rate it with 5 points
        And I add it
        Then I should be notified that my review is waiting for the acceptation
        And the "Scary but astonishing" product review of "Necronomicon" product should not be visible for customers
