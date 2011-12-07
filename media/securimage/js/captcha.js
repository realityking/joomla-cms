window.addEvent('domready', function(){
	var url;
	document.body.addEvent('click:relay(.securimage-reload)', function(){
		var img = this.getSiblings('.securimage-captcha')[0];
		if (!url) url = new URI(img.get('src'));
		img.set('src', url.setData('c', Math.random()));
	});
});