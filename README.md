PHP Terminal Emulator
=====================

This is designed to be a stand-alone Terminal Emulator useful for when you
don't have ssh access.
It is limited to the permissions of the PHP user.

Installation
------------

Change the password at the top of the code slightly for security. Copy the password to your clipboard. Drop this file in the desired directory. Paste in the password and have at it.

Commands
--------

These are the built in commands:

* clear -- Clears all previous commands and responses.
Acts as if you just logged in.

* logout -- Clears the session and re-enables the password protection.

You will notice the Persist button at the right. If, for example, you need to change directories, you must Persist that command. Commands which persist will execute each time a new command is entered.
