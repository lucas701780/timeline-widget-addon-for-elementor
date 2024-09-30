class TimelinePopup extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                timelineWrapper: '.twae-wrapper',
                PopUpcontent: '.twae-popup-content',
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');
        return {
            $timelineWrapper: this.$element.find(selectors.timelineWrapper),
            $PopUpcontent: this.$element.find(selectors.PopUpcontent),

        };
    }

    bindEvents() {

        var selector = this.elements.$timelineWrapper,
            timeline_style = selector.data("style"),
            enablePopup = selector.data("enable-popup");

        if (timeline_style == 'style-4' || enablePopup == "yes") {
            twae_popup();
            document.addEventListener('clickedLoadMore', function (e) {
                twae_popup();
            }, true);
        }

        function twae_popup() {
            // var link = selector.find('.twae-popup-links');
                jQuery(document).on("click",".twae-popup-links", function (el) {
                el.preventDefault();
                
                var popupID = jQuery(this).attr('href');
                var wrp_id = jQuery(this).parents('.twae-wrapper').attr('id');
                var wrp_id = wrp_id.replace(/twae-wrapper-/g, '');
                var main_id = wrp_id.split("-").pop();
                
                var popupCls = 'elementor-element elementor-element-'+ main_id+'-popup twae-popup';
                var popupContent = jQuery(popupID).find('.twae-content').html();
                
                
                if (jQuery('.twae-popup').length == 0) {
                    jQuery(".elementor").not('footer,header').append("<div class='" + popupCls + "'><div class='twae-popup-bg'></div> <div class = 'twae-popup-content twae-wrapper'><div class='twae-popup-body'></div><div class='twae-popup-footer'><span class = 'twae_close_button' > &#10006;</span></div></div></div>");
                } else {
                    jQuery('.twae-popup').show();
                }
                const popupContainer=jQuery('.twae-popup .twae-popup-body').html(popupContent);
                const images=jQuery(popupContainer).find('img');
                images.removeAttr('srcset');
                var slideshowSlector = jQuery('.twae-popup').find('.twae-slideshow.swiper-container'),
                autoplay = slideshowSlector.data("slideshow_autoplay");
                if (slideshowSlector.length) {
                    const swiperWrp=slideshowSlector[slideshowSlector.length - 1];
                    const nextButton = jQuery(swiperWrp).find('.twae-icon-right-open');
                    const prevButton = jQuery(swiperWrp).find('.twae-icon-left-open');
                    var Navigation;
                    var lang_dir = slideshowSlector.attr("dir");
                    if (lang_dir == 'rtl') {
                        var Navigation = {
                            nextEl: prevButton[0],
                            prevEl: nextButton[0],
                        }
                    } else {
                        Navigation = {
                            nextEl: nextButton[0],
                            prevEl: prevButton[0],
                        }
                    }
                    var swiper = new Swiper(swiperWrp, {
                        autoplay: autoplay,
                        delay: 5000,
                        slidesPerView: 1,
                        direction: 'horizontal',
                        navigation: Navigation,
                        autoHeight: true,
                    });
                }
                jQuery('.twae-popup').css('z-index', '99999');

                var PopupClose = jQuery("body").find('.twae_close_button');
                jQuery(PopupClose).on('click', function (el) {
                    jQuery('.twae-popup').remove();
                });


            });
            jQuery(document).mouseup(function (e) {
                if (jQuery('.twae-popup').length > 0) {
                    var container = jQuery('.twae-popup-content');
                    if (!container.is(e.target) && container.has(e.target).length === 0) {
                        jQuery('.twae-popup').remove();
                    }
                }
            });
        }
    }

}


jQuery(window).on('elementor/frontend/init', () => {

    const addHandler = ($element) => {
        elementorFrontend.elementsHandler.addHandler(TimelinePopup, {
            $element,
        });
    };

    elementorFrontend.hooks.addAction('frontend/element_ready/timeline-widget-addon.default', addHandler);
    elementorFrontend.hooks.addAction('frontend/element_ready/twae-post-timeline-widget.default', addHandler);
});