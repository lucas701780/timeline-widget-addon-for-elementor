class VerticalTimeline extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                wrapper: '.twae-vertical',
                line_inner: '.twae-inner-line',
                year_container: '.twae-year-container',
                animationSelector: '.twae-labels',
                contentanimationSelector: '.twae-content',
                load_more_button: '.twae-ajax-load-more',
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');
        return {
            $wrapper: this.$element.find(selectors.wrapper),
            $line_inner: this.$element.find(selectors.line_inner),
            $year_container: this.$element.find(selectors.year_container),
            $animationSelector: this.$element.find(selectors.animationSelector),
            $contentanimationSelector: this.$element.find(selectors.contentanimationSelector),
            $load_more_button: this.$element.find(selectors.load_more_button),
        };
    }

    bindEvents() {
        var timelineWrapper = this.elements.$wrapper;
        var line_inner = this.elements.$line_inner;
        var year_container = this.elements.$year_container;
        var line_outer = timelineWrapper.find('.twae-line');
        var animationSelector = this.elements.$animationSelector;
        var contentanimationSelector = this.elements.$contentanimationSelector;
        var loadMoreButton = this.elements.$load_more_button;
        var label_animation=animationSelector && animationSelector.data("aos");
        var content_animation=contentanimationSelector && contentanimationSelector.data("aos");

        if (timelineWrapper.length > 0 && (animationSelector.length > 0 || contentanimationSelector.length > 0)) {
            if (label_animation != 'none' || content_animation !== 'none') {
                document.addEventListener('clickedLoadMore', function (e) {
                    AOS.init();
                }, true);

                AOS.init();

                setTimeout(()=>{AOS.init()},2000);
            }
        }

        function twae_scroll_callback() {
            const timelineEntry = timelineWrapper.find('.twae-repeater-item');
            if (timelineWrapper.length < 1) {
                return false;
            }
            // fill line color start
            var rootElement = document.documentElement;
            var half_viewport = (jQuery(window).height()) / 2;
            var lineID = line_outer[0];

            if (lineID == null) {
                return;
            }
            var rect = lineID.getBoundingClientRect();
            var timelineTop;


            if (rect.top < 0) {
                timelineTop = Math.abs(rect.top);
            } else {
                timelineTop = -Math.abs(rect.top);
            }
            var lineInnerHeight = timelineTop + half_viewport;
            var line_outer_height = line_outer.outerHeight();
            var timelineWrapper_position = jQuery(timelineWrapper).offset().top;
            var timelineWrapper_top = timelineWrapper_position - rootElement.scrollTop;

            timelineWrapper.addClass("twae-start-out-viewport");

            if (lineInnerHeight <= line_outer_height) {
                timelineWrapper.addClass("twae-end-out-viewport");
                timelineWrapper.addClass("twae-start-out-viewport");
                line_inner.height(lineInnerHeight);
                if ((timelineWrapper_top) < ((half_viewport))) {
                    timelineWrapper.removeClass("twae-start-out-viewport");
                    timelineWrapper.addClass("twae-start-fill");
                }
            } else {
                timelineWrapper.removeClass("twae-end-out-viewport");
                timelineWrapper.addClass("twae-end-fill");
                line_inner.height(line_outer_height);
            }
            // fill line color end

            var timelineEntry_position, timelineEntry_top,
                year_container_pos, year_container_top;

            for (var i = 0; i < timelineEntry.length; i++) {
                const icon=jQuery(timelineEntry[i]).find('.twae-icon, .twae-icondot');

                const iconPosition=icon.length > 0 ? icon[0].offsetTop : 0;

                timelineEntry_position = jQuery(timelineEntry[i]).offset().top + iconPosition;

                timelineEntry_top = timelineEntry_position - rootElement.scrollTop;

                if ((timelineEntry_top) < ((half_viewport))) {

                    timelineEntry[i].classList.remove("twae-out-viewport");
                } else {
                    timelineEntry[i].classList.add("twae-out-viewport");
                }

            }

            //fill year_container border
            for (var i = 0; i < year_container.length; i++) {

                year_container_pos = jQuery(year_container[i]).offset().top;

                year_container_top = year_container_pos - rootElement.scrollTop;

                if ((year_container_top) < ((half_viewport))) {
                    year_container[i].classList.remove("twae-out-viewport");
                } else {
                    year_container[i].classList.add("twae-out-viewport");
                }
            }
        }

        var lineFilling = timelineWrapper.data("line-filling");
        
        if (lineFilling !== 'undefined' && lineFilling == "on") {
            twae_scroll_callback();
            window.addEventListener("scroll", twae_scroll_callback);
        }

        /* Ajax Load More */
        loadMoreButton.on('click', function () {
            let thisBtn = jQuery(this);
            thisBtn.find('.lm_default_state').hide();
            thisBtn.find('.lm_active_state').show();

            let widgetId = timelineWrapper.attr('id');
            widgetId = widgetId.replace('twae-', '');
            const postWindowObject = window['post_timeline_' + widgetId];
            let allAtts = postWindowObject.attribute;
            if (timelineWrapper.hasClass('twae-loadMore-complete') === false) {
                let ajax_url = postWindowObject.url;
                let wp_nonce = postWindowObject.private_key;
                let page_no = parseInt(timelineWrapper.attr("data-page-no"));
                let max_pages = parseInt(timelineWrapper.attr("data-total-pages"));

                const clickedLoadMore = new Event('clickedLoadMore');
                jQuery.ajax({
                    type: "POST",
                    dataType: "json",
                    url: ajax_url,
                    data: {
                        action: 'twae_post_load_more',
                        private_key: wp_nonce,
                        page_no: page_no,
                        settings: allAtts
                    },
                    beforeSend: function () {
                        window["twae_ajax_post_" + widgetId] = true;
                    }
                }).done(function (res) {
                    timelineWrapper.find(".twae-story").last().after(res.html);
                    let thisPage = page_no + 1;
                    timelineWrapper.attr("data-page-no", thisPage);
                    thisBtn.find('.lm_default_state').show();
                    thisBtn.find('.lm_active_state').hide();
                    if (thisPage >= max_pages) {
                        timelineWrapper.addClass('twae-loadMore-complete'); // add class to prevent loadMore on this timeline
                        loadMoreButton.hide();
                    }
                    //if( El.hasClass('twae-compact') ){
                    document.dispatchEvent(clickedLoadMore); //reinitialize masonry only for compact layout  
                    twae_scroll_callback();
                    // }
                }).always(function (res) {
                    delete (window["twae_ajax_post_" + widgetId]);
                })
            }

        });

    }

}


jQuery(window).on('elementor/frontend/init', () => {

    const addHandler = ($element) => {
        elementorFrontend.elementsHandler.addHandler(VerticalTimeline, {
            $element,
        });
    };

    elementorFrontend.hooks.addAction('frontend/element_ready/timeline-widget-addon.default', addHandler);
    elementorFrontend.hooks.addAction('frontend/element_ready/twae-post-timeline-widget.default', addHandler);
});