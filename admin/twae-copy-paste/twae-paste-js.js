!(function () {
    "use strict";
    function t(e, t) {
        return null != t && "undefined" != typeof Symbol && t[Symbol.hasInstance] ? !!t[Symbol.hasInstance](e) : e instanceof t;
    }
    function n(e, n) {
        if (!t(e, n)) throw new TypeError("Cannot call a class as a function");
    }
    function o(e, t) {
        for (var n = 0; n < t.length; n++) {
            var o = t[n];
            (o.enumerable = o.enumerable || !1), (o.configurable = !0), "value" in o && (o.writable = !0), Object.defineProperty(e, o.key, o);
        }
    }
    function i(e, t, n) {
        return t && o(e.prototype, t), n && o(e, n), e;
    }
    var a = (function () {
        function e() {
            n(this, e);
        }
        return (
            i(e, null, [
                {
                    key: "prepare",
                    value: function (e, t, n, o) {
                        var i = _.findIndex(e, function (e) {
                                return "clipboard" === e.name;
                            }),
                            a = _.findIndex(e, function (e) {
                                return "addNew" === e.name;
                            });
                        return (
                            a >= 0 &&
                                e[a].actions.push({
                                    name: "coolplugins-insert",
                                    title: "Add Nested Section",
                                    icon: "eicon-time-line",
                                    callback: function () {
                                        r.insert(t);
                                    },
                                    isEnabled: function () {
                                        return !0;
                                    },
                                }),
                            e.splice(i + 1, 0, {
                                name: "hax-clipboard",
                                actions: [
                                 
                                    {
                                        name: "coolplugins-paste",
                                        title: "Paste (CoolPlugins)",
                                        icon: "eicon-time-line",
                                        // shortcut: "Live Paste",
                                        callback: function () {
                                            n.paste(t);
                                        },
                                    },
                                ],
                            }),
                            e
                        );
                    },
                },
                
            ]),
            e
        );
    })(),
    s = (function () {
            function e(t, o) {
                n(this, e), (this.widgetType = t), (this.widgetCode = o);
            }
            return (
                i(e, [
                    {
                        key: "getWidgetType",
                        value: function () {
                            return this.widgetType;
                        },
                    },
                    {
                        key: "getWidgetCode",
                        value: function () {
                            return this.widgetCode;
                        },
                    },
                    {
                        key: "toJSON",
                        value: function () {
                            return { widgetType: this.widgetType, widgetCode: this.widgetCode };
                        },
                    },
                ]),
                e
            );
        })(),
        l = (function () {
            function e(t) {
                n(this, e), (this.widgetStorage = t), (this.lastCopiedElement = ""), (this.lastPastedElementModel = {});
            }
            return (
                i(e, [
                   
                    {
                        key: "paste",
                        value: async function (e) {
                            var t = this;
                            this.widgetStorage.fetch(function (n) {
                                var i = n.widgetCode,
                                    a = JSON.stringify(i),
                                    r = /\.(gif|jpg|jpeg|svg|png)/gi.test(a),
                                    s = e.model.get("elType"),
                                    l = i.elType,
                                    c = { elType: l, settings: i.settings },
                                    u = null,
                                    d = { at: 0 };
                                   
                                if ("section" === l) {
                                  
                                    var p = null;
                                    "widget" === s ? (p = e.getContainer().parent.parent) : "column" === s ? (p = e.getContainer().parent) : "section" === s && (p = e.getContainer()),
                                        (c.elements = i.elements),
                                        (u = p.parent),
                                        (d.at = p.view.getOption("_index") + 1),
                                        p.model.get("isInner") && (c.isInner = !0);
                                }
                               
                                  else "column" === l ? ((c.elements = i.elements), "widget" === s ? ((u = e.getContainer().parent.parent), (d.at = e.getContainer().parent.view.getOption("_index") + 1)) : "column" === s ? ((u = e.getContainer().parent), (d.at = e.getOption("_index") + 1)) : "section" === s && (u = e.getContainer())) : 
                                 "widget" === l && ((c.widgetType = i.widgetType), (u = e.getContainer()), "widget" === s ? ((u = e.getContainer().parent), (d.at = e.getOption("_index") + 1)) : "column" === s ? (u = e.getContainer()) : "section" === s && (u = e.children.findByIndex(0).getContainer()));
                             if (r && !_.isEmpty(t.lastPastedElementModel) && t.lastCopiedElement === a) return (d.clone = !0), void $e.run("document/elements/create", { model: t.lastPastedElementModel, container: u, options: d });
                                 var m = $e.run("document/elements/create", { model: c, container: u, options: d });
                                r &&
                                    jQuery
                                        .ajax({
                                            url: twaepastejs.ajaxURL,
                                            method: "POST",
                                            data: { type: "import", action: "twae_process_ixport", nonce: twaepastejs.nonce, content: a },
                                            beforeSend: function () {
                                                m.view.$el.addClass("ccpd-ixport").attr("data-ixport-text", "Processing...");
                                            },
                                        })
                                        .done(function (e) {
                                            if (e.success) {
                                                var n = e.data[0];
                                                (c.settings = n.settings),
                                                    (c.elType = n.elType),
                                                    "widget" === n.elType ? (c.widgetType = n.widgetType) : (c.elements = n.elements),
                                                    m.view.$el.attr("data-ixport-text", "Processing completed");
                                                var o = setTimeout(function () {
                                                    $e.run("document/elements/delete", { container: m });
                                                    var e = $e.run("document/elements/create", { model: c, container: u, options: d });
                                                    // console.log(e);
                                                    (t.lastPastedElementModel = elementorCommon.helpers.cloneObject(e.model.toJSON())), clearTimeout(o);
                                                }, 750);
                                            }
                                        }),
                                    (t.lastCopiedElement = a);
                             });
                         },
                    },
                ]),
                e
            );
        })(),
        c = (function () {
            function e(t) {
                n(this, e), (this.storageKey = t);
            }
            return (
                i(e, [
                    {
                        key: "fetch",
                        value: async function (e) {
                            try {
                                const text = await navigator.clipboard.readText();
                                try {
                                    const a=JSON.parse(text);
                                    if(this.storageKey !== a.widgetKey){
                                        console.log('It\'s not a valid Timeline Widget data')
                                    }else{
                                        if('timeline-widget-addon' !== a.widgetCode.widgetType){
                                            console.log('It\'s not a valid Timeline Widget data')
                                        }else{
                                            o = new s(a.widgetType, a.widgetCode);
                                            e(o); 
                                        }
                                    }
                                } catch (err) {
                                    console.log('It\'s not a valid widget data');
                                }
                            } catch (err) {
                                console.log('Failed to read clipboard contents:', err);
                            }
                        },
                    },
                ]),
                e
            );
        })();

var p = elementor.hooks.addFilter,
    m = new c(twaepastejs.storageKey),
    g = new l(m);
    p("elements/widget/contextMenuGroups", function (e, t) {
        return a.prepare(e, t, g, "Widget");
    }),
        p("elements/section/contextMenuGroups", function (e, t) {
            return a.prepare(e, t, g, "Section");
        }),
        p("elements/column/contextMenuGroups", function (e, t) {
            return a.prepare(e, t, g, "Column");
        }),
        p("elements/container/contextMenuGroups", function (e, t) {
            return a.prepare(e, t, g, "Container");
        });
       
})();
