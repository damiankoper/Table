function copyToClipboard() {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(this).text()).select();
    if ($(this).text() != "") {
        alert($(this).text() + " - skopiowano do schowka");
        document.execCommand("copy");
    }
    $temp.remove();
}

function hideCol(nth, table) {
    table.find('th').eq(nth).hide();
    table.find('tr td:nth-child(' + parseInt(nth + 1) + ')').hide();
}
function showCol(nth, table) {
    table.find('th').eq(nth).show();
    table.find('tr td:nth-child(' + parseInt(nth + 1) + ')').show();
}
function linkify(text) {
    var urlRegex = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
    return text.replace(urlRegex, function (url) {
        return '<a href="' + url + '">' + url + '</a>';
    });
}
function scrollToObject(object) {
    $('html, body').animate({ scrollTop: object.offset().top - $('#menu-container').height() }, '300', 'swing', null);
}
function scrollToAppId(id) {

    var item = $('.app_id').filter(function () {
        return ($(this).text() == id) ? true : false;
    });
    scrollToObject(item.eq(0));
    item.parents('tr').css('background-color', '#d9d9d9');
    setTimeout(function () {
        item.parents('tr').css('background-color', 'white');
    }, 3000);
}
function removeCol(array, table) {
    for (var a in array) {
        table.children('thead').find('tr th').eq(array[a]).remove();
        table.children('tbody').find('tr').each(function () {
            $(this).children('td').eq(array[a]).remove();
        });
    }
    return table;
}
function appendCol(name, width, table) {

    table.children('thead').find('tr').append('<th width="' + width + '">' + name + '</th>');
    table.children('tbody').find('tr').each(function () {
        $(this).append('<td height="60" width="' + width + '"></td>');
    });

    return table;
}
function toPrintSM() {
    var data = $('#filter_date option:selected').text();
    var sm_table = $('#sm_signups').clone();
    sm_table.find('th:nth-child(2)').click();
    var obj = $('<h1>SM ' + data + '</h1>');

    sm_table = removeCol([0, 3, 3, 3, 3, 3, 3], sm_table);
    sm_table = appendCol("10zł", 10, sm_table);
    sm_table = appendCol("Śniadanie + cena", 100, sm_table);
    sm_table = appendCol("Notatki", 1000, sm_table);
    sm_table.find('tbody tr td.email').css('max-width', '100px').css('word-wrap', 'break-word');
    sm_table.find('tbody td').css('height', 'auto!important');

    //var w = window.open();
    /* $(w.document.head).append('\
         <link rel="stylesheet" type="text/css" href="http://pkfo.pl/stylesheets/main.css">\
         <link rel="stylesheet" type="text/css" href="http://pkfo.pl/admin/admin.css">\
         <link rel="stylesheet" type="text/css" href="http://pkfo.pl/admin/printCSS.css">');*/
    //$(w.document.body).append(sm_table);
    obj.add(sm_table).printThis({ loadCSS: 'printCSS.css' });
}

