module.exports = function(grunt, options) {
	return {
		finished: {
			options: {
				title: options.dachi.siteName,
				message: 'Build Finished',
				timeout: 3
			}
		}
	};
}