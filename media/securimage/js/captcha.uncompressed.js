window.addEvent('domready', function(){
	$$('form').each(function(form){
		var img = form.getElement('.securimage-captcha');
		if (img == null) return;
		var reload_btn = form.getElement('.securimage-reload'),
			imgUri = new URI(img.get('src'));

		reload_btn.addEvent('click', function(e){
			img.set('src', imgUri.setData('c', Math.random()).toString());
		});
	});
});
