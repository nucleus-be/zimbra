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

   Retrieve list all domains available in the system

   :response OK 200: succesfully retrieved a list of domains

   :Request:
      .. code-block:: http

         GET /nug/domain/ HTTP/1.1
         Host: http://data.nucleus.be

   :Response:

      .. code-block:: json

         [
             {
                 "defaultCosId": null,
                 "id": "d60c6cbc-6c53-456e-ad3d-3b75117cbc64",
                 "name": "chris.be",
                 "subresources": {
                     "account_list": "/nug/domain/d60c6cbc-6c53-456e-ad3d-3b75117cbc64/account/",
                     "detail": "/nug/domain/d60c6cbc-6c53-456e-ad3d-3b75117cbc64/"
                 }
             },
             {
                 "defaultCosId": null,
                 "id": "29cf77b1-45d2-4464-b4e1-6e995eac3128",
                 "name": "jan.be",
                 "subresources": {
                     "account_list": "/nug/domain/29cf77b1-45d2-4464-b4e1-6e995eac3128/account/",
                     "detail": "/nug/domain/29cf77b1-45d2-4464-b4e1-6e995eac3128/"
                 }
             },
             {
                 "defaultCosId": "e00428a1-0c00-11d9-836a-000d93afea2a",
                 "id": "9f61f68f-fcd2-460e-8098-de01d250b5df",
                 "name": "mail.webruimte.eu",
                 "subresources": {
                     "account_list": "/nug/domain/9f61f68f-fcd2-460e-8098-de01d250b5df/account/",
                     "default_cos": "/nug/cos/e00428a1-0c00-11d9-836a-000d93afea2a",
                     "detail": "/nug/domain/9f61f68f-fcd2-460e-8098-de01d250b5df/"
                 }
             },
             {
                 "defaultCosId": null,
                 "id": "48d08aa6-6628-44c3-a988-9370c74a8c56",
                 "name": "mattias.be",
                 "subresources": {
                     "account_list": "/nug/domain/48d08aa6-6628-44c3-a988-9370c74a8c56/account/",
                     "detail": "/nug/domain/48d08aa6-6628-44c3-a988-9370c74a8c56/"
                 }
             },
             {
                 "defaultCosId": "e00428a1-0c00-11d9-836a-000d93afea2a",
                 "id": "156fe7f9-bb1e-4134-9bc4-47177ecad66d",
                 "name": "nucleus.be",
                 "subresources": {
                     "account_list": "/nug/domain/156fe7f9-bb1e-4134-9bc4-47177ecad66d/account/",
                     "default_cos": "/nug/cos/e00428a1-0c00-11d9-836a-000d93afea2a",
                     "detail": "/nug/domain/156fe7f9-bb1e-4134-9bc4-47177ecad66d/"
                 }
             }
         ]

.. http:method:: POST /nug/domain/

   Create a new domain on the NUG server, the data should be sent as a JSON encoded object
   in the Request body.

   .. note:: Make sure to also set the content type to ``application/json`` so the REST API will know how to decode your request data

   :response 201: A domain was successfully created

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

      These are the properties that can be sent over to the DATA API, just make sure you always send at least the required properties. If
      any property isn't valid the server will return a response with information on the missing/wrong properties.

      ============= ============ ====
      *Properties*
      ------------- ------------ ----
      Name          Type         Info
      ============= ============ ====
      name          string       required
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

   :arg string {id}: The domain id on the NUG server

   Retrieve detail info on a domain from the NUG server

   :Request:
      .. code-block:: http

         GET /nug/domain/d60c6cbc-6c53-456e-ad3d-3b75117cbc64/ HTTP/1.1
         Host: http://data.nucleus.be

   :Response:
      .. code-block:: json

         {
             "defaultCosId": null,
             "id": "d60c6cbc-6c53-456e-ad3d-3b75117cbc64",
             "name": "chris.be",
             "subresources": {
                 "account_list": "/nug/domain/d60c6cbc-6c53-456e-ad3d-3b75117cbc64/account/",
                 "detail": "/nug/domain/d60c6cbc-6c53-456e-ad3d-3b75117cbc64/"
             }
         }

