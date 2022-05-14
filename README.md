# CPS 475 - Secure Application Development Projects

## John Conroy
## <conroyj4@udayton.edu>

### Project Repository Information

This repository includes three projects:
1. A Chat Client written in JavaScript, and a Chat Server written in Go. 
	1. Multiple clients can connect to the server, successful login required.
	2. The user can send public messages, private messages, return the current userlist, and disconnect.
2. A web page Form and Index written in PHP. 
	1. The form page requires a username and password and verifies these inputs against a SQL database. 
	2. Upon successful login, the user is sent to the index page. 
	3. The user may register a new account or change their password, both of which having their own forms and querying a SQL database.
	4. The user may logout.
3. miniFacebook (Team Project), a web-based social media platform written in php.
	1. The form page requires a username and password and verifies these inputs against a SQL database. 
		1. The user may also register a new account which updates an SQL database.
	3. Upon successful login, the user is sent to an index page where the user can:
		1. Change their password
		2. Create a forum post
			1. Edit a forum post
			2. Delete a forum post
		3. Comment on a forum post
		4. Logout
	4. There is also a separate admin page for super users
		1. The admin form only allows super users to login
		2. Upon login, an admin may view the current registered user list


### Secure Feature Implementation

This repository includes several secure application development features:
1. The system is deployed on HTTPS.
	1. All transaction data is encrypted and protected.
2. The HTML outputs are sanitized.
	1. Cross-site scripting attacks are prevented.
3. Prepared Statements are implemented for SQL queries.
	1. SQL Injection Attacks are prevented.
4. The system is protected from session hijacking.
	1. HttpOnly and Secure Cookies for Session.
	2. Browser information is recorded and verified.
5. The system implements Session authentication.
	1. Protected pages are only accessible to logged-in users.
6. The system implements access control for the database.
	1. A user cannot change the password of another user.
7. The system implements secret token generation and validation.
	1. CSRF attacks will be prevented.
