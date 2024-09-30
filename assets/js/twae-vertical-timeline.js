class VerticalTimeline extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                wrapper: '.twae-vertical',
                lineInner: '.twae-inner-line',
                yearContainer: '.twae-year-container',
                animationSelector: '.twae-labels',
                contentanimationSelector: '.twae-content',
                loadMoreButton: '.twae-ajax-load-more',
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');
        return {
            $wrapper: this.$element.find(selectors.wrapper),
            $lineInner: this.$element.find(selectors.lineInner),
            $yearContainer: this.$element.find(selectors.yearContainer),
            $animationSelector: this.$element.find(selectors.animationSelector),
            $contentanimationSelector: this.$element.find(selectors.contentanimationSelector),
            $loadMoreButton: this.$element.find(selectors.loadMoreButton),
        };
    }

    bindEvents() {
        const timelineWrapper = this.elements.$wrapper;
        var animationSelector = this.elements.$animationSelector;
        var contentanimationSelector = this.elements.$contentanimationSelector;
        var loadMoreButton = this.elements.$loadMoreButton;
        var labelAnimation = animationSelector && animationSelector.data("aos");
        var contentAnimation = contentanimationSelector && contentanimationSelector.data("aos");
        const yearContainer = this.elements.$yearContainer;

        if (timelineWrapper.length > 0 && (animationSelector.length > 0 || contentanimationSelector.length > 0)) {
            if (labelAnimation != 'none' || contentAnimation !== 'none') {
                document.addEventListener('clickedLoadMore', function (e) {
                    AOS.init();
                }, true);

                AOS.init();

                setTimeout(() => { AOS.init() }, 2000);
            }
        }

        var lineFilling = timelineWrapper.data("line-filling");
        
        if(loadMoreButton.length > 0){
            this.twaeAjaxLoadMore(loadMoreButton,lineFilling);
        }
        
        if(timelineWrapper.hasClass('twae-vertical-right') && (timelineWrapper.hasClass('twae-label-content-top') || timelineWrapper.hasClass('twae-label-content-inside')) && yearContainer.length <= 0){
            timelineWrapper.addClass('twae-year-empty');
        };

        if (lineFilling !== 'undefined' && lineFilling == "on") {
            this.twaeScrollCallback();
            window.addEventListener("scroll", ()=>{this.twaeScrollCallback()});
            
            // Line filling update after media slide change.
            elementorFrontend.hooks.addAction('frontend/twaeWidget/mediaSlideChange', (e)=>{
                this.twaeScrollCallback();
            })
        }

    }

    twaeScrollCallback() {
        if(this.elements.$wrapper.length <= 0){
            return;
        }

        const timelineWrapper = this.elements.$wrapper;
        var lineInner = this.elements.$lineInner;
        var yearContainer = this.elements.$yearContainer;
        var line_outer = timelineWrapper.find('.twae-line');
        const timelineEntry = timelineWrapper.find('.twae-repeater-item');
        const oneSidedLayout = timelineWrapper.hasClass('twae-vertical-right') || timelineWrapper.hasClass('twae-vertical-left');
        const screenSize = window.innerWidth;

        if (timelineWrapper.length < 1) {
            return false;
        }
        // fill line color start
        var rootElement = document.documentElement;
        var halfViewport = (jQuery(window).height()) / 2;
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
        var lineInnerHeight = timelineTop + halfViewport;
        var lineOuterHeight = line_outer.outerHeight();
        var timelineWrapperPosition = jQuery(timelineWrapper).offset().top;
        var timelineWrapperTop = timelineWrapperPosition - rootElement.scrollTop;

        timelineWrapper.addClass("twae-start-out-viewport");

        if (lineInnerHeight <= lineOuterHeight) {
            timelineWrapper.addClass("twae-end-out-viewport");
            timelineWrapper.addClass("twae-start-out-viewport");
            lineInner.height(lineInnerHeight);
            if ((timelineWrapperTop) < ((halfViewport))) {
                timelineWrapper.removeClass("twae-start-out-viewport");
                timelineWrapper.addClass("twae-start-fill");
            }
        } else {
            timelineWrapper.removeClass("twae-end-out-viewport");
            timelineWrapper.addClass("twae-end-fill");
            lineInner.height(lineOuterHeight);
        }
        // fill line color end

        var timelineEntryPosition, timelineEntryTop,
            yearContainerPos, yearContainerTop;

        for (var i = 0; i < timelineEntry.length; i++) {
            const icon = jQuery(timelineEntry[i]).find('.twae-icon, .twae-icondot');

            const iconPosition = icon.length > 0 ? icon[0].offsetTop : 0;

            timelineEntryPosition = jQuery(timelineEntry[i]).offset().top + iconPosition;

            timelineEntryTop = timelineEntryPosition - rootElement.scrollTop;

            if ((timelineEntryTop) < ((halfViewport))) {

                timelineEntry[i].classList.remove("twae-out-viewport");
            } else {
                timelineEntry[i].classList.add("twae-out-viewport");
            }

        }

        //fill yearContainer border
        for (var i = 0; i < yearContainer.length; i++) {

            yearContainerPos = jQuery(yearContainer[i]).offset().top;
            yearContainerTop = yearContainerPos - rootElement.scrollTop;

            if (oneSidedLayout || 768 > screenSize) {
                const yearLabel = jQuery(yearContainer[i]).find('.twae-year-label');
                const yearBorderSize = parseInt(yearLabel.css('border-width'));
                let yearHeight = yearLabel[0].offsetHeight / 2;
                yearContainerTop = yearContainerTop + yearHeight - (yearBorderSize / 2);
            }

            if ((yearContainerTop) < ((halfViewport))) {
                yearContainer[i].classList.remove("twae-out-viewport");
            } else {
                yearContainer[i].classList.add("twae-out-viewport");
            }
        }
    }

    /* Ajax Load More */
    twaeAjaxLoadMore(loadBtn,lineFilling) {
        loadBtn.on('click',  (e)=> {
            const timelineWrapper = this.elements.$wrapper;
            let thisBtn = jQuery(e.currentTarget);
            thisBtn.find('.lm_default_state').hide();
            thisBtn.find('.lm_active_state').show();

            let widgetId = timelineWrapper.attr('id');
            widgetId = widgetId.replace('twae-', '');
            const postWindowObject = window['post_timeline_' + widgetId];
            let allAtts = postWindowObject.attribute;
            if (timelineWrapper.hasClass('twae-loadMore-complete') === false) {
                let ajaxUrl = postWindowObject.url;
                let wpNonce = postWindowObject.private_key;
                let pageNo = parseInt(timelineWrapper.attr("data-page-no"));
                let maxPages = parseInt(timelineWrapper.attr("data-total-pages"));

                const clickedLoadMore = new Event('clickedLoadMore');
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
                    beforeSend:  ()=> {
                        window["twae_ajax_post_" + widgetId] = true;
                    }
                }).done( (res)=> {
                    timelineWrapper.find(".twae-story").last().after(res.html);
                    let thisPage = pageNo + 1;
                    timelineWrapper.attr("data-page-no", thisPage);
                    thisBtn.find('.lm_default_state').show();
                    thisBtn.find('.lm_active_state').hide();
                    if (thisPage >= maxPages) {
                        timelineWrapper.addClass('twae-loadMore-complete'); // add class to prevent loadMore on this timeline
                        loadBtn.hide();
                    }

                    document.dispatchEvent(clickedLoadMore); //reinitialize masonry only for compact layout 
                    if (lineFilling !== 'undefined' && lineFilling == "on") {
                        this.twaeScrollCallback();
                    }
                }).always( (res)=> {
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