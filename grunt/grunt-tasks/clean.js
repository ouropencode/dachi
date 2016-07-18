module.exports = function(grunt, options) {
	return {
		options: {
			force: true
		},
		bower: ['bower_components/**/*-dachi.css'],
		local: ['tmp']
	};
};