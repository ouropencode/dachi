module.exports = function(grunt, options) {
	var files = {};
	for(var lessFile in options.grunt.less.files)
		files["build/" + options.__versionString + "/" + lessFile.replace('.css', '.min.css')] = options.grunt.less.files[lessFile];

	return {
		build: {
			options: {
				paths: ["assets/lcss/"],
				rootpath: options.dachi.baseURLassets,
				plugins: [
					new (require('less-plugin-autoprefix'))({browsers: ["last 2 versions"]}),
					new (require('less-plugin-clean-css'))()
				],
				modifyVars: {
					baseURL: '"' + options.dachi.baseURL + '"',
					assetsURL: '"' + options.dachi.assetsURL.replace(/%v/g, options.__versionString) + '"'
				}
			},
			files: files
		}
	};
};