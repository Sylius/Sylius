$(function(){
	Options.init();
});

/* 
	Options
*/
Options = {
	init: function() {
		$('.options .more').click(function() {
			var more = $(this);
			var options = more.parent();

			if (more.hasClass('closed')) {
				more.find('.fa').removeClass('fa-cog');
				more.find('.fa').addClass('fa-times');
				options.animate(
					{left: "+=220"}, 300, function() {
					more.removeClass('closed');
				});
			} else {
				more.find('.fa').removeClass('fa-times');
				more.find('.fa').addClass('fa-cog');
				options.animate(
					{left: "-=220"}, 300, function() {
					more.addClass('closed');
				});
			}
		});

	}
}