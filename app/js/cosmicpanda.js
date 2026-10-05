/**
 * Renders a video card for CosmicPanda
 * @param {string} id The video ID
 * @param {string} title The title
 * @param {string} thumbnail The thumbnail URL
 * @param {string} durationFormatted The formatted video duration string
 * @param {string|null} description The video description or null for none
 * @param {string} uploaderDisplayName The uploader's display name
 * @param {number} views The video's view count
 * @param {string} uploadedAgo A localized string representing the time ago the video was uploaded
 * @returns {string} The rendered video card
 */
function renderCosmicPandaVideoCard(id, title, thumbnail, durationFormatted, description, uploaderDisplayName, views, uploadedAgo) {
    var titleSanitized = sanitizeHTML(title);
    var uploaderSanitized = sanitizeHTML(uploaderDisplayName);

    return ''
        + '<div class="cosmic_big_video">'
            + '<a href="/watch?v='+id+'">'
                + '<div style="display:inline-block;position: relative;width:295px">'
                    + '<div class="th_t">'+durationFormatted+'</div>'
                    + '<img class="vid_th" src="'+thumbnail+'" alt="'+titleSanitized+'" title="'+titleSanitized+'" width="295px" height="155px">'
                + '</div>'
                + '<div class="cosmic_big_video_info">'
                    + '<div>'+titleSanitized+'</div>'
                    + '<div class="cosmic_big_small_stats">'
                        + '<span>'+uploaderSanitized+'</span>'
                        + '<span style="color:#444">'+views+' view'+(views === 1 ? '' : 's')+'</span>'
                        + '<span>'+uploadedAgo+'</span>'
                    + '</div>'
                    + '<div class="big_video_description">'+(description === null ? '<i>No Description...</i>' : sanitizeHTML(trimWithEllipsis(description, 128)))+'</div>'
                + '</div>'
            + '</a>'
        + '</div>';
}

/**
 * Renders a channel 2.0 "Load 10 more videos" button
 * @param {'uploads'|'favorites'|'playlists'} type The type of cards to show more of
 * @param {string} before Show cards after this date
 * @returns {string} The rendered HTML
 */
function renderCosmicPandaShowMoreButton(type, before) {
    return '<button type="button" id="show_more" onclick="fetchAndShowMoreCards(\''+type+'\', \''+before+'\')" class="cosmic_button" style="width:100%">Load 10 more videos</button>';
}

/**
 * Fetches and shows more CosmicPanda 2.0 video cards
 * @param {'uploads'|'favorites'|'playlists'} type The card type
 * @param {string} before Show cards before this date
 */
function fetchAndShowMoreCards(type, before) {
    var username = $("#cm_usn").text();
    var showMoreBtn = document.getElementById('show_more');
    showMoreBtn.disabled = true;
    showMoreBtn.innerText = 'Loading...';

    // Determine request data based on type
    var reqData = { user: username };
    if(type === 'uploads') {
        reqData.includeUploads = true;
        reqData.uploadsBefore = before;
        reqData.uploadsLimit = 10;
    } else if(type === 'favorites') {
        reqData.includeFavorites = true;
        reqData.favoritesBefore = before;
        reqData.favoritesLimit = 10;
    } else if(type === 'playlists') {
        reqData.includePlaylists = true;
        reqData.playlistsBefore = before;
        reqData.playlistsLimit = 10;
    }

    apiGet('/ajax/get_channel_cards', reqData, function(err, res) {
        if(logApiErrorsIfAny(err, res, 'Failed to fetch more cards'))
            return;

        var vidsContainer = $('.cosmic_featured_videos');

        // Append cards
        var cards = res.data[type].items;
        for(var i = 0; i < cards.length; i++) {
            var card = cards[i];
            vidsContainer.append(renderCosmicPandaVideoCard(card.id, card.title, card.thumbnail, card.durationFormatted, card.description, card.uploaderDisplayName, card.views, card.createdAgo));
        }

        showMoreBtn.parentElement.removeChild(showMoreBtn);

        // Show "Load 10 more videos" button if there are likely more results
        if(cards.length >= 10)
            vidsContainer.append(renderCosmicPandaShowMoreButton(type, cards[cards.length-1].createdOn));
    });
}

function show_more_comments(e, t) {
    document.getElementById("show_more_comments").disabled = !0, document.getElementById("show_more_comments").innerHTML = "Loading...", $.ajax({
        type: "POST",
        url: "/ajax/cosmic_show_more",
        data: {
            type: "comments",
            page: e,
            user: t
        },
        success: function(e) {
            document.getElementById("show_more_comments").outerHTML = "", $("#cosmic_channel_comments").append(e)
        }
    })
}

function post_cosmic_bulletin() {
    var e = $('#cosmic_textarea').val();
    if(e.length > 0) {
        $('#no_recent_activity').remove();
        $("#cosmic_textarea").val('');
        $.ajax({
            type: "POST",
            url: "/ajax/post_cosmic_bulletin",
            data: {
                bulletin: e
            },
            success: function(e) {
                $(".cosmic_recent").prepend(e)
            }
        })
    } else {
        alert('Bulletins can\'t be empty!')
    }
}

function delete_cosmic_bulletin(e) {
    $("#cb_" + e).remove(), 0 == $(".cosmic_recent").html().trim().length && $(".cosmic_recent").html('<span id="no_recent_activity" style="margin-top:40px;display:block;color: #808080;text-align: center;font-size:20px;">This user doesn\'t have any activity!</span>');
    var t = new FormData;
    if (window.XMLHttpRequest) a = new XMLHttpRequest;
    else if (window.ActiveXObject) var a = new ActiveXObject("Microsoft.XMLHTTP");
    t.append("bulletin", e), a.open("POST", "/ajax/delete_bulletin"), a.send(t)
}

