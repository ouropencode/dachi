"use strict";
module.exports = {
    entry: "./assets/entry.js",
    output: {
        path: "./",
        filename: "bundle.js"
    },
    module: {
        loaders: [
            { test: /\.less$/, loader: "style!css!less" },
			{ test: /\.js$/, loader: "babel", exclude: /(node_modules|bower_components)/, query: {presets: ['es2015']}}
        ]
    }
};
