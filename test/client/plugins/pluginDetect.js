/*
 * PluginDetect v0.9.0 from www.pinlady.net/PluginDetect/license/
 * with additions and changes by Martynenko S.V. (Bazis Ltd., Kiev).
*/
(function () {
    var j = {
        version : "0.9.0", name : "PluginDetect", addPlugin : function (p, q) {
            if (p && j.isString(p) && q && j.isFunc(q.getVersion)) {
                p = p.replace(/\s/g, "").toLowerCase();
                j.Plugins[p] = q;
                if (!j.isDefined(q.getVersionDone)) {
                    q.installed = null;
                    q.version = null;
                    q.version0 = null;
                    q.getVersionDone = null;
                    q.pluginName = p;
                }
            }
        },
        uniqueName : function () {
            return j.name + "998"
        },
        openTag : "<", hasOwnPROP : ( {
        }).constructor.prototype.hasOwnProperty, hasOwn : function (s, t) {
            var p;
            try {
                p = j.hasOwnPROP.call(s, t)
            }
            catch (q) {
            }
            return !!p
        },
        rgx :  {
            str : /string/i, num : /number/i, fun : /function/i, arr : /array/i
        },
        toString : ( {
        }).constructor.prototype.toString, isDefined : function (p) {
            return typeof p != "undefined"
        },
        isArray : function (p) {
            return j.rgx.arr.test(j.toString.call(p))
        },
        isString : function (p) {
            return j.rgx.str.test(j.toString.call(p))
        },
        isNum : function (p) {
            return j.rgx.num.test(j.toString.call(p))
        },
        isStrNum : function (p) {
            return j.isString(p) && (/\d/).test(p)
        },
        isFunc : function (p) {
            return j.rgx.fun.test(j.toString.call(p))
        },
        getNumRegx : /[\d][\d\.\_,\-]*/, splitNumRegx : /[\.\_,\-]/g, getNum : function (q, r) {
            var p = j.isStrNum(q) ? (j.isDefined(r) ? new RegExp(r) : j.getNumRegx).exec(q) : null;
            return p ? p[0] : null
        },
        compareNums : function (w, u, t) {
            var s, r, q, v = parseInt;
            if (j.isStrNum(w) && j.isStrNum(u)) {
                if (j.isDefined(t) && t.compareNums) {
                    return t.compareNums(w, u)
                }
                s = w.split(j.splitNumRegx);
                r = u.split(j.splitNumRegx);
                for (q = 0;q < Math.min(s.length, r.length);q++) {
                    if (v(s[q], 10) > v(r[q], 10)) {
                        return 1
                    }
                    if (v(s[q], 10) < v(r[q], 10)) {
                        return  - 1
                    }
                }
            }
            return 0
        },
        formatNum : function (q, r) {
            var p, s;
            if (!j.isStrNum(q)) {
                return null
            }
            if (!j.isNum(r)) {
                r = 4
            }
            r--;
            s = q.replace(/\s/g, "").split(j.splitNumRegx).concat(["0", "0", "0", "0"]);
            for (p = 0;p < 4;p++) {
                if (/^(0+)(.+)$/.test(s[p])) {
                    s[p] = RegExp.$2
                }
                if (p > r || !(/\d/).test(s[p])) {
                    s[p] = "0"
                }
            }
            return s.slice(0, 4).join(",")
        },
        pd :  {
            getPROP : function (s, q, p) {
                try {
                    if (s) {
                        p = s[q]
                    }
                }
                catch (r) {
                }
                return p
            },
            findNavPlugin : function (u) {
                if (u.dbug) {
                    return u.dbug
                }
                var A = null;
                if (window.navigator) {
                    var z = {
                        Find : j.isString(u.find) ? new RegExp(u.find, "i") : u.find, Find2 : j.isString(u.find2) ? new RegExp(u.find2, "i") : u.find2, Avoid : u.avoid ? (j.isString(u.avoid) ? new RegExp(u.avoid, "i") : u.avoid) : 0, Num : u.num ? /\d/ : 0
                    },
                    s, r, t, y, x, q, p = navigator.mimeTypes, w = navigator.plugins;
                    if (u.mimes && p) {
                        y = j.isArray(u.mimes) ? [].concat(u.mimes) : (j.isString(u.mimes) ? [u.mimes] : []);
                        for (s = 0;s < y.length;s++) {
                            r = 0;
                            try {
                                if (j.isString(y[s]) && /[^\s]/.test(y[s])) {
                                    r = p[y[s]].enabledPlugin
                                }
                            }
                            catch (v) {
                            }
                            if (r) {
                                t = this.findNavPlugin_(r, z);
                                if (t.obj) {
                                    A = t.obj
                                }
                                if (A && !j.dbug) {
                                    return A
                                }
                            }
                        }
                    }
                    if (u.plugins && w) {
                        x = j.isArray(u.plugins) ? [].concat(u.plugins) : (j.isString(u.plugins) ? [u.plugins] : []);
                        for (s = 0;s < x.length;s++) {
                            r = 0;
                            try {
                                if (x[s] && j.isString(x[s])) {
                                    r = w[x[s]]
                                }
                            }
                            catch (v) {
                            }
                            if (r) {
                                t = this.findNavPlugin_(r, z);
                                if (t.obj) {
                                    A = t.obj
                                }
                                if (A && !j.dbug) {
                                    return A
                                }
                            }
                        }
                        q = w.length;
                        if (j.isNum(q)) {
                            for (s = 0;s < q;s++) {
                                r = 0;
                                try {
                                    r = w[s]
                                }
                                catch (v) {
                                }
                                if (r) {
                                    t = this.findNavPlugin_(r, z);
                                    if (t.obj) {
                                        A = t.obj
                                    }
                                    if (A && !j.dbug) {
                                        return A
                                    }
                                }
                            }
                        }
                    }
                }
                return A
            },
            findNavPlugin_ : function (t, s) {
                var r = t.description || "", q = t.name || "", p = {
                };
                if ((s.Find.test(r) && (!s.Find2 || s.Find2.test(q)) && (!s.Num || s.Num.test(RegExp.leftContext + RegExp.rightContext))) || (s.Find.test(q) && (!s.Find2 || s.Find2.test(r)) && (!s.Num || s.Num.test(RegExp.leftContext + RegExp.rightContext)))) {
                    if (!s.Avoid || !(s.Avoid.test(r) || s.Avoid.test(q))) {
                        p.obj = t
                    }
                }
                return p
            },
            getVersionDelimiter : ",", findPlugin : function (r) {
                var q, p = {
                    status :  - 3, plugin : 0
                };
                if (!j.isString(r)) {
                    return p
                }
                if (r.length == 1) {
                    this.getVersionDelimiter = r;
                    return p
                }
                r = r.toLowerCase().replace(/\s/g, "");
                q = j.Plugins[r];
                if (!q || !q.getVersion) {
                    return p
                }
                p.plugin = q;
                p.status = 1;
                return p
            }
        },
        AXO : (function () {
            var q;
            try {
                q = new window.ActiveXObject()
            }
            catch (p) {
            }
            return q ? null : window.ActiveXObject
        })(), getAXO : function (p) {
            var r = null;
            try {
                r = new j.AXO(p)
            }
            catch (q) {
                j.errObj = q;
            }
            if (r) {
                j.browser.ActiveXEnabled = !0
            }
            return r
        },
        browser :  {
            detectPlatform : function () {
                var r = this, q, p = window.navigator ? navigator.platform || "" : "";
                j.OS = 100;
                if (p) {
                    var s = ["Win", 1, "Mac", 2, "Linux", 3, "FreeBSD", 4, "iPhone", 21.1, "iPod", 21.2, "iPad", 21.3, "Win.*CE", 22.1, "Win.*Mobile", 22.2, "Pocket\\s*PC", 22.3, "", 100];
                    for (q = s.length - 2;q >= 0;q = q - 2) {
                        if (s[q] && new RegExp(s[q], "i").test(p)) {
                            j.OS = s[q];
                            break 
                        }
                    }
                }
            },
            detectIE : function () {
                var r = this, u = document, t, q, v = window.navigator ? navigator.userAgent || "" : "", w, p, y;
                r.ActiveXFilteringEnabled = !1;
                r.ActiveXEnabled = !1;
                try {
                    r.ActiveXFilteringEnabled = !!window.external.msActiveXFilteringEnabled()
                }
                catch (s) {
                }
                p = ["Msxml2.XMLHTTP", "Msxml2.DOMDocument", "Microsoft.XMLDOM", "TDCCtl.TDCCtl", "Shell.UIHelper", "HtmlDlgSafeHelper.HtmlDlgSafeHelper", "Scripting.Dictionary"];
                y = ["WMPlayer.OCX", "ShockwaveFlash.ShockwaveFlash", "AgControl.AgControl"];
                w = p.concat(y);
                for (t = 0;t < w.length;t++) {
                    if (j.getAXO(w[t]) && !j.dbug) {
                        break 
                    }
                }
                if (r.ActiveXEnabled && r.ActiveXFilteringEnabled) {
                    for (t = 0;t < y.length;t++) {
                        if (j.getAXO(y[t])) {
                            r.ActiveXFilteringEnabled = !1;
                            break 
                        }
                    }
                }
                q = u.documentMode;
                try {
                    u.documentMode = ""
                }
                catch (s) {
                }
                r.isIE = r.ActiveXEnabled;
                r.isIE = r.isIE || j.isNum(u.documentMode) || new Function("return/*@cc_on!@*/!1")();
                try {
                    u.documentMode = q
                }
                catch (s) {
                }
                r.verIE = null;
                if (r.isIE) {
                    r.verIE = (j.isNum(u.documentMode) && u.documentMode >= 7 ? u.documentMode : 0) || ((/^(?:.*?[^a-zA-Z])??(?:MSIE|rv\s*\:)\s*(\d+\.?\d*)/i).test(v) ? parseFloat(RegExp.$1, 10) : 7)
                }
            },
            detectNonIE : function () {
                var p = this, s = window.navigator ? navigator :  {
                },
                r = s.userAgent || "", t = s.vendor || "", q = s.product || "";

                p.isEdge = (/Edge\s*\/\s*(\d[\d\.]*)/i).test(r);
                p.verEdge = p.isEdge ? j.formatNum(RegExp.$1) : null;

                p.isGecko = (/Gecko/i).test(q) && (/Gecko\s*\/\s*\d/i).test(r);
                p.verGecko = p.isGecko ? j.formatNum((/rv\s*\:\s*([\.\,\d]+)/i).test(r) ? RegExp.$1 : "0.9") : null;

                p.isOpera = (/(OPR\s*\/|Opera\s*\/\s*\d.*\s*Version\s*\/|Opera\s*[\/]?)\s*(\d+[\.,\d]*)/i).test(r);
                p.verOpera = p.isOpera ? j.formatNum(RegExp.$2) : null;

                p.isYandex = !p.isOpera && (/YaBrowser\s*\/\s*(\d[\d\.]*)/i).test(r);
                p.verYandex = p.isYandex ? j.formatNum(RegExp.$1) : null;

                p.isChrome = !p.isEdge && !p.isOpera && !p.verYandex && (/(Chrome|CriOS)\s*\/\s*(\d[\d\.]*)/i).test(r);
                p.verChrome = p.isChrome ? j.formatNum(RegExp.$2) : null;

                p.isSafari = !p.isEdge && !p.isOpera && !p.isChrome && !p.verYandex && (/Safari\s*\/\s*(\d[\d\.]*)/i).test(r);
                p.verSafari = p.isSafari && (/Version\s*\/\s*(\d[\d\.]*)/i).test(r) ? j.formatNum(RegExp.$1) : null;

                p.isFirefox = !p.isOpera && !p.isChrome && !p.verYandex && (/Firefox\s*\/\s*(\d[\d\.]*)/i).test(r);
                p.verFirefox = p.isFirefox ? j.formatNum(RegExp.$1) : null;
            },
            init : function () {
                var p = this;
                p.detectPlatform();
                p.detectIE();
                p.detectNonIE();
            }
        },
        init :  {
            hasRun : 0, library : function () {
                window[j.name] = j;
                var q = this, p = document;
                j.win.init();
                j.head = p.getElementsByTagName("head")[0] || p.getElementsByTagName("body")[0] || p.body || null;
                j.browser.init();
                q.hasRun = 1;
            }
        },
        ev :  {
            addEvent : function (r, q, p) {
                if (r && q && p) {
                    if (r.addEventListener) {
                        r.addEventListener(q, p, false)
                    }
                    else {
                        if (r.attachEvent) {
                            r.attachEvent("on" + q, p)
                        }
                        else {
                            r["on" + q] = this.concatFn(p, r["on" + q])
                        }
                    }
                }
            },
            removeEvent : function (r, q, p) {
                if (r && q && p) {
                    if (r.removeEventListener) {
                        r.removeEventListener(q, p, false)
                    }
                    else {
                        if (r.detachEvent) {
                            r.detachEvent("on" + q, p)
                        }
                    }
                }
            },
            concatFn : function (q, p) {
                return function () {
                    q();
                    if (typeof p == "function") {
                        p()
                    }
                }
            },
            handler : function (t, s, r, q, p) {
                return function () {
                    t(s, r, q, p)
                }
            },
            handlerOnce : function (s, r, q, p) {
                return function () {
                    var u = j.uniqueName();
                    if (!s[u]) {
                        s[u] = 1;
                        s(r, q, p)
                    }
                }
            },
            handlerWait : function (s, u, r, q, p) {
                var t = this;
                return function () {
                    t.setTimeout(t.handler(u, r, q, p), s)
                }
            },
            setTimeout : function (q, p) {
                if (j.win && j.win.unload) {
                    return 
                }
                setTimeout(q, p)
            },
            fPush : function (q, p) {
                if (j.isArray(p) && (j.isFunc(q) || (j.isArray(q) && q.length > 0 && j.isFunc(q[0])))) {
                    p.push(q)
                }
            },
            call0 : function (q) {
                var p = j.isArray(q) ? q.length :  - 1;
                if (p > 0 && j.isFunc(q[0])) {
                    q[0](j, p > 1 ? q[1] : 0, p > 2 ? q[2] : 0, p > 3 ? q[3] : 0)
                }
                else {
                    if (j.isFunc(q)) {
                        q(j)
                    }
                }
            },
            callArray0 : function (p) {
                var q = this, r;
                if (j.isArray(p)) {
                    while (p.length) {
                        r = p[0];
                        p.splice(0, 1);
                        if (j.win && j.win.unload && p !== j.win.unloadHndlrs) {
                        }
                        else {
                            q.call0(r)
                        }
                    }
                }
            },
            call : function (q) {
                var p = this;
                p.call0(q);
                p.ifDetectDoneCallHndlrs()
            },
            callArray : function (p) {
                var q = this;
                q.callArray0(p);
                q.ifDetectDoneCallHndlrs()
            },
            allDoneHndlrs : [], ifDetectDoneCallHndlrs : function () {
                var r = this, p, q;
                if (!r.allDoneHndlrs.length) {
                    return 
                }
                if (j.win) {
                    if (!j.win.loaded || j.win.loadPrvtHndlrs.length || j.win.loadPblcHndlrs.length) {
                        return 
                    }
                }
                if (j.Plugins) {
                    for (p in j.Plugins) {
                        if (j.hasOwn(j.Plugins, p)) {
                            q = j.Plugins[p];
                            if (q && j.isFunc(q.getVersion)) {
                                if (q.OTF == 3 || (q.DoneHndlrs && q.DoneHndlrs.length) || (q.BIHndlrs && q.BIHndlrs.length)) {
                                    return 
                                }
                            }
                        }
                    }
                }
                r.callArray0(r.allDoneHndlrs);
            }
        },
        isMinVersion : function (v, u, r, q) {
            var s = j.pd.findPlugin(v), t, p =  - 1;
            if (s.status < 0) {
                return s.status
            }
            t = s.plugin;
            u = j.formatNum(j.isNum(u) ? u.toString() : (j.isStrNum(u) ? j.getNum(u) : "0"));
            if (t.getVersionDone != 1) {
                t.getVersion(u, r, q);
                if (t.getVersionDone === null) {
                    t.getVersionDone = 1
                }
            }
            if (t.installed !== null) {
                p = t.installed <= 0.5 ? t.installed : (t.installed == 0.7 ? 1 : (t.version === null ? 0 : (j.compareNums(t.version, u, t) >= 0 ? 1 :  - 0.1)))
            }
            return p
        },
        getVersion : function (u, r, q) {
            var s = j.pd.findPlugin(u), t, p;
            if (s.status < 0) {
                return null
            }
            t = s.plugin;
            if (t.getVersionDone != 1) {
                t.getVersion(null, r, q);
                if (t.getVersionDone === null) {
                    t.getVersionDone = 1
                }
            }
            p = (t.version || t.version0);
            p = p ? p.replace(j.splitNumRegx, j.pd.getVersionDelimiter) : p;
            return p
        },
        hasMimeType : function (t) {
            if (t && window.navigator && navigator.mimeTypes) {
                var w, v, q, s, p = navigator.mimeTypes, r = j.isArray(t) ? [].concat(t) : (j.isString(t) ? [t] : []);
                s = r.length;
                for (q = 0;q < s;q++) {
                    w = 0;
                    try {
                        if (j.isString(r[q]) && /[^\s]/.test(r[q])) {
                            w = p[r[q]]
                        }
                    }
                    catch (u) {
                    }
                    v = w ? w.enabledPlugin : 0;
                    if (v && (v.name || v.description)) {
                        return w
                    }
                }
            }
            return null
        },
        getInfo : function (v, r, q) {
            var p = null, t = j.pd.findPlugin(v), u, s;
            if (t.status < 0) {
                return p
            }
            u = t.plugin;
            if (j.isFunc(u.getInfo)) {
                if (u.getVersionDone === null) {
                    s = j.getVersion ? j.getVersion(v, r, q) : j.isMinVersion(v, "0", r, q)
                }
                p = u.getInfo()
            }
            return p
        },
        onDetectionDone : function (u, t, q, p) {
            var r = j.pd.findPlugin(u), v, s;
            if (r.status ==  - 3) {
                return  - 1
            }
            s = r.plugin;
            if (!j.isArray(s.DoneHndlrs)) {
                s.DoneHndlrs = [];
            }
            if (s.getVersionDone != 1) {
                v = j.getVersion ? j.getVersion(u, q, p) : j.isMinVersion(u, "0", q, p)
            }
            if (s.installed !=  - 0.5 && s.installed != 0.5) {
                j.ev.call(t);
                return 1
            }
            j.ev.fPush(t, s.DoneHndlrs);
            return 0
        },
        codebase :  {
            isDisabled : function () {
                if (j.browser.ActiveXEnabled && j.isDefined(j.pd.getPROP(document.createElement("object"), "object"))) {
                    return 0
                }
                return 1
            },
            isMin : function (u, t) {
                var s = this, r, q, p = 0;
                if (!j.isStrNum(t) || s.isDisabled()) {
                    return p
                }
                s.init(u);
                if (!u.L) {
                    u.L = {
                    };
                    for (r = 0;r < u.Lower.length;r++) {
                        if (s.isActiveXObject(u, u.Lower[r])) {
                            u.L = s.convert(u, u.Lower[r]);
                            break 
                        }
                    }
                }
                if (u.L.v) {
                    q = s.convert(u, t, 1);
                    if (q.x >= 0) {
                        p = (u.L.x == q.x ? s.isActiveXObject(u, q.v) : j.compareNums(t, u.L.v) <= 0) ? 1 :  - 1
                    }
                }
                return p
            },
            search : function (v) {
                var B = this, w = v.$$, q = 0, r;
                r = v.searchHasRun || B.isDisabled() ? 1 : 0;
                v.searchHasRun = 1;
                if (r) {
                    return v.version || null
                }
                B.init(v);
                var F, E, D, s = v.DIGITMAX, t, p, C = 99999999, u = [0, 0, 0, 0], G = [0, 0, 0, 0];
                var A = function (y, J) {
                    var H = [].concat(u), I;
                    H[y] = J;
                    I = B.isActiveXObject(v, H.join(","));
                    if (I) {
                        q = 1;
                        u[y] = J
                    }
                    else {
                        G[y] = J
                    }
                    return I
                };
                for (F = 0;F < G.length;F++) {
                    u[F] = Math.floor(v.DIGITMIN[F]) || 0;
                    t = u.join(",");
                    p = u.slice(0, F).concat([C, C, C, C]).slice(0, u.length).join(",");
                    for (D = 0;D < s.length;D++) {
                        if (j.isArray(s[D])) {
                            s[D].push(0);
                            if (s[D][F] > G[F] && j.compareNums(p, v.Lower[D]) >= 0 && j.compareNums(t, v.Upper[D]) < 0) {
                                G[F] = Math.floor(s[D][F])
                            }
                        }
                    }
                    for (E = 0;E < 30;E++) {
                        if (G[F] - u[F] <= 16) {
                            for (D = G[F];D >= u[F] + (F ? 1 : 0);D--) {
                                if (A(F, D)) {
                                    break 
                                }
                            }
                            break 
                        }
                        A(F, Math.round((G[F] + u[F]) / 2))
                    }
                    if (!q) {
                        break 
                    }
                    G[F] = u[F];
                }
                if (q) {
                    v.version = B.convert(v, u.join(",")).v
                }
                return v.version || null
            },
            emptyNode : function (p) {
                try {
                    p.innerHTML = ""
                }
                catch (q) {
                }
            },
            HTML : [], len : 0, onUnload : function (r, q) {
                var p, t = q.HTML, s;
                for (p = 0;p < t.length;p++) {
                    s = t[p];
                    if (s) {
                        t[p] = 0;
                        q.emptyNode(s.span());
                        s.span = 0;
                        s.spanObj = 0;
                        s = 0
                    }
                }
                q.iframe = 0
            },
            init : function (u) {
                var t = this;
                if (!t.iframe) {
                    var s = j.DOM, q;
                    q = s.iframe.insert(0, "$.codebase{ }");
                    t.iframe = q;
                    s.iframe.write(q, " ");
                    s.iframe.close(q);
                }
                if (!u.init) {
                    u.init = 1;
                    var p, r;
                    j.ev.fPush([t.onUnload, t], j.win.unloadHndlrs);
                    u.tagA = '<object width="1" height="1" style="display:none;" codebase="#version=';
                    r = u.classID || u.$$.classID || "";
                    u.tagB = '" ' + ((/clsid\s*:/i).test(r) ? 'classid="' : 'type="') + r + '">' + j.openTag + "/object>";
                    for (p = 0;p < u.Lower.length;p++) {
                        u.Lower[p] = j.formatNum(u.Lower[p]);
                        u.Upper[p] = j.formatNum(u.Upper[p]);
                    }
                }
            },
            isActiveXObject : function (u, q) {
                var t = this, p = 0, s = u.$$, r = (j.DOM.iframe.doc(t.iframe) || document).createElement("span");
                if (u.min && j.compareNums(q, u.min) <= 0) {
                    return 1
                }
                if (u.max && j.compareNums(q, u.max) >= 0) {
                    return 0
                }
                r.innerHTML = u.tagA + q + u.tagB;
                if (j.pd.getPROP(r.firstChild, "object")) {
                    p = 1
                }
                if (p) {
                    u.min = q;
                    t.HTML.push( {
                        spanObj : r, span : t.span
                    })
                }
                else {
                    u.max = q;
                    r.innerHTML = ""
                }
                return p
            },
            span : function () {
                return this.spanObj
            },
            convert_ : function (t, p, q, s) {
                var r = t.convert[p];
                return r ? (j.isFunc(r) ? j.formatNum(r(q.split(j.splitNumRegx), s).join(",")) : q) : r
            },
            convert : function (v, r, u) {
                var t = this, q, p, s;
                r = j.formatNum(r);
                p = {
                    v : r, x :  - 1
                };
                if (r) {
                    for (q = 0;q < v.Lower.length;q++) {
                        s = t.convert_(v, q, v.Lower[q]);
                        if (s && j.compareNums(r, u ? s : v.Lower[q]) >= 0 && (!q || j.compareNums(r, u ? t.convert_(v, q, v.Upper[q]) : v.Upper[q]) < 0)) {
                            p.v = t.convert_(v, q, r, u);
                            p.x = q;
                            break 
                        }
                    }
                }
                return p
            },
            z : 0
        },
        win :  {
            disable : function () {
                this.cancel = true
            },
            cancel : false, loaded : false, unload : false, hasRun : 0, init : function () {
                var p = this;
                if (!p.hasRun) {
                    p.hasRun = 1;
                    if ((/complete/i).test(document.readyState || "")) {
                        p.loaded = true;
                    }
                    else {
                        j.ev.addEvent(window, "load", p.onLoad)
                    }
                    j.ev.addEvent(window, "unload", p.onUnload)
                }
            },
            loadPrvtHndlrs : [], loadPblcHndlrs : [], unloadHndlrs : [], onUnload : function () {
                var p = j.win;
                if (p.unload) {
                    return 
                }
                p.unload = true;
                j.ev.removeEvent(window, "load", p.onLoad);
                j.ev.removeEvent(window, "unload", p.onUnload);
                j.ev.callArray(p.unloadHndlrs)
            },
            onLoad : function () {
                var p = j.win;
                if (p.loaded || p.unload || p.cancel) {
                    return 
                }
                p.loaded = true;
                j.ev.callArray(p.loadPrvtHndlrs);
                j.ev.callArray(p.loadPblcHndlrs);
            }
        },
        DOM :  {
            isEnabled :  {
                objectTag : function () {
                    var q = j.browser, p = q.isIE ? 0 : 1;
                    if (q.ActiveXEnabled) {
                        p = 1
                    }
                    return !!p
                },
                objectTagUsingActiveX : function () {
                    var p = 0;
                    if (j.browser.ActiveXEnabled) {
                        p = 1
                    }
                    return !!p
                },
                objectProperty : function (p) {
                    if (p && p.tagName && j.browser.isIE) {
                        if ((/applet/i).test(p.tagName)) {
                            return (!this.objectTag() || j.isDefined(j.pd.getPROP(document.createElement("object"), "object")) ? 1 : 0)
                        }
                        return j.isDefined(j.pd.getPROP(document.createElement(p.tagName), "object")) ? 1 : 0
                    }
                    return 0
                }
            },
            HTML : [], div : null, divID : "plugindetect", divWidth : 500, getDiv : function () {
                return this.div || document.getElementById(this.divID) || null
            },
            initDiv : function () {
                var q = this, p;
                if (!q.div) {
                    p = q.getDiv();
                    if (p) {
                        q.div = p;
                    }
                    else {
                        q.div = document.createElement("div");
                        q.div.id = q.divID;
                        q.setStyle(q.div, q.getStyle.div());
                        q.insertDivInBody(q.div)
                    }
                    j.ev.fPush([q.onUnload, q], j.win.unloadHndlrs)
                }
                p = 0
            },
            pluginSize : 1, iframeWidth : 40, iframeHeight : 10, altHTML : "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", emptyNode : function (q) {
                var p = this;
                if (q && (/div|span/i).test(q.tagName || "")) {
                    if (j.browser.isIE) {
                        p.setStyle(q, ["display", "none"])
                    }
                    try {
                        q.innerHTML = ""
                    }
                    catch (r) {
                    }
                }
            },
            removeNode : function (p) {
                try {
                    if (p && p.parentNode) {
                        p.parentNode.removeChild(p)
                    }
                }
                catch (q) {
                }
            },
            onUnload : function (u, t) {
                var r, q, s, v, w = t.HTML, p = w.length;
                if (p) {
                    for (q = p - 1;q >= 0;q--) {
                        v = w[q];
                        if (v) {
                            w[q] = 0;
                            t.emptyNode(v.span());
                            t.removeNode(v.span());
                            v.span = 0;
                            v.spanObj = 0;
                            v.doc = 0;
                            v.objectProperty = 0
                        }
                    }
                }
                r = t.getDiv();
                t.emptyNode(r);
                t.removeNode(r);
                v = 0;
                s = 0;
                r = 0;
                t.div = 0
            },
            span : function () {
                var p = this;
                if (!p.spanObj) {
                    p.spanObj = p.doc.getElementById(p.spanId)
                }
                return p.spanObj || null
            },
            width : function () {
                var t = this, s = t.span(), q, r, p =  - 1;
                q = s && j.isNum(s.scrollWidth) ? s.scrollWidth : p;
                r = s && j.isNum(s.offsetWidth) ? s.offsetWidth : p;
                s = 0;
                return r > 0 ? r : (q > 0 ? q : Math.max(r, q))
            },
            obj : function () {
                var p = this.span();
                return p ? p.firstChild || null : null
            },
            readyState : function () {
                var p = this;
                return j.browser.isIE && j.isDefined(j.pd.getPROP(p.span(), "readyState")) ? j.pd.getPROP(p.obj(), "readyState") : j.UNDEFINED
            },
            objectProperty : function () {
                var r = this, q = r.DOM, p;
                if (q.isEnabled.objectProperty(r)) {
                    p = j.pd.getPROP(r.obj(), "object")
                }
                return p
            },
            onLoadHdlr : function (p, q) {
                q.loaded = 1
            },
            getTagStatus : function (q, A, E, D, t, H, v) {
                var F = this;
                if (!q || !q.span()) {
                    return  - 2
                }
                var y = q.width(), r = q.obj() ? 1 : 0, s = q.readyState(), p = q.objectProperty();
                if (p) {
                    return 1.5
                }
                var u = /clsid\s*\:/i, C = E && u.test(E.outerHTML || "") ? E : (D && u.test(D.outerHTML || "") ? D : 0), w = E && !u.test(E.outerHTML || "") ? E : (D && !u.test(D.outerHTML || "") ? D : 0), z = q && u.test(q.outerHTML || "") ? C : w;
                if (!A || !A.span() || !z || !z.span()) {
                    return  - 2
                }
                var x = z.width(), B = A.width(), G = z.readyState();
                if (y < 0 || x < 0 || B <= F.pluginSize) {
                    return 0
                }
                if (v && !q.pi && j.isDefined(p) && j.browser.isIE && q.tagName == z.tagName && q.time <= z.time && y === x && s === 0 && G !== 0) {
                    q.pi = 1
                }
                if (x < B || !q.loaded || !A.loaded || !z.loaded) {
                    return q.pi ?  - 0.1 : 0
                }
                if (y == B || !r) {
                    return q.pi ?  - 0.5 :  - 1
                }
                else {
                    if (y == F.pluginSize && r && (!j.isNum(s) || s === 4)) {
                        return 1
                    }
                }
                return q.pi ?  - 0.5 :  - 1
            },
            setStyle : function (q, t) {
                var s = q.style, p;
                if (s && t) {
                    for (p = 0;p < t.length;p = p + 2) {
                        try {
                            s[t[p]] = t[p + 1]
                        }
                        catch (r) {
                        }
                    }
                }
                q = 0;
                s = 0
            },
            getStyle :  {
                iframe : function () {
                    return this.span()
                },
                span : function (r) {
                    var q = j.DOM, p;
                    p = r ? this.plugin() : ([].concat(this.Default).concat(["display", "inline", "fontSize", (q.pluginSize + 3) + "px", "lineHeight", (q.pluginSize + 3) + "px"]));
                    return p
                },
                div : function () {
                    var p = j.DOM;
                    return [].concat(this.Default).concat(["display", "block", "width", p.divWidth + "px", "height", (p.pluginSize + 3) + "px", "fontSize", (p.pluginSize + 3) + "px", "lineHeight", (p.pluginSize + 3) + "px", "position", "absolute", "right", "9999px", "top", "-9999px"])
                },
                plugin : function (q) {
                    var p = j.DOM;
                    return "background-color:transparent;background-image:none;vertical-align:baseline;outline-style:none;border-style:none;padding:0px;margin:0px;visibility:" + (q ? "hidden;" : "visible;") + "display:inline;font-size:" + (p.pluginSize + 3) + "px;line-height:" + (p.pluginSize + 3) + "px;"
                },
                Default : ["backgroundColor", "transparent", "backgroundImage", "none", "verticalAlign", "baseline", "outlineStyle", "none", "borderStyle", "none", "padding", "0px", "margin", "0px", "visibility", "visible"]
            },
            insertDivInBody : function (v, t) {
                var u = "pd33993399", q = null, s = t ? window.top.document : window.document, p = s.getElementsByTagName("body")[0] || s.body;
                if (!p) {
                    try {
                        s.write('<div id="' + u + '">.' + j.openTag + "/div>");
                        q = s.getElementById(u)
                    }
                    catch (r) {
                    }
                }
                p = s.getElementsByTagName("body")[0] || s.body;
                if (p) {
                    p.insertBefore(v, p.firstChild);
                    if (q) {
                        p.removeChild(q)
                    }
                }
                v = 0
            },
            iframe :  {
                onLoad : function (p, q) {
                    j.ev.callArray(p);
                },
                insert : function (r, q) {
                    var s = this, v = j.DOM, p, u = document.createElement("iframe"), t;
                    v.setStyle(u, v.getStyle.iframe());
                    u.width = v.iframeWidth;
                    u.height = v.iframeHeight;
                    v.initDiv();
                    p = v.getDiv();
                    p.appendChild(u);
                    try {
                        s.doc(u).open()
                    }
                    catch (w) {
                    }
                    u[j.uniqueName()] = [];
                    t = j.ev.handlerOnce(j.isNum(r) && r > 0 ? j.ev.handlerWait(r, s.onLoad, u[j.uniqueName()], q) : j.ev.handler(s.onLoad, u[j.uniqueName()], q));
                    j.ev.addEvent(u, "load", t);
                    if (!u.onload) {
                        u.onload = t
                    }
                    j.ev.addEvent(s.win(u), "load", t);
                    return u
                },
                addHandler : function (q, p) {
                    if (q) {
                        j.ev.fPush(p, q[j.uniqueName()])
                    }
                },
                close : function (p) {
                    try {
                        this.doc(p).close()
                    }
                    catch (q) {
                    }
                },
                write : function (p, r) {
                    try {
                        this.doc(p).write(r)
                    }
                    catch (q) {
                    }
                },
                win : function (p) {
                    try {
                        return p.contentWindow
                    }
                    catch (q) {
                    }
                    return null
                },
                doc : function (p) {
                    var r;
                    try {
                        r = p.contentWindow.document
                    }
                    catch (q) {
                    }
                    try {
                        if (!r) {
                            r = p.contentDocument
                        }
                    }
                    catch (q) {
                    }
                    return r || null
                }
            },
            insert : function (t, s, u, p, y, w, v) {
                var D = this, F, E, C, B, A;
                if (!v) {
                    D.initDiv();
                    v = D.getDiv()
                }
                if (v) {
                    if ((/div/i).test(v.tagName)) {
                        B = v.ownerDocument
                    }
                    if ((/iframe/i).test(v.tagName)) {
                        B = D.iframe.doc(v)
                    }
                }
                if (B && B.createElement) {
                }
                else {
                    B = document
                }
                if (!j.isDefined(p)) {
                    p = ""
                }
                if (j.isString(t) && (/[^\s]/).test(t)) {
                    t = t.toLowerCase().replace(/\s/g, "");
                    F = j.openTag + t + " ";
                    F += 'style="' + D.getStyle.plugin(w) + '" ';
                    var r = 1, q = 1;
                    for (A = 0;A < s.length;A = A + 2) {
                        if (/[^\s]/.test(s[A + 1])) {
                            F += s[A] + '="' + s[A + 1] + '" '
                        }
                        if ((/width/i).test(s[A])) {
                            r = 0
                        }
                        if ((/height/i).test(s[A])) {
                            q = 0
                        }
                    }
                    F += (r ? 'width="' + D.pluginSize + '" ' : "") + (q ? 'height="' + D.pluginSize + '" ' : "");
                    if (t == "embed" || t == "img") {
                        F += " />"
                    }
                    else {
                        F += ">";
                        for (A = 0;A < u.length;A = A + 2) {
                            if (/[^\s]/.test(u[A + 1])) {
                                F += j.openTag + 'param name="' + u[A] + '" value="' + u[A + 1] + '" />'
                            }
                        }
                        F += p + j.openTag + "/" + t + ">"
                    }
                }
                else {
                    t = "";
                    F = p
                }
                E = {
                    spanId : "", spanObj : null, span : D.span, loaded : null, tagName : t, outerHTML : F, DOM : D, time : new Date().getTime(), width : D.width, obj : D.obj, readyState : D.readyState, objectProperty : D.objectProperty, doc : B
                };
                if (v && v.parentNode) {
                    if ((/iframe/i).test(v.tagName)) {
                        D.iframe.addHandler(v, [D.onLoadHdlr, E]);
                        E.loaded = 0;
                        E.spanId = j.name + "Span" + D.HTML.length;
                        C = '<span id="' + E.spanId + '" style="' + D.getStyle.span(1) + '">' + F + "</span>";
                        D.iframe.write(v, C)
                    }
                    else {
                        if ((/div/i).test(v.tagName)) {
                            C = B.createElement("span");
                            D.setStyle(C, D.getStyle.span());
                            v.appendChild(C);
                            try {
                                C.innerHTML = F
                            }
                            catch (z) {
                            }
                            E.spanObj = C
                        }
                    }
                }
                C = 0;
                v = 0;
                D.HTML.push(E);
                return E
            }
        },
        file :  {
            any : "fileStorageAny999", valid : "fileStorageValid999", save : function (s, t, r) {
                var q = this, p;
                if (s && j.isDefined(r)) {
                    if (!s[q.any]) {
                        s[q.any] = []
                    }
                    if (!s[q.valid]) {
                        s[q.valid] = []
                    }
                    s[q.any].push(r);
                    p = q.split(t, r);
                    if (p) {
                        s[q.valid].push(p)
                    }
                }
            },
            getValidLength : function (p) {
                return p && p[this.valid] ? p[this.valid].length : 0
            },
            getAnyLength : function (p) {
                return p && p[this.any] ? p[this.any].length : 0
            },
            getValid : function (r, p) {
                var q = this;
                return r && r[q.valid] ? q.get(r[q.valid], p) : null
            },
            getAny : function (r, p) {
                var q = this;
                return r && r[q.any] ? q.get(r[q.any], p) : null
            },
            get : function (s, p) {
                var r = s.length - 1, q = j.isNum(p) ? p : r;
                return (q < 0 || q > r) ? null : s[q]
            },
            split : function (t, q) {
                var s = null, p, r;
                t = t ? t.replace(".", "\\.") : "";
                r = new RegExp("^(.*[^\\/])(" + t + "\\s*)$");
                if (j.isString(q) && r.test(q)) {
                    p = (RegExp.$1).split("/");
                    s = {
                        name : p[p.length - 1], ext : RegExp.$2, full : q
                    };
                    p[p.length - 1] = "";
                    s.path = p.join("/")
                }
                return s
            }
        },
        Plugins :  {
        }
    };
    j.init.library();
    var a = {
        mimeType : ["application/x-java-applet", "application/x-java-vm", "application/x-java-bean"], mimeType_dummy : "application/dummymimejavaapplet", classID : "clsid:8AD9C840-044E-11D1-B3E9-00805F499D93", classID_dummy : "clsid:8AD9C840-044E-11D1-B3E9-BA9876543210", navigator :  {
            init : function () {
                var q = this, p = a;
                q.mimeObj = j.hasMimeType(p.mimeType);
                if (q.mimeObj) {
                    q.pluginObj = q.mimeObj.enabledPlugin
                }
            },
            a : (function () {
                try {
                    return window.navigator.javaEnabled()
                }
                catch (p) {
                }
                return 1
            })(), javaEnabled : function () {
                return !!this.a
            },
            mimeObj : 0, pluginObj : 0
        },
        OTF : null, info :  {
            pluginObj : null, getPluginObj : function () {
                var p = this;
                if (p.pluginObj === null) {
                    p.pluginObj = a.navMime.pluginObj || a.navigator.pluginObj || 0
                }
                return p.pluginObj
            },
            getNavPluginName : function () {
                var p = this.getPluginObj();
                return p ? p.name || "" : ""
            },
            getNavPluginDescription : function () {
                var p = this.getPluginObj();
                return p ? p.description || "" : ""
            },
            Plugin2Status : 0, setPlugin2Status : function (p) {
                if (j.isNum(p)) {
                    this.Plugin2Status = p
                }
            },
            getPlugin2Status : function () {
                var u = this, r, s, p, t, q;
                if (u.Plugin2Status === 0) {
                    s = /Next.*Generation.*Java.*Plug-?in|Java.*Plug-?in\s*2\s/i;
                    p = /Classic.*Java.*Plug-?in/i;
                    t = u.getNavPluginDescription();
                    q = u.getNavPluginName();
                    if (s.test(t) || s.test(q)) {
                        u.setPlugin2Status(1);
                    }
                    else {
                        if (p.test(t) || p.test(q)) {
                            u.setPlugin2Status( - 1);
                        }
                        else {
                            if (j.browser.isIE && (/Sun|Oracle/i).test(u.getVendor())) {
                                r = u.isMinJre4Plugin2();
                                if (r > 0) {
                                    u.setPlugin2Status(1);
                                }
                                else {
                                    if (r < 0) {
                                        u.setPlugin2Status( - 1);
                                    }
                                }
                            }
                        }
                    }
                }
                return u.Plugin2Status
            },
            isMinJre4Plugin2 : function (p) {
                var r = a, s = "", t, q = r.applet.getResult()[0];
                if (j.OS == 1) {
                    s = "1,6,0,10"
                }
                else {
                    if (j.OS == 2) {
                        s = "1,6,0,12"
                    }
                    else {
                        if (j.OS == 3) {
                            s = "1,6,0,10"
                        }
                        else {
                            s = "1,6,0,10"
                        }
                    }
                }
                if (!p) {
                    p = (q && !r.applet.isRange(q) ? q : 0) || r.version;
                    t = r.applet.codebase;
                    p = p || (t.min && s ? (t.isMin(s) > 0 ? s : "0,0,0,0") : 0);
                }
                p = j.formatNum(j.getNum(p));
                return p ? (j.compareNums(p, s) >= 0 ? 1 :  - 1) : 0
            },
            BrowserForbidsPlugin2 : function () {
                var p = j.browser;
                if (j.OS >= 20) {
                    return 0
                }
                if ((p.isGecko && j.compareNums(p.verGecko, "1,9,0,0") < 0) || (p.isOpera && j.compareNums(p.verOpera, "10,50,0,0") < 0)) {
                    return 1
                }
                return 0
            },
            BrowserRequiresPlugin2 : function () {
                var p = j.browser;
                if (j.OS >= 20) {
                    return 0
                }
                if ((p.isGecko && j.compareNums(p.verGecko, "1,9,2,0") >= 0) || p.isYandex || p.isChrome || (j.OS == 1 && p.isOpera && j.compareNums(p.verOpera, "10,60,0,0") >= 0)) {
                    return 1
                }
                return 0
            },
            VENDORS : ["Sun Microsystems Inc.", "Apple Computer, Inc.", "Oracle Corporation", "IBM Corporation"], VENDORS_reg : [/Sun/i, /Apple/i, /Oracle/i, /IBM/i], getNavVendor : function () {
                var t = this, q, r = t.getNavPluginName(), s = t.getNavPluginDescription(), p = "";
                if (r || s) {
                    for (q = 0;q < t.VENDORS.length;q++) {
                        if (t.VENDORS_reg[q].test(r) || t.VENDORS_reg[q].test(s)) {
                            p = t.VENDORS[q];
                            break 
                        }
                    }
                }
                return p
            },
            OracleMin : "1,7,0,0", OracleOrSun : function (p) {
                var q = this;
                return q.VENDORS[j.compareNums(j.formatNum(p), q.OracleMin) < 0 ? 0 : 2]
            },
            OracleOrApple : function (p) {
                var q = this;
                return q.VENDORS[j.compareNums(j.formatNum(p), q.OracleMin) < 0 ? 1 : 2]
            },
            getVendor : function () {
                var r = this, q = a, t = q.vendor || q.applet.getResult()[1] || "", s, p;
                if (!t) {
                    p = q.DTK.version;
                    s = q.applet.codebase;
                    p = p || s.version || (s.min ? (s.isMin(r.OracleMin) > 0 ? r.OracleMin : "0,0,0,0") : 0);
                    if (p) {
                        t = r.OracleOrSun(p)
                    }
                    else {
                        t = r.getNavVendor() || "";
                        if (t) {
                        }
                        else {
                            if (q.version) {
                                if (j.OS == 2) {
                                    t = r.OracleOrApple(q.version)
                                }
                                else {
                                    if (j.OS == 1 || j.OS == 3) {
                                        t = r.OracleOrSun(q.version)
                                    }
                                }
                            }
                        }
                    }
                }
                return t
            },
            isPlugin2InstalledEnabled : function () {
                var u = this, t = a, p =  - 1, s = t.installed, w = u.getPlugin2Status(), v = u.BrowserRequiresPlugin2(), r = u.BrowserForbidsPlugin2(), q = u.isMinJre4Plugin2();
                if (s !== null && s >=  - 0.1) {
                    if (w >= 3) {
                        p = 1
                    }
                    else {
                        if (w <=  - 3) {
                        }
                        else {
                            if (w == 2) {
                                p = 1
                            }
                            else {
                                if (w ==  - 2) {
                                }
                                else {
                                    if (v && w >= 0 && q > 0) {
                                        p = 1
                                    }
                                    else {
                                        if (r && w <= 0 && q < 0) {
                                        }
                                        else {
                                            if (v) {
                                                p = 1
                                            }
                                            else {
                                                if (r) {
                                                }
                                                else {
                                                    if (w > 0) {
                                                        p = 1
                                                    }
                                                    else {
                                                        if (w < 0) {
                                                        }
                                                        else {
                                                            if (q < 0) {
                                                            }
                                                            else {
                                                                p = 0
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                return p
            },
            result :  {
                getDeploymentToolkitObj : function () {
                    var p = a, r = p.info, q = p.DTK;
                    q.query(1);
                    r.updateResult();
                    return q.status && q.HTML ? q.HTML.obj() : q.status
                }
            },
            updateResult : function () {
                var r = this, q = a, p = q.applet, w, y = q.installed, v = q.DTK, u = p.results, z = r.result;
                z.DeployTK_versions = [].concat(j.isArray(v.VERSIONS) ? v.VERSIONS : []);
                z.vendor = r.getVendor();
                z.isPlugin2 = r.isPlugin2InstalledEnabled();
                z.OTF = q.OTF < 3 ? 0 : (q.OTF == 3 ? 1 : 2);
                z.JavaAppletObj = null;
                for (w = 0;w < u.length;w++) {
                    if (u[w][0] && p.HTML[w] && p.HTML[w].obj()) {
                        z.JavaAppletObj = p.HTML[w].obj();
                        break 
                    }
                }
                var t = [null, null, null, null];
                for (w = 0;w < u.length;w++) {
                    if (u[w][0]) {
                        t[w] = 1
                    }
                    else {
                        if (u[w][0] !== null) {
                            if (q.NOTF) {
                                q.NOTF.isAppletActive(w)
                            }
                            if (p.active[w] > 0) {
                                t[w] = 0
                            }
                            else {
                                if (p.allowed[w] >= 1 && q.OTF != 3 && (p.isDisabled.single(w) || y ==  - 0.2 || y ==  - 1 || p.active[w] < 0 || (w == 3 && (/Microsoft/i).test(z.vendor)))) {
                                    t[w] =  - 1
                                }
                            }
                        }
                        else {
                            if (w == 3 && u[0][0]) {
                                t[w] = 0
                            }
                            else {
                                if (p.isDisabled.single(w)) {
                                    t[w] =  - 1
                                }
                            }
                        }
                    }
                }
                z.objectTag = t[1];
                z.appletTag = t[2];
                z.objectTagActiveX = t[3];
                z.name = r.getNavPluginName();
                z.description = r.getNavPluginDescription();
                z.All_versions = [].concat((z.DeployTK_versions.length ? z.DeployTK_versions : (j.isString(q.version) ? [q.version] : [])));
                var s = z.All_versions;
                for (w = 0;w < s.length;w++) {
                    s[w] = j.formatNum(j.getNum(s[w]))
                }
                return z
            }
        },
        getInfo : function () {
            var p = this.info;
            p.updateResult();
            return p.result
        },
        getVerifyTagsDefault : function () {
            return [1, this.applet.isDisabled.VerifyTagsDefault_1() ? 0 : 1, 1]
        },
        getVersion : function (x, u, w) {
            var q = this, s, p = q.applet, v = q.verify, y = q.navigator, t = null, z = null, r = null;
            if (q.getVersionDone === null) {
                q.OTF = 0;
                y.init();
                if (v) {
                    v.init()
                }
            }
            p.setVerifyTagsArray(w);
            j.file.save(q, ".jar", u);
            if (q.getVersionDone === 0) {
                if (p.should_Insert_Query_Any()) {
                    s = p.insert_Query_Any(x);
                    q.setPluginStatus(s[0], s[1], t, x)
                }
                return 
            }
            if ((!t || j.dbug) && q.navMime.query().version) {
                t = q.navMime.version
            }
            if ((!t || j.dbug) && q.navPlugin.query().version) {
                t = q.navPlugin.version
            }
            if ((!t || j.dbug) && q.DTK.query().version) {
                t = q.DTK.version
            }
            if (q.nonAppletDetectionOk(t)) {
                r = t
            }
            q.setPluginStatus(r, z, t, x);
            if (p.should_Insert_Query_Any()) {
                s = p.insert_Query_Any(x);
                if (s[0]) {
                    r = s[0];
                    z = s[1]
                }
            }
            q.setPluginStatus(r, z, t, x)
        },
        nonAppletDetectionOk : function (q) {
            var t = this, p = t.navigator, r = j.browser, s = 1;
            if (!q || !p.javaEnabled() || (!r.isIE && !p.mimeObj)) {
                s = 0
            }
            return s
        },
        setPluginStatus : function (v, w, p, u) {
            var t = this, s, q = 0, r = t.applet;
            p = p || t.version0;
            s = r.isRange(v);
            if (s) {
                if (r.setRange(s, u) == v) {
                    q = s
                }
                v = 0
            }
            if (t.OTF < 3) {
                t.installed = q ? (q > 0 ? 0.7 :  - 0.1) : (v ? 1 : (p ?  - 0.2 :  - 1))
            }
            if (t.OTF == 2 && t.NOTF && !t.applet.getResult()[0]) {
                t.installed = p ?  - 0.2 :  - 1
            }
            if (t.OTF == 3 && t.installed !=  - 0.5 && t.installed != 0.5) {
                t.installed = (t.NOTF.isJavaActive(1) >= 1 ? 0.5 :  - 0.5)
            }
            if (t.OTF == 4 && (t.installed ==  - 0.5 || t.installed == 0.5)) {
                if (v) {
                    t.installed = 1
                }
                else {
                    if (q) {
                        t.installed = q > 0 ? 0.7 :  - 0.1
                    }
                    else {
                        if (t.NOTF.isJavaActive(1) >= 1) {
                            if (p) {
                                t.installed = 1;
                                v = p
                            }
                            else {
                                t.installed = 0
                            }
                        }
                        else {
                            if (p) {
                                t.installed =  - 0.2
                            }
                            else {
                                t.installed =  - 1
                            }
                        }
                    }
                }
            }
            if (p) {
                t.version0 = j.formatNum(j.getNum(p))
            }
            if (v && !q) {
                t.version = j.formatNum(j.getNum(v))
            }
            if (w && j.isString(w)) {
                t.vendor = w
            }
            if (!t.vendor) {
                t.vendor = ""
            }
            if (t.verify && t.verify.isEnabled()) {
                t.getVersionDone = 0
            }
            else {
                if (t.getVersionDone != 1) {
                    if (t.OTF < 2) {
                        t.getVersionDone = 0
                    }
                    else {
                        t.getVersionDone = t.applet.can_Insert_Query_Any() ? 0 : 1
                    }
                }
            }
        },
        DTK :  {
            hasRun : 0, status : null, VERSIONS : [], version : "", HTML : null, Plugin2Status : null, classID : ["clsid:CAFEEFAC-DEC7-0000-0001-ABCDEFFEDCBA", "clsid:CAFEEFAC-DEC7-0000-0000-ABCDEFFEDCBA"], mimeType : ["application/java-deployment-toolkit", "application/npruntime-scriptable-plugin;DeploymentToolkit"], isDisabled : function (p) {
                var q = this;
                if (q.HTML) {
                    return 1
                }
                if (p || j.dbug) {
                    return 0
                }
                if (q.hasRun || !j.DOM.isEnabled.objectTagUsingActiveX()) {
                    return 1
                }
                return 0
            },
            query : function (B) {
                var z = this, t = a, A, v, p = j.DOM.altHTML, u = {
                },
                q, s = null, w = null, r = z.isDisabled(B);
                z.hasRun = 1;
                if (r) {
                    return z
                }
                z.status = 0;
                if (j.DOM.isEnabled.objectTagUsingActiveX()) {
                    for (A = 0;A < z.classID.length;A++) {
                        z.HTML = j.DOM.insert("object", ["classid", z.classID[A]], [], p);
                        s = z.HTML.obj();
                        if (j.pd.getPROP(s, "jvms")) {
                            break 
                        }
                    }
                }
                else {
                    v = j.hasMimeType(z.mimeType);
                    if (v && v.type) {
                        z.HTML = j.DOM.insert("object", ["type", v.type], [], p);
                        s = z.HTML.obj()
                    }
                }
                if (s) {
                    try {
                        if (Math.abs(t.info.getPlugin2Status()) < 2) {
                            z.Plugin2Status = s.isPlugin2()
                        }
                    }
                    catch (y) {
                    }
                    if (z.Plugin2Status !== null) {
                        if (z.Plugin2Status) {
                            t.info.setPlugin2Status(2)
                        }
                        else {
                            if (j.DOM.isEnabled.objectTagUsingActiveX() || t.info.getPlugin2Status() <= 0) {
                                t.info.setPlugin2Status( - 2)
                            }
                        }
                    }
                    try {
                        q = j.pd.getPROP(s, "jvms");
                        if (q) {
                            w = q.getLength();
                            if (j.isNum(w)) {
                                z.status = w > 0 ? 1 :  - 1;
                                for (A = 0;A < w;A++) {
                                    v = j.getNum(q.get(w - 1 - A).version);
                                    if (v) {
                                        z.VERSIONS.push(v);
                                        u["a" + j.formatNum(v)] = 1
                                    }
                                }
                            }
                        }
                    }
                    catch (y) {
                    }
                    if (z.VERSIONS.length) {
                        z.version = j.formatNum(z.VERSIONS[0])
                    }
                }
                return z
            }
        },
        navMime :  {
            hasRun : 0, mimetype : "", version : "", mimeObj : 0, pluginObj : 0, regexJPI : /^\s*application\/x-java-applet;jpi-version\s*=\s*(\d.*)$/i, isDisabled : function () {
                var p = this, q = a;
                if (p.hasRun || !q.navigator.mimeObj) {
                    return 1
                }
                return 0
            },
            update : function (s) {
                var p = this, r = s ? s.enabledPlugin : 0, q = s && p.regexJPI.test(s.type || "") ? j.formatNum(j.getNum(RegExp.$1)) : 0;
                if (q && r && (r.description || r.name)) {
                    if (j.compareNums(q, p.version || j.formatNum("0")) > 0) {
                        p.version = q;
                        p.mimeObj = s;
                        p.pluginObj = r;
                        p.mimetype = s.type;
                    }
                }
            },
            query : function () {
                var t = this, s = a, w, v, B, A, z, r, q = navigator.mimeTypes, p = t.isDisabled();
                t.hasRun = 1;
                if (p) {
                    return t
                }
                r = q.length;
                if (j.isNum(r)) {
                    for (w = 0;w < r;w++) {
                        B = 0;
                        try {
                            B = q[w]
                        }
                        catch (u) {
                        }
                        t.update(B)
                    }
                }
                if (!t.version || j.dbug) {
                    z = j.isArray(s.mimeType) ? s.mimeType : [s.mimeType];
                    for (w = 0;w < z.length;w++) {
                        B = 0;
                        try {
                            B = q[z[w]]
                        }
                        catch (u) {
                        }
                        A = B ? B.enabledPlugin : 0;
                        r = A ? A.length : null;
                        if (j.isNum(r)) {
                            for (v = 0;v < r;v++) {
                                B = 0;
                                try {
                                    B = A[v]
                                }
                                catch (u) {
                                }
                                t.update(B)
                            }
                        }
                    }
                }
                return t
            }
        },
        navPlugin :  {
            hasRun : 0, version : "", getPlatformNum : function () {
                var q = a, p = 0, r = /Java.*TM.*Platform[^\d]*(\d+)[\.,_]?(\d*)\s*U?(?:pdate)?\s*(\d*)/i, s = j.pd.findNavPlugin( {
                    find : r, mimes : q.mimeType, plugins : 1
                });
                if (s && (r.test(s.name || "") || r.test(s.description || "")) && parseInt(RegExp.$1, 10) >= 5) {
                    p = "1," + RegExp.$1 + "," + (RegExp.$2 ? RegExp.$2 : "0") + "," + (RegExp.$3 ? RegExp.$3 : "0");
                }
                return p
            },
            getPluginNum : function () {
                var s = this, q = a, p = 0, u, t, r, w, v = 0;
                r = /Java[^\d]*Plug-in/i;
                w = j.pd.findNavPlugin( {
                    find : r, num : 1, mimes : q.mimeType, plugins : 1, dbug : v
                });
                if (w) {
                    u = s.checkPluginNum(w.description, r);
                    t = s.checkPluginNum(w.name, r);
                    p = u && t ? (j.compareNums(u, t) > 0 ? u : t) : (u || t)
                }
                if (!p) {
                    r = /Java.*\d.*Plug-in/i;
                    w = j.pd.findNavPlugin( {
                        find : r, mimes : q.mimeType, plugins : 1, dbug : v
                    });
                    if (w) {
                        u = s.checkPluginNum(w.description, r);
                        t = s.checkPluginNum(w.name, r);
                        p = u && t ? (j.compareNums(u, t) > 0 ? u : t) : (u || t)
                    }
                }
                return p
            },
            checkPluginNum : function (s, r) {
                var p, q;
                p = r.test(s) ? j.formatNum(j.getNum(s)) : 0;
                if (p && j.compareNums(p, j.formatNum("10")) >= 0) {
                    q = p.split(j.splitNumRegx);
                    p = j.formatNum("1," + (parseInt(q[0], 10) - 3) + ",0," + q[1])
                }
                if (p && (j.compareNums(p, j.formatNum("1,3")) < 0 || j.compareNums(p, j.formatNum("2")) >= 0)) {
                    p = 0
                }
                return p
            },
            query : function () {
                var t = this, s = a, r, p = 0, q = t.hasRun || !s.navigator.mimeObj;
                t.hasRun = 1;
                if (q) {
                    return t
                }
                if (!p || j.dbug) {
                    r = t.getPlatformNum();
                    if (r) {
                        p = r
                    }
                }
                if (!p || j.dbug) {
                    r = t.getPluginNum();
                    if (r) {
                        p = r
                    }
                }
                if (p) {
                    t.version = j.formatNum(p)
                }
                return t
            }
        },
        applet :  {
            codebase :  {
                isMin : function (p) {
                    this.$$ = a;
                    return j.codebase.isMin(this, p)
                },
                search : function () {
                    this.$$ = a;
                    return j.codebase.search(this)
                },
                DIGITMAX : [[15, 128], [6, 0, 512], 0, [1, 5, 2, 256], 0, [1, 4, 1, 1], [1, 4, 0, 64], [1, 3, 2, 32]], DIGITMIN : [1, 0, 0, 0], Upper : ["999", "10", "5,0,20", "1,5,0,20", "1,4,1,20", "1,4,1,2", "1,4,1", "1,4"], Lower : ["10", "5,0,20", "1,5,0,20", "1,4,1,20", "1,4,1,2", "1,4,1", "1,4", "0"], convert : [function (r, q) {return q ? [parseInt(r[0], 10) > 1 ? "99" : parseInt(r[1], 10) + 3 + "", r[3], "0", "0"] : ["1", parseInt(r[0], 10) - 3 + "", "0", r[1]]},function (r, q) {return q ? [r[1], r[2], r[3] + "0", "0"] : ["1", r[0], r[1], r[2].substring(0, r[2].length - 1 || 1)]},0, function (r, q) {return q ? [r[0], r[1], r[2], r[3] + "0"] : [r[0], r[1], r[2], r[3].substring(0, r[3].length - 1 || 1)]},0, 1, function (r, q) {return q ? [r[0], r[1], r[2], r[3] + "0"] : [r[0], r[1], r[2], r[3].substring(0, r[3].length - 1 || 1)]},1]
            },
            results : [[null, null], [null, null], [null, null], [null, null]], getResult : function () {
                var q = this, s = q.results, p, r = [];
                for (p = s.length - 1;p >= 0;p--) {
                    r = s[p];
                    if (r[0]) {
                        break 
                    }
                }
                r = [].concat(r);
                return r
            },
            DummySpanTagHTML : 0, HTML : [0, 0, 0, 0], active : [0, 0, 0, 0], DummyObjTagHTML : 0, DummyObjTagHTML2 : 0, allowed : [1, 1, 1, 1], VerifyTagsHas : function (q) {
                var r = this, p;
                for (p = 0;p < r.allowed.length;p++) {
                    if (r.allowed[p] === q) {
                        return 1
                    }
                }
                return 0
            },
            saveAsVerifyTagsArray : function (r) {
                var q = this, p;
                if (j.isArray(r)) {
                    for (p = 1;p < q.allowed.length;p++) {
                        if (r.length > p - 1 && j.isNum(r[p - 1])) {
                            if (r[p - 1] < 0) {
                                r[p - 1] = 0
                            }
                            if (r[p - 1] > 3) {
                                r[p - 1] = 3
                            }
                            q.allowed[p] = r[p - 1]
                        }
                    }
                    q.allowed[0] = q.allowed[3];
                }
            },
            setVerifyTagsArray : function (r) {
                var q = this, p = a;
                if (p.getVersionDone === null) {
                    q.saveAsVerifyTagsArray(p.getVerifyTagsDefault())
                }
                if (j.dbug) {
                    q.saveAsVerifyTagsArray([3, 3, 3])
                }
                else {
                    if (r) {
                        q.saveAsVerifyTagsArray(r)
                    }
                }
            },
            isDisabled :  {
                single : function (q) {
                    var p = this;
                    if (p.all()) {
                        return 1
                    }
                    if (q == 1) {
                        return !j.DOM.isEnabled.objectTag()
                    }
                    if (q == 2) {
                        return p.AppletTag()
                    }
                    if (q === 0) {
                        return j.codebase.isDisabled()
                    }
                    if (q == 3) {
                        return !j.DOM.isEnabled.objectTagUsingActiveX()
                    }
                    return 1
                },
                all_ : null, all : function () {
                    var r = this, t = a, q = t.navigator, p, s = j.browser;
                    if (r.all_ === null) {
                        if ((s.isOpera && j.compareNums(s.verOpera, "13,0,0,0") < 0 && !q.javaEnabled()) || (r.AppletTag() && !j.DOM.isEnabled.objectTag()) || (!q.mimeObj && !s.isIE)) {
                            p = 1
                        }
                        else {
                            p = 0
                        }
                        r.all_ = p
                    }
                    return r.all_
                },
                AppletTag : function () {
                    var q = a, p = q.navigator;
                    return j.browser.isIE ? !p.javaEnabled() : 0
                },
                VerifyTagsDefault_1 : function () {
                    var q = j.browser, p = 1;
                    if (q.isIE && !q.ActiveXEnabled) {
                        p = 0
                    }
                    if ((q.isIE && q.verIE < 9) || (q.verGecko && j.compareNums(q.verGecko, j.formatNum("2")) < 0) || (q.isSafari && (!q.verSafari || j.compareNums(q.verSafari, j.formatNum("4")) < 0)) || (q.isOpera && j.compareNums(q.verOpera, j.formatNum("11")) < 0)) {
                        p = 0
                    }
                    return p
                }
            },
            can_Insert_Query : function (s) {
                var q = this, r = q.results[0][0], p = q.getResult()[0];
                if (q.HTML[s] || (s === 0 && r !== null && !q.isRange(r)) || (s === 0 && p && !q.isRange(p))) {
                    return 0
                }
                return !q.isDisabled.single(s)
            },
            can_Insert_Query_Any : function () {
                var q = this, p;
                for (p = 0;p < q.results.length;p++) {
                    if (q.can_Insert_Query(p)) {
                        return 1
                    }
                }
                return 0
            },
            should_Insert_Query : function (s) {
                var r = this, t = r.allowed, q = a, p = r.getResult()[0];
                p = p && (s > 0 || !r.isRange(p));
                if (!r.can_Insert_Query(s) || t[s] === 0) {
                    return 0
                }
                if (t[s] == 3 || (t[s] == 2.8 && !p)) {
                    return 1
                }
                if (!q.nonAppletDetectionOk(q.version0)) {
                    if (t[s] == 2 || (t[s] == 1 && !p)) {
                        return 1
                    }
                }
                return 0
            },
            should_Insert_Query_Any : function () {
                var q = this, p;
                for (p = 0;p < q.allowed.length;p++) {
                    if (q.should_Insert_Query(p)) {
                        return 1
                    }
                }
                return 0
            },
            query : function (t) {
                var p = this, s = a, x = null, y = null, q = p.results, r, v, u = p.HTML[t];
                if (!u || !u.obj() || q[t][0] || s.bridgeDisabled) {
                    return 
                }
                r = u.obj();
                v = u.readyState();
                if (!j.isNum(v) || v == 4) {
                    try {
                        x = j.getNum(r.getVersion() + "");
                        y = r.getVendor() + "";
                        r.statusbar(j.win.loaded ? " " : " ")
                    }
                    catch (w) {
                    }
                    if (x && j.isStrNum(x) && !(j.dbug && s.OTF < 3)) {
                        q[t] = [x, y];
                        p.active[t] = 2;
                    }
                }
            },
            isRange : function (p) {
                return (/^[<>]/).test(p || "") ? (p.charAt(0) == ">" ? 1 :  - 1) : 0
            },
            setRange : function (q, p) {
                return (q ? (q > 0 ? ">" : "<") : "") + (j.isString(p) ? p : "")
            },
            insertJavaTag : function (z, w, p, s, D) {
                var t = a, v = "A.class", A = j.file.getValid(t), y = A.name + A.ext, x = A.path;
                var u = ["archive", y, "code", v], E = (s ? ["width", s] : []).concat(D ? ["height", D] : []), r = ["mayscript", "true"], C = ["scriptable", "true", "codebase_lookup", "false"].concat(r), B = t.navigator, q = !j.browser.isIE && B.mimeObj && B.mimeObj.type ? B.mimeObj.type : t.mimeType[0];
                if (z == 1) {
                    return j.browser.isIE ? j.DOM.insert("object", ["type", q].concat(E), ["codebase", x].concat(u).concat(C), p, t, 0, w) : j.DOM.insert("object", ["type", q].concat(E), ["codebase", x].concat(u).concat(C), p, t, 0, w)
                }
                if (z == 2) {
                    return j.browser.isIE ? j.DOM.insert("applet", ["alt", p].concat(r).concat(u).concat(E), ["codebase", x].concat(C), p, t, 0, w) : j.DOM.insert("applet", ["codebase", x, "alt", p].concat(r).concat(u).concat(E), [].concat(C), p, t, 0, w)
                }
                if (z == 3) {
                    return j.browser.isIE ? j.DOM.insert("object", ["classid", t.classID].concat(E), ["codebase", x].concat(u).concat(C), p, t, 0, w) : j.DOM.insert()
                }
                if (z == 4) {
                    return j.DOM.insert("embed", ["codebase", x].concat(u).concat(["type", q]).concat(C).concat(E), [], p, t, 0, w)
                }
                return j.DOM.insert()
            },
            insertIframe : function (p) {
                return j.DOM.iframe.insert(99, p)
            },
            insert_Query_Any : function (w) {
                var q = this, r = a, y = j.DOM, u = q.results, x = q.HTML, p = y.altHTML, t, s, v = j.file.getValid(r);
                if (q.should_Insert_Query(0)) {
                    if (r.OTF < 2) {
                        r.OTF = 2
                    }
                    u[0] = [0, 0];
                    t = w ? q.codebase.isMin(w) : q.codebase.search();
                    if (t) {
                        u[0][0] = w ? q.setRange(t, w) : t
                    }
                    q.active[0] = t ? 1.5 :  - 1
                }
                if (!v) {
                    return q.getResult()
                }
                if (!q.DummySpanTagHTML) {
                    s = q.insertIframe("applet.DummySpanTagHTML");
                    q.DummySpanTagHTML = y.insert("", [], [], p, 0, 0, s);
                    y.iframe.close(s)
                }
                if (q.should_Insert_Query(1)) {
                    if (r.OTF < 2) {
                        r.OTF = 2
                    }
                    s = q.insertIframe("applet.HTML[1]");
                    x[1] = q.insertJavaTag(1, s, p);
                    y.iframe.close(s);
                    u[1] = [0, 0];
                    q.query(1)
                }
                if (q.should_Insert_Query(2)) {
                    if (r.OTF < 2) {
                        r.OTF = 2
                    }
                    s = q.insertIframe("applet.HTML[2]");
                    x[2] = q.insertJavaTag(2, s, p);
                    y.iframe.close(s);
                    u[2] = [0, 0];
                    q.query(2)
                }
                if (q.should_Insert_Query(3)) {
                    if (r.OTF < 2) {
                        r.OTF = 2
                    }
                    s = q.insertIframe("applet.HTML[3]");
                    x[3] = q.insertJavaTag(3, s, p);
                    y.iframe.close(s);
                    u[3] = [0, 0];
                    q.query(3)
                }
                if (y.isEnabled.objectTag()) {
                    if (!q.DummyObjTagHTML && (x[1] || x[2])) {
                        s = q.insertIframe("applet.DummyObjTagHTML");
                        q.DummyObjTagHTML = y.insert("object", ["type", r.mimeType_dummy], [], p, 0, 0, s);
                        y.iframe.close(s)
                    }
                    if (!q.DummyObjTagHTML2 && x[3]) {
                        s = q.insertIframe("applet.DummyObjTagHTML2");
                        q.DummyObjTagHTML2 = y.insert("object", ["classid", r.classID_dummy], [], p, 0, 0, s);
                        y.iframe.close(s)
                    }
                }
                r.NOTF.init();
                return q.getResult()
            }
        },
        NOTF :  {
            count : 0, count2 : 0, countMax : 25, intervalLength : 250, init : function () {
                var q = this, p = a;
                if (p.OTF < 3 && q.shouldContinueQuery()) {
                    p.OTF = 3;
                    j.ev.setTimeout(q.onIntervalQuery, q.intervalLength);
                }
            },
            allHTMLloaded : function () {
                var r = a.applet, q, p = [r.DummySpanTagHTML, r.DummyObjTagHTML, r.DummyObjTagHTML2].concat(r.HTML);
                for (q = 0;q < p.length;q++) {
                    if (p[q] && p[q].loaded !== null && !p[q].loaded) {
                        return 0
                    }
                }
                return 1
            },
            shouldContinueQuery : function () {
                var t = this, s = a, r = s.applet, q, p = 0;
                if (t.allHTMLloaded()) {
                    if (t.count - t.count2 > 2) {
                        return p
                    }
                }
                else {
                    t.count2 = t.count
                }
                for (q = 0;q < r.results.length;q++) {
                    if (r.HTML[q]) {
                        if (!r.results[q][0] && (r.allowed[q] >= 2 || (r.allowed[q] == 1 && !r.getResult()[0])) && (!t.count || t.isAppletActive(q) >= 0)) {
                            p = 1
                        }
                    }
                }
                return p
            },
            isJavaActive : function (s) {
                var u = this, r = a, p, q, t =  - 9;
                for (p = 0;p < r.applet.HTML.length;p++) {
                    q = u.isAppletActive(p, s);
                    if (q > t) {
                        t = q
                    }
                }
                return t
            },
            isAppletActive : function (t, u) {
                var v = this, q = a, A = q.navigator, p = q.applet, w = p.HTML[t], s = p.active, z, r = 0, y, B = s[t];
                if (u || B >= 1.5 || !w || !w.span()) {
                    return B
                }
                y = j.DOM.getTagStatus(w, p.DummySpanTagHTML, p.DummyObjTagHTML, p.DummyObjTagHTML2, v.count);
                for (z = 0;z < s.length;z++) {
                    if (s[z] > 0) {
                        r = 1
                    }
                }
                if (y != 1) {
                    B = y
                }
                else {
                    if (j.browser.isIE || (q.version0 && A.javaEnabled() && A.mimeObj && (w.tagName == "object" || r))) {
                        B = 1
                    }
                    else {
                        B = 0
                    }
                }
                s[t] = B;
                return B
            },
            onIntervalQuery : function () {
                var q = a.NOTF, p;
                q.count++;
                if (a.OTF == 3) {
                    p = q.queryAllApplets();
                    if (!q.shouldContinueQuery()) {
                        q.queryCompleted(p)
                    }
                }
                if (a.OTF == 3) {
                    j.ev.setTimeout(q.onIntervalQuery, q.intervalLength)
                }
            },
            queryAllApplets : function () {
                var t = this, s = a, r = s.applet, q, p;
                for (q = 0;q < r.results.length;q++) {
                    r.query(q)
                }
                p = r.getResult();
                return p
            },
            queryCompleted : function (p) {
                var r = this, q = a;
                if (q.OTF >= 4) {
                    return 
                }
                q.OTF = 4;
                r.isJavaActive();
                q.setPluginStatus(p[0], p[1], 0);
                j.ev.callArray(q.DoneHndlrs);
            }
        }
    };
    j.addPlugin("java", a);
})();