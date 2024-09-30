class SlideshowClass extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                swiperContainer: '.twae-slideshow.swiper-container',
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');
        return {
            $swiperContainer: this.$element.find(selectors.swiperContainer),
        };
    }

    bindEvents() {

        var selector = this.elements.$swiperContainer;
        selector.each(function (index) {
            var selectorID = ".twae-slideshow#" + jQuery(this).attr('id');
            var swiperElement = jQuery(selectorID).not('.swiper-container-initialized')[0];
            var auto_height = true;
            var Navigation;
            var nextButton = jQuery(swiperElement).find('.twae-icon-right-open')[0];
            var prevButton = jQuery(swiperElement).find('.twae-icon-left-open')[0];
            var lang_dir = jQuery(swiperElement).attr("dir");
            var autoplay = jQuery(swiperElement).data("slideshow_autoplay");
            
            if (lang_dir == 'rtl') {
                var Navigation = {
                    nextEl: prevButton,
                    prevEl: nextButton,
                }
            } else {
                Navigation = {
                    nextEl: nextButton,
                    prevEl: prevButton,
                }
            }
            var swiper;
            
            var swiperConfig = {
                autoplay: autoplay,
                delay: 5000,
                slidesPerView: 1,
                direction: 'horizontal',
                navigation: Navigation,
                autoHeight: auto_height,
                on: {
                    slideChange: function (){
                        const vr_layout=jQuery('.twae-vertical.twae-wrapper');
                        if(vr_layout.length > 0){
                            const animation=vr_layout.find('.twae-content.aos-init').data('aos');
                            if('none' !== animation && 'object' === typeof AOS){
                                setTimeout(()=>{
                                    AOS.refresh();
                                },500);
                            }
                        }
                    }
                },
            };
            if ('undefined' === typeof Swiper) {
                const asyncSwiper = elementorFrontend.utils.swiper;
                new asyncSwiper(swiperElement, swiperConfig).then((newSwiperInstance) => {
                    swiper = newSwiperInstance;
                });

            } else {
                swiper = new Swiper(swiperElement, swiperConfig);
            }

        });

    }

}


jQuery(window).on('elementor/frontend/init', () => {

    const addHandler = ($element) => {
        elementorFrontend.elementsHandler.addHandler(SlideshowClass, {
            $element,
        });
    };

    elementorFrontend.hooks.addAction('frontend/element_ready/timeline-widget-addon.default', addHandler);
});