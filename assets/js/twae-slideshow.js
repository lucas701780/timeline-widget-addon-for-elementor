class SlideshowClass extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                swiperContainer: '.twae-slideshow.swiper-container',
            },
        };
    }

    bindEvents() {
        this.medisSliderInitialize();

        elementorFrontend.hooks.addAction('frontend/twaeWidget/horizontalSliderInitialize', (e)=>{
            this.medisSliderInitialize();
        })
    }

    medisSliderInitialize(){
        const selectors = this.getSettings('selectors');
        var selector = this.$element.find(selectors.swiperContainer);

        if(selector.length <= 0){
            return;
        }

        selector.each( (_,ele) => {
            this.mediaSwiper(jQuery(ele));
        });

        this.verticalLayout=selector.closest('.twae-wrapper.twae-vertical');
    }

    async mediaSwiper (ele) {
        var selectorID = ".twae-slideshow#" + ele.attr('id');
        let swiperElement = jQuery(selectorID);
        let elementExists=false;

        for(let i=0; i < swiperElement.length; i++){
            if(swiperElement[i].swiper === undefined){
                swiperElement=swiperElement[i];
                elementExists=true;
                break;
            }
        }

        if(!elementExists){
            return;
        }

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
                },
                slideChangeTransitionEnd: function(){
                    elementorFrontend.hooks.doAction('frontend/twaeWidget/mediaSlideChange','custom hook');
                }
            },
        };

        await this.swiperInitialize(swiperElement,swiperConfig);

        
        if(autoplay && this.verticalLayout.length > 0){
            let debounceTimeout;

            this.swiperStopAutoPlay(swiperElement)

            jQuery(window).on('scroll', _=>{
                clearTimeout(debounceTimeout);
                debounceTimeout = setTimeout(()=>{
                    this.swiperStopAutoPlay(swiperElement)
                },200)
            });
        }
    }

    swiperStopAutoPlay(ele){
        const swiperWrp=jQuery(ele);
        const swiperContainer=swiperWrp.find('.swiper-wrapper');
        const height=swiperWrp[0].offsetHeight / 2;
        const top=swiperContainer[0].getBoundingClientRect().top + height;

        if(top < 0){
            swiperWrp[0].swiper.autoplay.stop();
        }else{
            swiperWrp[0].swiper.autoplay.start();
        }

        // this.setTimeout
    }

    async swiperInitialize(ele, config) {
        if ('undefined' === typeof Swiper) {
            const asyncSwiper = elementorFrontend.utils.swiper;
            return new Promise((resolve, reject) => {
                new asyncSwiper(ele, config).then((newSwiperInstance) => {
                    resolve(newSwiperInstance);
                }).catch((error) => {
                    reject(error);
                });
            });
        } else {
            return new Promise((resolve, reject) => {
                resolve(new Swiper(ele, config));
            });
        }
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