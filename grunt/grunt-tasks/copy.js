module.exports = function(grunt, options) {
	return {
		bower: {
			files: [
				{expand: true, cwd: "bower_components", src: "**", dest: "build/" + options.__versionString + "/bower_components"}
			]
		},
		static_assets: {
			files: [
				{expand: true, cwd: "assets/static", src: [
					"**",
					"**/*",
					"**/**/*"
				], dest: "build/" + options.__versionString + "/static"}
			]
		}
	};

};