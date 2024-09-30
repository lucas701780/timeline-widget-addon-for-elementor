class CompactTimeline extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                wrapper: '.twae-vertical',
                compactContainer: '.twae-compact',
                timelineEntry: '.twae-story',
                load_more_button: '.twae-ajax-load-more',
                line_inner: '.twae-inner-line'
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');
        return {
            $wrapper: this.$element.find(selectors.wrapper),
            $compactContainer: this.$element.find(selectors.compactContainer),
            $timelineEntry: this.$element.find(selectors.timelineEntry),
            $load_more_button: this.$element.find(selectors.load_more_button),
            $line_inner: this.$element.find(selectors.line_inner),
        };
    }

    bindEvents() {
        var timelineWrapper = this.elements.$wrapper;
        var compactContainer = this.elements.$compactContainer;
        var loadMoreButton = this.elements.$load_more_button;

        var line_inner = this.elements.$line_inner;
        var line_outer = timelineWrapper.find('.twae-line');
        var timelineEntry = this.elements.$timelineEntry;

        function twae_masonry_init() {
            compactContainer.masonryCustom({
                itemSelector: '.twae-story',
                initLayout: true,
                fitWidth: true,
            });

            // layout images after they are loaded
            compactContainer.imagesLoaded().progress(function () {
                compactContainer.masonryCustom('layout');
            });

            compactContainer.on('layoutComplete', function () {
                compactContainer.css('visibility', 'visible');
                var leftPos = 0;
                var topPosDiff;
                compactContainer.find('.twae-story').each(function () {
                    var thisStory = jQuery(this);
                    leftPos = thisStory.position().left;
                    if (leftPos <= 0) {
                        thisStory.removeClass("twae-story-right").addClass('twae-story-left');
                    } else {
                        thisStory.removeClass("twae-story-left").addClass('twae-story-right');
                    }

                    if (thisStory.prev().length != 0) {
                        let previous = thisStory.prev().position().top;
                        let positionTop = thisStory.position().top;
                        topPosDiff = positionTop - previous;
                        let lblHeight = 0;

                        if (thisStory.find('.twae-labels').length == 1) {
                            lblHeight = thisStory.find('.twae-labels').height();
                        } else {
                            lblHeight = 54;
                        }
                        let iconContHeight = 0;
                        if (thisStory.find('.twae-icon').length == 1) {
                            iconContHeight = thisStory.find('.twae-icon').outerHeight();
                        } else {
                            iconContHeight = 40;
                        }

                        topPosDiff = parseInt(topPosDiff);
                        
                        const prevElement=this.previousSibling;
                        const currentStyle=getComputedStyle(this);
                        const currentMarginString=currentStyle.getPropertyValue('margin-top');
                        const currentTopMargin=parseInt(currentMarginString.replace('px', ''));

                        const prevStyle=getComputedStyle(prevElement);
                        const prevMarginString=prevStyle.getPropertyValue('margin-top');
                        const prevTopMargin=parseInt(prevMarginString.replace('px', ''));

                        const prevOffsetTop=prevTopMargin + previous;
                        const currentOffsetTop=currentTopMargin + positionTop;

                        
                        const spaceBetween=Math.abs(currentOffsetTop - prevOffsetTop);
                        if (spaceBetween < iconContHeight) {
                            const marginTop=(iconContHeight - spaceBetween + 20);
                            this.setAttribute('data-twae-margin',marginTop);
                            thisStory.css('margin-top', currentTopMargin+marginTop + 'px');
                        }else{
                            const twaeMargin=parseInt(thisStory.data('twaeMargin'));
                            if(!isNaN(twaeMargin)){
                                const spaceBetween=Math.abs(currentOffsetTop - prevOffsetTop) - twaeMargin;
                                if(spaceBetween > iconContHeight){
                                    thisStory.css('margin-top', currentTopMargin-twaeMargin + 'px');
                                }
                            }
                        }
                    }

                });

            });

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
                    // }
                }).always(function (res) {
                    delete (window["twae_ajax_post_" + widgetId]);
                })
            }

        });


        document.addEventListener('clickedLoadMore', function (e) {
            compactContainer.masonryCustom('reloadItems');
            twae_masonry_init();
        }, true);

        jQuery(window).on("resize", function () {
            twae_masonry_init();
        });

        jQuery(document).ready(function () {
            twae_masonry_init();
        });

        jQuery(window).on("load", function () {
            twae_masonry_init();
        });

        function twae_scroll_callback() {

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

            var timelineEntry_position, timelineEntry_top;

            for (var i = 0; i < timelineEntry.length; i++) {

                const icon = jQuery(timelineEntry[i]).find('.twae-icon, .twae-icondot');

                const iconPosition = icon.length > 0 ? icon[0].offsetTop : 0;

                timelineEntry_position = jQuery(timelineEntry[i]).offset().top + iconPosition;

                timelineEntry_top = timelineEntry_position - rootElement.scrollTop;

                if ((timelineEntry_top) < ((half_viewport))) {

                    timelineEntry[i].classList.remove("twae-out-viewport");
                } else {
                    timelineEntry[i].classList.add("twae-out-viewport");
                }

            }

        }

        var lineFilling = timelineWrapper.data("line-filling");

        if (lineFilling !== 'undefined' && lineFilling == "on") {
            twae_scroll_callback();
            window.addEventListener("scroll", twae_scroll_callback);
        }

    }
}

jQuery(window).on('elementor/frontend/init', () => {

    const addHandler = ($element) => {
        elementorFrontend.elementsHandler.addHandler(CompactTimeline, {
            $element,
        });
    };

    elementorFrontend.hooks.addAction('frontend/element_ready/timeline-widget-addon.default', addHandler);
    elementorFrontend.hooks.addAction('frontend/element_ready/twae-post-timeline-widget.default', addHandler);
});