.. http:method:: GET /nug/domain/{id}/account/

   :arg string {id}: The domain id on the NUG server

   Retrieve a list of accounts created for the domain identified by ``{id}``

   :Request:
      .. code-block:: http

         GET /nug/domain/d60c6cbc-6c53-456e-ad3d-3b75117cbc64/account/ HTTP/1.1
         Host: http://data.nucleus.be

   :Response:
      .. code-block:: json

         [
             {
                 "accountstatus": "active",
                 "displayname": "Chris Ramakers",
                 "host": "mail.webruimte.eu",
                 "id": "d1239eef-9a14-4f10-97f4-059da31d4190",
                 "mailquota": "524288000",
                 "name": "info2@chris.be",
                 "password": "VALUE-BLOCKED",
                 "username": "info2"
             },
             {
                 "accountstatus": "active",
                 "displayname": "Chris Ramakers",
                 "host": "mail.webruimte.eu",
                 "id": "cbc6c3f4-8a6c-4403-b8c6-9aa8400bc44c",
                 "mailquota": "524288000",
                 "name": "info@chris.be",
                 "password": "VALUE-BLOCKED",
                 "username": "info"
             }
         ]

Account Resource
----------------

:Resource URI: /nug/account/
:Properties:
   :id: the internal ID of this account on the NUG server
   :name: the name of this account (this is the actual emailaddress, eg: chris@nucleus.be)
   :displayname: the default display name used when sending mails (eg: Chris Ramakers)
   :username: the username used when logging in with this account
   :password: the password used when logging in with this account (obfuscated in all GET requests!)
   :host: the hostname where the user needs to connect to when loggin in (to get his mail for example)
   :accountstatus: the current account status (eg: active)
   :mailquota: the remaining amount of bytes available before the storage quota is reached (eg: 52428800 = 500Mb)

Usage
*****

.. http:method:: GET /nug/account/

   Retrieve a list of all available accounts in the system

   :Request:
      .. code-block:: http

         GET /nug/account/ HTTP/1.1
         Host: http://data.nucleus.be

   :Response:
      .. code-block:: json

         [
             {
                 "accountstatus": "active",
                 "displayname": null,
                 "host": "mail.webruimte.eu",
                 "id": "18fb081f-8fcd-4843-ab97-a5f4ee97fc90",
                 "mailquota": "524288000",
                 "name": "admin@mail.webruimte.eu",
                 "password": "VALUE-BLOCKED",
                 "username": "admin"
             },
             {
                 "accountstatus": "active",
                 "displayname": "Chris Ramakers",
                 "host": "mail.webruimte.eu",
                 "id": "d16f387d-159d-4b37-a9bb-0bbff53ed7b6",
                 "mailquota": "524288000",
                 "name": "chris@nucleus.be",
                 "password": "VALUE-BLOCKED",
                 "username": "chris"
             }
         ]

.. http:method:: GET /nug/account/{id}/

   :arg string {id}: The account id on the NUG server

   Retrieve the details of a single account identified by the id passed in the ``{id}`` path argument.

   :Request:
      .. code-block:: http

         GET /nug/account/d1239eef-9a14-4f10-97f4-059da31d4190/ HTTP/1.1
         Host: http://data.nucleus.be

   :Response:
      .. code-block:: json

         {
             "accountstatus": "active",
             "displayname": "Chris Ramakers",
             "host": "mail.webruimte.eu",
             "id": "d1239eef-9a14-4f10-97f4-059da31d4190",
             "mailquota": "524288000",
             "name": "info2@chris.be",
             "password": "VALUE-BLOCKED",
             "username": "info2"
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

   :Response:
      .. code-block:: json

         [
             {
                 "id": "a8f379c0-6a0e-48bf-98c7-3e7facb294d3",
                 "name": "Bronze"
             },
             {
                 "id": "e00428a1-0c00-11d9-836a-000d93afea2a",
                 "name": "default"
             }
         ]

.. http:method:: GET /nug/cos/{id}/

   :arg string {id}: The COS id on the NUG server

   Retrieve the details about a specific COS from the NUG server

   :Request:
      .. code-block:: http

         GET /nug/cos/a8f379c0-6a0e-48bf-98c7-3e7facb294d3/ HTTP/1.1
         Host: http://data.nucleus.be

   :Response:
      .. code-block:: json

          {
              "id": "a8f379c0-6a0e-48bf-98c7-3e7facb294d3",
              "name": "Bronze"
          }
