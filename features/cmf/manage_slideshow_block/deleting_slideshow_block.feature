@managing_slideshow_blocks
Feature: Deleting a slideshow
    In order to remove test, obsolete or incorrect slideshows
    As an Administrator
    I want to be able to delete a slideshow

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Deleted taxon should disappear from the registry
        Given the store has slideshow "Slideshow for Christmas" with name "slideshow-for-christmas"
        When I delete slideshow block "Slideshow for Christmas"
        Then I should be notified that it has been successfully deleted
        And the slideshow block "Slideshow for Christmas" should no longer exist in the store
