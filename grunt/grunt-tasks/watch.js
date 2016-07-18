module.exports = function(grunt, options) {

	return {

		local: {
			files: [
				'assets/javascript/**', 'assets/lcss/**',
				'assets/javascript-*/**', 'assets/lcss-*/**',
			],
			tasks: ['compile-local'],
			options: {spawn: false, nonull: false}
		},

		bower: {
			files: 'bower_components/**',
			tasks: ['compile-bower'],
			options: {spawn: false, nonull: false}
		},

		dachi: {
			files: options.grunt.template["tag-replacement"],
			tasks: ['compile-dachi'],
			options: {spawn: false, nonull: false}
		},

		livereload: {
			files: ['build/*.css', 'build/*.js'],
			options: {spawn: false, nonull: false, livereload: true}
		}
	};

};
