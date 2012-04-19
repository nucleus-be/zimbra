Nucleus Universal Groupware
===========================

This is the API documentations for the Nucleus Universal Groupware product. It allows you to retrieve, update, delete and create accounts and domains. You can also retrieve information on usage of individual accounts or manage aliasses and forwards for individual accounts.

Domain Resource
-----------------

:Resource URI: /nug/domain/
:Properties:
   :id: the internal ID of this domain on the NUG server
   :name: the name of this domain
   :defaultCosId: the default COS for accounts created under this domain

Usage
*****

.. http:method:: GET /nug/domain/

   Retrieve a list of all domains available on the server

   :response OK 200: succesfully retrieved a list of domains

   :Request:
      .. code-block:: http

         GET /nug/domain/ HTTP/1.1
         Host: http://data.nucleus.be
         Connection: close

   :Response:

      .. code-block:: json

         [{
             "id": "d60c6cbc-6c53-456e-ad3d-3b75117cbc64",
             "defaultCosId": null,
             "name": "chris.be",
             "uri": "\/nug\/domain\/d60c6cbc-6c53-456e-ad3d-3b75117cbc64\/",
             "subresources": {
                 "account_list": "\/nug\/domain\/d60c6cbc-6c53-456e-ad3d-3b75117cbc64\/account\/"
             }
         }, {
             "id": "9f61f68f-fcd2-460e-8098-de01d250b5df",
             "defaultCosId": "e00428a1-0c00-11d9-836a-000d93afea2a",
             "name": "mail.webruimte.eu",
             "uri": "\/nug\/domain\/9f61f68f-fcd2-460e-8098-de01d250b5df\/",
             "subresources": {
                 "account_list": "\/nug\/domain\/9f61f68f-fcd2-460e-8098-de01d250b5df\/account\/",
                 "default_cos": "\/nug\/cos\/e00428a1-0c00-11d9-836a-000d93afea2a"
             }
         }, {
             "id": "156fe7f9-bb1e-4134-9bc4-47177ecad66d",
             "defaultCosId": "e00428a1-0c00-11d9-836a-000d93afea2a",
             "name": "nucleus.be",
             "uri": "\/nug\/domain\/156fe7f9-bb1e-4134-9bc4-47177ecad66d\/",
             "subresources": {
                 "account_list": "\/nug\/domain\/156fe7f9-bb1e-4134-9bc4-47177ecad66d\/account\/",
                 "default_cos": "\/nug\/cos\/e00428a1-0c00-11d9-836a-000d93afea2a"
             }
         }]

.. http:method:: POST /nug/domain/

   Create a new domain on the NUG server. The data should be sent as a JSON encoded object
   in the Request body.

   :response Created 201: A domain was successfully created

   :Request:
      .. code-block:: http

         POST /nug/domain/ HTTP/1.1
         Host: http://data.nucleus.be
         Content-type: application/json; charset=UTF-8
         Content-length: 123456
         Connection: close

      .. code-block:: json

         {
            "name"         : "domain.com",
            "defaultCosId" : "a8f379c0-6a0e-48bf-98c7-3e7facb294d3"
         }

      ============= ============ ====
      *Properties*
      ------------- ------------ ----
      Name          Type         Info
      ============= ============ ====
      name          string       **required** The FQDN of the domain
      defaultCosId  string(id)   can be the ID of a COS or ``null``, if ommitted from the request data it will be set to ``null``
      ============= ============ ====

   :Response:
      .. code-block:: json

         {
             "domain": {
                 "id": "4d9c4fbb-4c98-43b8-a10e-21c0959397a7",
                 "defaultCosId": "a8f379c0-6a0e-48bf-98c7-3e7facb294d3",
                 "name": "domain.com",
                 "uri": "\/nug\/domain\/4d9c4fbb-4c98-43b8-a10e-21c0959397a7\/",
                 "subresources": {
                     "account_list": "\/nug\/domain\/4d9c4fbb-4c98-43b8-a10e-21c0959397a7\/account\/"
                 }
             }
         }

