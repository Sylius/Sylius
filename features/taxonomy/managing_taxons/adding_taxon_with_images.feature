@managing_taxons
Feature: Adding a new taxon with images
    In order to categorize my merchandise
    As an Administrator
    I want to add a new taxon to the registry

    Background:
        Given the store is available in "English (United States)"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Adding a new taxon with a single image
        Given I want to create a new taxon
        When I specify its code as "t-shirts"
        And I name it "T-Shirts" in "English (United States)"
        And I attach the "t-shirts.jpg" image with a code "banner"
        And I add it
        Then I should be notified that it has been successfully created
        And the "T-Shirts" taxon should appear in the registry
        And this taxon should have an image with a code "banner"

    @ui @javascript
    Scenario: Adding a new taxon with multiple images
        Given I want to create a new taxon
        When I specify its code as "t-shirts"
        And I name it "T-Shirts" in "English (United States)"
        And I attach the "t-shirts.jpg" image with a code "banner"
        And I attach the "t-shirts.jpg" image with a code "thumbnail"
        And I add it
        Then I should be notified that it has been successfully created
        And the "T-Shirts" taxon should appear in the registry
        And this taxon should have an image with a code "banner"
        And this taxon should have also an image with a code "thumbnail"
