@managing_taxons
Feature: Taxon image unique code validation within a taxon
    In order to uniquely identify images within a taxon
    As an Administrator
    I want to be prevented from adding two images with the same code to the same taxon

    Background:
        Given the store is available in "English (United States)"
        And the store classifies its products as "T-Shirts" and "Mugs"
        And the "T-Shirts" taxon has an image "t-shirts.jpg" with a code "banner"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Adding images with the same code to different taxons
        When I want to modify the "Mugs" taxon
        And I attach the "mugs.jpg" image with a code "banner"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this taxon should have an image with a code "banner"

    @ui @javascript
    Scenario: Trying to add an image with a code that is already used by other image of this taxon
        When I want to modify the "T-Shirts" taxon
        And I attach the "mugs.jpg" image with a code "banner"
        And I try to save my changes
        Then I should be notified that the image with this code already exists
        And there should still be only one image in the "T-Shirts" taxon
