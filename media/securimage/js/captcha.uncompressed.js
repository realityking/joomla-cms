window.addEvent('domready', function(){
	$$('form').each(function(form){
		var img = form.getElement('.securimage-captcha');
		if (img == null) return;
		var audio = form.getElement('.securimage-sound'),
			reload_btn = form.getElement('.securimage-reload'),
			play_btn = form.getElement('.securimage-play'),
			imgUri = new URI(img.get('src')),
			audioUri = new URI(audio.get('src')),
		reloadAudio = function(){
			audio.set('src', audioUri.setData('c', Math.random()));
			play_btn.removeClass('playing');
		};

		img.addEvent('load', reloadAudio);
		audio
			.addListener('ended', reloadAudio)
			.addListener('pause', reloadAudio)
			.addListener('play', function(){
				play_btn.addClass('playing');
			});

		reload_btn.addEvent('click', function(e){
			img.set('src', imgUri.setData('c', Math.random()));
		});

		play_btn.addEvent('click', function(e){
			audio.paused ? audio.play() : audio.pause();
		});
	});
});
