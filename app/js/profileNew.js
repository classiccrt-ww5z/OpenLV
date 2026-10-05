/**
 * Renders video info HTML for channel 2.0
 * @param {string} id The video ID
 * @param {string} title The video title
 * @param {string} uploaderDisplayName The uploader's display name
 * @param {string} uploadedOn A string representation of the video upload date
 * @param {number} views The video view count
 * @param {string} description The video's description (should already be sanitized)
 * @param {string} rating The video's rating HTML
 * @returns {string} The rendered video info HTML
 */
function renderChannel2VideoInfo(id, title, uploaderDisplayName, uploadedOn, views, description, rating) {
    const idSanitized = sanitizeHTML(id);

    return ''
        + '<a href="/watch?v='+idSanitized+'">' + sanitizeHTML(title) + '</a>'
        + '<span>'
            + 'From: <a href="/user/'+ sanitizeHTML(uploaderDisplayName) + '">' + sanitizeHTML(uploaderDisplayName) + '</a> | ' + sanitizeHTML(uploadedOn) + ' | ' + sanitizeHTML(views) + ' views'
            + '<br>'
            + '<div class="pr_pl_descr">' + sanitizeHTML(description) + '</div>'
        + '</span>'
        + '<div style=" bottom:16px; left:19px; font-size:15px; font-weight:bold">'
            + '<a href="/watch?v=' + idSanitized + '">View comments, related videos, and more</a>'
        + '</div>'
        + '<div style="position:absolute; top:17px; right:19px; padding: 2px 5px 0px; background:white; border-radius:8px">' + rating + '</div>';
}

/**
 * Renders a video card for channel 2.0
 * @param {string|null} id The video ID
 * @param {string|null} title The title
 * @param {string} thumbnail The thumbnail URL
 * @param {string|null} uploaderDisplayName The uploader's display name
 * @param {number} views The video's view count
 * @param {boolean} selected Whether the video is selected
 * @param {number?} index The card index (optional, used for Show More in some special cases)
 * @returns {string} The rendered video card
 */
function renderChannel2VideoCard(id, title, thumbnail, uploaderDisplayName, views, selected, index) {
    var titleSanitized = title === null ? '???' : sanitizeHTML(title);
    var uploaderSanitized = uploaderDisplayName === null ? '???' : sanitizeHTML(uploaderDisplayName);

    return ''
        + '<div class="mnu_vid"'+(selected ? ' id="v_sel"' : '')+' watch="'+(id === null ? '' : id)+'"'+(index === undefined ? '' : ' data-index="'+index+'"')+'>'
            + '<a href="javascript:void(0)"><img src="'+sanitizeHTML(thumbnail)+'" alt="'+titleSanitized+'" title="'+titleSanitized+'" width="100" height="75"></a>'
            + '<div>'
                + '<a href="javascript:void(0)">'+titleSanitized+'</a>'
                + '<span><a href="/user/'+uploaderSanitized+'">'+uploaderSanitized+'</a> - '+views+' view'+(views === 1 ? '' : 's')+'</span>'
            + '</div>'
        + '</div>';
}

/**
 * Renders a playlist card for channel 2.0
 * @param {string} id The playlist ID
 * @param {string} title The title
 * @param {string} thumbnail The thumbnail URL
 * @param {string} creatorDisplayName The creator's display name
 * @returns {string} The rendered playlist card
 */
function renderChannel2PlaylistCard(id, title, thumbnail, creatorDisplayName) {
    var titleSanitized = sanitizeHTML(title);
    var creatorSanitized = sanitizeHTML(creatorDisplayName);

    return ''
        + '<div class="mnu_vid" pl="'+id+'" style="padding-bottom:0">'
            + '<div class="playlist" style="float:left;margin-right:8px;width:125px;position:relative;top:2px">'
                + '<img src="'+sanitizeHTML(thumbnail)+'" alt="'+titleSanitized+'" title="'+titleSanitized+'" style="top:1.5px;width:117px;height:69px">'
            + '</div>'
            + '<div>'
                + '<a href="javascript:void(0)">'+titleSanitized+'</a>'
                + '<span><a href="/user/'+creatorSanitized+'">'+creatorSanitized+'</a></span>'
            + '</div>'
        + '</div>';
}

/**
 * Renders a channel 2.0 "Show More" button
 * @param {'uploads'|'favorites'|'playlists'} type The type of cards to show more of
 * @param {string|null} cursor Show cards after this date string (or prefix with "+" followed by a number to use as an offset)
 * @returns {string} The rendered HTML
 */
function renderChannel2ShowMoreButton(type, cursor) {
    return ''
        + '<center>'
            + '<button id="show_more" onclick="fetchAndShowMoreCards(\''+type+'\', \''+cursor+'\')">Show More</button>'
        + '</center>';
}

/**
 * @typedef Channel2VideoCard A channel 2.0 video card
 * @property {string} id The video ID
 * @property {string} title The video title
 * @property {string} thumbnail The video thumbnail URL
 * @property {string} createdOn The video creation date
 * @property {string} uploaderDisplayName The video uploader's display name
 * @property {number} views The video view count
 * @property {number} comments The video comment count
 */

/**
 * Renders HTML for channel 2.0 upload cards
 * @param {Channel2VideoCard[]} items The upload card items
 * @param {number} total The total number of cards
 * @param {string|null} selected The ID of the selected video, if any
 * @param {boolean} includeBorder Include a border at the bottom of the container element
 * @param {boolean} includeShowMore Include a "Show More" button if there are more results
 * @param {boolean} includeSearch Include search bar and options
 * @return {string} The rendered HTML
 */
function renderChannel2Uploads(items, total, selected, includeBorder, includeShowMore, includeSearch) {
    var html = '<div class="mnu_sct" style="'+(includeBorder ? '' : 'border:0')+'">';

    if(includeSearch)
        html += ''
            + '<div id="channel2-uploads-search-container">'
                + '<form id="channel2-uploads-search-form" onsubmit="submitChannel2UploadsSearch(event)">'
                    + '<input type="text" id="channel2-uploads-search-input">'
                    + '<input type="submit" id="channel2-uploads-search-submit" value="Search">'
                + '</form>'
                + '<div id="channel2-uploads-sorts">'
                    + '<span class="channel2-uploads-sort channel2-uploads-sort-desc" id="channel2-uploads-sort-date-added" onclick="toggleChannel2UploadsSearchSort(\'date-added\')">Date Added</span>'
                    + '|'
                    + '<span class="channel2-uploads-sort" id="channel2-uploads-sort-most-viewed" onclick="toggleChannel2UploadsSearchSort(\'most-viewed\')">Most Viewed</span>'
                    + '|'
                    + '<span class="channel2-uploads-sort" id="channel2-uploads-sort-most-discussed" onclick="toggleChannel2UploadsSearchSort(\'most-discussed\')">Most Discussed</span>'
                + '</div>'
            + '</div>';
    else
        html += '<div>Uploads ('+total+')</div>';

    html += '<div id="channel-cards-container">';

    /** @type {Channel2VideoCard} */
    let item;
    for(let i = 0; i < items.length; i++) {
        item = items[i];
        html += renderChannel2VideoCard(item.id, item.title, item.thumbnail, item.uploaderDisplayName, item.views, item.id === selected);
    }
    html += '</div></div>';

    if (includeShowMore !== false && total > items.length) {
        html += renderChannel2ShowMoreButton('uploads', item.createdOn + '|' + item.createdOn);
    }

    return html;
}

/**
 * Renders HTML for channel 2.0 favorite cards
 * @param {any[]} items The favorite card items
 * @param {number} total The total number of cards
 * @param {string?} selected The ID of the selected video, if any
 * @param {boolean?} includeBorder Include a border at the bottom of the container element
 * @param {boolean?} includeShowMore Include a "Show More" button if there are more results (defaults to true)
 * @return {string} The rendered HTML
 */
function renderChannel2Favorites(items, total, selected, includeBorder, includeShowMore) {
    var html = ''
        + '<div class="mnu_sct" style="'+(includeBorder ? '' : 'border:0')+'">'
            + '<div>Favorites ('+total+')</div>'
            + '<div id="channel-cards-container">';

    var item = null;
    for(var i = 0; i < items.length; i++) {
        item = items[i];
        html += renderChannel2VideoCard(item.id, item.title, item.thumbnail, item.uploaderDisplayName, item.views, item.id === selected);
    }
    html += '</div></div>';

    if(includeShowMore !== false && total > items.length)
        html += renderChannel2ShowMoreButton('favorites', item.createdOn + '|' + item.favoritedOn);

    return html;
}

/**
 * Renders HTML for channel 2.0 playlist cards
 * @param {any[]} items The playlist card items
 * @param {number} total The total number of cards
 * @param {boolean?} includeBorder Include a border at the bottom of the container element
 * @param {boolean?} includeShowMore Include a "Show More" button if there are more results (defaults to true)
 * @return {string} The rendered HTML
 */
