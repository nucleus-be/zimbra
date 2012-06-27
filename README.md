# Zimbra ZCS PHP library

## Usage

`TODO`

## Exceptions and Errorcodes

These are the built in Exceptions and their error codes this library throws when something goes wrong.

### \Zimbra\ZCS\Exception

A generic exception that indicates something went wrong in the Zimbra library, specifics on the error are contained in the exception message, most of the times this is a 1-on-1 copy of the error the SoapClient received as Fault message/code from the Zimbra Soap API.

This exception is only ever thrown with the error code `1000`

### \Zimbra\ZCS\Exception\InvalidEntity

This exception is thrown when the library tries to construct an Entity but fails to do so because of failed validation. What exactly is wrong can be retrieved by calling `$exception->getErrors()` which returns an associative array of all failed validation rules. 

This exception is only ever thrown with the error code `1100`

### \Zimbra\ZCS\Exception\EntityNotFound

This exception is thrown when an entity is requested but cannot be found on the ZCS server. There are several possible error codes, each code translates to what exactly is missing.

* `1201` domain cannot be found
* `1202` account cannot be found
* `1203` alias cannot be found
* `1204` cos cannot be found