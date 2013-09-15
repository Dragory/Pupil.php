# Pupil
An easy and powerful string-based validation library.

This is __Pupil.php__, the PHP version of the library.  

Other available versions:

* [Pupil.js](https://github.com/Dragory/Pupil.js)

## Features
* Nested validation rules
* String-based validation rules for compatibility between different languages
* Light revalidation via caching

## Changelog

## Installation
### Via Composer
Add `"mivir/pupil": "1.*"` to the "require" section of composer.json.

### Via downloading
Download the repository. Point a PSR-0 autoloader at the src folder or require the files manually.

## Usage
The basic syntax is this:

```php
$pupil = new \Mivir\Pupil\Pupil();
$pupil->validate($rules, $values);
```

Where `$rules` and `$values` are arrays with matching keys. The rules are specified as `rule strings`; more information on those below.

For example:

```php
$rules = array(
	'name' => 'min(3) && max(8) && regex("^[a-zA-Z]+$")',
	'country' => 'min(2)'
);

$values = array(
	'name' => $nameInput,
	'country' => $countryInput
);
```

The two arrays don't have to have identical keys, but values without a matching key in rules won't be evaluated at all.

The `validate()` method returns an object that has the following methods:

```php
isValid()   // Whether the validation was successful or not
hasErrors() // The opposite of isValid()
errors()    // Returns the fields that didn't pass validation
fields()    // Returns all of the fields and their validation results
```

## Rule strings
Rule strings are Pupil's primary method of specifying validation rules.

The syntax aims to mimic C-like languages. You can use logical operators (`&& (and)`, `|| (or)`, `! (not)`),
ternaries (`condition ? thenRule : elseRule`), nested "blocks" (`rule && (some || nested || rules)`) and validation
functions (`validationFunction("arg1", "arg2")`).

**String parameters for validation functions, such as the regex in the "regex" function, should be quoted.**  
Non-quoted parameters will be cast to floats (numbers with decimals).

For each validation function, there is also a matching function prepended by `other` that allows you to run functions
on other values than the one the rule string is for. This can be useful for fields that have differing requirements depending on another field. For example:

```php
array(
	'state' => 'otherEquals("country", "US") ? lenMin(2) : lenMin(0)'
)
```

Validation function arguments can be either strings or numerical values. Numerical arguments should not be wrapped in quotation marks or apostrophes: ```lenMin(5)```.

## Validation functions
The following functions are available by default:
```
equals
iEquals      # A case-insensitive comparison
sEquals      # A strict comparison
siEquals
lenMin
lenMax
lenEquals
min
max
between
in           # Compare to a list of values
required
optional
numeric
alpha
alphaNumeric
email
regex        # Supply a custom regex
integer
equalsTo     # Compare to another field by its key
```

### Adding custom functions
You can use the following syntax to add your own validation functions:

```php
$pupil->addFunction($name, $callable);
```

Where callable is either an anonymous function or one created with create_function and should, at the very least, accept two arguments: `$allValues` and `$value`. `$allValues` is an object containing every value that's being validated at the moment while `$value` contains the value we're validating at the moment. Further arguments can be passed in rule strings like so:

```php
customFunction("arg1", "arg2")
```

The function names are case-insensitive.