function renderChannel2Playlists(items, total, includeBorder, includeShowMore) {
    var html = ''
        + '<div class="mnu_sct" style="'+(includeBorder ? '' : 'border:0;')+'margin:0;">'
            + '<div>Playlists ('+total+')</div>'
            + '<div id="channel-cards-container">';

    var item = null;
    for(var i = 0; i < items.length; i++) {
        item = items[i];
        html += renderChannel2PlaylistCard(item.id, item.title, item.thumbnail, item.creatorDisplayName);
    }
    html += '</div></div>';

    if (includeShowMore !== false && total > items.length) {
        html += renderChannel2ShowMoreButton('playlists', item.createdOn + '|' + item.createdOn);
    }

    return html;
}

/**
 * Returns the current channel 2.0 uploads search query
 * @returns {string} The current uploads search query
 */
function getChannel2UploadsSearchQuery() {
    var inputElem = document.getElementById('channel2-uploads-search-input');
    if(!inputElem)
        return '';

    var query = inputElem.dataset.query;

    return query ? query : '';
}

/**
 * Returns the current channel 2.0 uploads search order
 * @returns {string} The current uploads search order
 */
function getChannel2UploadsSearchOrder() {
    var dateAddedElem = document.getElementById('channel2-uploads-sort-date-added');
    var mostViewedElem = document.getElementById('channel2-uploads-sort-most-viewed');
    var mostDiscElem = document.getElementById('channel2-uploads-sort-most-discussed');

    // If any of the elements are missing, don't do anything; return default order
    if (!dateAddedElem || !mostViewedElem || !mostDiscElem) {
        return 'createdOn DESC';
    }

    // This is a bit tedious, but it saves from maintaining sorting state somewhere else and already handles styling (kills two birds with one stone)
    if (dateAddedElem.classList.contains('channel2-uploads-sort-asc')) {
        return 'createdOn ASC';
    } else if(dateAddedElem.classList.contains('channel2-uploads-sort-desc')) {
        return 'createdOn DESC';
    } else if (mostViewedElem.classList.contains('channel2-uploads-sort-asc')) {
        return 'views ASC';
    } else if (mostViewedElem.classList.contains('channel2-uploads-sort-desc')) {
        return 'views DESC';
    } else if (mostDiscElem.classList.contains('channel2-uploads-sort-asc')) {
        return 'comments ASC';
    } else if (mostDiscElem.classList.contains('channel2-uploads-sort-desc')) {
        return 'comments DESC';
    } else {
        return 'createdOn DESC';
    }
}

/**
 * Toggles a channel 2.0 uploads search sort type
 * @param {'date-added'|'most-viewed'|'most-discussed'} type The sort type
 */
function toggleChannel2UploadsSearchSort(type) {
    var btns = [
        document.getElementById('channel2-uploads-sort-date-added'),
        document.getElementById('channel2-uploads-sort-most-viewed'),
        document.getElementById('channel2-uploads-sort-most-discussed')
    ];
    var typeElem = document.getElementById('channel2-uploads-sort-'+type);

    // Remove state from existing buttons
    for(var i = 0; i < btns.length; i++) {
        var btn = btns[i];
        if(btn.id !== typeElem.id) {
            btn.classList.remove('channel2-uploads-sort-asc');
            btn.classList.remove('channel2-uploads-sort-desc');
        }
    }

    // Set state on the type button
    if(typeElem.classList.contains('channel2-uploads-sort-desc')) {
        typeElem.classList.remove('channel2-uploads-sort-desc');
        typeElem.classList.add('channel2-uploads-sort-asc');
    } else {
        typeElem.classList.remove('channel2-uploads-sort-asc');
        typeElem.classList.add('channel2-uploads-sort-desc');
    }

    doChannel2UploadsSearch();
}

/**
 * Performs a channel 2.0 uploads search using the current search state
 * @param {(() => void)?} callback The callback to be called when the query is finished (regardless of status)
 */
function doChannel2UploadsSearch(callback) {
    var query = getChannel2UploadsSearchQuery();

    var searchInput = document.getElementById('channel2-uploads-search-input');
    var searchBtn = document.getElementById('channel2-uploads-search-submit');
    var cardsContainer = $('#channel-cards-container');
    var showMoreBtn = document.getElementById('show_more');

    // Modify elements before search
    searchInput.disabled = true;
    searchBtn.disabled = true;
    if(showMoreBtn)
        showMoreBtn.parentElement.removeChild(showMoreBtn);

    // Perform query
    var order = getChannel2UploadsSearchOrder();
    var reqData = {
        user: getChannelUsername(),
        includeUploads: true,
        uploadsOrder: order
    };
    if(query.length >= 3)
        reqData.uploadsQuery = query;
    apiGet('/ajax/get_channel_cards', reqData, function(err, res) {
        // Fire callback before doing anything else
        if(callback)
            callback();

        searchInput.disabled = false;
        searchBtn.disabled = false;
        searchInput.focus();

        if(logApiErrorsIfAny(err, res, 'Failed to search channel uploads'))
            return;

        cardsContainer.html('');

        var uploads = res.data.uploads;
        var cards = uploads.items;
        for(var i = 0; i < cards.length; i++) {
            var card = cards[i];
            card.index = i+1;
            cardsContainer.append(renderChannel2VideoCard(card.id, card.title, card.thumbnail, card.uploaderDisplayName, card.views, card.id === $("#pl_url").html(), i+1));
        }

        // Append "Show More" button if there are more uploads, and if we're only sorting by upload date
        if(cards.length < uploads.total) {
            const lastCard = cards[cards.length - 1];

            const uplCol = order.split(' ')[0];

            let cursor = lastCard.createdOn + '|';
            if (uplCol === 'createdOn') {
                cursor += lastCard.createdOn;
            } else if (uplCol === 'views') {
                cursor += lastCard.views;
            } else if (uplCol === 'comments') {
                cursor += lastCard.comments;
            }

            $('#pl_list').append(renderChannel2ShowMoreButton('uploads', cursor));
        }
    });
}

/**
 * Handles a channel 2.0 uploads search form submission
 * @param {Event} event The event that triggered the form submission
 */
function submitChannel2UploadsSearch(event) {
    event.preventDefault();

    var inputElem = document.getElementById('channel2-uploads-search-input');
    var query = inputElem.value.trim();

    // Don't accept non-blank queries under 3 chars
    if(query.length < 3 && query.length > 0) {
        alert('Your search query must be at least 3 characters long');
        return;
    }

    // Set query
    inputElem.dataset.query = query;

    // Do search
    doChannel2UploadsSearch();
}

/**
 * Returns the current channel 2.0 channel's username
 * @returns {string} The channel's username
 */
function getChannelUsername() {
    return document.getElementById('ch_user').innerHTML;
}

function c_move_up(e) {
    var t = $("#" + e).prev().attr("id");
    if (void 0 != t && $("#" + t).hasClass("hddn"))
        for (var s = !1; 0 == s;) {
            var l = $("#" + t).prev().attr("id");
            s = !$("#" + l).hasClass("hddn"), t = l
        }
    if (void 0 != t) {
        $("#" + e).swapWith("#" + t);
        var a = $("#" + e).attr("module"),
            n = $("#" + t).attr("module");
        a.indexOf("_r") >= 0 ? (a = a.replace("_r", ""), n = n.replace("_r", ""), vertical_r = vertical_r.replace(a, n), vertical_r = vertical_r.replace(n, a), $.ajax({
            type: "POST",
            url: "/ajax/change_vert",
            data: {
                type: 0,
                vert: vertical_r
            }
        })) : (a = a.replace("_l", ""), n = n.replace("_l", ""), vertical_l = vertical_l.replace(a, n), vertical_l = vertical_l.replace(n, a), $.ajax({
            type: "POST",
            url: "/ajax/change_vert",
            data: {
                type: 1,
                vert: vertical_l
            }
        })), i
    }
}

function c_move_down(e) {
    var t = $("#" + e).next().attr("id");
    if (void 0 != t && $("#" + t).hasClass("hddn"))
        for (var s = !1; 0 == s;) {
            var l = $("#" + t).next().attr("id");
            s = !$("#" + l).hasClass("hddn"), t = l
        }
    if (void 0 != t) {
        $("#" + e).swapWith("#" + t);
        var a = $("#" + e).attr("module"),
            n = $("#" + t).attr("module");
        a.indexOf("_r") >= 0 ? (a = a.replace("_r", ""), n = n.replace("_r", ""), vertical_r = vertical_r.replace(n, a), vertical_r = vertical_r.replace(a, n), $.ajax({
            type: "POST",
            url: "/ajax/change_vert",
            data: {
                type: 0,
                vert: vertical_r
            }
        })) : (a = a.replace("_l", ""), n = n.replace("_l", ""), vertical_l = vertical_l.replace(n, a), vertical_l = vertical_l.replace(a, n), $.ajax({
            type: "POST",
            url: "/ajax/change_vert",
            data: {
                type: 1,
                vert: vertical_l
            }
        }))
    }
}

