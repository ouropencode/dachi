var _ = require('underscore');
var fs = require('fs');
var path = require('path');

module.exports = function(grunt, options) {
	var mainFiles = {
		'alertify.js' : ['themes/alertify.core.css', 'themes/alertify.default.css', 'lib/alertify.min.js'],
		'vex'         : ['css/vex.css', 'css/vex-theme-plain.css', 'js/vex.combined.min.js'],
		'history.js'  : 'scripts/bundled/html4+html5/jquery.history.js'
	};

	for(var lib in options.grunt.bower.mainFiles)
		mainFiles[lib] = options.grunt.bower.mainFiles[lib];

	return {
		all: {
			dest: {
				js: 'build/' + options.__versionString + '/bower.min.js',
				css: 'build/' + options.__versionString + '/bower.css',
			},
			options: { separator: ';\n\n//------breakbreak-------\\\n\n' },
			mainFiles: mainFiles,
			exclude: options.grunt.bower.exclude,
			dependencies: {
				'history.js': 'jquery'
			},

			callback: function(mf, component) {
				return _.map(mf, function(filepath) {
					var min;
					if(filepath.substr(-4) == ".css") {
						min = filepath.replace(/\.css$/, '.min.css');
						if(grunt.file.exists(min))
							filepath = min;

						var file = fs.readFileSync(filepath).toString();

						var urlsUnquoted = file.match(/url\s*\(([^"'][^\)]+)\)/g);
						var urlsSingleQuoted = file.match(/url\s*\('([^']+)'\)/g);
						var urlsDoubleQuoted = file.match(/url\s*\("([^"]+)"\)/g);

						var urls = [].concat(urlsUnquoted).concat(urlsSingleQuoted).concat(urlsDoubleQuoted);

						_.each(urls, function(url) {
							if(!url) return false;

							var newUrl = url.replace(/url\s*\(/, '');
							newUrl = newUrl.substr(0, newUrl.length - 1);

							if(newUrl[0] == "'" || newUrl[0] == "\"")
								newUrl = newUrl.substr(1, newUrl.length - 2);

							newUrl = path.dirname(filepath).replace(path.resolve('.') + path.sep, '').replace(/\\/g, '/') + '/' + newUrl;
							newUrl = "url(\"" + options.dachi.assetsURL.replace(/%v/g, options.__versionString) + "/" + newUrl + "\")";

							file = file.replace(url, newUrl);
						});
						fs.writeFileSync(filepath + "-dachi.css", file);
						return filepath + "-dachi.css";
					} else if(filepath.substr(-3) == ".js") {
						min = filepath.replace(/\.js$/, '.min.js');
						return grunt.file.exists(min) ? min : filepath;
					}

					return filepath;
				});
			}
		}
	};
};
