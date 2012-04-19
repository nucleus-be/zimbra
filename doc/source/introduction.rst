Introduction to the Nucleus DATA API
====================================

This documentation describes the purpose and usage of the Nucleus DATA API, the API one can use to communicate with all information available on the Nucleus network, ranging from webhosting, Universal groupware and Online Backup to Colocation servers and domainnames.

The API is built upon restful principles and talks JSON in request and response formats.

Request types
*************

There are 4 request types used in the DATA API.

:GET: Get a resource from the server, this type of request NEVER modifies anything on the server-side. It's purpose is only to retrieve resources.
:POST: Create a new resource on the server
:PUT: Update an existing resource on the server
:DELETE: Delete a resource from the server

Sending data to the server
**************************

With ``POST`` and ``PUT`` requests you are required to send additional information to the server in most cases. Either the info needed to create a resource of to update it. The DATA API is built only to accept JSON as format for data. This means the following:

* You'll have to put a valid content-type (application/json) in your request
* Data should be UTF-8 encoded
* The JSON data should be sent in the request body as an encoded and valid json string, not as a query parameter or argument!