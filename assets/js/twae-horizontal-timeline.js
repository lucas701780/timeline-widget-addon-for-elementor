class HorizontalSliderClass extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                swiperContainer: '.twae-slider-container.swiper-container',
                thumbnailSwiperContainer: '.year-swiper-container.swiper-container',
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
            $thumbnailSwiperContainer: this.$element.find(selectors.thumbnailSwiperContainer),
            $nextButton: this.$element.find(selectors.nextButton),
            $prevButton: this.$element.find(selectors.prevButton),
            $prevNav: this.$element.find(selectors.prevNav),
            $nextNav: this.$element.find(selectors.nextNav),
            $paginationEl: this.$element.find(selectors.paginationEl),
            $hrNavigationBar: this.$element.find(selectors.hrNavigationBar),
        };
    }

    bindEvents() {
        const selector = this.elements.$swiperContainer;
        const thumbnailSwiperContainer = this.elements.$thumbnailSwiperContainer;
        this.attr = {
            selector: selector,
            thumbnailSwiperContainer: thumbnailSwiperContainer,
            slidestoshow: selector.data("slidestoshow"),
            autoplay: selector.data("autoplay"),
            speed: selector.data("speed"),
            infiniteLoop: selector.data("infinite-loop"),
            ajaxLoadMore: selector.data("ajax-pagination"),
            lineFilling: selector.data('lineFilling'),
            nextBtn: this.elements.$nextButton[0],
            prevBtn: this.elements.$prevButton[0],
            preNavBtn: this.elements.$prevNav[0],
            nextNavBtn: this.elements.$nextNav[0],
            ajaxProcessing: false,
            yearHtLabel: thumbnailSwiperContainer.length,
            hrNavigationBar: this.elements.$hrNavigationBar,
            langDir: selector.data("dir"),
            mainSwiperObj: {},
            navSwiperObj: {},
            thumSwiperObj: {},
        }

        this.mainSwiper(selector);

        if (this.attr.yearHtLabel) {
            this.thumbnailSwiper(thumbnailSwiperContainer);
        }
    }

    async mainSwiper(selector) {
        this.slideChangeComplete=false;
        const autoHeight=selector.data('autoHeight');
        const spacebw = this.attr.selector.data("spacebw");
        const paginationEl = this.elements.$paginationEl;
        const yearHtLabel = this.attr.yearHtLabel;
        let slidestoshow = this.attr.slidestoshow;
        let autoplay = this.attr.autoplay;
        let navigation = {};
        let loopedSlides;
        let pagination=false;

        if(this.attr.lineFilling){
            pagination= {
                el: paginationEl[0],
                type: 'progressbar',
            }
        }

        if (this.attr.langDir == 'rtl') {
            navigation = {
                nextEl: this.attr.prevBtn,
                prevEl: this.attr.nextBtn,
            }
        } else {
            navigation = {
                nextEl: this.attr.nextBtn,
                prevEl: this.attr.prevBtn,
            }
        }

        if (yearHtLabel) {
            loopedSlides = slidestoshow;
            slidestoshow = 1;
            navigation = '';
            autoplay = false;
        };

        let respsSlidesperview = 1;
        if (slidestoshow > 2) {
            respsSlidesperview = 2;
        }


        this.attr.mainSwiperObj = await this.swipperGlobalCheck(selector, {
            autoHeight: autoHeight ? true : false,
            spaceBetween: spacebw,
            autoplay: autoplay,
            delay: 3000,
            loop: this.attr.infiniteLoop,
            speed: this.attr.speed,
            slidesPerView: slidestoshow,
            direction: 'horizontal',
            pagination,
            navigation,
            loopedSlides: loopedSlides,
            slidesPerGroup: 1,
            disableOnInteraction: true,
            breakpoints: {
                280: {
                    slidesPerView: 1,
                    loopedSlides: 2,
                },
                768: {
                    slidesPerView: respsSlidesperview,
                    loopedSlides: 3,
                },
                1024: {
                    slidesPerView: slidestoshow,
                    loopedSlides: loopedSlides,
                }
            },
            on: {
                slideChange: (e) => {
                    if (this.attr.ajaxLoadMore === 'yes') {
                        let args = e;
                        let sliderEl = (args.$el).attr('id');
                        let El = jQuery("#" + sliderEl);
                        if (!this.attr.ajaxProcessing && !yearHtLabel && args.isEnd && El.hasClass("twae-loadMore-complete") === false) {
                            // Post ajax load more request
                            this.ajaxLoadMoreRequest();
                        }
                    }
                    
                    if(this.attr.lineFilling){
                        let scrollFilling;
                        this.slideChangeComplete=false;
                        scrollFilling=setInterval(()=>{
                            this.twaeScrollFilling();
                            if(this.slideChangeComplete){
                                clearInterval(scrollFilling);
                            }
                        },250);
                    }
                },
                slideChangeTransitionEnd: ()=>{
                    this.slideChangeComplete=true;
                }
            }
        });

        if(this.attr.lineFilling){
            let debounceTimeout;

            this.twaeScrollFilling();

            jQuery(window).on('resize', _=>{
                clearTimeout(debounceTimeout);
                debounceTimeout = setTimeout(()=>{
                    this.twaeScrollFilling()
                },200)
            });
        }

        if (this.attr.hrNavigationBar.length > 0) {
            this.navBarHtml();
        }

        if(this.attr.infiniteLoop){
            elementorFrontend.hooks.doAction('frontend/twaeWidget/horizontalSliderInitialize','custom hook');
        }
    }

    async thumbnailSwiper(selector) {
        let navigation = {};

        if (this.attr.langDir == 'rtl') {
            navigation = {
                nextEl: this.attr.prevBtn,
                prevEl: this.attr.nextBtn,
            }
        } else {
            navigation = {
                nextEl: this.attr.nextBtn,
                prevEl: this.attr.prevBtn,
            }
        }

        this.attr.thumSwiperObj= await this.swipperGlobalCheck(selector, {
            spaceBetween: 0,
            slidesPerView: this.attr.slidestoshow,
            autoplay: this.attr.autoplay,
            delay: 3000,
            speed: this.attr.speed,
            loop: this.attr.infiniteLoop,
            navigation,
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
                    slidesPerView: this.attr.slidestoshow,
                }
            },
            on: {
                slideChange: (e) => {
                    if (this.attr.ajaxLoadMore == 'yes') {
                        const activeIndex = e.activeIndex;
                        const slidesPerView = e.params.slidesPerView;
                        const totalSlides = e.slides.length;
                        const remaingSlide = Math.ceil(slidesPerView / 2);
                        let activeSlide = activeIndex + remaingSlide;
                        if (!this.attr.ajaxProcessing && activeSlide === totalSlides && this.attr, selector.hasClass("twae-loadMore-complete") === false) {
                            // Post ajax load more request
                            this.ajaxLoadMoreRequest();
                        };
                    }
                }
            }
        });
    }

    async navSwiper(selector) {
        let navigation = {};
        if (this.attr.langDir == 'rtl') {
            navigation = {
                nextEl: this.attr.preNavBtn,
                prevEl: this.attr.nextNavBtn,
            }
        } else {
            navigation = {
                nextEl: this.attr.nextNavBtn,
                prevEl: this.attr.preNavBtn,
            }
        }

        let navPerView = 10;
        this.attr.navSwiperObj = await this.swipperGlobalCheck(selector, {
            slidesPerView: navPerView,
            clickable: true,
            allowTouchMove: false,
            slideToClickedSlide: true,
            spaceBetween: 20,
            breakpoints: {
                280: {
                    slidesPerView: 2,
                },
                576: {
                    slidesPerView: 3,
                },
                768: {
                    slidesPerView: 5,
                },
                1024: {
                    slidesPerView: navPerView,
                }
            },
            navigation,
        });

    }

    navBarHtml() {
        var navPostId = this.attr.hrNavigationBar.attr('id');

        navPostId = navPostId.replace('twae-horizontal-navigationBar-', '');

        let navigatorYear = this.attr.selector.find('.twae-year');
        const timelineStories=this.attr.selector.find('.twae-story.swiper-slide');

        
        const updateDuplicateIndex=()=>{
            for (var i = 0; i < timelineStories.length; i++) {
                timelineStories[i].setAttribute('data-index',i);
            }
        }
        
        const navListItems=()=>{
            let hrlistAll = document.createElement('div');
            hrlistAll.classList.add('twae-horizontal-navigation-items', 'swiper-wrapper');
        
            const activeSlide=this.attr.selector[0].swiper.activeIndex;

            let hrlistEl = '';
            this.attr.hrNavigationBar[0].appendChild(hrlistAll);
            const indexCache = new Array();
            for (var i = 0; i < navigatorYear.length; i++) {
                indexCache.push(i);
                let uniqueID = navPostId + '-item-' + i;
                let HrscrollbarID = 'twae-horizontal-unique-' + uniqueID;
                navigatorYear[i].setAttribute('id', HrscrollbarID);
    
                let labelClassHr = jQuery('#' + HrscrollbarID).find('.twae-year-text');
    
                hrlistEl = document.createElement('div');
                if (activeSlide === i) {
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

        updateDuplicateIndex();
        navListItems();
        // Call the navSwiper function with the hrNavigationBar element
        this.navSwiper(this.attr.hrNavigationBar);
    }

    // Swiper year navigation controller function start
    swiperYearNavController(element) {
        const navPostIdAttr = element.attr('id');
        const navPostId = navPostIdAttr.replace('twae-horizontal-navigationBar-', '');
        const mainSwiper = jQuery(`#twae-wrapper-${navPostId}`).find('.twae-slider-container')[0];
        const yearSwiper = element[0];

        // Swiper index update.
        const swiperIndexUpdate = (id,type) => {
            if ('undefined' !== typeof id) {
                const mainSwiperIndex = jQuery(mainSwiper).find(`.twae-year#${id}`).closest('.twae-story.swiper-slide').data('index');
                // Update main swiper index.
                if('mainSwiper' !== type){
                    mainSwiper.swiper.slideTo(mainSwiperIndex);
                }

                // Remove active class from previous year nav
                jQuery(yearSwiper).find('.swiper-slide').removeClass('active');
                // Added Year Navigation class on current active nav.
                const activeNav = jQuery(yearSwiper).find(`.swiper-slide[data-id="${id}"]`);
                activeNav.addClass('active');

                const navActiveIndex = activeNav.data('index');
                // Update Nav Swiper index.
                yearSwiper.swiper.slideTo(navActiveIndex);
            }
        }

        // Year Navigation Swiper on click function.
        yearSwiper.swiper.on('click', (e) => {
            const targetElement = e.clickedSlide ? e.clickedSlide : e.target;
            const id = jQuery(targetElement).data('id');
            swiperIndexUpdate(id,'yearSwiper');
        });

        // Main Swiper slide change function.
        mainSwiper.swiper.on('slideChange', () => {
            const activeIndex = mainSwiper.swiper.activeIndex;
            const activeSlide = mainSwiper.swiper.slides[activeIndex];
            const id = jQuery(activeSlide).find('.twae-year').attr('id');
            swiperIndexUpdate(id, 'mainSwiper');
        });

        // Year Navigation nav button class update on window resize.
        const updateNavbtnCls = () => {
            const screenSize = window.innerWidth;
            const navList = jQuery(yearSwiper).find('.swiper-slide').length;
            let navHidden = true;
            if (screenSize > 1024 && navList > 10) {
                navHidden = false;
            } else if (screenSize > 768 && screenSize < 1024 && navList > 5) {
                navHidden = false;
            } else if (screenSize > 576 && screenSize < 768 && navList > 3) {
                navHidden = false;
            } else if (screenSize < 575 && navList > 2) {
                navHidden = false;
            } else {
                navHidden = true;
            }

            const nav = jQuery(yearSwiper).closest('.twae-hor-nav-wrapper').find('.twae-nav-next, .twae-nav-prev');
            navHidden ? nav.hide() : nav.show();
        };

        jQuery(window).on('resize', updateNavbtnCls);
        updateNavbtnCls();
    }

    twaeScrollFilling(){
        const paginationObj=this.attr.selector[0].swiper.pagination;

        if(paginationObj){
            let paginationWrp=paginationObj.el;
            if(paginationWrp){
                paginationWrp=jQuery(paginationWrp);
                const sliderLeftPostion=this.attr.selector[0].getBoundingClientRect().left - this.attr.selector[0].offsetLeft;
                const pagination=paginationWrp.find('span.swiper-pagination-progressbar-fill');
                const paginationTransformStyle=pagination[0].style.transform;
                const progressValue=paginationTransformStyle.match(/scaleX\(([^)]+)\)/)[1];
                const paginationWidth=Math.round(pagination[0].offsetWidth * progressValue);

                if(this.attr.selector.closest('.twae-wrapper').hasClass('twae-horizontal-highlighted-timeline')){
                    const icons=this.attr.thumbnailSwiperContainer.find('.swiper-slide .twae-icon,.swiper-slide .twae-icondot');
                    icons.each((_,icon)=>{
                        const iconLeftPosition=icon.getBoundingClientRect().left;

                        const iconCurrentPosition=iconLeftPosition - sliderLeftPostion;

                        if(paginationWidth > iconCurrentPosition){
                            jQuery(icon).closest('.swiper-slide').addClass('twae-in-view-port');
                        }else{
                            jQuery(icon).closest('.swiper-slide').removeClass('twae-in-view-port');
                        }
                    })
                }else{
                    const slides=this.attr.selector.find('.twae-story.swiper-slide');
                    slides.each((index,slide)=>{
                        const icons=jQuery(slide).find('.twae-icon,.twae-icondot')
                        const yearLabel=jQuery(slide).find('.twae-year');

                        if(icons.length > 0){
                            const iconLeftPosition=icons[0].getBoundingClientRect().left;
                            const storyPosition=iconLeftPosition - sliderLeftPostion;

                            if(paginationWidth >= storyPosition){
                                jQuery(slide).addClass('twae-in-view-port');
                            }else{
                                jQuery(slide).removeClass('twae-in-view-port');
                            }
                        }
    
                        if(yearLabel.length > 0){
                            const yearLeftPosition=yearLabel[0].getBoundingClientRect().left;
    
                            const yearPosition=yearLeftPosition - sliderLeftPostion;
                            
                            if(paginationWidth >= yearPosition){
                                jQuery(yearLabel).addClass('twae-in-view-port');
                            }else{
                                jQuery(yearLabel).removeClass('twae-in-view-port');
                            }
                        }
                    })
                }
            }
        }
    }

    // Post ajax load more request
    ajaxLoadMoreRequest(attr) {
        const clickedLoadMore = new Event('clickedLoadMore');
        const mainSwiperWrp = this.attr.selector;
        const Widget_id = mainSwiperWrp[0].id.replace('twae-', '');
        const postWindowObject = window['post_timeline_' + Widget_id];
        var allAtts = postWindowObject.attribute;
        let ajaxUrl = postWindowObject.url;
        let wpNonce = postWindowObject.private_key;
        let pageNo = parseInt(mainSwiperWrp[0].getAttribute('data-page-no'));
        let totalPage = parseInt(mainSwiperWrp.data("total-pages"));
        if (pageNo === totalPage) {
            this.attr.selector.addClass('twae-loadMore-complete');
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
            beforeSend: () => {
                this.attr.ajaxProcessing = true;
                jQuery(this.attr.nextBtn).addClass('swiper-button-disabled');
                jQuery(this.attr.nextBtn).find('.lm_active_state').show();

            }
        }).done((res) => {
            if (this.attr.yearHtLabel) {
                this.attr.thumbnailSwiperContainer[0].swiper.appendSlide(res.highlightedcontent);
                this.attr.thumbnailSwiperContainer[0].swiper.update(true)
            }
            this.attr.selector[0].swiper.appendSlide(res.html);
            this.attr.selector[0].swiper.update(true)
            jQuery(this.attr.nextBtn).find('.lm_active_state').hide();
            let thisPage = pageNo + 1;
            this.attr.selector.attr("data-page-no", thisPage);
            document.dispatchEvent(clickedLoadMore); //reinitialize masonry only for compact layout  
            if (thisPage === totalPage) {
                this.attr.selector.addClass('twae-loadMore-complete');	// add class to prevent loadMore on this timeline
            }
        }).always((res) => {
            jQuery(this.attr.nextBtn).removeClass('swiper-button-disabled');
            this.attr.ajaxProcessing = false;
        });
    }

    async swipperGlobalCheck(swiperElement, swiperConfig) {
        const autoplayStopOnhover = this.attr.selector.data("stop-autoplay-onhover");

        // Swiper autoplay stop on hover
        const swiperHoverEffect = (element) => {
            const mainSwiper = element[0].swiper;

            jQuery(element).mouseenter(function () {
                mainSwiper.autoplay.stop();
            });
            jQuery(element).mouseleave(function () {
                mainSwiper.autoplay.start();
            });

        };

        // Horizontal Highlighted thumbnail controller
        const swiperThumbController = (element) => {
            const yearSwiper = element[0];
            const mainSwiper = element.closest('.twae-wrapper-inside').find('.twae-slider-container')[0]
            yearSwiper.swiper.controller.control = mainSwiper.swiper;
            mainSwiper.swiper.controller.control = yearSwiper.swiper;
        };

        if (swiperElement.length) {
            if ('undefined' === typeof Swiper) {
                const asyncSwiper = elementorFrontend.utils.swiper;
                return new Promise((resolve, reject) => {
                    new asyncSwiper(swiperElement, swiperConfig).then((newSwiperInstance) => {

                        // Stop swiper autoplay on hover
                        if (autoplayStopOnhover) {
                            if (!this.attr.yearHtLabel && swiperElement.hasClass('twae-slider-container')) {
                                swiperHoverEffect(swiperElement);
                            } else if (swiperElement.hasClass('year-swiper-container')) {
                                swiperHoverEffect(swiperElement);
                            }
                        }

                        // Horizontal Highlighted thumbnail controller
                        if (this.attr.yearHtLabel && swiperElement.hasClass('year-swiper-container')) {
                            swiperThumbController(swiperElement);
                        }

                        // Horiozntal Navigation Bar controller
                        if (this.attr.hrNavigationBar.length && swiperElement.hasClass('twae-horizontal-navigationBar')) {
                            this.swiperYearNavController(swiperElement);
                        }

                        resolve(newSwiperInstance);
                    }).catch((error) => {
                        reject(error);
                    });
                });
            } else {
                const swiper = new Swiper(swiperElement[0], swiperConfig);

                // Stop swiper autoplay on hover
                if (autoplayStopOnhover) {
                    if (!this.attr.yearHtLabel && swiperElement.hasClass('twae-slider-container')) {
                        swiperHoverEffect(swiperElement);
                    } else if (swiperElement.hasClass('year-swiper-container')) {
                        swiperHoverEffect(swiperElement);
                    }
                }

                // Horizontal Highlighted thumbnail controller
                if (this.attr.yearHtLabel && swiperElement.hasClass('year-swiper-container')) {
                    swiperThumbController(swiperElement);
                }

                // Horiozntal Navigation Bar controller
                if (this.attr.hrNavigationBar.length && swiperElement.hasClass('twae-horizontal-navigationBar')) {
                    this.swiperYearNavController(swiperElement);
                }

                return swiper;
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