$(function() {
	Initializations.init();
	Grid.init();
	Portfolio.init();
	Scroller.init();
	GoogleMap.init();
	Charts.init();
	CountTo.init();
	
	var wow = new WOW({
		mobile: false,
		live: true
	});
	wow.init();
});

Initializations = {
	init: function() {
		// Carousels initialization
		$('.j-carousel').owlCarousel({
			loop: true,
			nav: true,
			items: 1,
			mouseDrag: false,
			autoHeight: true,
			navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>']
		});

		// Home section carousel initialization
		if ($('.home-carousel').length) {
			$('.home-carousel').carousel({
				pause: "false",
				interval: 5000
			});
		}

		// Video popup
		if ($('.js-video').length) {
			$('.js-video').magnificPopup({
				disableOn: 700,
				type: 'iframe',
				mainClass: 'mfp-fade',
				removalDelay: 160,
				preloader: false,
				fixedContentPos: false
			});
		}
	}
}

Portfolio = {
	init: function() {
		$('#portfolio .filter a').click(function (e) {
			e.preventDefault();

			// Remove animations delays
			$('#portfolio .i .c').removeAttr('data-wow-delay');
			$('#portfolio .i .c').attr('style', '');
			

			// Set active class
			$('#portfolio .filter li').removeClass('active');
			$(this).parent().addClass('active');

			// Get group name from clicked item
			var category = $(this).attr('data-filter');

			if ($('.og-expanded .og-close').length) {
				$('.og-expanded .og-close').click();
				setTimeout(function() {
					Portfolio.filter(category);
				}, 400);
			} else {
				Portfolio.filter(category);
			}

		});
	},
	filter: function(category) {
		$('.og-grid .i').each(function(){
			if($(this).hasClass(category)){
				$(this).removeClass('hide');
			}
			else{
				$(this).addClass('hide');
			}
		});
	}
}

Scroller = {
	init: function() {
		$('.j-scroll').on('click', function(e) {
			e.preventDefault();	
		  
			var target = this.hash;
			$target = $(target);
			$('html, body').stop().animate({ 'scrollTop': $target.offset().top - 69}, 1000, 'easeInOutExpo');

			// Close navigation menu on item click for mobile devices
			if ($('.visible-xs').is(':visible') && $('.navbar-collapse').hasClass('in')) {
				$('.navbar-toggle').click();
			}
		});
	}
}

// Google map initialization
GoogleMap = {
	map: null,
	marker: null,
	position: null,

	init: function() {
		if ($('#googleMap').length) {
			var lat = $('#googleMap').attr('data-lat');
			var lng = $('#googleMap').attr('data-lng');
			
			function initialize() {
				var view = new google.maps.LatLng(lat, lng);
				var mapOptions = {
					zoom: 16,
					scrollwheel: false,
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					center: view,
					disableDefaultUI: true,
					styles: [{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]}]
				}
				
				GoogleMap.map = new google.maps.Map(document.getElementById('googleMap'), mapOptions);
				GoogleMap.position = new google.maps.LatLng(lat, lng);
				GoogleMap.setMarker();
			}

			google.maps.event.addDomListener(window, 'load', initialize);
		}
	},
	setMarker: function() {
		// Set custom marker
		GoogleMap.marker = new google.maps.Marker({
			position: GoogleMap.position,
			map: GoogleMap.map,
			icon: 'img/map-pin-' + mainColor +'.png'
		});

		GoogleMap.marker.setMap(GoogleMap.map);
	}
}

Charts = {
	init: function() {
		// Charts initialization
		if ($('.chart').length) {
			$('.chart').waypoint(function() {
				$('.chart').easyPieChart({
					barColor: $('.navbar-brand').css('color'),
					animate: 3000,
					trackColor: '#f1f1f1',
					lineWidth: 12,
					size: 160,
					lineCap: 'square',
					scaleColor: '#f7f7f7'
				});
			}, {
				triggerOnce: true,
				offset: 'bottom-in-view'
			});
		}
	},
	update: function() {
		$('.chart').each(function() {
			var api = $(this).data('easyPieChart');
			var value = $(this).attr('data-percent');
			api.options.barColor = $('.navbar-brand').css('color');
			api.update(value);
		});
	}
}

CountTo = {
	init: function() {
		if ($('.timer').length) {
			$('.timer').waypoint(function() {
				$('.timer').data('countToOptions', {
					formatter: function (value, options) {
						return value.toFixed(options.decimals).replace(/\B(?=(?:\d{3})+(?!\d))/g, ',');
					}
				});
				$('.timer').each(CountTo.count);
			}, {
				triggerOnce: true,
				offset: 'bottom-in-view'
			});
		}	
	},
	count: function(options) {
		var $this = $(this);
		options = $.extend({}, options || {}, $this.data('countToOptions') || {});
		$this.countTo(options);
	}
}