function get_video_info(id) {
    // Ignore if the ID is blank
    if(id === '')
        return;

    $('#pl_inf_i').addClass('pl_nav_sel');
    $('#pl_inf_f').removeClass('pl_nav_sel');
    $('#pl_inf_s').removeClass('pl_nav_sel');
    $('#pl_inf_p').removeClass('pl_nav_sel');
    $('#pl_inf_fl').removeClass('pl_nav_sel');

    apiGet('/ajax/get_video_info', { id: id }, function (err, res) {
        

        var data = res.data;

        $("#pl_inf").html(renderChannel2VideoInfo(data.url, data.title, data.displayname, data.uploaded_on, data.views, data.description, data.rating));

        // Collect data
        var duration = data.length;
        var videoUrl = '/watch?v='+id;
        var src = '/usfi/v/'+id+'.'+data.file+'.mp4';
        var hdSrc = res.data.hd ? '/usfi/v/'+id+'.'+data.file+'.720.mp4' : null;
        var preview = '/usfi/thmp/'+id+'.jpg';

        if(vplayer === 2) {
            window.modernPlayer = new ModernPlayer({
                instance: jwplayer('vlplayer'),
                duration: duration,
                videoUrl: videoUrl,
                src: src,
                hdsrc: hdSrc,
                startinhd: getCookie('vlphd') === '1',
                preview: preview,
                autoplay: true
            });
        } else {
            window.vlp.change({
                src: src,
                hdsrc: hdSrc,
                preview: preview,
                duration: duration,
                videoUrl: videoUrl,
                autoplay: true
            });
        }
    });
}

function fntpreview() {
    var e = $("#ch_fnt").val();
    if (0 == e) var t = "Arial";
    else if (1 == e) t = "Georgia";
    else if (2 == e) t = "Times New Roman";
    else if (3 == e) t = "Comic Sans MS";
    else if (4 == e) t = "Impact";
    else if (5 == e) t = "Tahoma";
    else if (6 == e) t = "Courier New";
    $(".wrapper").css("font-family", t)
}

function copyToClipboard(e) {
    var t = $("<input>");
    $("body").append(t), t.val($(e).text()).select(), document.execCommand("copy"), t.remove()
}

function add_playlist() {
    var e = $("#pl_sel option:selected").val();
    $.ajax({
        type: "POST",
        url: "/ajax/add_playlist",
        data: {
            pid: e
        },
        success: function(e) {
            "success" == e.response ? ($("#pl_text").remove(), $("#pl_box").append('<div class="pl_row" id="pl_' + e.purl + '"><div class="playlist"><a href="/playlist?p=' + e.purl + '"><img src="/usfi/thmp/' + e.thumbnail + '.jpg"></a></div><div class="pl_info"><a href="/playlist?p=' + e.purl + '">' + e.title + '</a><em>No Description...</em></div><div><a href="javascript:void(0)">Play All</a><br><a href="javascript:void(0)">Share</a><br><a href="javascript:void(0)" onclick="remove_pl(\'' + e.purl + "')\">Remove</a></div></div>"), $("#pl_box").css("padding-bottom", "1px")) : "too_many" == e.response ? alert("You can display 3 playlists on your channel!") : "already" == e.response && alert("You already have this playlist displayed on your channel!")
        }
    })
}

function remove_pl(e) {
    $("#pl_" + e).remove(), 0 == $(".pl_row").length && $("#pl_box").append('<div id="pl_text" style="text-align: center;font-size: 14px;margin-bottom:4px">You have not added any playlists yet!</div>'), $.ajax({
        type: "POST",
        url: "/ajax/remove_playlist",
        data: {
            pid: e
        }
    })
}

function add_to_playlist() {
    var e = $("#playlist_select option:selected").val(),
        t = $("#pl_url").html();
    void 0 != e ? $.ajax({
        type: "POST",
        url: "/ajax/add_to_playlist",
        data: {
            pid: e,
            id: t
        },
        success: function(e) {
            "success" == e.response ? $("#pl_inf").html("<div style='font-size:14px;text-align:center'><div style='font-size:16px;margin-bottom:10px'>Video successfully added to playlist!</div></div>") : "already" == e.response && alert("Video is already in this playlist!")
        }
    }) : alert("Please select a playlist!")
}

function move_hor(e, t) {
    $("#" + e).toggleClass("hddn"), $("#" + t).toggleClass("hddn"), $.ajax({
        type: "POST",
        url: "/ajax/move_hor_module",
        data: {
            Module: e
        }
    })
}

function update_cc_privacy(e) {
    if (0 == e) {
        if ($("#cc_setting1").is(":checked")) var t = 0;
        else if ($("#cc_setting2").is(":checked")) t = 1;
        else if ($("#cc_setting3").is(":checked")) t = 2
    } else if ($("#cc_setting4").is(":checked")) t = 0;
    else if ($("#cc_setting5").is(":checked")) t = 1;
    else if ($("#cc_setting6").is(":checked")) t = 2;
    $.ajax({
        type: "POST",
        url: "/ajax/update_comment_privacy",
        data: {
            setting: t
        },
        success: function(e) {
            $("#edit_cc").toggleClass("hddn"), alert("Channel comment privacy successfully updated!")
        }
    })
}

function delete_cc(e) {
    $("#cc2_" + e).remove();
    $("#cc_" + e).remove();

    if ($(".ch_cmt").length === 0) {
        $("#ch_cmt_sct").html('<div id="no_comments" style="margin:7px 0;text-align: center;font-size: 13px">There are no comments for this user.</div>');
        $("#ch_cmt_sct2").html('<div id="no_comments2" style="margin:7px 0;text-align: center;font-size: 13px">There are no comments for this user.</div>');
        $(".prbx_in .cc_pagination").remove();
    }

    if ($(".chn_cmt_sct").length === 0) {
        $("#channel_comments").html('<div id="no_comments">There are no comments for this user.</div>');
        $("#channel_comments2").html('<div id="no_comments2" style="text-align: center;font-size:13px;margin:15px 0">There are no comments for this user.</div>');
        $("#channel_comments").toggleClass("no_border");
        $(".in_box .cc_pagination").remove();
    }

    apiPost('/ajax/delete_ch_comment', { id: e }, function(err, res) {
        if (err && logApiErrorsIfAny(err, res, 'Failed to delete comment')) {
            return;
        } else if (!res.success) {
            alert(res.error.message);
            return;
        }

        var t = document.getElementById("cc_count").innerText;
        if (t.indexOf(",") > -1) {
            t = t.replace(",", "");
            t = (--t).toLocaleString("us");
        } else {
            t--;
        }

        $("#cc_count").html(t);
    });
}

function save_information() {
    var e = $("#check_name").is(":checked"),
        t = $("#check_website").is(":checked"),
        s = $("#check_description").is(":checked"),
        l = $("#check_occupation").is(":checked"),
        a = $("#check_schools").is(":checked"),
        n = $("#check_interests").is(":checked"),
        r = $("#check_movies").is(":checked"),
        i = $("#check_music").is(":checked"),
        o = $("#check_books").is(":checked"),
        c = $("#check_subs").is(":checked"),
        d = $("#check_last").is(":checked"),
        _ = $("#check_age").is(":checked"),
        m = $("#check_country").is(":checked"),
        h = $("#check_subs2").is(":checked"),
        v = $("#name_value").val(),
        p = $("#website_value").val(),
        u = $("#description_value").val(),
        f = $("#occupation_value").val(),
        g = $("#schools_value").val(),
        b = $("#interests_value").val(),
        y = $("#movies_value").val(),
        C = $("#music_value").val(),
        x = $("#books_value").val(),
        w = $("#country option:selected").val();
    $.ajax({
        type: "POST",
        url: "/ajax/update_profile",
        data: {
            Subs2_Checked: h,
            Country_Value: w,
            Country_Checked: m,
            Age_Checked: _,
            Last_Checked: d,
            Subs_Checked: c,
            Name_Checked: e,
            Website_Checked: t,
            Description_Checked: s,
            Occupation_Checked: l,
            Schools_Checked: a,
            Interests_Checked: n,
            Movies_Checked: r,
            Music_Checked: i,
            Books_Checked: o,
            Name_Value: v,
            Website_Value: p,
            Description_Value: u,
            Occupation_Value: f,
            Schools_Value: g,
            Interests_Value: b,
            Movies_Value: y,
            Music_Value: C,
            Books_Value: x
        },
        success: function(e) {
            "error" !== e ? document.location.reload(!0) : alert("Your information isn't valid!")
        }
    })
}

