Joomla = Joomla || {};

Joomla.UpdateNotification = new Class({
	Implements: Options,
	
	options: {
		id: '',
		url: '',
		updateFoundString: '',
		upToDateString: '',
		errorString: '',
		updateFoundImg: '',
		upToDateImg: '',
		errorImg: ''
	},
	
	initialize: function(options) {
		this.setOptions(options);

		var req = new Request.JSON({
			url: this.options.url,
			method: 'get',
			onSuccess: function(r) {
				if (updateInfoList instanceof Array) {
					console.log(r);
				} else {
					this.errorMsg();
				}
			},
			onFailure: function(xhr) {
				this.errorMsg();
			}.bind(this)
		}).send();
	},
	
	errorMsg: function() {
		document.id(this.options.id).getElements('img').setProperty('src', this.options.errorImg);
		document.id(this.options.id).getElements('span').set('html', this.options.errorString);
	}
	
});