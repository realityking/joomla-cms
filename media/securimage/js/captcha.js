window.addEvent("domready",function(){$$("form").each(function(b){var c=b.getElement(".securimage-captcha");if(null!=c){var a=b.getElement(".securimage-sound"),e=b.getElement(".securimage-reload"),d=b.getElement(".securimage-play"),f=new URI(c.get("src")),g=new URI(a.get("src"));a.addListener("ended",function(){a.pause()}).addListener("pause",function(){a.currentTime=0;a.pause();d.removeClass("playing")}).addListener("play",function(){d.addClass("playing")});e.addEvent("click:once",function(){c.addEvent("load",function(){d.removeClass("playing");a.set("src",g.setData("c",Math.random()))})}).addEvent("click",function(){c.set("src",f.setData("c",Math.random()))});d.addEvent("click",function(){a.paused?a.play():a.pause()})}})});