function Filter(tableId, filterArray, selected) {
    this.tableId = tableId;
    this.filterArray = filterArray;
    this.selected = selected;
    this.auto = true;
}
Filter.prototype.autoOff = function () {
    this.auto = false;
}
Filter.prototype.getFilterURL = function () {
    var a = [];
    var filterArray = this.filterArray;
    for (var key in this.filterArray) {
        a.push(key + '=' + $("#" + key)[0].selectedIndex);
    }
    return a.join("&");
};
Filter.prototype.filterCustomByOne = function (objects, data_label, filter_value) {

    objects.each(function () {
        if ($(this).data(data_label) === filter_value || data_label === 'all') {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
};
Filter.prototype.filter = function () {
    var table = $("#" + this.tableId + " tbody");
    var filterArray = this.filterArray;
    var selected = this.selected;
    table.find('tr:not(.slidable_info)').show();
    var f_this = this;
    //table.find('tr').css('background-color', "#ffffff");
    for (var key in this.filterArray) {
        table.find('tr:visible').each(function () {
            var col_txt = $(this).find('td').eq(filterArray[key]).text();
            if (selected.length !== 0) {
                var sel_txt = $("#" + key + " option").eq(selected[key]).text();
                $("#" + key + " option").prop('selected', '');
                $("#" + key + " option").eq(selected[key]).prop('selected', 'selected');
                f_this.selected = [];
            } else
                var sel_txt = $("#" + key + " option:selected").text();

            var ifAll = $("#" + key + " option:selected").data('all');
            var ifDate = $("#" + key + " option:selected").data('date');
            //console.log(col_txt + " " + sel_txt + " " + ifAll);
            if ((col_txt != sel_txt && !ifAll) && !ifDate) {
                $(this).hide();
                //$(this).css('background-color', "#d9d9ff");
            } else if (ifDate) {
                var date = new Date(col_txt);
                var now = new Date();
                date.setHours(0, 0, 0, 0);
                now.setHours(0, 0, 0, 0);
                if (date >= now) {
                } else {
                    $(this).hide();
                }
            }
        });
    }
    history.replaceState("", "", [location.protocol, '//', location.host, location.pathname].join('') + '?' + filter.getFilterURL());
};

function tableSort_more(it) { //BACKWARD COMPABILITY
    var direction = it.data('dir');
    it.parent().children().find('.sort_marker').remove();
    if (direction === 1) {
        direction = 0;
        it.data('dir', 0);
        it.append("<span class='sort_marker'>&nbsp;▼</span>");
    } else if (direction === 0 || direction === undefined) {
        direction = 1;
        it.data('dir', 1);
        it.append("<span class='sort_marker'>&nbsp;▲</span>");

    }

    var col = it.index();
    var rows = it.closest('table').children('tbody').children('tr:even').toArray();
    var rows_add = it.closest('table').children('tbody').children('tr:odd').toArray();

    for (var i = 0; rows.length > i; i++) {
        for (var j = 0; rows.length - 1 > j; j++) {
            if (direction && $(rows[j]).children('td').eq(col).text().toLowerCase().localeCompare($(rows[j + 1]).children('td').eq(col).text().toLowerCase(), 'pl') > 0 || !direction && $(rows[j]).children('td').eq(col).text().toLowerCase().localeCompare($(rows[j + 1]).children('td').eq(col).text().toLowerCase(), 'pl') < 0) {
                var temp_e = rows[j];
                rows[j] = rows[j + 1];
                rows[j + 1] = temp_e;

                var temp_o = rows_add[j];
                rows_add[j] = rows_add[j + 1];
                rows_add[j + 1] = temp_o;
            }
        }
    }
    it.closest('table').children('tbody').children('tr:even').remove();
    for (var i = 0; i < rows.length; i++) {
        it.closest('table').children('tbody').append(rows[i]);
        it.closest('table').children('tbody').append(rows_add[i]);
    }
}

function tableSort(it) {
    var more = it.closest('table').data('more');
    var direction = it.data('dir');
    console.log(more + direction);

    if (direction == 'up')
        direction = 0;
    else if (direction == 'down')
        direction = 1;
    it.parent().children().find('.sort_marker').remove();
    if (direction === 1) {
        direction = 0;
        it.data('dir', 0);
        it.append("<span class='sort_marker'>&nbsp;▼</span>");
    } else if (direction === 0 || direction === undefined) {
        direction = 1;
        it.data('dir', 1);
        it.append("<span class='sort_marker'>&nbsp;▲</span>");

    }
    var col = it.index();
    var rows = it.closest('table').children('tbody').children('tr').toArray();
    for (var i = 0; rows.length > i; i += more + 1) {
        for (var j = 0; rows.length - 1 - more > j; j += more + 1) {
            if ($(rows[j]).children('td').eq(col).hasClass('digits') && $(rows[j + 1 + more]).children('td').eq(col).hasClass('digits')) {
                if (direction && parseInt($(rows[j]).children('td').eq(col).text()) > parseInt($(rows[j + 1 + more]).children('td').eq(col).text()) ||
                    !direction && parseInt($(rows[j]).children('td').eq(col).text()) < parseInt($(rows[j + 1 + more]).children('td').eq(col).text())) {
                    var temp_e = rows[j];
                    rows[j] = rows[j + 1 + more];
                    rows[j + 1 + more] = temp_e;
                    for (var k = 1; k <= more; k++) {
                        var temp_e = rows[j + k];
                        rows[j + k] = rows[j + k + 1 + more];
                        rows[j + k + 1 + more] = temp_e;
                    }
                }
            }
            else if (direction && $(rows[j]).children('td').eq(col).text().toLowerCase().localeCompare($(rows[j + 1 + more]).children('td').eq(col).text().toLowerCase(), 'pl') > 0 || !direction && $(rows[j]).children('td').eq(col).text().toLowerCase().localeCompare($(rows[j + 1 + more]).children('td').eq(col).text().toLowerCase(), 'pl') < 0) {
                var temp_e = rows[j];
                rows[j] = rows[j + 1 + more];
                rows[j + 1 + more] = temp_e;
                for (var k = 1; k <= more; k++) {
                    var temp_e = rows[j + k];
                    rows[j + k] = rows[j + k + 1 + more];
                    rows[j + k + 1 + more] = temp_e;
                }
            }
        }
    }
    it.closest('table').children('tbody').children('tr').remove();
    for (var i = 0; i < rows.length; i++) {
        it.closest('table').children('tbody').append(rows[i]);
    }
}
function tableFilter_more(it, col, phrase) {
    it.children('tbody').children('tr:even').each(function () {
        console.log($(this).children('td').eq(col).text());
        if (phrase == null) {
            $(this).show();
            return 0;
        }
        if ($(this).children('td').eq(col).text() != phrase) {
            $(this).add($(this).next().add($(this).next().children().children())).hide();
        } else
            $(this).show();
    });
}

function checkCB() {
    if (!$(this).is(':checked'))
        $(this).parent().css('background-color', '#68a0dd');
    else
        $(this).parent().css('background-color', '#d9d9d9');
}
function fU() {
    frameUpdate($(this).parents('.cl_com_main'));
}
$(function () {
    $('table').on('click', '.user_id', function () {
        if ($(this).text().match(/^\d{8}$/) && $(this).prop('contenteditable') != 'true') {
            var form = $('<form action="users.php" method="get">');
            form.append("<input type='hidden' name='search_id' value='" + $(this).text() + "'/>");
            form.appendTo($('body')).submit();
        }
    });

    $('.status').each(function () {
        if ($(this).text() == '-1')
            $(this).html("<p style='color:red'>ODRZUCONY</p>");
        if ($(this).text() == '0')
            $(this).html("<p style='color:orange'>W TRAKCIE</p>");
        if ($(this).text() == '1')
            $(this).html("<p style='color:green'>ZAKOŃCZONY</p>");
    });
    $('.awaits_payment').each(function () {
        if ($(this).text() == 'TAK')
            $(this).parent().css('background-color', 'rgba(255,0,0,0.5)');
    });
    $(".app-checklist input[type='checkbox'], .cl input[type='checkbox']").each(checkCB);
    $('table').on('change', '.app-checklist input[type="checkbox"], .cl input[type="checkbox"]', checkCB);
    $('table,.plan-main').on('change', '.cl input[type="checkbox"]', fU);
    $('table').on('click', 'td.elipse', copyToClipboard);
    $('.plan-main, table').on('info_slided', '.cl_com_main', function () {
        frameRefresh($(this));
    });

    $(window).resize(function () {
        clearTimeout(this.id);
        this.id = setTimeout(function () {
            $('.elipse').each(function () {
                $(this).css('height', 'auto');
                $(this).css('height', $(this).height());
            });
            $('td .wrapper').each(function () {
                $(this).find('.wrapper2').removeClass('slidable')
                if ($(this).innerHeight() < $(this).children('.wrapper2').innerHeight()) {
                    $(this).find('.wrapper2').addClass('slidable');
                }
            });
            $('.subplan label').each(function () {
                $(this).parent().removeClass('slidable');
                if ($(this).innerHeight() < $(this).children('.cl_com_text').innerHeight()) {
                    $(this).parent().addClass('slidable');
                }
            });
        }, 100);

    });
    $(window).trigger('resize');
    $('table, .subplan').on('keypress', '.cl_com .cl_com_text', function (e) {
        if (e.which == 13 && !e.shiftKey) {
            e.preventDefault();
            frameUpdate($(this).parents('.cl_com_main'));
        }
    });
    $('.u_bar').on('change', '#show_add_col', function () {
        if ($(this).is(':checked')) $('.table_single tr:not(.slidable_info) td:not(:visible):not(.hide_ever), .table_single tr:not(.slidable_info):not(.hide_ever) th:not(:visible):not(.hide_ever)').addClass('to_hide').show();
        else $('.table_single .to_hide').hide();
        $('.elipse').each(function () {
            $(this).css('height', 'auto');
            $(this).css('height', $(this).height());
        });
    });
    $('table').on('change', '.cash_cb', function () {
        $(this).next().next().add($(this).next().next().next()).toggle();
    });
    $('table').on('dblclick', 'tr.main', function () {
        $(this).find('.more_info button').first().click();

    });
    $('select').each(function () {
        $(this).children('option').eq($(this).data('filter-by')).prop('selected', 'selected');
    });
    filter_sm($('#sm_signups'), 7, $('#filter_select').data('filter-by'));
    //filter_case($('#cases'), 1, $('#filter_select').data('filter-by'));
    if ($('#date').length != 0)
        $('#date').datepick({ dateFormat: 'yyyy-mm-dd' });

    $('table.table_single').each(function () {
        $(this).find('th').eq($(this).data('sort-col')).data('dir', $('table.table_single').data('sort-dir'));
        tableSort($(this).find('th').eq($(this).data('sort-col')));
    });
    if (typeof filter != 'undefined')
        if (filter.auto)
            filter.filter();
    $('td, .cl_com_main').each(function () {
        $(this).html(linkify($(this).html()));
        $(this).find('*').css('font-family', 'inherit');
    });
    $('.nip-1-comp').each(function () {
        $(this).text($(this).text().replace(/\-/g, ''));
    });
});
function scrollToObject(object) {
    $('html, body').animate({ scrollTop: object.offset().top - 20 }, '300', 'swing', null);
}
var TO;
function tableShowScroll(it, col, phrase) {
    console.log(phrase);
    it.children('tbody').children('tr:even').each(function () {
        $(this).css('background-color', '#ffffff');
        clearTimeout(TO);
        if ($(this).children('td').eq(col).text() == phrase) {

            scrollToObject($(this));
            $(this).css('background-color', '#dfdfdf');
            var this_ = this;
            TO = setTimeout(function () {
                $(this_).css('background-color', '#dfdfdf');
            }, 2000);
        }
    });
}
function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();
    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;
    return [year, month, day].join('-');
}
function edit(it) {

    it.parents('tr').find('#edit_remove').hide(0);
    it.parents('tr').find('#ok_cancel').show(0);

    it.parents('tr').find('td').css({ backgroundColor: 'rgba(0,255,0,0.025)' });
    it.parents('tr').find('td.editable').css({ backgroundColor: 'rgba(0,255,0,0.5)' });
    it.parents('tr').find('td.elipse').removeClass('elipse').addClass('elipse-edit');
    it.parents('tr').find('.editable').each(function () {
        $(this).find('select').prop('disabled', false);
        $(this).data('content', $(this).html());
        var props;
        switch ($(this).prop('id')) {
            case 'phone':
                props = "data-mask='000-000-000'";
                break;
        }
        $(this).not('.select').prop("contenteditable", true);
        $(this).append("<textarea style='display:none;' name='" + $(this).prop('id') + "' " + props + "></textarea>");
        if ($(this).prop('id') == 'date') {
            _this = $(this);
            $(this).datepick({
                dateFormat: 'yyyy-mm-dd',
                onSelect: function (dateText) {

                    var date = new Date(dateText);
                    _this.text(formatDate(date)).blur();
                },
            });
        }
    });
}

function edit_kik(it) {

    it.parents('.sm_buttons').find('#edit_remove').hide(0);
    it.parents('.sm_buttons').find('#ok_cancel').show(0);
    it.parents('td').find('table td.editable_app:last-child').css({ backgroundColor: 'rgba(0,255,0,0.1)' });
    it.parents('td').find('table td.editable_app:last-child').each(function () {
        $(this).data('content', $(this).text());
        $(this).prop("contenteditable", true);
        $(this).append("<textarea style='display:none;' name='" + $(this).prop('id') + "'></textarea>");
    });
}

function checklist_submit(it) {
    if (confirm('Czy na pewno chcesz wykonać tę operację?')) {
        it.parents('.checklist_main').find('input').each(function () {
            $(this).find('textarea').text($(this).text());
        });
        if (filter != undefined)
            var form = $('<form action="' + [location.protocol, '//', location.host, location.pathname].join('') + '?' + filter.getFilterURL() + '" method="POST">');
        else
            var form = $('<form action="' + [location.protocol, '//', location.host, location.pathname].join('') + '" method="POST">');
        it.parents('.checklist_main').children('input, textarea').each(function () {
            form.append($(this).clone());
        });
        it.parents('.checklist_main').find('.other').find('input').each(function () {
            form.prepend($(this).clone());
        });
        it.parents('.checklist_main').find('.other').find('select').each(function () {
            form.prepend("<input type='hidden' name='" + $(this).prop('name') + "' value='" + $(this).prop('value') + "'/>");
        });
        it.parents('.checklist_main').find('.app-checklist').find('input, textarea').each(function () {
            form.append($(this).clone());
        });
        console.log(form);
        form.prepend("<input type='hidden' name='action' value='update-checklist'/>");
        form.appendTo($('body')).submit();
    }
}

function edit_submit(it) {
    if (confirm('Czy na pewno chcesz edytować ten wpis?')) {
        it.parents('tr').find('.editable').each(function () {
            if (!$(this).find('select').length) $(this).find('textarea').text($(this).text());
            else $(this).find('textarea').text($(this).find('select :selected').val());
        });
        if (filter != undefined)
            var form = $('<form action="' + [location.protocol, '//', location.host, location.pathname].join('') + '?' + filter.getFilterURL() + '" method="POST">');
        else
            var form = $('<form action="' + [location.protocol, '//', location.host, location.pathname].join('') + '" method="POST">');
        it.parents('tr').find('.editable, .select').children('textarea').each(function () {
            form.append($(this).clone());
        });
        form.append("<input type='hidden' name='action' value='update'/>");
        form.append("<input type='hidden' name='id' value='" + it.parents('tr').find('td:first-child').text() + "'/>");
        form.appendTo($('body')).submit();
    }
}
function edit_kik_submit(it) {
    if (confirm('Czy na pewno chcesz edytować ten wpis?')) {
        it.parents('tr').find('table td.editable_app:last-child').each(function () {
            $(this).find('textarea').text($(this).text());
        });
        if (typeof filter != undefined)
            var form = $('<form action="' + [location.protocol, '//', location.host, location.pathname].join('') + '?' + filter.getFilterURL() + '" method="POST">');
        else
            var form = $('<form action="' + [location.protocol, '//', location.host, location.pathname].join('') + '" method="POST">');
        it.parents('tr').find('.kik_content td:last-child').children('textarea').each(function () {
            form.append($(this).clone());
        });
        it.parents('tr').find('.vertical-table td:last-child').children('textarea').each(function () {
            form.append($(this).clone());
        });
        it.parents('td').find('#id').each(function () {
            form.prepend("<input type='hidden' name='id' value='" + $(this).text() + "'/>");
        });
        form.prepend("<input type='hidden' name='action' value='update-kik'/>");
        form.appendTo($('body')).submit();
    }
}
function edit_exit(it) {
    it.parents('tr').find('td').css({ backgroundColor: '' });
    it.parents('tr').find('td').find('textarea').remove();
    it.parents('tr').find('#edit_remove').show(0);
    it.parents('tr').find('#ok_cancel').hide(0);
    it.parents('tr').find('td.elipse-edit').removeClass('elipse-edit').addClass('elipse');

    it.parents('tr').find('.editable').each(function () {
        $(this).prop("contenteditable", false);
        $(this).find('select').prop('disabled', true);
        if (!$(this).find('select').length) $(this).html($(this).data('content'));
    });
}
function edit_kik_exit(it) {
    it.parents('tr').find('table td:last-child').css({ backgroundColor: 'rgba(0,255,0,0)' });
    it.parents('tr').find('table td:last-child').find('textarea').remove();
    it.parents('tr').find('#edit_remove').show(0);
    it.parents('tr').find('#ok_cancel').hide(0);
    it.parents('tr').find('table td:last-child').each(function () {
        $(this).prop("contenteditable", false);
        $(this).text($(this).data('content'));
    });
}
function row_remove(it) {
    if (confirm('Czy na pewno chcesz usunąć ten wpis?')) {
        if (filter != undefined)
            var form = $('<form action="' + [location.protocol, '//', location.host, location.pathname].join('') + '?' + filter.getFilterURL() + '" method="POST">');
        else
            var form = $('<form action="' + [location.protocol, '//', location.host, location.pathname].join('') + '" method="POST">');
        form.append("<input type='hidden' name='action' value='remove'/>");
        form.append("<input type='hidden' name='id' value='" + it.parents('tr').find('td:first-child').text() + "'/>");
        form.appendTo($('body')).submit();
    }
}
function case_close(it) {
    if (confirm('Czy na pewno chcesz zmienić status sprawy?')) {
        var form = $('<form action="' + document.location + '" method="POST">');
        form.append("<input type='hidden' name='action' value='case_close'/>");
        form.append("<input type='hidden' name='id' value='" + it.parents('tr').find('td:first-child').text() + "'/>");
        form.appendTo($('body')).submit();
    }
}
function per_end(it) {
    if (it.parents('tr').find('.awaits_payment .wrapper').text() == 'NIE') {
        if (!confirm('Ten użytkownik nie ma rozpoczętego procesu wypłacania prowizji. Czy mimo to, chcesz przeprowadzić tę operację?'))
            return;
        else {
            var form = $('<form action="' + document.location + '" method="POST">');
            form.append("<input type='hidden' name='action' value='per_end'/>");
            form.append("<input type='hidden' name='id' value='" + it.parents('tr').find('td:first-child').text() + "'/>");
            form.appendTo($('body')).submit();
        }
    } else if (confirm('Czy na pewno chcesz zakończyć proces wypłaty prowizji dla tego użytkownika?')) {
        var form = $('<form action="' + document.location + '" method="POST">');
        form.append("<input type='hidden' name='action' value='per_end'/>");
        form.append("<input type='hidden' name='id' value='" + it.parents('tr').find('td:first-child').text() + "'/>");
        form.appendTo($('body')).submit();
    }
}
function filter_sm(it, col, index) {
    console.log(index);
    it.children('tbody').children('tr').each(function () {
        if (index == 1) {
            $(this).show();
            return 0;
        } else if (index == 0) {
            var date = new Date($(this).children('td').eq(col).text())
            var now = new Date();
            date.setHours(0, 0, 0, 0);
            now.setHours(0, 0, 0, 0);
            if (date >= now) {
                $(this).show();
            } else {
                $(this).hide();
            }
            return 0;
        } else if (index > 1) {
            var date = new Date($(this).children('td').eq(col).text())
            var now = new Date($('#filter_select').children('option').eq(index).text());
            console.log("D " + date);
            console.log("N " + now);
            date.setHours(0, 0, 0, 0);
            now.setHours(0, 0, 0, 0);
            if (date >= now && date <= now) {
                $(this).show();
            } else {
                $(this).hide();
            }
            return 0;
        } else {
            $(this).hide();
        }
        //        if ($(this).children('td').eq(col).text() != phrase) {
        //            $(this).add($(this).next().add($(this).next().children().children())).hide();
        //        } else
        //            $(this).show();
    });
}
function filter_case(it, col, index) {
    console.log($('#filter_select').children('option').eq(index).data('all'));
    it.children('tbody').children('tr').each(function () {
        if ($(this).children('td').eq(col).text() == $('#filter_select').children('option').eq(index).text() || $('#filter_select').children('option').eq(index).data('all') == true) {
            $(this).show();
        } else {
            $(this).hide();
        }
        if ($(this).hasClass('slidable_info'))
            $(this).hide();
        return 0;
    });
}
