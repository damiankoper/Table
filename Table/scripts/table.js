(function ($, r) { var h, n = Array.prototype.slice, t = decodeURIComponent, a = $.param, j, c, m, y, b = $.bbq = $.bbq || {}, s, x, k, e = $.event.special, d = "hashchange", B = "querystring", F = "fragment", z = "elemUrlAttr", l = "href", w = "src", p = /^.*\?|#.*$/g, u, H, g, i, C, E = {}; function G(I) { return typeof I === "string" } function D(J) { var I = n.call(arguments, 1); return function () { return J.apply(this, I.concat(n.call(arguments))) } } function o(I) { return I.replace(H, "$2") } function q(I) { return I.replace(/(?:^[^?#]*\?([^#]*).*$)?.*/, "$1") } function f(K, P, I, L, J) { var R, O, N, Q, M; if (L !== h) { N = I.match(K ? H : /^([^#?]*)\??([^#]*)(#?.*)/); M = N[3] || ""; if (J === 2 && G(L)) { O = L.replace(K ? u : p, "") } else { Q = m(N[2]); L = G(L) ? m[K ? F : B](L) : L; O = J === 2 ? L : J === 1 ? $.extend({}, L, Q) : $.extend({}, Q, L); O = j(O); if (K) { O = O.replace(g, t) } } R = N[1] + (K ? C : O || !N[1] ? "?" : "") + O + M } else { R = P(I !== h ? I : location.href) } return R } a[B] = D(f, 0, q); a[F] = c = D(f, 1, o); a.sorted = j = function (J, K) { var I = [], L = {}; $.each(a(J, K).split("&"), function (P, M) { var O = M.replace(/(?:%5B|=).*$/, ""), N = L[O]; if (!N) { N = L[O] = []; I.push(O) } N.push(M) }); return $.map(I.sort(), function (M) { return L[M] }).join("&") }; c.noEscape = function (J) { J = J || ""; var I = $.map(J.split(""), encodeURIComponent); g = new RegExp(I.join("|"), "g") }; c.noEscape(",/"); c.ajaxCrawlable = function (I) { if (I !== h) { if (I) { u = /^.*(?:#!|#)/; H = /^([^#]*)(?:#!|#)?(.*)$/; C = "#!" } else { u = /^.*#/; H = /^([^#]*)#?(.*)$/; C = "#" } i = !!I } return i }; c.ajaxCrawlable(0); $.deparam = m = function (L, I) { var K = {}, J = { "true": !0, "false": !1, "null": null }; $.each(L.replace(/\+/g, " ").split("&"), function (O, T) { var N = T.split("="), S = t(N[0]), M, R = K, P = 0, U = S.split("]["), Q = U.length - 1; if (/\[/.test(U[0]) && /\]$/.test(U[Q])) { U[Q] = U[Q].replace(/\]$/, ""); U = U.shift().split("[").concat(U); Q = U.length - 1 } else { Q = 0 } if (N.length === 2) { M = t(N[1]); if (I) { M = M && !isNaN(M) ? +M : M === "undefined" ? h : J[M] !== h ? J[M] : M } if (Q) { for (; P <= Q; P++) { S = U[P] === "" ? R.length : U[P]; R = R[S] = P < Q ? R[S] || (U[P + 1] && isNaN(U[P + 1]) ? {} : []) : M } } else { if ($.isArray(K[S])) { K[S].push(M) } else { if (K[S] !== h) { K[S] = [K[S], M] } else { K[S] = M } } } } else { if (S) { K[S] = I ? h : "" } } }); return K }; function A(K, I, J) { if (I === h || typeof I === "boolean") { J = I; I = a[K ? F : B]() } else { I = G(I) ? I.replace(K ? u : p, "") : I } return m(I, J) } m[B] = D(A, 0); m[F] = y = D(A, 1); $[z] || ($[z] = function (I) { return $.extend(E, I) })({ a: l, base: l, iframe: w, img: w, input: w, form: "action", link: l, script: w }); k = $[z]; function v(L, J, K, I) { if (!G(K) && typeof K !== "object") { I = K; K = J; J = h } return this.each(function () { var O = $(this), M = J || k()[(this.nodeName || "").toLowerCase()] || "", N = M && O.attr(M) || ""; O.attr(M, a[L](N, K, I)) }) } $.fn[B] = D(v, B); $.fn[F] = D(v, F); b.pushState = s = function (L, I) { if (G(L) && /^#/.test(L) && I === h) { I = 2 } var K = L !== h, J = c(location.href, K ? L : {}, K ? I : 2); location.href = J }; b.getState = x = function (I, J) { return I === h || typeof I === "boolean" ? y(I) : y(J)[I] }; b.removeState = function (I) { var J = {}; if (I !== h) { J = x(); $.each($.isArray(I) ? I : arguments, function (L, K) { delete J[K] }) } s(J, 2) }; e[d] = $.extend(e[d], { add: function (I) { var K; function J(M) { var L = M[F] = c(); M.getState = function (N, O) { return N === h || typeof N === "boolean" ? m(L, N) : m(L, O)[N] }; K.apply(this, arguments) } if ($.isFunction(I)) { K = I; return J } else { K = I.handler; I.handler = J } } }) })(jQuery, this);
!function (e) { function t(e, t) { t && e.append(t.jquery ? t.clone() : t) } function n(t, n, a) { var i = n.clone(); a.removeScripts && i.find("script").remove(), a.printContainer ? t.append(e("<div/>").html(i).html()) : i.each(function () { t.append(e(this).html()) }) } var a; e.fn.printThis = function (i) { a = e.extend({}, e.fn.printThis.defaults, i); var o = this instanceof jQuery ? this : e(this), r = "printThis-" + (new Date).getTime(); if (window.location.hostname !== document.domain && navigator.userAgent.match(/msie/i)) { var s = 'javascript:document.write("<head><script>document.domain=\\"' + document.domain + '\\";<\/script></head><body></body>")', c = document.createElement("iframe"); c.name = "printIframe", c.id = r, c.className = "MSIE", document.body.appendChild(c), c.src = s } else e("<iframe id='" + r + "' name='printIframe' />").appendTo("body"); var d = e("#" + r); a.debug || d.css({ position: "absolute", width: "0px", height: "0px", left: "-600px", top: "-600px" }), setTimeout(function () { a.doctypeString && function (e, t) { var n, a; (a = (n = (n = e.get(0)).contentWindow || n.contentDocument || n).document || n.contentDocument || n).open(), a.write(t), a.close() }(d, a.doctypeString); var i, r = d.contents(), s = r.find("head"), c = r.find("body"), l = e("base"); i = !0 === a.base && l.length > 0 ? l.attr("href") : "string" == typeof a.base ? a.base : document.location.protocol + "//" + document.location.host, s.append('<base href="' + i + '">'), a.importCSS && e("link[rel=stylesheet]").each(function () { var t = e(this).attr("href"); if (t) { var n = e(this).attr("media") || "all"; s.append("<link type='text/css' rel='stylesheet' href='" + t + "' media='" + n + "'>") } }), a.importStyle && e("style").each(function () { e(this).clone().appendTo(s) }), a.pageTitle && s.append("<title>" + a.pageTitle + "</title>"), a.loadCSS && (e.isArray(a.loadCSS) ? jQuery.each(a.loadCSS, function (e, t) { s.append("<link type='text/css' rel='stylesheet' href='" + this + "'>") }) : s.append("<link type='text/css' rel='stylesheet' href='" + a.loadCSS + "'>")); var p = a.copyTagClasses; if (p && (-1 !== (p = !0 === p ? "bh" : p).indexOf("b") && c.addClass(e("body")[0].className), -1 !== p.indexOf("h") && r.find("html").addClass(e("html")[0].className)), t(c, a.header), a.canvas) { var h = 0; o.find("canvas").each(function () { e(this).attr("data-printthis", h++) }) } if (n(c, o, a), a.canvas && c.find("canvas").each(function () { var t = e(this).data("printthis"), n = e('[data-printthis="' + t + '"]'); this.getContext("2d").drawImage(n[0], 0, 0), n.removeData("printthis") }), a.formValues) { var m = o.find("input"); m.length && m.each(function () { var t = e(this), n = e(this).attr("name"), a = t.is(":checkbox") || t.is(":radio"), i = r.find('input[name="' + n + '"]'), o = t.val(); a ? t.is(":checked") && (t.is(":checkbox") ? i.attr("checked", "checked") : t.is(":radio") && r.find('input[name="' + n + '"][value="' + o + '"]').attr("checked", "checked")) : i.val(o) }); var f = o.find("select"); f.length && f.each(function () { var t = e(this), n = e(this).attr("name"), a = t.val(); r.find('select[name="' + n + '"]').val(a) }); var u = o.find("textarea"); u.length && u.each(function () { var t = e(this), n = e(this).attr("name"), a = t.val(); r.find('textarea[name="' + n + '"]').val(a) }) } a.removeInline && (e.isFunction(e.removeAttr) ? r.find("body *").removeAttr("style") : r.find("body *").attr("style", "")), t(c, a.footer), setTimeout(function () { d.hasClass("MSIE") ? (window.frames.printIframe.focus(), s.append("<script>  window.print(); <\/script>")) : document.queryCommandSupported("print") ? d[0].contentWindow.document.execCommand("print", !1, null) : (d[0].contentWindow.focus(), d[0].contentWindow.print()), a.debug || setTimeout(function () { d.remove() }, 1e3) }, a.printDelay) }, 333) }, e.fn.printThis.defaults = { debug: !1, importCSS: !0, importStyle: !1, printContainer: !0, loadCSS: "", pageTitle: "", removeInline: !1, printDelay: 333, header: null, footer: null, formValues: !0, canvas: !1, base: !1, doctypeString: "<!DOCTYPE html>", removeScripts: !1, copyTagClasses: !1 } }(jQuery);//pojawia
$.fn.rightShow = function (speed, fn) {
    var css = {
        opacity: $(this).css("opacity"),
        width: $(this).css("width") || $(this).width(),
        marginLeft: $(this).css("marginLeft"),
        marginRight: $(this).css("marginRight"),
        marginTop: $(this).css("marginTop"),
        marginBottom: $(this).css("marginBottom"),
        padding: $(this).css("paddingTop"),
    };
    if ($(this).data("css") === undefined) {
        $(this).data("css", css);
        $(this).addClass("notransition");
        $(this).css({
            'opacity': 0,
            'width': '0px',
            'margin': '0px',
            'padding': '0px',
            'display': 'block',
        });
    }
    $(this)[0].offsetHeight;
    $(this).removeClass("notransition");
    $(this).css({
        'opacity': $(this).data("css").opacity,
        'width': '',
        'margin-top': $(this).data("css").marginTop,
        'margin-left': $(this).data("css").marginLeft,
        'margin-bottom': $(this).data("css").marginBottom,
        'margin-right': $(this).data("css").marginRight,
        'padding': $(this).data("css").padding,
    });
    return $(this);
}
//ukrywa
$.fn.leftHide = function (speed, fn) {
    var css = {
        opacity: $(this).css("opacity"),
        width: $(this).css("width"),
        marginLeft: $(this).css("marginLeft"),
        marginRight: $(this).css("marginRight"),
        marginTop: $(this).css("marginTop"),
        marginBottom: $(this).css("marginBottom"),
        padding: $(this).css("paddingTop"),
    };
    $(this).data("css", css);
    $(this).css({
        'opacity': 0,
        'width': '0px',
        'margin': '0px',
        'padding': '0px'
    });
    return $(this);
}
function placeCaretAtEnd(el) {
    el.focus();
    if (typeof window.getSelection != "undefined"
        && typeof document.createRange != "undefined") {
        var range = document.createRange();
        range.selectNodeContents(el);
        range.collapse(false);
        var sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(range);
    } else if (typeof document.body.createTextRange != "undefined") {
        var textRange = document.body.createTextRange();
        textRange.moveToElementText(el);
        textRange.collapse(false);
        textRange.select();
    }
}
$.fn.tableQuery = function () {
    var q = {};
    if ($(this).find(".header > div.sortedUp, .header > div.sortedDown").length)
        q["sort"] = {
            col: $(this).find(".header > div.sortedUp, .header > div.sortedDown").attr('class').split(' ')[0],
            dir: ($(this).find(".header > div.sortedUp, .header > div.sortedDown").hasClass("sortedDown") ? "d" : "u")
        };
    q["filter"] = {};
    $(this).find(".table-control-panel select").each(function () {
        if ($(this).val() !== "all") {
            q["filter"][$(this).data("col")] = $(this).prop("value");
        }
    });
    q["hidden"] = {};
    $(this).find(".table-control-panel input[type='checkbox']").each(function () {
        if ($(this).is(":checked")) {
            q["hidden"][$(this).parents(".table-whole").find(".table-container .header ." + $(this).data("class")).text()] = $(this).data("class");
        }
    });
    console.log(q);
    history.pushState(q, "Table query", "?" + $.param(q));
}

$.fn.copyToClipboard = function () {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(this).text().trim()).select();
    if ($(this).text() != "") {
        document.execCommand("copy");
        alert($(this).text() + " - skopiowano do schowka");
    }
    $temp.remove();
}

$(".wrapper").parent().unbind('click').click(function (e) {
    if (!$(e.target).hasClass("wrapper")) {
        e.preventDefault();
        $(this).find(".wrapper").focus();
        placeCaretAtEnd($(this).find(".wrapper")[0]);
    }
});
$(".wrapper > .settings-button").unbind('click').click(function () {
    if ($(this).prev().hasClass("visible")) {
        $(".settings-button-bar").removeClass("visible");
    }
    else {
        $(".settings-button-bar").removeClass("visible");
        $(this).prev().toggleClass("visible");
    }
});
$('body').unbind('click').click(function (evt) {
    if (evt.target.className == "settings-button-bar")
        return;
    if ($(evt.target).closest('.settings').length)
        return;
    $(this).find(".settings-button-bar").removeClass("visible");
});
$(".remove-button").unbind('click').click(function () {
    if (!confirm("Czy na pewno chcesz usunąć ten element?")) return;
    $(".settings-button-bar").removeClass("visible");
    var _this = this;
    $.ajax({
        type: "POST",
        url: window.location.href,
        data: {
            action: "remove",
            id: $(_this).parents(".table-row").find(".id").text().trim()
        },
        dataType: "json",
        beforeSend: function () {
            $(_this).parents(".settings-button-bar").siblings(".settings-button").find("img").addClass("loading");
            $(_this).prop("disabled");
        },
        success: function (response) {
            console.log(response);
            $(_this).parents(".settings-button-bar").siblings(".settings-button").find("img").removeClass("loading");
            switch (response.type) {
                case "success":
                    $(_this).parents(".table-row").css({ overflow: "hidden", height: $(_this).parents(".table-row").outerHeight() });
                    $(_this)[0].offsetHeight;
                    $(_this).parents(".table-row").css({ height: 0 });
                    setTimeout(function () {
                        $(_this).parents(".table-row").remove();
                    }, 600);
                    $.notify("Usuwanie pomyślne", "success");
                    break;
                case "error":
                    $.notify("Błąd po stronie serwera przy usuwaniu", "error");
                    break;
                default:
                    $.notify("Nieznany błąd", "error");
                    break;
            }
        }
    });
});
$(".edit-button").unbind('click').click(function () {
    var row = $(this).parents(".cells");
    var editable = $(this).parents(".cells").children(".editable");
    var height = 0;
    $(this).parents(".table-container").find(".editing_row").find(".edit-button-exit").click();
    console.log(row.filter(".editing_row"));
    row.data("height", row.css("height"));
    row.addClass("editing_row").children().each(function () {
        $(this).data("classlist", $(this)[0].className);
        if (height < $(this)[0].scrollHeight) {
            height = $(this)[0].scrollHeight;
        }
        $(this).not(".settings").addClass("noexpand overflowhidden");
    });
    setTimeout(function () {
        row.css("height", height);
        setTimeout(function () {
            row.addClass("heightauto");
            row.children().not(".settings").removeClass("overflowhidden");
        }, 100);
    }, 100);
    $(this).siblings(".edit-button-check").rightShow();
    $(this).leftHide(100);
    editable.each(function () {
        $(this).data("content", $(this).find(".wrapper").html());
        $(this).find(".wrapper").prop("contenteditable", true).parent().addClass("editing");
    });
});
$(".edit-button-exit").unbind('click').click(function () {
    var row = $(this).parents(".cells");
    var editable = $(this).parents(".cells").children(".editable");
    row.removeClass("heightauto editing_row");
    setTimeout(function () {
        row.css("height", '3em');
    }, 100);

    row.children().each(function () {
        $(this)[0].className = $(this).data("classlist");
    });
    $(this).parent().children(".edit-button-check").leftHide();
    $(this).siblings(".edit-button").rightShow(100);
    editable.each(function () {
        $(this).find(".wrapper").html($(this).data("content"));
        $(this).find(".wrapper").prop("contenteditable", false).parent().removeClass("editing");
    });
});
$(".edit-button-submit").unbind('click').click(function () {
    var _this = this;
    var editable = $(this).parents(".cells").children(".editable");
    var formData = {};

    editable.each(function () {
        var obj = { text: $(this).find(".wrapper").html(), column: $(this).data("orgname") };
        obj[$(this).data("orgname")] = $(this).find(".wrapper").html();
        (formData[$(this).data("table")] = formData[$(this).data("table")] || []).push(obj);
    });
    console.log(formData);
    $.ajax({
        async: true,
        type: "POST",
        url: window.location.href,
        data: {
            action: "edit",
            id: $(_this).parents(".cells").find(".id").text().trim(),
            data: formData
        },
        dataType: "json",
        beforeSend: function () {
            $(_this).parents(".settings-button-bar").siblings(".settings-button").find("img").addClass("loading");
            $(_this).prop("disabled");
        },
        success: function (response) {
            console.log(response);
            $(_this).parents(".settings-button-bar").siblings(".settings-button").find("img").removeClass("loading");
            switch (response.type) {
                case "success":
                    $.notify("Edycja zakończona pomyślnie", "success");
                    editable.each(function () {
                        $(this).data("content", $(this).find(".wrapper").html());
                    });
                    $(_this).siblings(".edit-button-exit").click();
                    break;
                case "error":
                    $.notify("Błąd po stronie serwera przy edycji", "error");
                    break;
                default:
                    $.notify("Nieznany błąd", "error");
                    break;
            }
        }
    });
});
$(".phone .wrapper").unbind('click').click(function (e) {
    if ($(this).text() != "" && !$(this).parent().hasClass("editing"))
        window.location.href = ("tel:" + $(this).text());
});
var slideButton = function (e) {
    console.log(e);
    if (!$(e.currentTarget).find("img").hasClass("rotated"))
        $('html, body').animate({
            scrollTop: $(e.currentTarget).offset().top - 5
        }, 300);
    var row = $(this).parents(".table-row");
    row.find(".slidable-div").slideToggle(100);
    if ($(this).hasClass("slidable-button"))
        $(this).find('img').toggleClass("rotated");
    else
        $(this).find('.slidable-button img').toggleClass("rotated");
};
$(".slidable-button").unbind('click').click(slideButton);
$(".cells").unbind('dblclick').dblclick(slideButton);
$(".table-control-panel select").unbind('change').change(function () {
    $(this).parents(".table-control-panel").find(".reload-button").click();
});
$(".table-control-panel .hidden-columns input[type='checkbox']").unbind('change').change(function () {
    $(this).parents(".table-whole").find(".table-container ." + $(this).data("class")).toggleClass("hidden");
    $(this).parents(".table-whole").tableQuery();
    var hidden = [];
    $(this).parents(".hidden-columns").find("input[type='checkbox']").each(function () {
        if ($(this).is(":checked")) {
            hidden.push($(this).data("name"));
        }
        console.log(hidden);
    });
    $(this).parents(".table-control-info").find(".additional").text((hidden.join(", ") != "") ? hidden.join(", ") : "Brak");
});
$(".header > div").unbind('click').click(function () {
    if ($(this).hasClass("sort")) {
        var direction = $(this).data("sorted");
        $(this).children(".sorting-icon")
            .animate({ width: "10px", opacity: 1, margin: '3px' }, 100)
            .parent().siblings().removeData("sorted", undefined).children(".sorting-icon")
            .animate({ width: "0", opacity: 0, margin: 0 }, 100);
        var index = $(this).index();
        var origin = $(this).parents(".header").siblings();
        var elements = origin.detach();
        $(this).add($(this).siblings()).removeClass("sortedUp sortedDown");
        if (direction === "down" || direction == undefined) {
            $(this).data("sorted", "up").addClass("sortedUp");
            $(this).children(".sorting-icon").html("&#x25B2;");
        } else {
            $(this).data("sorted", "down").addClass("sortedDown");
            $(this).children(".sorting-icon").html("&#x25BC;");
        }
        direction = $(this).data("sorted");
        var _this = this;
        elements.sort(function (a, b) {
            var cells1 = $(a).find(".cells > *");
            var cells2 = $(b).find(".cells > *");
            var text1 = cells1.eq(index).text().toLowerCase().trim();
            var text2 = cells2.eq(index).text().toLowerCase().trim();
            if (!$(_this).hasClass("num") && !$(_this).hasClass("cash")) {
                if (direction === "up") {
                    return text1.localeCompare(text2);
                }
                else if (direction === "down")
                    return text2.localeCompare(text1);
            }
            else {
                text1 = /(?:[\d ,]+)/.exec(text1)[0].replace(/\s/g, "").replace(",", ".");
                text2 = /(?:[\d ,]+)/.exec(text2)[0].replace(/\s/g, "").replace(",", ".");
                console.log(text1);
                if (direction === "up") {
                    return (parseFloat(text1) > parseFloat(text2)) ? 1 : -1;
                }
                else if (direction === "down")
                    return (parseFloat(text1) < parseFloat(text2)) ? 1 : -1;
            }
        });
        $(this).parents(".table-container").append($(elements));
        $(this).parents(".table-whole").tableQuery();

    }
    else {
        $.notify("Nie możesz sortować tej kolumny", "info");
    }
});
$(".table-control-menu > *:not(.add-row)").unbind('click').click(function () {
    $(".table-control-info .info > *").removeClass("active").eq($(this).index()).addClass("active").addClass("active").siblings().removeClass("active");
    $(this).addClass("active").siblings().removeClass("active");
    console.log($(this).parents(".table-whole").find(".table-container").is(":visible"));
    if (!$(this).parents(".table-whole").find(".table-container").is(":visible")) {
        var whole = $(this).parents(".table-whole");
        whole.find(".add_form-main").fadeOut(100, function () {
            whole.find(".table-container").fadeIn(100);
        });
    }
});
$(".table-control-panel .reload-button").unbind('click').click(function () {
    var _this = this;
    $(_this).parents(".table-whole").tableQuery();
    $.ajax({
        type: "POST",
        url: window.location.href,
        data: { action: "refresh" },
        dataType: "html",
        beforeSend: function () {
            $(_this).find("img").addClass("loading");
            $(_this).prop("disabled");
        },
        success: function (response) {
            $(_this).find("img").removeClass("loading");
            $(_this).parents(".table-control-panel").find(".rows-num").text($(response).find(".table-row").length);
            $(_this).parents(".table-whole").find(".table-container").replaceWith($(response));
            var text = [];
            $(_this).parents(".table-control-panel").find(".info .filters select").each(function () {
                if ($(this).val() !== "all")
                    text.push($(this).data("name"));
            });
            $(_this).parents(".table-control-panel").find(".info .filter").text(text.join(", ") || "Brak");
            $.notify("Odświeżono tabelę", "success");
        }
    });
});

$(".table-control-menu > .add-row").unbind('click').click(function () {
    var whole = $(this).parents(".table-whole");
    whole.find(".table-container").fadeOut(100, function () {
        whole.find(".add_form-main").fadeIn(100);
    });

    $(this).addClass("active").siblings().removeClass("active");
});
$(".table-whole form.add_form").unbind('submit').submit(function (event) {
    var _this = this;
    $.ajax({
        type: "post",
        url: window.location.href,
        data: {
            action: "insert",
            data: $(_this).serialize()
        },
        dataType: "json",
        beforeSend: function () {
            $(_this).parents(".table-whole").find(".reload-button").find("img").addClass("loading");
            $(_this).prop("disabled");
        },
        success: function (response) {
            $(_this).parents(".table-whole").find(".reload-button").find("img").removeClass("loading");
            switch (response.type) {
                case "success":
                    $(_this).prop("disabled", false);
                    $(_this)[0].reset();
                    $(_this).parent().hide(0);
                    $(_this).parents(".table-whole").find(".table-container").fadeIn(100);
                    var body = $("html, body");
                    body.stop().animate({ scrollTop: 0 }, 100);
                    var whole = $(_this).parents(".table-whole");
                    $(_this).parents(".table-whole").find(".reload-button").click();
                    whole.find(".add_form-main").fadeOut(100, function () {
                        whole.find(".table-container").fadeIn(100);
                    });
                    $.notify("Dodano pomyślne", "success");
                    break;
                case "error":
                    $.notify("Błąd po stronie serwera przy dodawaniu", "error");
                    break;
                default:
                    $.notify("Nieznany błąd", "error");
                    break;
            }
        }
    });
    event.preventDefault();
    return false;
});
$(".table-whole .email").unbind('click').click(function (event) {
    if (!$(this).hasClass("editing"))
        $(this).copyToClipboard();
});
$(".table-whole .payment-button").unbind('click').click(function (event) {
    console.log($(this).parents(".cells").find(".wyp").text());
    if ($(this).parents(".cells").find(".wyp").text().trim() === "NIE") {
        if (!confirm('Ten użytkownik nie ma rozpoczętego procesu wypłacania prowizji. Czy mimo to, chcesz przeprowadzić tę operację?'))
            return;
    } else if (!confirm('Czy na pewno chcesz zakończyć proces wypłaty prowizji dla tego użytkownika?')) {
        return
    }
    var sendData = {
        action: "payment",
        id: $(this).parents(".table-row").find(".id").text().trim(),
    };
    console.log(sendData);
    var _this = this;
    $.ajax({
        type: "POST",
        url: window.location.href,
        data: sendData,
        dataType: "JSON",
        beforeSend: function () {
            $(_this).parents(".settings-button-bar").siblings(".settings-button").find("img").addClass("loading");
            $(_this).prop("disabled");
        },
        success: function (response) {
            console.log(response);
            $(_this).parents(".settings-button-bar").siblings(".settings-button").find("img").removeClass("loading");
            $(_this).prop("disabled", false);
            switch (response.type) {
                case "success":
                    $.notify("Zmiany zapisano pomyślnie", "success");
                    $(_this).parents(".table-row").removeClass("pkf");
                    $(_this).parents(".table-row").find(".wyp .wrapper").text("NIE");
                    $(_this).parents(".table-row").find(".do-wypłaty .wrapper").text("0,00zł");
                    $("body").click();
                    break;
                case "error":
                default:

                    $.notify("Nieznany błąd", "error");
                    break;
            }
        }
    });
});
$(".table-whole .print-button").unbind('click').click(function (event) {
    var to_print = $(this).parents(".table-whole").find(".table-container").clone();
    to_print.find(".settings, .termin, .polecony-od, .date").remove();
    to_print.find(".cells").css({ height: "5em"});
    to_print.find(".cells .wrapper").css({ wordWrap: "break-word"});
    to_print.printThis();
});
$(function () {
    var q = $.deparam.querystring();
    if (typeof q["filter"] !== 'undefined') {
        for (var prop in q["filter"]) {
            $(".table-control-panel select." + prop.replace(".", "-")).val(q["filter"][prop]);
            console.log((".table-control-panel select." + prop));
        }
    }
    if (typeof q["hidden"] !== 'undefined') {
        for (var prop in q["hidden"]) {
            $(".table-whole .table-container ." + q["hidden"][prop]).removeClass("hidden");
            $(".table-control-panel .hidden-columns ." + q["hidden"][prop]).prop("checked", true);
        }
        var hidden = [];
        $(".hidden-columns").find("input[type='checkbox']").each(function () {
            if ($(this).is(":checked")) {
                hidden.push($(this).data("name"));
            }
            console.log(hidden);
        });
        $(".table-control-info").find(".additional").text((hidden.join(", ") != "") ? hidden.join(", ") : "Brak");
    }
    if (typeof q["sort"] !== 'undefined') {
        $(".table-whole .header > div." + q["sort"]["col"]).click();
        if (q["sort"]["dir"] === "d")
            $(".table-whole .header > div." + q["sort"]["col"]).click();
    }

});