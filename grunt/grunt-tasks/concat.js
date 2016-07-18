module.exports = function(grunt, options) {
	var tasks = {
		'options': {
			separator: ';\n',
			banner: '/*! Developed by LemonDigits.com - build date: ' + grunt.template.today("yyyy-mm-dd")  + ' */\r\n',
			stripBanners: {
				block: true,
				line: true
			}
		}
	};

	for(var task in options.grunt.javascript.folders) {
		tasks[task] = {
			src:  "tmp/uglified-js/" + task + "/**/*.js",
			dest: "build/" + options.__versionString + "/" + task.replace('.js', '.min.js')
		};
	}

	return tasks;
};