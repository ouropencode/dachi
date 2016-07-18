module.exports = function(grunt, options) {
	var tasks = {
		'options': {
			preserveComments: true,
			banner: '/*! Developed by LemonDigits.com - build date: ' + grunt.template.today("yyyy-mm-dd")  + ' */\r\n'
		}
	};

	for(var task in options.grunt.javascript.folders) {
		tasks[task] = {
			mangle: false,
			expand: true,
			src: "**/*.js",
			cwd: options.grunt.javascript.folders[task],
			dest: "tmp/uglified-js/" + task,
			ext: ".min.js",
			extDot: "last"
		};
	}

	return tasks;
};