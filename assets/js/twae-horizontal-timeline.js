class HorizontalSliderClass extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                swiperContainer: '.twae-slider-container.swiper-container',
                yearSwiperContainer: '.year-swiper-container.swiper-container',
                nextButton: '.twae-button-next',
                prevButton: '.twae-button-prev',
                prevNav: '.twae-nav-prev',
                nextNav: '.twae-nav-next',
                paginationEl: '.twae-line-fill',
                hrNavigationBar: '.twae-horizontal-navigationBar',
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');
        return {
            $swiperContainer: this.$element.find(selectors.swiperContainer),
            $yearSwiperContainer: this.$element.find(selectors.yearSwiperContainer),
            $nextButton: this.$element.find(selectors.nextButton),
            $prevButton: this.$element.find(selectors.prevButton),
            $prevNav: this.$element.find(selectors.prevNav),
            $nextNav: this.$element.find(selectors.nextNav),
            $paginationEl: this.$element.find(selectors.paginationEl),
            $hrNavigationBar: this.$element.find(selectors.hrNavigationBar),
        };
    }

    bindEvents() {
        var yearSwiperContainer=this.elements.$yearSwiperContainer,
            selector = this.elements.$swiperContainer,
            slidestoshow = selector.data("slidestoshow"),
            spacebw = selector.data("spacebw"),
            autoplay = selector.data("autoplay"),
            infiniteLoop = selector.data("infinite-loop"),
            speed = selector.data("speed"),
            infinite_scrolling = selector.data("ajax-pagination"),
            nextButton = this.elements.$nextButton[0],
            prevButton = this.elements.$prevButton[0],
            prevNav = this.elements.$prevNav,
            nextNav = this.elements.$nextNav,
            paginationEl = this.elements.$paginationEl,
            year_ht_label = yearSwiperContainer.length,
            autoplay_stop_onhover = selector.data("stop-autoplay-onhover"),
            hrNavigationBar = this.elements.$hrNavigationBar;
            let ajaxProcessing=false;
            const clickedLoadMore = new Event('clickedLoadMore');

        // Swiper autoplay stop on hover function start
        const swiperHoverEffect=(element)=>{
            const mainSwiper=element[0].swiper;
            
            jQuery(element).mouseenter(function() {
                mainSwiper.autoplay.stop();
            });
            jQuery(element).mouseleave(function() {
                mainSwiper.autoplay.start();
            });
            
        };
        // Swiper autoplay stop on hover function end

        // Swiper year navigation controller function start
        const swiperYearNavController=(element)=>{
            const navPostIdAttr = element.attr('id');
            const navPostId = navPostIdAttr.replace('twae-horizontal-navigationBar-', '');
            const mainSwiper=jQuery(`#twae-wrapper-${navPostId}`).find('.twae-slider-container')[0];
            const yearSwiper=element[0];
            
            // Swiper index update.
            const swiperIndexUpdate=(id)=>{
                if ('undefined' !== typeof id){
                    const mainSwiperIndex=jQuery(mainSwiper).find(`.twae-year#${id}`).closest('.twae-story.swiper-slide').data('index');
                    // Update main swiper index.
                    mainSwiper.swiper.slideTo(mainSwiperIndex);

                    // Remove active class from previous year nav
                    jQuery(yearSwiper).find('.swiper-slide').removeClass('active');
                    // Added Year Navigation class on current active nav.
                    const activeNav=jQuery(yearSwiper).find(`.swiper-slide[data-id="${id}"]`);
                    activeNav.addClass('active');

                    const navActiveIndex=activeNav.data('index');
                    // Update Nav Swiper index.
                    yearSwiper.swiper.slideTo(navActiveIndex);
                }
            }
            
            // Year Navigation Swiper on click function.
            yearSwiper.swiper.on('click', (e)=>{
                const targetElement=e.clickedSlide ? e.clickedSlide : e.target;
                const id=jQuery(targetElement).data('id');
                swiperIndexUpdate(id);
            });
            
            // Main Swiper slide change function.
            mainSwiper.swiper.on('slideChange', ()=>{
                const activeIndex=mainSwiper.swiper.activeIndex;
                const activeSlide=mainSwiper.swiper.slides[activeIndex];
                const id=jQuery(activeSlide).find('.twae-year').attr('id');
                swiperIndexUpdate(id,'testing');
            });

            // Year Navigation nav button class update on window resize.
            const updateNavbtnCls=()=>{
                const screenSize=window.innerWidth;
                const navList=jQuery(yearSwiper).find('.swiper-slide').length;
                let navHidden=true;
                if(screenSize > 1024 && navList > 10){
                    navHidden=false;
                }else if(screenSize > 768 && screenSize < 1024 && navList > 5){
                    navHidden=false;
                }else if(screenSize > 576 && screenSize < 768 && navList > 3){
                    navHidden=false;
                }else if(screenSize < 575 && navList > 2){
                    navHidden=false;
                }else{
                    navHidden=true;
                }

                const nav=jQuery(yearSwiper).closest('.twae-hor-nav-wrapper').find('.twae-nav-next, .twae-nav-prev');
                navHidden ? nav.hide() : nav.show();
            };

            jQuery(window).resize(updateNavbtnCls);
            updateNavbtnCls();
        }
        // Swiper year navigation controller function end
        
        // Horizontal Highlighted thumbnail controller Function start
        const swiperThumbController=(element)=>{
            const yearSwiper=element[0];
            const mainSwiper=element.closest('.twae-wrapper-inside').find('.twae-slider-container')[0]
            yearSwiper.swiper.controller.control = mainSwiper.swiper;
            mainSwiper.swiper.controller.control = yearSwiper.swiper;
        };
        // Horizontal Highlighted thumbnail controller Function end

        // Post ajax load more request function start
        const ajaxRequest=(wrapperId)=>{
            const mainSwiperWrp=jQuery(`#${wrapperId}`);
            const Widget_id=wrapperId.replace('twae-', '');
            const postWindowObject = window['post_timeline_' + Widget_id];
            var allAtts = postWindowObject.attribute;
            let ajaxUrl = postWindowObject.url;
            let wpNonce = postWindowObject.private_key;
            let pageNo = parseInt(mainSwiperWrp[0].getAttribute('data-page-no'));
            let totalPage = parseInt(mainSwiperWrp.data("total-pages"));
            if(pageNo === totalPage){
                selector.addClass('twae-loadMore-complete');
                return;
            }
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: ajaxUrl,
                data: {
                    action: 'twae_post_load_more',
                    private_key: wpNonce,
                    page_no: pageNo,
                    settings: allAtts
                },
                beforeSend: function () {
                    ajaxProcessing=true;
                    jQuery(nextButton).addClass('swiper-button-disabled');
                    jQuery(nextButton).find('.lm_active_state').show();

                }
            }).done(function (res) {
                if(year_ht_label){
                    yearSwiperContainer[0].swiper.appendSlide(res.highlightedcontent);
                    yearSwiperContainer[0].swiper.update(true)
                }
                selector[0].swiper.appendSlide(res.html);
                selector[0].swiper.update(true)
                jQuery(nextButton).find('.lm_active_state').hide();
                let thisPage = pageNo + 1;
                selector.attr("data-page-no", thisPage);
                document.dispatchEvent(clickedLoadMore); //reinitialize masonry only for compact layout  
                if (thisPage === totalPage) {
                    selector.addClass('twae-loadMore-complete');	// add class to prevent loadMore on this timeline
                }
            }).complete(function (res) {
                jQuery(nextButton).removeClass('swiper-button-disabled');
                ajaxProcessing=false;
            });
        }
        // Post ajax load more request function end
        
        var Navigation;
        var lang_dir = selector.data("dir");
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
        
        if(year_ht_label){
            var thumbslidesPerView = slidestoshow;
            var loopedSlides = slidestoshow;
            slidestoshow = 1;

            if (lang_dir == 'rtl') {
                var yearNavigation= Navigation;
                Navigation = '';
            } else {
                var yearNavigation= Navigation;
                Navigation = '';
            }
            autoplay = false;
        };

        var responsive_slidesperview=1;
        if(slidestoshow > 2){
            responsive_slidesperview = 2;
        }

        if(hrNavigationBar.length > 0){
            var navPostId = hrNavigationBar.attr('id');
           
            navPostId = navPostId.replace('twae-horizontal-navigationBar-', '');
            var NavigationYear;
            var lang_dir_nav = selector.data("dir");
            if (lang_dir_nav == 'rtl') {
                var NavigationYear = {
                    nextEl: prevNav,
                    prevEl: nextNav,
                }
            } else {
                NavigationYear = {
                    nextEl: nextNav,
                    prevEl: prevNav,
                }
            }
            var navigatorYear = selector.find(' .twae-year');
            let hrlistAll = document.createElement('div');
            hrlistAll.classList.add('twae-horizontal-navigation-items', 'swiper-wrapper');
            let hrlistEl = '';
            $ = jQuery;
            hrNavigationBar[0].appendChild(hrlistAll);
            for( var i = 0; i < navigatorYear.length; i++){ 
                let uniqueID = navPostId + '-item-' + i;
                let HrscrollbarID = 'twae-horizontal-unique-' + uniqueID;
                navigatorYear[i].setAttribute('id', HrscrollbarID);

                let labelClassHr = $('#' + HrscrollbarID).find('.twae-year-text');
  
                hrlistEl = document.createElement('div');
                if(0 === i){
                    hrlistEl.classList.add('active');
                }
                hrlistEl.classList.add('swiper-slide');
                hrlistEl.classList.add('twae-year-nav');
                hrlistEl.setAttribute('data-id', HrscrollbarID);
                hrlistEl.setAttribute('data-index', i);
                hrlistEl.innerText = labelClassHr.text();
                hrlistAll.appendChild(hrlistEl);
            }
        }
        
        var swiper = swipperGlobalCheck(selector, {
            spaceBetween: spacebw,
            autoplay: autoplay,
            delay: 3000,
            loop: infiniteLoop,
            speed: speed,
            slidesPerView: slidestoshow,
            direction: 'horizontal',
            pagination: {
                el: paginationEl[0],
                type: 'progressbar',
            },
            
            navigation: Navigation,
            loopedSlides: loopedSlides,
            slidesPerGroup: 1,
            disableOnInteraction: true,
            // Responsive breakpoints
            breakpoints: {
                // when window width is >= 280px
                280: {
                    slidesPerView: 1,
                    loopedSlides: 2,
                },
                768: {
                    slidesPerView: responsive_slidesperview,
                    loopedSlides: 3,
                },
                1024: {
                    slidesPerView: slidestoshow,
                    loopedSlides: loopedSlides,
                }
            },
            on: {
                slideChange: function () {
                    if (infinite_scrolling == 'yes') {
                        let args = this;
                        let sliderEl = (args.$el).attr('id');
                        let El = jQuery("#" + sliderEl);
                        if (!ajaxProcessing && !year_ht_label && args.isEnd && El.hasClass("twae-loadMore-complete") === false) {
                            // Post ajax load more request
                            ajaxRequest(selector[0].id);
                        }
                    }
                }
            }
        });

        if(year_ht_label){
            autoplay = selector.data("autoplay");

            var yearSwiper = swipperGlobalCheck(yearSwiperContainer, {
                spaceBetween: 0,
                slidesPerView: thumbslidesPerView,
                autoplay: autoplay,
                delay: 3000,
                speed: speed,
                loop: infiniteLoop,
                navigation: yearNavigation,
                slideToClickedSlide: true,
                centeredSlides: true,
                roundLengths: true,
                slidesPerGroup: 1,
                disableOnInteraction: true,
                breakpoints: {
                    280: {
                        slidesPerView: 2,
                    },
                    768: {
                        slidesPerView: 3,
                    },
                    1024: {
                        slidesPerView: thumbslidesPerView,
                    }
                },
                on: {
                    slideChange: function () {
                        if(infinite_scrolling == 'yes'){
                            const activeIndex=this.activeIndex;
                            const slidesPerView=this.params.slidesPerView;
                            const totalSlides=this.slides.length;
                            const remaingSlide=Math.ceil(slidesPerView/2);
                            let activeSlide=activeIndex + remaingSlide;
                            if (!ajaxProcessing && activeSlide === totalSlides && selector.hasClass("twae-loadMore-complete") === false) {
                                // Post ajax load more request
                                ajaxRequest(selector[0].id);
                            };
                        }
                    }
                }
            });
        }
        
        if(hrNavigationBar.length){
            let navPerView = 10;
            var navswiper = swipperGlobalCheck(hrNavigationBar, {
                slidesPerView: navPerView,
                clickable: true,
                allowTouchMove:false,
                slideToClickedSlide:true,
                spaceBetween:20,
                breakpoints: {
                    280: {
                        slidesPerView: 2,
                    },
                    576:{
                        slidesPerView: 3,
                    },
                    768: {
                        slidesPerView: 5,
                    },
                    1024: {
                        slidesPerView: navPerView,
                    }
                },
                navigation:NavigationYear,
            });
        }

        //Swiper Global Check Function
        function swipperGlobalCheck(swiperElement, swiperConfig) {
            if(swiperElement.length){
                if ('undefined' === typeof Swiper) {
                    const asyncSwiper = elementorFrontend.utils.swiper;
                    new asyncSwiper(swiperElement, swiperConfig).then((newSwiperInstance) => {
    
                        // Stop swiper autoplay on hover
                        if(autoplay_stop_onhover){
                            if(!year_ht_label && swiperElement.hasClass('twae-slider-container')){
                                swiperHoverEffect(swiperElement);
                            }else if(swiperElement.hasClass('year-swiper-container')){
                                swiperHoverEffect(swiperElement);
                            }
                        }

                        // Horizontal Highlighted thumbnail controller
                        if(year_ht_label && swiperElement.hasClass('year-swiper-container')){
                            swiperThumbController(swiperElement);
                        }

                        // Horiozntal Navigation Bar controller
                        if(hrNavigationBar.length && swiperElement.hasClass('twae-horizontal-navigationBar')){
                            swiperYearNavController(swiperElement);
                        }

                        return newSwiperInstance;
                    });
                } else {
                    const swiper=new Swiper(swiperElement[0], swiperConfig);

                    // Stop swiper autoplay on hover
                    if(autoplay_stop_onhover){
                        if(!year_ht_label && swiperElement.hasClass('twae-slider-container')){
                            swiperHoverEffect(swiperElement);
                        }else if(swiperElement.hasClass('year-swiper-container')){
                            swiperHoverEffect(swiperElement);
                        }
                    }

                    // Horizontal Highlighted thumbnail controller
                    if(year_ht_label && swiperElement.hasClass('year-swiper-container')){
                        swiperThumbController(swiperElement);
                    }

                    // Horiozntal Navigation Bar controller
                    if(hrNavigationBar.length && swiperElement.hasClass('twae-horizontal-navigationBar')){
                        swiperYearNavController(swiperElement);
                    }

                    return swiper;
                }
            }
        }
    }
}

jQuery(window).on('elementor/frontend/init', () => {

    const addHandler = ($element) => {
        elementorFrontend.elementsHandler.addHandler(HorizontalSliderClass, {
            $element,
        });
    };

    elementorFrontend.hooks.addAction('frontend/element_ready/timeline-widget-addon.default', addHandler);
    elementorFrontend.hooks.addAction('frontend/element_ready/twae-post-timeline-widget.default', addHandler);
});