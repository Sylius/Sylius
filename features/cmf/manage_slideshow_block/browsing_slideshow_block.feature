@managing_slideshow_blocks
Feature: Browsing slideshow blocks
    In order to see all slideshow blocks in the store
    As an Administrator
    I want to browse slideshow blocks

    Background:
        Given the store has slideshow "Slideshow for Christmas" with name "slideshow-for-christmas"
        And I am logged in as an administrator

    @ui
    Scenario: Browsing slideshow blocks in store
        When I want to browse slideshow blocks of the store
        Then I should see 1 slideshow blocks in the list
        And I should see the slideshow block "Slideshow for Christmas" in the list

