
# Storybridge
Storybridge Lights Website

# Idea

Create a website that scraps data from the council website thats mobile friendly and looks decent around the story bridge.

# Solution

I went to upwork and created a website. Spent $350AU on a design and code

Currently looking to promote the website and setup a dedicated page on AWS.

# Setup - Read me

To set the login username and password, add a file called 'login.php' in the folder 'data'.
Its contents are:

<?php $ADMIN_AUTH = "username:password";

If this file doesn't exist, the default username and password will be "admin:blinkenlichten".

Also, while not stricly necessary, it is recommended to set a cronjob which calls /php/scrape_now.php every day.

# Partner and Madprops

Team at Dotlabs.co and David Trapps
http://www.dotlabs.at/

# Demo

http://storybridge.app.dotlabs.co/