function cosmic_bg(e) {
    $("#gbg").css("background-color", "#" + e, 100), $("#bg_color").val("#" + e)
}

function cosmic_remove_ft(e) {
    $("#fc_" + e).remove();
    var t = $("#featured_channels_small_input").val();
    t = t.toLowerCase().replace(e.toLowerCase(), ""), $("#featured_channels_small_input").val(t)
}

function cosmic_remove_pl(e) {
    $("#pl_" + e).remove();
    var t = $("#playlists_small_input").val();
    t = t.toLowerCase().replace(e.toLowerCase(), ""), $("#playlists_small_input").val(t)
}

function cosmic_add_channel() {
    var e = $("#ft_channel_name").val(),
        t = $("#featured_channels_small_input").val();
    $("#featured_channels_small > div").length < 8 ? e.length > 0 && (t.toLowerCase().indexOf(e + ",".toLowerCase()) >= 0 ? alert("You have already added this channel to your featured channels") : $.ajax({
        type: "POST",
        url: "/ajax/user_exists",
        data: {
            user: e
        },
        success: function(a) {
            "false" != a ? ($("#ft_channel_name").val(""), $("#featured_channels_small_input").val(t + a + ","), $("#featured_channels_small").append('<div id="fc_' + a + '" style="overflow:hidden;background:white;border-radius:3px;padding:4px;margin-top:11px"><div style="float:left;color:black;font-weight:bold">' + e + '</div><div style="float:right"><a href="javascript:void(0)" onclick="cosmic_remove_ft(\'' + a + "')\">Remove</a></div></div>")) : alert("This user doesn't exist!")
        }
    })) : alert("You can only have up to 8 channels in your featured channels box!")
}

function cosmic_add_playlist() {
    var e = $("#select_playlist option:selected").val(),
        t = $("#select_playlist option:selected").html(),
        a = $("#playlists_small_input").val();
    $("#featured_playlists_small > div").length < 3 ? a.toLowerCase().indexOf(e.toLowerCase()) >= 0 ? alert("You have already added this playlist to your featured playlists!") : ($("#playlists_small_input").val(a + e + ","), $("#featured_playlists_small").append('<div id="pl_' + e + '" style="overflow:hidden;background:white;border-radius:3px;padding:4px;margin-top:11px"><div style="float:left;color:black;font-weight:bold">' + t + '</div><div style="float:right"><a href="javascript:void(0)" onclick="cosmic_remove_pl(\'' + e + "')\">Remove</a></div></div>")) : alert("You can only feature up to 3 playlists!")
}

function delete_background_new() {
    document.getElementById("bg_delete").disabled = !0;
    var e = new FormData;
    if (window.XMLHttpRequest) t = new XMLHttpRequest;
    else if (window.ActiveXObject) var t = new ActiveXObject("Microsoft.XMLHTTP");
    e.append("bg", "ar"), t.addEventListener("load", bg_del_comp, !1), t.open("POST", "/ajax/delete_background"), t.send(e)
}

function post_channel_comment() {
    var e = $('#cosmic_textarea').val();
    var t = $("#cm_usn").html();
    if(e.length > 1) {
        document.getElementById('cosmic_comment_post').disabled = true;
        $('#cosmic_textarea').val('');
        apiPost('/ajax/post_channel_comment', {
            comment: e,
            on_channel: t
        }, function(err, res) {
            if(err && logApiErrorsIfAny(err, res, 'Failed to post channel comment'))
                return;
            else if(!res.success)
                alert(res.error.message);

            document.getElementById('cosmic_comment_post').disabled = false;
            $('#no_comments').remove();
            $('#cosmic_channel_comments').prepend('<div class="cosmic_comment" id="cc_' + res.data.id + '"><div><div><img src="' + res.data.avatar + '" width="26" height="26" class="avt2 " alt="' + res.data.by_user + '"></div> <div><a href="/user/' + res.data.by_user + '">' + res.data.by_user + '</a> posted a comment <span>1 second ago</span></div></div><a href="javascript:void(0)" class="cosmic_delete" onclick="delete_comment(' + res.data.id + ')">Delete</a><div><div>' + res.data.markup + "</div></div></div>")
        });
    } else {
        alert('Your comment must be at least 2 characters long!')
    }
}

function cosmic_subscribe(e) {
    $.ajax({
        type: "POST",
        url: "/ajax/subscribe",
        data: {
            user: e
        },
        success: function(e) {
            '<div id="sub-icon"></div>Subscribe' == $(".cosmic_sub").html() ? ($(".cosmic_sub").html('<div id="sub-icon"></div>Unsubscribe'), $(".cosmic_sub").addClass("cosmic_subbed")) : ($(".cosmic_sub").html('<div id="sub-icon"></div>Subscribe'), $(".cosmic_sub").removeClass("cosmic_subbed"))
        }
    })
}

function delete_comment(e) {
    apiPost('/ajax/delete_ch_comment', { id: e }, function(err, res) {
        if (err && logApiErrorsIfAny(err, res, 'Failed to delete comment')) {
            return;
        } else if (!res.success) {
            alert(res.error.message);
            return;
        }
    });

    $("#cc_" + e).remove();
}
