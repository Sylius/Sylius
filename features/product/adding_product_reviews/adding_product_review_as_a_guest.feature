@adding_product_review
Feature: Adding product review as a guest
    In order to share my opinion about product with other customers
    As a Visitor
    I want to be able to add product review

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Necronomicon"

    @ui @javascript
    Scenario: Adding product reviews as a guest
        Given I want to review product "Necronomicon"
        When I leave a comment "I'm never gonna read this terrible book again.", titled "Never again" as "castiel@heaven.com"
        And I rate it with 1 point
        And I add it
        Then I should be notified that my review is waiting for the acceptation
