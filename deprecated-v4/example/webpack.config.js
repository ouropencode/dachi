"use strict";
module.exports = {
    entry: {
		"base": "./assets/entry.js",
	},
    output: {
        path: "./__assets",
        filename: "[name].bundle.js"
    },
    module: {
        loaders: [
            { test: /\.(less|lcss|css)$/, loader: "style!css!less" },
	        { test: /\.(sass|scss)$/, loader: "style!css!sass" },
			{ test: /\.js$/, loader: "babel", exclude: /(node_modules|bower_components)/, query: {presets: ['es2015']}}
        ]
    },
    plugins: [new BowerWebpackPlugin({
		modulesDirectories: ["bower_components"],
		manifestFiles:      ["bower.json", ".bower.json"]
	})]
};