/**
 * Fetches data for and renders the channel 2.0 sidebar
 * @param {'uploads'|'favorites'|'playlists'|'all'} type The type of cards to fetch
 */

function fetchAndRenderChannel2Sidebar(type) {
    var username = getChannelUsername();
    var videoId = $("#pl_url").html();

    var includeAll = type === 'all';
    var includeUploads = type === 'uploads' || includeAll;
    var includeFavorites = type === 'favorites' || includeAll;
    var includePlaylists = type === 'playlists' || includeAll;

    apiGet('/ajax/get_channel_cards', {
        user: username,
        includeUploads: includeUploads,
        includeFavorites: includeFavorites,
        includePlaylists: includePlaylists,
        uploadsLimit: includeAll ? 3 : 16,
        favoritesLimit: includeAll ? 3 : 16,
        playlistsLimit: includeAll ? 3 : 16
    }, function(err, res) {
      
        var html = '';

        if(type === 'uploads') {
            var uploads = res.data.uploads;
            if(uploads.items.length > 0) {
                html = renderChannel2Uploads(uploads.items, uploads.total, videoId, false, true, true);
            }
        } else if(type === 'favorites') {
            var favorites = res.data.favorites;
            if(favorites.items.length > 0) {
                html = renderChannel2Favorites(favorites.items, favorites.total, videoId);
            }
        } else if(type === 'playlists') {
            var playlists = res.data.playlists;
            if(playlists.items.length > 0) {
                html = renderChannel2Playlists(playlists.items, playlists.total);
            }
        } else if(type === 'all') {
            var uploads = res.data.uploads;
            var favorites = res.data.favorites;
            var playlists = res.data.playlists;

            if(uploads.items.length > 0) html += renderChannel2Uploads(uploads.items, uploads.total, videoId, true, false);
            if(favorites.items.length > 0) html += renderChannel2Favorites(favorites.items, favorites.total, videoId, true, false);
            if(playlists.items.length > 0) html += renderChannel2Playlists(playlists.items, playlists.total, false);
        }

        $('#pl_list').html(html);
        $('.pr_pl_mnu').animate({ scrollTop: 0 }, 'fast');
    });
}


function getTimeHash() {
    var e, t = 0;
    return (e = window.location.href.indexOf("#t=")) >= 0 ? (t = window.location.href.substr(e + 3), parseInt(t)) : 0
}

function edit_channel_info() {
    "none" == $("#ch_edit_info").css("display") ? ($("#ch_info_sct").css("display", "none"), $("#ch_edit_info").css("display", "block")) : ($("#ch_edit_info").css("display", "none"), $("#ch_info_sct").css("display", "block"))
}

function post_bulletin() {
    var e = document.getElementById("bulletin").value,
        t = document.getElementById("bulletin2").value,
        s = $("#ch_displayname").html();
    if (e.length > 0 && e.length < 501 || t.length > 0 && t.length < 501) {
        document.getElementById("bulletin").value = "", document.getElementById("bulletin2").value = "";
        var l = new FormData;
        if (window.XMLHttpRequest) var a = new XMLHttpRequest;
        else if (window.ActiveXObject) a = new ActiveXObject("Microsoft.XMLHTTP");
        e.length > 0 ? l.append("bulletin", e) : l.append("bulletin", t), a.open("POST", "/ajax/post_bulletin"), a.send(l), t.length > 0 ? (document.getElementById("no_ra2") && (document.getElementById("no_ra2").outerHTML = ""), document.getElementById("ra_in2").style.display = "table", document.getElementById("ra_in2").innerHTML = '<tr><td valign="top" width="21"><img src="/img/ra1.png"></td><td><strong>' + sanitizeHTML(s) + "</strong> " + sanitizeHTML(t) + ' <span>(1 second ago)</span></td><td width="21"><a style="text-decoration: none" href="javascript:void(0)">X</a></td> </tr>' + document.getElementById("ra_in2").innerHTML) : (document.getElementById("no_ra") && (document.getElementById("no_ra").outerHTML = ""), document.getElementById("ra_in").style.display = "table", document.getElementById("ra_in").innerHTML = '<tr><td valign="top" width="21"><img src="/img/ra1.png"></td><td><strong>' + sanitizeHTML(s) + "</strong> " + sanitizeHTML(e) + ' <span>(1 second ago)</span></td><td width="21"><a style="text-decoration: none" href="javascript:void(0)">X</a></td> </tr>' + document.getElementById("ra_in").innerHTML)
    } else alert("Bulletins can't be empty!")
}

function delete_bulletin(e) {
    document.getElementById("b_" + e).outerHTML = "", document.getElementById("b2_" + e).outerHTML = "";
    var t = new FormData;
    if (window.XMLHttpRequest) var s = new XMLHttpRequest;
    else if (window.ActiveXObject) s = new ActiveXObject("Microsoft.XMLHTTP");
    t.append("bulletin", e), s.open("POST", "/ajax/delete_bulletin"), s.send(t)
}

function add_ft_channel() {
    var e = document.getElementById("channel_add").value,
        t = document.getElementById("channel_add2").value;
    if (e.length > 1 || t.length > 1) {
        var s = new FormData;
        if (window.XMLHttpRequest) var l = new XMLHttpRequest;
        else if (window.ActiveXObject) l = new ActiveXObject("Microsoft.XMLHTTP");
        0 != e.length ? s.append("user", e) : s.append("user", t), l.addEventListener("load", add_ft_channel_succ, !1), l.open("POST", "/ajax/add_ft_channel"), l.send(s)
    } else alert("You must type in a valid channel!")
}

function add_ft_channel_succ(e) {
    if (e.target.responseText.length > 5) {
        $("#add_fttxt").css("display", "none");
        var t = JSON.parse(e.target.responseText);
        document.getElementById("fc").innerHTML = document.getElementById("fc").innerHTML + '<div class="fc_sct"> <a href="/user/' + t.displayname + '">' + t.displayname + '</a><img src="' + t.avatar + '" class="avt pr_avt" width="50" height="50"><br><a href="javascript:void(0)" onclick="remove_ft(' + t.username + ')">Remove</a><br>Videos: ' + t.videos + "<br>Video Views: " + t.video_views + "<br>Subscribers: " + t.subscribers + " </div>", document.getElementById("fc2").innerHTML = document.getElementById("fc2").innerHTML + '<div class="fc_sct2" id="fc2_' + t.username + '"><img src="' + t.avatar + '" class="avt pr_avt" width="64" height="64"><div style="float:left;width:390px"> <a href="/user/' + t.displayname + '">' + t.displayname + "</a><br> " + t.channel_description + ' <br> <a href="javascript:void(0)" onclick="remove_ft(\'' + t.username + "')\">Remove</a><br> </div> <div>Videos: " + t.videos + "<br>Video Views: " + t.video_views + "<br>Subscribers: " + t.subscribers + " </div> </div>"
    } else "u_d" == e.target.responseText ? alert("User doesn't exist!") : "u_m" == e.target.responseText ? alert("You can have at most 8 featured channels!") : "u_e" == e.target.responseText && alert("You already have this channel featured!")
}

function remove_ft(e) {
    if (e.length > 1) {
        var t = new FormData;
        if (window.XMLHttpRequest) var s = new XMLHttpRequest;
        else if (window.ActiveXObject) s = new ActiveXObject("Microsoft.XMLHTTP");
        t.append("user", e), s.open("POST", "/ajax/delete_ft_channel"), s.send(t), document.getElementById("fc_" + e).outerHTML = "", document.getElementById("fc2_" + e).outerHTML = ""
    }
}

function save_ft_title(e) {
    var t = $("#ft_title_change").val(),
        s = $("#ft_title_change2").val();
    t.length > 0 || s.length > 0 ? 0 == e && t.length > 0 ? ($("#ft_title").html($("#ft_title_change").val()), $("#ft_title2").html($("#ft_title_change").val()), $("#ft_title_change2").val($("#ft_title_change").val())) : 1 == e && t.length > 0 ? ($("#ft_title").html($("#ft_title_change2").val()), $("#ft_title2").html($("#ft_title_change2").val()), $("#ft_title_change").val($("#ft_title_change2").val()), t = s) : ($("#ft_title").html("Featured Channels"), $("#ft_title2").html("Featured Channels"), $("#ft_title_change").val("Featured Channels"), $("#ft_title_change2").val("Featured Channels")) : ($("#ft_title").html("Featured Channels"), $("#ft_title2").html("Featured Channels"), $("#ft_title_change").val("Featured Channels"), $("#ft_title_change2").val("Featured Channels")), $.ajax({
        type: "POST",
        url: "/ajax/save_ft_title",
        data: {
            title: t
        }
    })
}

