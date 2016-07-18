var path = require('path');
var LoadGruntConfig = require('load-grunt-config');
module.exports = function(grunt) {
	var environment = (process.env.LD_ENVIRONMENT || "local");

	var package_config = grunt.file.readJSON(path.join(process.cwd(), 'package.json'));
	var dachi_config   = grunt.file.readJSON(path.join(process.cwd(), 'cache', 'dachi.config.json'));

	var project_config = dachi_config[environment];
	project_config.__versionString = (process.env.CI_BUILD_REF || "ffffff").substr(0, 6);
	project_config.environment     = environment;

	require('time-grunt')(grunt);

	LoadGruntConfig(grunt, {
		configPath: path.join(process.cwd(), 'grunt-tasks'),
		data: project_config
	});

	LoadGruntConfig(grunt, {
		configPath: path.join(process.cwd(), 'vendor', 'ld-packages', 'dachi', 'grunt', 'grunt-tasks'),
		data: project_config
	});
};
