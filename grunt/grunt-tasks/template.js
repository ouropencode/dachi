module.exports = function(grunt, options) {
	return {
		'process-tag-replacement-template': {
			'options': {
				data: options
			},
			'files': [{
				expand: true,
				src: options.grunt.template["tag-replacement"],
				ext: '.twig',
				extDot: 'last'
			}]
		}
	};
};