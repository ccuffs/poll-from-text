<p align="center">
    <img width="800" src=".github/logo.png" title="Project logo"><br />
    <img src="https://img.shields.io/maintenance/yes/2021?style=for-the-badge" title="Project status">
    <img src="https://img.shields.io/github/workflow/status/ccuffs/poll-from-text/CI?label=Build&logo=github&logoColor=white&style=for-the-badge" title="Build status">
</p>

# Introduction

`poll-from-text` is a PHP package to parse semi-structured text into structured-data that can be used to build questionnaires (forms). The main goal is to allow end users to build dynamic forms, e.g. Google Forms, using plain text like they would if the forms were to be printed on paper.

> **NOTICE:** this package assumes a certain format in the "pure" text, so it might not be as generic as possible. The world has bigger problems.

## ✨Features

* Simple input questions (useful to create `<input type="text" />` elements);
* Question with options (useful to create `<select>` elements);
* Options with no value (only text), e.g. `- Text`, or `* Text` (like in `<option>Text</option>`);
* Options with a value, e.g `a) Text` (value is `"a"`, like in `<option value="a">Text</option>`);
* Data attributes (as JSON objects) for both questions and options, e.g. `{"type":"file"} What?` (useful to create custom form elements such as `<input type="file" />`).

## 🚀 Getting started

### 1. Add this package to your project

At the root of your project, run:

```
composer require ccuffs/poll-from-text
```

### 2. Basic usage

Instantiate the class `CCUFFS\Text\PollFromText` then call `parse()`:

>*Tip:* use `CCUFFS\Text\PollFromText::make()` if you don't want to instantiate an object.

```php
$poller = new CCUFFS\Text\PollFromText();
$questions = $poller->parse('Favorite color?')

var_dump($questions);
```

The output should be something like:

```php
array(1) {
  [0]=>
  array(2) {
    ["text"]=>
    string(15) "Favorite color?"
    ["type"]=>
    string(5) "input"
  }
}
```

A new line (without the option marks) indicates a new question:

```php
$poller = new CCUFFS\Text\PollFromText();
$questions = $poller->parse('
    Favorite color?
    Favorite food?
')

var_dump($questions);
```

The output should be something like:

```php
array(2) {
  [0]=>
  array(2) {
    ["text"]=>
    string(15) "Favorite color?"
    ["type"]=>
    string(5) "input"
  }
  [1]=>
  array(2) {
    ["text"]=>
    string(15) "Favorite food?"
    ["type"]=>
    string(5) "input"
  }  
}
```

You can create questions with options by prefixing lines with `-` or `*`:

```php
$poller = new CCUFFS\Text\PollFromText();
$questions = $poller->parse('
   Choose favorite color
   - Green
');

var_dump($questions);
```

The output should be something like:

```php
array(1) {
  [0]=>
  array(3) {
    ["text"]=>
    string(21) "Choose favorite color"
    ["type"]=>
    string(6) "select"
    ["options"]=>
    array(1) {
      [0]=>
      string(5) "Green"
    }
  }
}
```

### 3. Advanced usage

You can create questions with options and their values by using `)`, for instance:

```php
$poller = new CCUFFS\Text\PollFromText();
$questions = $poller->parse('
   Choose favorite color
   a) Green
');

var_dump($questions);
```

The output should be something like:

```php
array(1) {
  [0]=>
  array(3) {
    ["text"]=>
    string(21) "Choose favorite color"
    ["type"]=>
    string(6) "select"
    ["options"]=>
    array(1) {
      ["a"]=>
      string(5) "Green"
    }
  }
}
```

Both questions and options accept a json string as a data field, e.g.

```php
$poller = new CCUFFS\Text\PollFromText();
$questions = $poller->parse('{"attr":"value", "attr2":"value"} Type favorite color');

var_dump($questions);
```

The output should be something like:

```php
array(1) {
  [0]=>
  array(3) {
    ["text"]=>
    string(21) "Type favorite color"
    ["type"]=>
    string(5) "input"
    ["data"]=>
    array(2) {
      ["attr"]=>
      string(5) "value"
      ["attr2"]=>
      string(5) "value"
    }
  }
}
```

Data attribute for an options:

```php
$poller = new CCUFFS\Text\PollFromText();
$questions = $poller->parse('
   Choose favorite color
   {"attr":"hi"} a) Green
');

var_dump($questions);
```

The output should be something like:

```php
array(1) {
  [0]=>
  array(3) {
    ["text"]=>
    string(21) "Choose favorite color"
    ["type"]=>
    string(6) "select"
    ["options"]=>
    array(1) {
      ["a"]=> array(2) {
          ["text"]=>
          string(5) "Green"
          ["data"]=>
          array(1) {
              ["attr"]=>
              string(2) "hi"
          }
      }
    }
  }
}
```

### 4. Testing (related to the package development)

If you plan on changing how the package works, be sure to clone it first: 

```
git clone https://github.com/ccuffs/poll-from-text && cd poll-from-text
```

Install dependencies

```
composer install
```

Make your changes. After, run the tests it to ensure nothing breaked:

```
./vendor/bin/pest
```

There should be plenty of green marks all over 😁


## 🤝 Contribute

Your help is most welcome regardless of form! Check out the [CONTRIBUTING.md](CONTRIBUTING.md) file for all ways you can contribute to the project. For example, [suggest a new feature](https://github.com/ccuffs/poll-from-text/issues/new?assignees=&labels=&poll-from-text=feature_request.md&title=), [report a problem/bug](https://github.com/ccuffs/poll-from-text/issues/new?assignees=&labels=bug&poll-from-text=bug_report.md&title=), [submit a pull request](https://help.github.com/en/github/collaborating-with-issues-and-pull-requests/about-pull-requests), or simply use the project and comment your experience. You are encourage to participate as much as possible, but stay tuned to the [code of conduct](./CODE_OF_CONDUCT.md) before making any interaction with other community members.

See the [ROADMAP.md](ROADMAP.md) file for an idea of how the project should evolve.

## 🎫 License

This project is licensed under the [MIT](https://choosealicense.com/licenses/mit/) open-source license and is available for free.

## 🧬 Changelog

See all changes to this project in the [CHANGELOG.md](CHANGELOG.md) file.

## 🧪 Similar projects

Below is a list of interesting links and similar projects:

* [Other project](https://github.com/project)
* [Project inspiration](https://github.com/project)
* [Similar tool](https://github.com/project)
