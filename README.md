# The OpenEuropa Whitelabel theme

## Requirements

This depends on the following software:

* [PHP 7.3](http://php.net/)

## Installation

OpenEuropa Whitelabel theme uses [Webpack](https://webpack.js.org) to compile and bundle SASS and JS.

#### Step 1
Make sure you have Node and npm installed.
You can read a guide on how to install node here: https://docs.npmjs.com/getting-started/installing-node

If you prefer to use [Yarn](https://yarnpkg.com) instead of npm, install Yarn by following the guide [here](https://yarnpkg.com/docs/install).

#### Step 2
Go to the root of OpenEuropa Whitelabel theme and run the following commands: `npm install` or `yarn install`.

#### Step 3
Run the following command to compile Sass and watch for changes: `npm run watch` or `yarn watch`.

*Important:* `style` and `copy` tasks are defined in the bcl-builder config file. You can change or improve them based on your needs. [bcl-builder.config.js](bcl-builder.config.js)

## Overriding inherited templates
Add template file with the same name in your sub-theme folder to have it override the template from the parent theme.
[layout](layout), [overrides](overrides), [paragraphs](paragraphs), [patterns](patterns) folders are there for this purpose.


