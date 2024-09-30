class TWAE_year_navigation extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                wrapper: '.twae-vertical',
                navigationBar: '.twae-navigationBar',
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');
        return {
            $wrapper: this.$element.find(selectors.wrapper),
            $navigationBar: this.$element.find(selectors.navigationBar),

        };
    }

    bindEvents() {
        var selector = this.elements.$wrapper;
        var timelineEntry=selector.find('.twae-repeater-item');
        var last_story=jQuery(timelineEntry[timelineEntry.length - 1]);

        var navigationBar = this.elements.$navigationBar;
        if (navigationBar.length > 0) {

            navigationBar.addClass("twae-out-viewport");
            var postID = navigationBar.attr('id');
            postID = postID.replace('twae-navigationBar-', '');

            let navigator = selector.find(' .twae-year-container');
            let listAll = document.createElement('ul');
            listAll.classList.add('twae-navigation-items');
            let listEl = '';
            $ = jQuery;
            navigationBar[0].appendChild(listAll);

            for (var i = 0; i < navigator.length; i++) {
                let uniqueID = postID + '-item-' + i;
                let scrollbarID = 'twae-scrollar-' + uniqueID;
                navigator[i].setAttribute('id', scrollbarID);

                let labelClass = $('#' + scrollbarID).find('.twae-year-label');
                listEl = document.createElement('li');
                let anchorEl = document.createElement('a');
                if (i == 0) {
                    anchorEl.classList.add("current");
                    listEl.classList.add("current");
                }
                anchorEl.setAttribute('href', '#' + scrollbarID);
                anchorEl.innerText = labelClass.text();
                listEl.appendChild(anchorEl);
                listAll.appendChild(listEl);
            }

            let mainNavLinks = navigationBar.find("ul li a");
            let previousEl = null;
            window.addEventListener("scroll", () => {
                // var laststory=last_story[timelineEntry.length - 1].id;
                var rootElement = document.documentElement;
                var viewport = (jQuery(window).height()) / 3;
                var timelineWrapper_position = jQuery(selector).offset().top;
                if(last_story.length > 0){
                    var timelineBottom = (last_story.offset().top + last_story.height()) - rootElement.scrollTop;
                    var timeline_top = timelineWrapper_position - rootElement.scrollTop;
                    if ((timeline_top) < ((viewport))) {
                        // console.log(navigationBar);
                        navigationBar.removeClass("twae-out-viewport");
                        navigationBar.addClass("twae-in-viewport");
                    } else {
                        navigationBar.removeClass("twae-in-viewport");
                        navigationBar.addClass("twae-out-viewport");
                    }
                    if (timelineBottom < viewport) {
                        navigationBar.removeClass("twae-in-viewport");
                        navigationBar.addClass("twae-out-viewport");
                    }

                    let extraspace = 400;
                    let fromTop = window.scrollY + extraspace;


                    for (var i = 0; i < mainNavLinks.length; i++) {

                        if ($(mainNavLinks[i].hash).length == 0) {
                            return false;
                        }
                        let section = $(mainNavLinks[i].hash).offset().top;
                        // highlight selected anchor tag if container hit the top
                        if (section <= fromTop) {
                            if (previousEl != null) {
                                // remove any previously selected anchor tag
                                previousEl.classList.remove("current");
                                previousEl.parentElement.classList.remove("current");
                            }
                            mainNavLinks[i].classList.add("current");
                            mainNavLinks[i].parentElement.classList.add("current");
                        }

                        // remove highlight from anchor tag if container went away from the page
                        if (section >= fromTop) {
                            mainNavLinks[i].classList.remove("current");
                            mainNavLinks[i].parentElement.classList.remove("current");
                        }

                        // save the current link as previous for use in next loop.
                        previousEl = mainNavLinks[i];
                    }
                }
            });

            $(navigationBar[0]).find('.twae-nav-icon').on('click', function (e) {
                e.preventDefault();
                $(this).parents().find(".twae-navigation-items").animate({
                    width: "toggle"
                });
            });

        }
    }
}


jQuery(window).on('elementor/frontend/init', () => {

    const addHandler = ($element) => {
        elementorFrontend.elementsHandler.addHandler(TWAE_year_navigation, {
            $element,
        });
    };

    elementorFrontend.hooks.addAction('frontend/element_ready/timeline-widget-addon.default', addHandler);
    elementorFrontend.hooks.addAction('frontend/element_ready/twae-post-timeline-widget.default', addHandler);
});