.. http:method:: GET /nug/domain/{id}/

   Retrieve detail info on a domain from the NUG server identified by the ``{id}`` path argument.

   :arg string {id}: The domain id on the NUG server

   :Request:
      .. code-block:: http

         GET /nug/domain/d60c6cbc-6c53-456e-ad3d-3b75117cbc64/ HTTP/1.1
         Host: http://data.nucleus.be
         Connection: close

   :Response:
      .. code-block:: json

         {
             "id": "d60c6cbc-6c53-456e-ad3d-3b75117cbc64",
             "defaultCosId": null,
             "name": "chris.be",
             "uri": "\/nug\/domain\/d60c6cbc-6c53-456e-ad3d-3b75117cbc64\/",
             "subresources": {
                 "account_list": "\/nug\/domain\/d60c6cbc-6c53-456e-ad3d-3b75117cbc64\/account\/"
             }
         }

.. http:method:: PUT /nug/domain/{id}/

   Update an existing domain on the NUG server identified by the ``{id}`` path argument. The data should be sent as a JSON encoded object
   in the request body.

   :arg string {id}: The domain id on the NUG server

   :response OK 200: The domain was successfully updated

   :Request:
      .. code-block:: http

         PUT /nug/domain/4d9c4fbb-4c98-43b8-a10e-21c0959397a7/ HTTP/1.1
         Host: http://data.nucleus.be
         Content-type: application/json; charset=UTF-8
         Content-length: 123456
         Connection: close

      .. code-block:: json

         {
            "defaultCosId" : "a8f379c0-6a0e-48bf-98c7-3e7facb294d3"
         }

      ============= ============ ====
      *Properties*
      ------------- ------------ ----
      Name          Type         Info
      ============= ============ ====
      defaultCosId  string(id)   can be the ID of a COS or ``null``, if ommitted from the request data it will be set to ``null``
      ============= ============ ====

      .. note:: A domain's ``name`` is immutable and cannot be changed! If you add it to the request JSON data it'll be ignored.

   :Response:
      .. code-block:: json

         {
             "domain": {
                 "id": "4d9c4fbb-4c98-43b8-a10e-21c0959397a7",
                 "defaultCosId": "a8f379c0-6a0e-48bf-98c7-3e7facb294d3",
                 "name": "domain.com",
                 "uri": "\/nug\/domain\/4d9c4fbb-4c98-43b8-a10e-21c0959397a7\/",
                 "subresources": {
                     "account_list": "\/nug\/domain\/4d9c4fbb-4c98-43b8-a10e-21c0959397a7\/account\/"
                 }
             }
         }

.. http:method:: DELETE /nug/domain/{id}/

   DELETE an existing domain on the NUG server identified by the ``{id}`` path argument.

   :arg string {id}: The domain id on the NUG server

   :response OK 200: The domain was successfully deleted

   :Request:
      .. code-block:: http

         DELETE /nug/domain/4d9c4fbb-4c98-43b8-a10e-21c0959397a7/ HTTP/1.1
         Host: http://data.nucleus.be
         Connection: close

      .. note:: All domain account should be deleted when a domain is deleted, else the request will return an error with the message
         that the domain isn't empty.

   :Response:
      .. code-block:: json

         {
             "success": true,
             "message": "The domain has been successfully deleted"
         }

.. http:method:: GET /nug/domain/{id}/account/

   Retrieve a list of accounts created for the domain identified by the ``{id}`` path argument.

   :arg string {id}: The domain id on the NUG server

   :Request:
      .. code-block:: http

         GET /nug/domain/156fe7f9-bb1e-4134-9bc4-47177ecad66d/account/ HTTP/1.1
         Host: http://data.nucleus.be
         Connection: close

   :Response:
      .. code-block:: json

         [{
             "id": "8282b006-cc43-4cde-86e8-87a4cf1c5f19",
             "name": "chris@nucleus.be",
             "displayname": null,
             "username": "chris",
             "password": "VALUE-BLOCKED",
             "host": "mail.webruimte.eu",
             "accountstatus": "active",
             "mailquota": "524288000",
             "uri": "\/nug\/account\/8282b006-cc43-4cde-86e8-87a4cf1c5f19\/"
         }, {
             "id": "d8114538-f9cf-4448-9b40-349f7a652391",
             "name": "info@nucleus.be",
             "displayname": "Ramakers",
             "username": "info",
             "password": null,
             "host": "mail.webruimte.eu",
             "accountstatus": "active",
             "mailquota": "524288000",
             "uri": "\/nug\/account\/d8114538-f9cf-4448-9b40-349f7a652391\/"
         }]

