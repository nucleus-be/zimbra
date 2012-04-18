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

   :Request:
      .. code-block:: http

         GET http://data.nucleus.be/nug/domain/

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

.. http:method:: GET /nug/account/{id}/

   :arg string {id}: The account id on the NUG server

   Retrieve the details of a single account identified by the id passed in the ``{id}`` path argument.

   :Request:
      .. code-block:: http

         GET http://data.nucleus.be/nug/account/d1239eef-9a14-4f10-97f4-059da31d4190/

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

         GET http://data.nucleus.be/nug/cos/

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

         GET http://data.nucleus.be/nug/cos/a8f379c0-6a0e-48bf-98c7-3e7facb294d3/

   :Response:
      .. code-block:: json

          {
              "id": "a8f379c0-6a0e-48bf-98c7-3e7facb294d3",
              "name": "Bronze"
          }
