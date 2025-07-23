# ICC Meta Redirect
YOURLS plugin ICC Meta Redirect

Inspired by https://github.com/pureexe/Yourls-meta-redirect with some fixes.

This is Yourls plugin use to skip banned URLs on some services that check for redirected URLs to filter them. Usually they do not check meta redirects, you can use virtually any special character vefore your keyword like:
* "_" (underscore)
* "." (dot)

How to
===================
* Download the plugin release
* Update plugin.php if needed (default keyworkd is "." (dot)
* Upload folder `icc-meta-redirect` into `/user/plugins
* Go to the Plugins administration page and activate the plugin
* You can now use your character to make a meta redirect instead of server redirect, example: icc.gg/.signal

========================

Tested on YOURLS 1.10+
