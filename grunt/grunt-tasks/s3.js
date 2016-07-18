module.exports = function(grunt, options) {
	var build = {
		cwd: "build/",
		src: "**"
	};

	if(!options.grunt.aws.versioned)
		build.cwd = "build/" + options.__versionString + "/";

	return {
		options: {
			cache: false,
			accessKeyId: options.grunt.aws.key,
			secretAccessKey: options.grunt.aws.secret,
			bucket: options.grunt.aws.bucket,
			region: options.grunt.aws.region,
			dryRun: options.environment == "local" // If we're running locally, perform a dry run
		},

		build: build
	};
};