function hex2rgba(e, t) {
    return e = e.replace("#", ""), r = parseInt(e.substring(0, e.length / 3), 16), g = parseInt(e.substring(e.length / 3, 2 * e.length / 3), 16), b = parseInt(e.substring(2 * e.length / 3, 3 * e.length / 3), 16), result = "rgba(" + r + "," + g + "," + b + "," + t / 100 + ")", result
}

function bg(e) {
    $("#gbg").css("background-color", hex2rgba("#" + e, 100))
}

function wrapper(e) {
    $(".ob_col").css("background-color", hex2rgba("#" + e, trans1)), $("#v_sel").css("background-color", hex2rgba("#" + e, 100)), $(".pr_pl_title_sty").css("border-left-color", hex2rgba("#" + e, trans1)), $(".pr_inf_sct").css("border-bottom-color", hex2rgba("#" + e, trans1)), $(".ra tr td").css("border-bottom-color", hex2rgba("#" + e, trans1)), $(".ra").css("border-color", hex2rgba("#" + e, trans1)), $(".mnu_sct").css("border-bottom-color", hex2rgba("#" + e, trans1)), $("#no_comments, #channel_comments").css("border-color", hex2rgba("#" + e, trans1))
}

function wrapper_text(e) {
    $(".ob_col").css("color", hex2rgba("#" + e, 100))
}

function wrapper_links(e) {
    $(".pr_tp_pl_nav a").css("color", hex2rgba("#" + e, 100))
}

function in_bg(e) {
    $(".ib_col").css("background-color", hex2rgba("#" + e, trans2)), $("#pl_toggle_sel b").css("background-color", hex2rgba("#" + e, 100)), $("#pl_toggle_sel em").css("background-color", hex2rgba("#" + e, 100)), $(".pl_nav_sel_hd").attr("style", "color: " + hex2rgba("#" + e, 100) + " !important"), $("#nav_ind").css("border-bottom-color", hex2rgba("#" + e, trans2))
}

function in_hd(e) {
    $(".box_title").css("color", hex2rgba("#" + e, 100))
}

function in_link(e) {
    $(".ib_col a").css("color", hex2rgba("#" + e, 100)), $(".pr_avt").css("border-color", hex2rgba("#" + e, 100)), $(".pr_pl_toggles a > i > b, em").css("background-color", hex2rgba("#" + e, 100))
}

function in_text(e) {
    $(".ib_col").css("color", hex2rgba("#" + e, 100)), $(".pl_nav_sel_hd").css("background", hex2rgba("#" + e, 100)), $("#pl_toggle_sel").css("background", hex2rgba("#" + e, 100))
}

function delete_background() {
    document.getElementById("bg_delete").disabled = !0;
    var e = new FormData;
    if (window.XMLHttpRequest) var t = new XMLHttpRequest;
    else if (window.ActiveXObject) t = new ActiveXObject("Microsoft.XMLHTTP");
    e.append("bg", "ar"), t.addEventListener("load", bg_del_comp, !1), t.open("POST", "/ajax/delete_background"), t.send(e)
}

function bg_del_comp() {
    document.getElementById("bg_upload").style.display = "block", document.getElementById("bg_delete").style.display = "none", document.getElementById("bg_info").style.display = "block", $("#gbg").css("background-image", "url('')")
}

function theme_select(e) {
    if ("grey" == e) {
        $("#default").addClass("theme_sel"), $("#blue").removeClass("theme_sel"), $("#red").removeClass("theme_sel"), $("#yellow").removeClass("theme_sel"), $("#green").removeClass("theme_sel"), $("#black").removeClass("theme_sel"), $("#pink").removeClass("theme_sel"), $("#fire").removeClass("theme_sel"), $("#stealth").removeClass("theme_sel"), $("#custom").removeClass("theme_sel"), $("#theme_title").html("Grey"), $("#theme_selectnum").val("0");
        var t = "CCCCCC",
            s = "999999",
            l = "000000",
            a = "0000cc",
            n = "eeeeff",
            r = "000000",
            i = "0000cc",
            o = "333333"
    } else if ("blue" == e) {
        $("#default").removeClass("theme_sel"), $("#blue").addClass("theme_sel"), $("#red").removeClass("theme_sel"), $("#yellow").removeClass("theme_sel"), $("#green").removeClass("theme_sel"), $("#black").removeClass("theme_sel"), $("#pink").removeClass("theme_sel"), $("#fire").removeClass("theme_sel"), $("#stealth").removeClass("theme_sel"), $("#custom").removeClass("theme_sel"), $("#theme_title").html("Blue"), $("#theme_selectnum").val("1");
        t = "003366", s = "0066CC", l = "ffffff", a = "0000CC", n = "3D8BD8", r = "ffffff", i = "99C2EB", o = "ffffff"
    } else if ("red" == e) {
        $("#default").removeClass("theme_sel"), $("#blue").removeClass("theme_sel"), $("#red").addClass("theme_sel"), $("#yellow").removeClass("theme_sel"), $("#green").removeClass("theme_sel"), $("#black").removeClass("theme_sel"), $("#pink").removeClass("theme_sel"), $("#fire").removeClass("theme_sel"), $("#stealth").removeClass("theme_sel"), $("#custom").removeClass("theme_sel"), $("#theme_title").html("Red"), $("#theme_selectnum").val("2");
        t = "660000", s = "990000", l = "FFFFFF", a = "FF0000", n = "660000", r = "FFFFFF", i = "FF0000", o = "FFFFFF"
    } else if ("yellow" == e) {
        $("#default").removeClass("theme_sel"), $("#blue").removeClass("theme_sel"), $("#red").removeClass("theme_sel"), $("#yellow").addClass("theme_sel"), $("#green").removeClass("theme_sel"), $("#black").removeClass("theme_sel"), $("#pink").removeClass("theme_sel"), $("#fire").removeClass("theme_sel"), $("#stealth").removeClass("theme_sel"), $("#custom").removeClass("theme_sel"), $("#theme_title").html("Sunlight"), $("#theme_selectnum").val("3");
        t = "FFE599", s = "E69138", l = "FFFFFF", a = "FFD966", n = "FFD966", r = "E69138", i = "E69138", o = "E69138"
    } else if ("green" == e) {
        $("#default").removeClass("theme_sel"), $("#blue").removeClass("theme_sel"), $("#red").removeClass("theme_sel"), $("#yellow").removeClass("theme_sel"), $("#green").addClass("theme_sel"), $("#black").removeClass("theme_sel"), $("#pink").removeClass("theme_sel"), $("#fire").removeClass("theme_sel"), $("#stealth").removeClass("theme_sel"), $("#custom").removeClass("theme_sel"), $("#theme_title").html("Forest"), $("#theme_selectnum").val("4");
        t = "274E13", s = "38761D", l = "ffffff", a = "FFFFFF", n = "6AA84F", r = "274E13", i = "38761D", o = "274E13"
    } else if ("black" == e) {
        $("#default").removeClass("theme_sel"), $("#blue").removeClass("theme_sel"), $("#red").removeClass("theme_sel"), $("#yellow").removeClass("theme_sel"), $("#green").removeClass("theme_sel"), $("#black").addClass("theme_sel"), $("#pink").removeClass("theme_sel"), $("#fire").removeClass("theme_sel"), $("#stealth").removeClass("theme_sel"), $("#custom").removeClass("theme_sel"), $("#theme_title").html("8-Bit"), $("#theme_selectnum").val("5");
        t = "666666", s = "444444", l = "FFFFFF", a = "FF0000", n = "000000", r = "AAAAAA", i = "FF0000", o = "666666"
    } else if ("pink" == e) {
        $("#default").removeClass("theme_sel"), $("#blue").removeClass("theme_sel"), $("#red").removeClass("theme_sel"), $("#yellow").removeClass("theme_sel"), $("#green").removeClass("theme_sel"), $("#black").removeClass("theme_sel"), $("#pink").addClass("theme_sel"), $("#fire").removeClass("theme_sel"), $("#stealth").removeClass("theme_sel"), $("#custom").removeClass("theme_sel"), $("#theme_title").html("Princess"), $("#theme_selectnum").val("6");
        t = "ff99cc", s = "aa66cc", l = "ffffff", a = "351C75", n = "ffffff", r = "8a2c87", i = "351C75", o = "333366"
    } else if ("fire" == e) {
        $("#default").removeClass("theme_sel"), $("#blue").removeClass("theme_sel"), $("#red").removeClass("theme_sel"), $("#yellow").removeClass("theme_sel"), $("#green").removeClass("theme_sel"), $("#black").removeClass("theme_sel"), $("#pink").removeClass("theme_sel"), $("#fire").addClass("theme_sel"), $("#stealth").removeClass("theme_sel"), $("#custom").removeClass("theme_sel"), $("#theme_title").html("Fire"), $("#theme_selectnum").val("7");
        t = "660000", s = "FF0000", l = "ffffff", a = "FFFF00", n = "FF9900", r = "FFFF00", i = "FFDBA6", o = "ffffff"
    } else if ("stealth" == e) {
        $("#default").removeClass("theme_sel"), $("#blue").removeClass("theme_sel"), $("#red").removeClass("theme_sel"), $("#yellow").removeClass("theme_sel"), $("#green").removeClass("theme_sel"), $("#black").removeClass("theme_sel"), $("#pink").removeClass("theme_sel"), $("#fire").removeClass("theme_sel"), $("#stealth").addClass("theme_sel"), $("#custom").removeClass("theme_sel"), $("#theme_title").html("Stealth"), $("#theme_selectnum").val("8");
        t = "000000", s = "444444", l = "000000", a = "CCCCCC", n = "666666", r = "000000", i = "444444", o = "444444"
    } else "custom" == e && ($("#default").removeClass("theme_sel"), $("#blue").removeClass("theme_sel"), $("#red").removeClass("theme_sel"), $("#yellow").removeClass("theme_sel"), $("#green").removeClass("theme_sel"), $("#black").removeClass("theme_sel"), $("#pink").removeClass("theme_sel"), $("#fire").removeClass("theme_sel"), $("#stealth").removeClass("theme_sel"), $("#custom").addClass("theme_sel"), $("#theme_title").html("Custom"), "none" == document.getElementById("advanced_customization").style.display && show_advanced_custom(), $("#theme_selectnum").val("9"));
    "custom" !== e && (document.getElementById("ed_bg_color").jscolor.fromString(t), document.getElementById("ed_wrp_color").jscolor.fromString(s), document.getElementById("ed_wrptxt_color").jscolor.fromString(l), document.getElementById("ed_wrplnk_color").jscolor.fromString(a), document.getElementById("in_wrapper").jscolor.fromString(n), document.getElementById("col_wrapper").jscolor.fromString(r), document.getElementById("in_link").jscolor.fromString(i), document.getElementById("in_txt").jscolor.fromString(o), bg(t), wrapper(s), wrapper_text(l), wrapper_links(a), in_hd(r), in_link(i), in_bg(n), in_text(o))
}

