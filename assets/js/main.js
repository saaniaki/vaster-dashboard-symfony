// assets/js/main.js

// loads the jquery package from node_modules
var $ = require('jquery');
require('jquery-ui-bundle');


// JS is equivalent to the normal "bootstrap" package
// no need to set this to a variable, just require it
require('bootstrap-sass');

require('eonasdan-bootstrap-datetimepicker');

// import the function from greet.js (the .js extension is optional)
// ./ (or ../) means to look for a local file
//var greet = require('./greet');

// a CSS file with the same name as the entry js will be output
require('../css/main.scss');


console.log("VDP 0.6 is awesome!");