PHP Terminal Emulator
=====================

This is designed to be a stand-alone Terminal Emulator useful when you
don't have ssh access.
It is limited to the permissions of the user PHP is set up to run commands
under, generally _www.

Installation
------------
Drop this file in the desired directory and have at it.

Commands
--------

These are the built in commands:

* clear -- Clears all previous commands and responses.
Acts as if you just logged in.

* logout -- Clears the session and re-enables the password protection.