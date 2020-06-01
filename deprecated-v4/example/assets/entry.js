// NPM
require('eventemitter2');

// Javascript
require("./javascript/index.js");

// LESS
require("./less/index.less");

// Require assets from module source folders
require.context("../src", true, /.(js|less|css)/)("./" + expr + "");
