###  [Declan Blanchard](https://github.com/declanblanc)'s Edit History for V (avzaman social)
**_4/18/24_**
* Added a function to the header file that establishes a boolean variable "`$logged_in`" that determines if the browser has stored user information. If the current browser is logged in, variable "`$username`" is created which stores their user ID. This change allows us to clean up some of the other code in the project where these variables are now used.
* Replaced all `$usflag` with `$user_liked`
* Realized `$user_liked` was now storing value with incorrect logic. Corrected values of all instances of `$user_liked` as well as if-statement logic to be more readable.
* Removed `$pageNum` functions from social-feed and social-profile and stored them in social-header.