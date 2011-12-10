window.addEvent('domready', function(){
	var url;
	document.body.addEvents({
		'click:relay(.securimage-reload)': function(e){
			var img = this.getParent().getSiblings('.securimage-captcha')[0];
			if (!url) url = new URI(img.get('src'));
			img.set('src', url.setData('c', Math.random()));
		}
	});
});
