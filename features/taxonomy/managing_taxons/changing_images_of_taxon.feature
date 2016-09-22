@managing_taxons
Feature: Changing images of an existing taxon
    In order to change images of my categories
    As an Administrator
    I want to be able to changing images of an existing taxon

    Background:
        Given the store is available in "English (United States)"
        And the store classifies its products as "T-Shirts"
        And the "T-Shirts" taxon has an image "mugs.jpg" with a code "banner"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Changing a single image of a taxon
        Given I want to modify the "T-Shirts" taxon
        When I change the image with the "banner" code to "t-shirts.jpg"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this taxon should have an image with a code "banner"

    @ui
    Scenario: Unable to change a code of an image
        When I want to modify the "T-Shirts" taxon
        Then the image code field should be disabled
