# Use raw data in commands and events

* Status: accepted
* Date: 2022-03-18

## Context and Problem Statement

While defining new commands or events we may define them with entities or any objects. Objects passed to commands or 
events may desynchronize with the main object which can be causing problems with handlers that may operate on wrong data.

## Decision Drivers <!-- optional -->

* always operate on the actual state of the object
