@managing_taxons
Feature: Adding images to an existing taxon
    In order to change images of my categories
    As an Administrator
    I want to be able to add new images to a taxon

    Background:
        Given the store is available in "English (United States)"
        And the store classifies its products as "T-Shirts"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Adding a single image to an existing taxon
        Given I want to modify the "T-Shirts" taxon
        When I attach the "t-shirts.jpg" image with a code "banner"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this taxon should have an image with a code "banner"

    @ui @javascript
    Scenario: Adding multiple images to an existing taxon
        Given I want to modify the "T-Shirts" taxon
        When I attach the "t-shirts.jpg" image with a code "banner"
        And I attach the "t-shirts.jpg" image with a code "thumbnail"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this taxon should have an image with a code "banner"
        And this taxon should have also an image with a code "thumbnail"