Account Resource
----------------

:Resource URI: /nug/account/
:Properties:
   :id: the internal ID of this account on the NUG server
   :name: the name of this account (this is the actual emailaddress, eg: chris@nucleus.be)
   :displayname: the default display name used when sending mails (eg: Chris Ramakers)
   :username: the username used when logging in with this account
   :password: the password used when logging in with this account (obfuscated in all GET requests!)
   :host: the hostname of the server the user needs to connect to when logging in (to get his mail for example)
   :accountstatus: the current account status (options: active, closed, locked, pending, maintenance)
   :mailquota: the remaining amount of bytes available before the storage quota is reached (eg: 1.048.576 bytes = 1Mb)

Usage
*****

.. http:method:: GET /nug/account/

   Retrieve a list of all accounts in the system

   :Request:
      .. code-block:: http

         GET /nug/account/ HTTP/1.1
         Host: http://data.nucleus.be
         Connection: closes

   :Response:
      .. code-block:: json

         [{
             "id": "18fb081f-8fcd-4843-ab97-a5f4ee97fc90",
             "name": "admin@mail.webruimte.eu",
             "displayname": null,
             "username": "admin",
             "password": "VALUE-BLOCKED",
             "host": "mail.webruimte.eu",
             "accountstatus": "active",
             "mailquota": "524288000",
             "uri": "\/nug\/account\/18fb081f-8fcd-4843-ab97-a5f4ee97fc90\/"
         }, {
             "id": "7ab4e5f5-f6a4-47bb-be18-e12b4b092a67",
             "name": "chris@mail.webruimte.eu",
             "displayname": "Chris Ramakers",
             "username": "chris",
             "password": "VALUE-BLOCKED",
             "host": "mail.webruimte.eu",
             "accountstatus": "active",
             "mailquota": "524288000",
             "uri": "\/nug\/account\/7ab4e5f5-f6a4-47bb-be18-e12b4b092a67\/"
         }, {
             "id": "8282b006-cc43-4cde-86e8-87a4cf1c5f19",
             "name": "chris@nucleus.be",
             "displayname": null,
             "username": "chris",
             "password": "VALUE-BLOCKED",
             "host": "mail.webruimte.eu",
             "accountstatus": "active",
             "mailquota": "524288000",
             "uri": "\/nug\/account\/8282b006-cc43-4cde-86e8-87a4cf1c5f19\/"
         }]

.. http:method:: POST /nug/account/

   Create a new account on the server

   .. note:: The name passed in the JSON data should contain a domain name that exists on the server!

   :Request:
      .. code-block:: http

         POST /nug/account/ HTTP/1.1
         Host: http://data.nucleus.be
         Content-type: application/json; charset=UTF-8
         Content-length: 123456
         Connection: close

      .. code-block:: json

         {
             "name"          : "sales@nucleus.be",
             "displayname"   : "Nucleus Sales Dept.",
             "password"      : "foobar",
             "accountstatus" : "active",
             "mailquota"     : 102400
         }

      ============= ============ ====
      *Properties*
      ------------- ------------ ----
      Name          Type         Info
      ============= ============ ====
      name          string       **required** The full emailaddress for the new account (the domain must exist on the NUG server!)
      displayname   string       The full name of the account user (eg: John Doe)
      password      string       **required** The plain text password to use when logging in with the account
      accountstatus string       The initial account status (options: active, closed, locked, pending, maintenance)
      mailquota     integer      The maximum mailbox size in bytes (1.048.576 bytes = 1Mb). If this value is omitted the domain default COS will be applied.
      ============= ============ ====

   :Response:
      .. code-block:: json

         {
             "account": {
                 "id": "e3380d28-ba9d-4704-bcf6-d48e163d1d1e",
                 "name": "sales@nucleus.be",
                 "displayname": "Nucleus Sales Dept.",
                 "username": "sales",
                 "password": "VALUE-BLOCKED",
                 "host": "mail.webruimte.eu",
                 "accountstatus": "active",
                 "mailquota": "102400",
                 "uri": "\/nug\/account\/e3380d28-ba9d-4704-bcf6-d48e163d1d1e\/"
             }
         }