function show_advanced_custom() {
    "none" == document.getElementById("advanced_customization").style.display ? (document.getElementById("advanced_customization").style.display = "block", document.getElementById("show_advanced_btn").innerHTML = "hide advanced options") : (document.getElementById("advanced_customization").style.display = "none", document.getElementById("show_advanced_btn").innerHTML = "show advanced options")
}

/** @typedef {'uploads'|'favorites'|'playlists'} Channel2CardType */

/**
 * Fetches and shows more channel 2.0 sidebar cards
 * @param {Channel2CardType} type The card type
 * @param {string} cursor Show cards before this date string (or prefix with "+" followed by a number to use as an offset)
 */
function fetchAndShowMoreCards(type, cursor) {
    let username = getChannelUsername();
    var showMoreBtn = /** @type {HTMLButtonElement} */ (document.getElementById('show_more'));
    showMoreBtn.disabled = true;
    showMoreBtn.innerText = 'Loading...';

    const cursorParts = cursor.split('|');
    const cursorTs = cursorParts[0];
    const cursorValue = cursorParts[1];

    // Determine request data based on type
    /** @type {any} */
    let reqData = { user: username, cursorTs: cursorTs };
    if (type === 'uploads') {
        reqData.includeUploads = true;

        // Sort out uploads-specific request data
        const uplOrder = getChannel2UploadsSearchOrder();
        const uplParts = uplOrder.split(' ');
        const uplQuery = getChannel2UploadsSearchQuery();

        const uplOrderCol = uplParts[0];
        const uplOrderDir = uplParts[1];

        reqData.uploadsOrder = uplOrder;

        if (uplOrderCol === 'createdOn') {
            if (uplOrderDir === 'DESC') {
                reqData.uploadsBefore = cursorValue;
            } else {
                reqData.uploadsAfter = cursorValue;
            }
        } else if (uplOrderCol === 'views') {
            if (uplOrderDir === 'DESC') {
                reqData.uploadsOrder = 'views DESC';
                reqData.uploadsViewsFewerThan = cursorValue;
            } else {
                reqData.uploadsOrder = 'views ASC';
                reqData.uploadsViewsMoreThan = cursorValue;
            }
        } else if (uplOrderCol === 'comments') {
            if (uplOrderDir === 'DESC') {
                reqData.uploadsOrder = 'comments DESC';
                reqData.uploadsCommentsFewerThan = cursorValue;
            } else {
                reqData.uploadsOrder = 'comments ASC';
                reqData.uploadsCommentsMoreThan = cursorValue;
            }
        }

        if (uplQuery) {
            reqData.uploadsQuery = uplQuery;
        }
    } else if(type === 'favorites') {
        reqData.includeFavorites = true;

        if (cursor.startsWith('+')) {
            reqData.favoritesOffset = cursor.substring(1);
        } else {
            reqData.favoritesBefore = cursor;
        }
    } else if(type === 'playlists') {
        reqData.includePlaylists = true;

        if(cursor.startsWith('+'))
            reqData.playlistsOffset = cursor.substring(1);
        else
            reqData.playlistsBefore = cursor;
    }

    apiGet('/ajax/get_channel_cards', reqData, function(err, res) {
        if (logApiErrorsIfAny(err, res, 'Failed to fetch more cards')) {
            return;
        }

        // For type annotation purposes only
        res = /** @type {ApiResponse<{ [key: Channel2CardType]: { items: Channel2VideoCard[], total: number } }>} */ (res);

        // Append cards
        const cardElems = document.getElementsByClassName('mnu_vid');
        const startIdx = cardElems[cardElems.length-1].dataset.index*1;
        const cards = /** @type {(Channel2VideoCard & { index: number })[]} */ (res.data[type].items);
        console.log('CARDS:', cards);
        for (let i = 0; i < cards.length; i++) {
            const card = cards[i];
            card.index = startIdx+i+1;
            var html;
            if (type === 'playlists') {
                html = renderChannel2PlaylistCard(card.id, card.title, card.thumbnail, card.creatorDisplayName);
            } else {
                html = renderChannel2VideoCard(card.id, card.title, card.thumbnail, card.uploaderDisplayName, card.views, false, card.index);
            }
            $("#channel-cards-container").append(html);
        }

        // Show "Show More" button if there are likely more results
        // The default limit is 16, so if the returned result count is more than or equal to that, show the button
        if(cards.length >= 16) {
            const lastCard = cards[cards.length - 1];

            const uplParts = getChannel2UploadsSearchOrder().split(' ');
            const uplOrderCol = uplParts[0];

            let cursor = lastCard.createdOn + '|';
            if (uplOrderCol === 'createdOn') {
                cursor += lastCard.createdOn;
            } else if (uplOrderCol === 'views') {
                cursor += lastCard.views;
            } else if (uplOrderCol === 'comments') {
                cursor += lastCard.comments;
            }

            showMoreBtn.outerHTML = renderChannel2ShowMoreButton(type, cursor);
        } else {
            // Delete "Show More" button since there are no more videos to show
            showMoreBtn.parentElement.removeChild(showMoreBtn);
        }
    });
}
$("#pl_inf_i").click(function() {
    var e = $("#pl_url").html();
    $("#nav_ind").css("left", "15px"), get_video_info(e)
}), $("#pl_inf_f").click(function() {
    var e = $("#pl_url").html();
    $("#pl_inf_i").removeClass("pl_nav_sel"), $("#pl_inf_f").addClass("pl_nav_sel"), $("#pl_inf_s").removeClass("pl_nav_sel"), $("#pl_inf_p").removeClass("pl_nav_sel"), $("#pl_inf_fl").removeClass("pl_nav_sel"), $("#nav_ind").css("left", "95px"), $.ajax({
        type: "POST",
        url: "/ajax/favorite_video",
        data: {
            id: e
        },
        success: function(e) {
            "added" == e.response ? $("#pl_inf").html("<div style='padding:12px;background:white;font-size:14px'>This video has been <strong>added</strong> to your favorites.</div>") : "removed" == e.response ? $("#pl_inf").html("<div style='padding:12px;background:white;font-size:14px'>This video has been <strong>removed</strong> from your favorites.</div>") : "not_logged_in" == e.response && $("#pl_inf").html("<div style='padding:12px;background:white;font-size:14px'>You must <strong><a href='/login'>log in</a></strong> to favorite this video.</div>")
        }
    })
}), $("#pl_inf_s").click(function() {
    var e = $("#pl_url").html();
    $("#pl_inf_i").removeClass("pl_nav_sel"), $("#pl_inf_f").removeClass("pl_nav_sel"), $("#pl_inf_s").addClass("pl_nav_sel"), $("#pl_inf_p").removeClass("pl_nav_sel"), $("#pl_inf_fl").removeClass("pl_nav_sel"), $("#nav_ind").css("left", "179px"), $("#pl_inf").html("<div style='font-size:14px;text-align:center'><div style='font-size:16px;margin-bottom:10px'>Share this video with others:</div>Permalink:<br><input style='width: 300px;-moz-user-select: all;-ms-user-select: all;-webkit-user-select: all;user-select: all;text-align:center' type='text' value='" + location.origin + "/watch?v=" + e + "' readonly><br><br>Embed Link:<br><input style='width: 300px;-moz-user-select: all;-ms-user-select: all;-webkit-user-select: all;user-select: all;text-align:center' type='text' value='" + location.origin + "/embed?v=" + e + "' readonly><br><br><br><a href='#'>Twitter</a> || <a href='#'>Facebook</a> || <a href='#'>Reddit</a></div>")
}), $("#pl_inf_p").click(function() {
    var e = $("#pl_url").html();
    $("#pl_inf_i").removeClass("pl_nav_sel"), $("#pl_inf_f").removeClass("pl_nav_sel"), $("#pl_inf_s").removeClass("pl_nav_sel"), $("#pl_inf_p").addClass("pl_nav_sel"), $("#pl_inf_fl").removeClass("pl_nav_sel"), $("#nav_ind").css("left", "265px"), $.ajax({
        type: "POST",
        url: "/ajax/get_playlists",
        data: {
            id: e
        },
        success: function(e) {
            "logged_in" == e.response ? $("#pl_inf").html("<div style='font-size:14px;text-align:center'><div style='font-size:16px;margin-bottom:10px'>Select a Playlist:</div>" + e.select + "</div>") : $("#pl_inf").html("<div style='font-size:14px;text-align:center'><div style='font-size:16px;margin-bottom:10px'>Please <a href='/login'>log in</a> to add videos to playlists!</div></div>")
        }
    })
}), $("#post_comment").click(function() {
    var contElem = $('#comment_content');
    var e = contElem.val();
    var t = getChannelUsername();
    contElem.val('');
    if(e.length > 1) {
        var postBtn = $('#post_comment');
        postBtn.attr('disabled', 'disabled');

        apiPost('/ajax/post_channel_comment', {
            comment: e,
            on_channel: t
        }, function(err, res) {
            if(err && logApiErrorsIfAny(err, res, 'Failed to post channel comment'))
                return;
            else if(!res.success)
                alert(res.error.message);

         

            $('#no_comments2').remove();
            if($('#no_comments').length > 0) {
                $('#no_comments').remove();
                $('#channel_comments').removeClass('no_border');
            }
            $('#channel_comments2').prepend('<div class="chn_cmt_sct" id="cc_' + e.id + '"><a href="/user/' + e.by_user + '"><img src="' + e.avatar + '" width="55" height="55" class="avt2 pr_avt" alt="' + e.by_user + '"></a><div><span><a href="/user/' + e.by_user + '">' + e.by_user + "</a> <span>(1 second ago)</span></span>" + e.markup + '</div><a href="javascript:void(0)" onclick="delete_cc(' + e.id + ')" style="position: absolute;top:0;right:0">Delete</a></div></div>'), $("#channel_comments").prepend('<div class="chn_cmt_sct" id="cc2_' + res.data.id + '"><a href="/user/' + res.data.by_user + '"><img src="' + res.data.avatar + '" width="55" height="55" class="avt2 pr_avt" alt="' + res.data.by_user + '"></a><div><span><a href="/user/' + res.data.by_user + '">' + res.data.by_user + "</a> <span>(1 second ago)</span></span>" + res.data.markup + '</div><a href="javascript:void(0)" onclick="delete_cc(' + res.data.id + ')" style="position: absolute;top:0;right:0">Delete</a></div></div>');
            $('#post_comment').removeAttr('disabled');

            var chanComCountElem = $('#cc_count');

            var s = chanComCountElem.text();
            if(s.indexOf(',') > -1) {
                s = s.replace(',', '');
                s = (s++).toLocaleString('us');
            } else {
                s++;
            }

            chanComCountElem.html(s);
            $('html, body').animate({
                scrollTop: chanComCountElem.offset().top
            }, 400);
        });
    } else {
        alert('Your comment must be at least 2 characters long')
    }
}), $("#aaf").click(function() {
    var e = $("#aaf").html(),
        t = $("#ch_user").html();
    if ("Cancel Invite" == e) {
        if (0 == confirm("Are you sure you want to cancel the friend invite?")) return !1
    } else if ("Unfriend" == e && 0 == confirm("Are you sure you want to remove this channel as a friend?")) return !1;
    $.ajax({
        type: "POST",
        url: "/ajax/add_friend",
        data: {
            user: t
        },
        success: function(e) {
            "0" == e.response ? $("#aaf").html("Add as Friend") : "1" == e.response ? $("#aaf").html("Unfriend") : "2" == e.response ? $("#aaf").html("Add as Friend") : "3" == e.response ? $("#aaf").html("Cancel Invite") : alert(e.response)
        }
    })
}), $("#post_comment2").click(function() {
    var e = $('#comment_content2').val();
    var t = getChannelUsername();
    $('#comment_content2').val('');
    if (e.length > 1) {
        $('#post_comment2').attr('disabled', 'disabled');
        apiPost('/ajax/post_channel_comment', {
            comment: e,
            on_channel: t
        }, function(err, res) {
            if(err && logApiErrorsIfAny(err, res, 'Failed to post channel comment'))
                return;
            else if(!res.success)
                alert(res.error.message);

           
            if($('#no_comments').length > 0) {
                $('#no_comments').remove();
                $('#channel_comments').removeClass('no_border')
            }
            if($('#no_comments2').length > 0) {
                $('#no_comments2').remove();
                $('#no_comments').remove();
                $('#channel_comments2').removeClass('no_border')
            }
            $('#channel_comments').prepend('<div class="chn_cmt_sct" id="cc_' + res.data.id + '"><a href="/user/' + res.data.by_user + '"><img src="' + res.data.avatar + '" width="55" height="55" class="avt2 pr_avt" alt="' + res.data.by_user + '"></a><div><span><a href="/user/' + res.data.by_user + '">' + res.data.by_user + "</a> <span>(1 second ago)</span></span>" + res.data.markup + '</div><a href="javascript:void(0)" onclick="delete_cc(' + res.data.id + ')" style="position: absolute;top:0;right:0">Delete</a></div></div>');
            $('#channel_comments2').prepend('<div class="chn_cmt_sct" id="cc2_' + res.data.id + '"><a href="/user/' + res.data.by_user + '"><img src="' + res.data.avatar + '" width="55" height="55" class="avt2 pr_avt" alt="' + res.data.by_user + '"></a><div><span><a href="/user/' + res.data.by_user + '">' + res.data.by_user + "</a> <span>(1 second ago)</span></span>" + res.data.markup + '</div><a href="javascript:void(0)" onclick="delete_cc(' + res.data.id + ')" style="position: absolute;top:0;right:0">Delete</a></div></div>');
            $('#post_comment2').removeAttr('disabled');
        });

        var s = document.getElementById('cc_count').innerText;
        if(s.indexOf(',') > -1) {
            s = s.replace(',', '');
            s = (s++).toLocaleString('us');
        } else {
            s++;
        }
        $('#cc_count').html(s);
    } else alert("Your comment must be at least 2 characters long!")
}), $("#post_comment1").click(function() {
    var e = $("#comment_content").val(),
        t = getChannelUsername();
    $('#comment_content').val("");
    if(e.length > 1) {
        $('#post_comment1').attr('disabled', "disabled");
        apiPost('/ajax/post_channel_comment', {
            comment: e,
            on_channel: t
        }, function(err, res) {
            try {
                if (err && logApiErrorsIfAny(err, res, 'Failed to post channel comment')) {
                    return;
                } else if (!res.success) {
                    alert(res.error.message);
                    return;
                }

            

                if ($('#no_comments').length > 0 || $('#no_comments2').length > 0) {
                    $('#no_comments').remove();
                    $('#no_comments2').remove()
                }

                $('#ch_cmt_sct').prepend('<div class="ch_cmt" id="cc_' + res.data.id + '"><a href="/user/' + res.data.by_user + '"><img src="' + res.data.avatar + '" width="68" height="68" class="avt2 pr_avt" alt="' + res.data.by_user + '"></a><div><a href="/user/' + res.data.by_user + '">' + res.data.by_user + '</a> (1 second ago)<div class="cmt_msg">' + res.data.markup + '</div></div><a href="javascript:void(0)" onclick="delete_cc(' + res.data.id + ')" class="cd">Delete</a></div></div>'), $("#ch_cmt_sct2").prepend('<div class="ch_cmt" id="cc2_' + res.data.id + '"><a href="/user/' + res.data.by_user + '"><img src="' + res.data.avatar + '" width="68" height="68" class="avt2 pr_avt" alt="' + res.data.by_user + '"></a><div><a href="/user/' + res.data.by_user + '">' + res.data.by_user + '</a> (1 second ago)<div class="cmt_msg">' + res.data.markup + '</div></div><a href="javascript:void(0)" onclick="delete_cc(' + res.data.id + ')" class="cd">Delete</a></div></div>');
                var s = document.getElementById("cc_count").innerText;
                if (s.indexOf(',') > -1) {
                    s = s.replace(',', '');
                    s = (s++).toLocaleString('us');
                } else {
                    s++;
                }
                $('#cc_count').html(s);
                $('html, body').animate({
                    scrollTop: $('#cc_count').offset().top
                }, 400);
            } finally {
                $('#post_comment1').removeAttr('disabled');
            }
        })
    } else {
        alert('Your comment must be at least 2 characters long!')
    }
}), $('#post_comment4').click(function() {
    var e = $('#comment_content2').val();
    var t = getChannelUsername();
    $('#comment_content2').val('');
    if(e.length > 1) {
        $('#post_comment4').attr('disabled', 'disabled');
        $.ajax({
            type: "POST",
            url: "/ajax/post_channel_comment",
            data: {
                comment: e,
                on_channel: t
            },
            success: function(e) {

                if($('#no_comments').length > 0 || $('#no_comments2').length > 0) {
                    $('#no_comments').remove();
                    $("#no_comments2").remove();
                }
                $('#ch_cmt_sct2').prepend('<div class="ch_cmt" id="cc2_' + data.res.id + '"><a href="/user/' + data.res.by_user + '"><img src="' + data.res.avatar + '" width="68" height="68" class="avt2 pr_avt" alt="' + data.res.by_user + '"></a><div><a href="/user/' + data.res.by_user + '">' + data.res.by_user + '</a> (1 second ago)<div class="cmt_msg">' + data.res.markup + '</div></div><a href="javascript:void(0)" onclick="delete_cc(' + data.res.id + ')" class="cd">Delete</a></div></div>');
                $('#ch_cmt_sct').prepend('<div class="ch_cmt" id="cc_' + data.res.id + '"><a href="/user/' + data.res.by_user + '"><img src="' + data.res.avatar + '" width="68" height="68" class="avt2 pr_avt" alt="' + data.res.by_user + '"></a><div><a href="/user/' + data.res.by_user + '">' + data.res.by_user + '</a> (1 second ago)<div class="cmt_msg">' + data.res.markup + '</div></div><a href="javascript:void(0)" onclick="delete_cc(' + data.res.id + ')" class="cd">Delete</a></div></div>');
                $('#post_comment4').removeAttr('disabled');
                var s = document.getElementById('cc_count').innerText;
                if(s.indexOf(",") > -1) {
                    s = s.replace(',', '');
                    s = (s++).toLocaleString('us')
                } else {
                    s++
                }
                $("#cc_count").html(s);
            }
        })
    } else {
        alert('Your comment must be at least 2 characters long!')
    }
}), $("#pr_all").click(function() {
    $("#pr_favorites").removeClass("pl_nav_sel_hd"), $("#pr_all").addClass("pl_nav_sel_hd"), $("#pr_playlists").removeClass("pl_nav_sel_hd"), $("#pr_uploads").removeClass("pl_nav_sel_hd"), fetchAndRenderChannel2Sidebar("all")
}), $("#pr_uploads").click(function() {
    $("#pr_all").removeClass("pl_nav_sel_hd"), $("#pr_uploads").addClass("pl_nav_sel_hd"), $("#pr_playlists").removeClass("pl_nav_sel_hd"), $("#pr_favorites").removeClass("pl_nav_sel_hd"), fetchAndRenderChannel2Sidebar("uploads")
}), $("#pr_playlists").click(function() {
    $("#pr_all").removeClass("pl_nav_sel_hd"), $("#pr_playlists").addClass("pl_nav_sel_hd"), $("#pr_uploads").removeClass("pl_nav_sel_hd"), $("#pr_favorites").removeClass("pl_nav_sel_hd"), fetchAndRenderChannel2Sidebar("playlists")
}), $("#pr_favorites").click(function() {
    $("#pr_all").removeClass("pl_nav_sel_hd"), $("#pr_playlists").removeClass("pl_nav_sel_hd"), $("#pr_favorites").addClass("pl_nav_sel_hd"), $("#pr_uploads").removeClass("pl_nav_sel_hd"), fetchAndRenderChannel2Sidebar("favorites")
}), $(".pl_toggler").click(function() {
    $(".pl_toggler").attr("id", ""), $(this).attr("id", "pl_toggle_sel"), "Switch to Player View" == $(this).attr("title") ? $(".pr_tp_btm").removeClass("grid") : $(".pr_tp_btm").addClass("grid")
}), $(".info_toggle").click(function() {
    $(this).next().toggleClass("opa"), $(this).parent().parent().next().find("input").toggleClass("opa"), $(this).parent().parent().next().find("select").toggleClass("opa"), $(this).parent().parent().next().find("textarea").toggleClass("opa"), $(this).parent().parent().next().find("span").toggleClass("opa")
}), $(document.body).on("click", ".mnu_vid", function() {
    $(this);
    var e = $(this).attr("watch");
    $("#v_sel").attr("id", "");
    if (void 0 != e) {
        if ($(this).attr("id", "v_sel"), "Switch to Player View" !== $("#pl_toggle_sel").attr("title")) {
            $(".pr_tp_btm").removeClass("grid");
            var t = $("#pl_toggle_sel");
            $(".pl_toggler").attr("id", "pl_toggle_sel"), $(t).attr("id", "")
        }
        $("#pl_url").html(e), get_video_info(e)
    } else PURL = $(this).attr("pl"), SELECTED = $("#pl_url").html(), $.ajax({
        type: "POST",
        url: "/ajax/get_playlist_videos",
        data: {
            pid: PURL,
            selected: SELECTED
        },
        success: function(e) {
            $("#pl_list").html(e), $(".pr_pl_mnu").animate({
                scrollTop: 0
            }, "fast")
        }
    })
}), $(".pr_edit_btn").click(function() {
    var e = $(this).attr("id");
    if ($("#" + e).is(".pr_edit_btn_sel")) return $("#" + e).removeClass("pr_edit_btn_sel"), void $("#edit_" + e).toggleClass("hddn");
    "settings" == e ? ($("#settings").addClass("pr_edit_btn_sel"), $("#themes").removeClass("pr_edit_btn_sel"), $("#modules").removeClass("pr_edit_btn_sel"), $("#vap").removeClass("pr_edit_btn_sel"), $("#edit_themes").is(".hddn") || $("#edit_themes").addClass("hddn"), $("#edit_modules").is(".hddn") || $("#edit_modules").addClass("hddn"), $("#edit_vap").is(".hddn") || $("#edit_vap").addClass("hddn"), $("#edit_settings").toggleClass("hddn")) : "themes" == e ? ($("#settings").removeClass("pr_edit_btn_sel"), $("#themes").addClass("pr_edit_btn_sel"), $("#modules").removeClass("pr_edit_btn_sel"), $("#vap").removeClass("pr_edit_btn_sel"), $("#edit_settings").is(".hddn") || $("#edit_settings").addClass("hddn"), $("#edit_modules").is(".hddn") || $("#edit_modules").addClass("hddn"), $("#edit_vap").is(".hddn") || $("#edit_vap").addClass("hddn"), $("#edit_themes").toggleClass("hddn")) : "modules" == e ? ($("#settings").removeClass("pr_edit_btn_sel"), $("#themes").removeClass("pr_edit_btn_sel"), $("#modules").addClass("pr_edit_btn_sel"), $("#vap").removeClass("pr_edit_btn_sel"), $("#edit_settings").is(".hddn") || $("#edit_settings").addClass("hddn"), $("#edit_themes").is(".hddn") || $("#edit_themes").addClass("hddn"), $("#edit_vap").is(".hddn") || $("#edit_vap").addClass("hddn"), $("#edit_modules").toggleClass("hddn")) : "vap" == e && ($("#settings").removeClass("pr_edit_btn_sel"), $("#themes").removeClass("pr_edit_btn_sel"), $("#modules").removeClass("pr_edit_btn_sel"), $("#vap").addClass("pr_edit_btn_sel"), $("#edit_settings").is(".hddn") || $("#edit_settings").addClass("hddn"), $("#edit_themes").is(".hddn") || $("#edit_themes").addClass("hddn"), $("#edit_modules").is(".hddn") || $("#edit_modules").addClass("hddn"), $("#edit_vap").toggleClass("hddn"))
}), $(".jscolor").click(function() {
    "9" !== $("#theme_selectnum").val() && theme_select("custom")
});
