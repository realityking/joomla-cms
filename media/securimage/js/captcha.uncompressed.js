window.addEvent('domready', function(){
	$$('form').each(function(form){
		var img = form.getElement('.securimage-captcha');
		if (img == null) return;
		var audio = form.getElement('.securimage-sound'),
			reload_btn = form.getElement('.securimage-reload'),
			play_btn = form.getElement('.securimage-play'),
			imgUri = new URI(img.get('src')),
			audioUri = new URI(audio.get('data-src'));

		audio
			.addListener('ended', function(){
				audio.pause();
			})
			.addListener('pause', function(){
				audio.currentTime = 0;
				/*
				 * When we set currentTime to 0, Firefox plays the audio,
				 * even if audio.paused == true, so we need to pause it again.
				 * It wont get into an infinite loop because onPause is fired only when audio.paused == false
				 */
				audio.pause();
				play_btn.removeClass('playing');
			})
			.addListener('play', function(){
				play_btn.addClass('playing');
			});

		img.addEvent('load', function(){
			audioUri.setData('c', Math.random());
		});
		reload_btn.addEvent('click', function(e){
			img.set('src', imgUri.setData('c', Math.random()).toString());
			if (!audio.paused) audio.pause();
		});

		play_btn.addEvent('click', function(e){
			if (audio.paused) {
				audio.set('src', audioUri.toString());
				audio.load();
				audio.play();
			} else {
				audio.pause();
			}
		});
	});
});