.. http:method:: GET /nug/account/{id}/

   Retrieve the details of a single account identified by the  ``{id}`` path argument.

   :arg string {id}: The account id on the NUG server

   :Request:
      .. code-block:: http

         GET /nug/account/092dfe48-9503-4bbc-b891-1e4206b9b1cd/ HTTP/1.1
         Host: http://data.nucleus.be
         Connection: close

   :Response:
      .. code-block:: json

         {
             "id": "092dfe48-9503-4bbc-b891-1e4206b9b1cd",
             "name": "mattias@mail.webruimte.eu",
             "displayname": "Mattias Geniar",
             "username": "mattias",
             "password": "VALUE-BLOCKED",
             "host": "mail.webruimte.eu",
             "accountstatus": "active",
             "mailquota": "524288000",
             "uri": "\/nug\/account\/092dfe48-9503-4bbc-b891-1e4206b9b1cd\/"
         }


.. http:method:: DELETE /nug/account/{id}/

   DELETE an existing account on the NUG server identified by the ``{id}`` path argument.

   :arg string {id}: The account id on the NUG server

   :response OK 200: The account was successfully deleted

   :Request:
      .. code-block:: http

         DELETE /nug/account/092dfe48-9503-4bbc-b891-1e4206b9b1cd/ HTTP/1.1
         Host: http://data.nucleus.be
         Connection: close

   :Response:
      .. code-block:: json

         {
             "success": true,
             "message": "The account has been successfully deleted"
         }

COS Resource
------------

COS stands for Class of Service and is a system used in NUG to determine the settings, peferences and limits for accounts.

:Resource URI: /nug/cos/
:Properties:
   :id: the internal ID of this cos on the NUG server
   :name: the name of this cos

Usage
*****

.. http:method:: GET /nug/cos/

   Retrieve a list of available COS'es from the NUG server

   :Request:
      .. code-block:: http

         GET /nug/cos/ HTTP/1.1
         Host: http://data.nucleus.be
         Connection: close

   :Response:
      .. code-block:: json

         [{
             "id": "a8f379c0-6a0e-48bf-98c7-3e7facb294d3",
             "name": "bronze",
             "uri": "\/nug\/cos\/a8f379c0-6a0e-48bf-98c7-3e7facb294d3\/"
         }, {
             "id": "e00428a1-0c00-11d9-836a-000d93afea2a",
             "name": "default",
             "uri": "\/nug\/cos\/e00428a1-0c00-11d9-836a-000d93afea2a\/"
         }]

.. http:method:: GET /nug/cos/{id}/

   Retrieve the details about a specific COS identified by the  ``{id}`` path argument.

   :arg string {id}: The COS id on the NUG server

   :Request:
      .. code-block:: http

         GET /nug/cos/a8f379c0-6a0e-48bf-98c7-3e7facb294d3/ HTTP/1.1
         Host: http://data.nucleus.be
         Connection: close

   :Response:
      .. code-block:: json

         {
             "id": "a8f379c0-6a0e-48bf-98c7-3e7facb294d3",
             "name": "bronze",
             "uri": "\/nug\/cos\/a8f379c0-6a0e-48bf-98c7-3e7facb294d3\/"
         }
