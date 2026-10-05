/**
 * @typedef ApiError
 * @property {string} name The computer-readable error name (e.g. "internal_error")
 * @property {string} message The human-readable error message
 * @property {any|null} data Additional data included in the error, or null if none was provided
 */

/**
 * @typedef ApiResponse
 * @property {boolean} success Whether it was a successful response
 * @property {ApiError[]|null} errors Any errors returned with the response, or null if it was a successful response
 * @property {ApiError|null} error The first error, or null if no errors were returned or if it was a successful response (utility for receiving responses with only one error)
 * @property {T|null} data Any data returned with the response, or null if it was not a successful response (or null if there was none provided)
 * @template T The type of data returned
 */

/**
 * @typedef ApiRequestOptions
 * @property {boolean} [useJsonBody=false] Whether to use JSON body encoding (defaults to false, has no effect on GET, HEAD, OPTIONS or DELETE requests)
 * @property {'silent'|'alert'} [errorLoggingType] If not undefined, specifies the way to log API errors (optional)
 */

/**
 * The default API request options to use when none are provided.
 * @type {ApiRequestOptions}
 */
const defaultApiRequestOptions = {
    useJsonBody: false,
    errorLoggingType: undefined,
}

/**
 * Performs an API request
 * @param {string} url The URL or path to request
 * @param {'GET'|'POST'|'PUT'|'DELETE'|'HEAD'|'OPTIONS'} method The HTTP method to use
 * @param {any|null} data The data to send along as either query parameters, or URL-encoded body (if using POST), or null for none
 * @param {(err: Error|null, res?: ApiResponse<T>) => void} callback The callback
 * @param {ApiRequestOptions} [options] The options for the request (defaults to {@link defaultApiRequestOptions})
 * @template T The type of data returned
 */
function apiRequest(url, method, data, callback, options = defaultApiRequestOptions) {
    const useJson = options.useJsonBody ?? defaultApiRequestOptions.useJsonBody;
    const errLogType = options.errorLoggingType ?? defaultApiRequestOptions.errorLoggingType;

    let urlRes = url;
    let dataRes = null;
    if (data !== null) {
        if (method === 'POST') {
            dataRes = useJson ? JSON.stringify(data) : $.param(data);
        } else {
            urlRes += '?' + $.param(data);
        }
    }

    $.ajax({
        url: urlRes,
        data: dataRes,
        method: method,
        type: method,
        cache: false,
        contentType: useJson ? 'application/json; charset=UTF-8' : 'application/x-www-form-urlencoded; charset=UTF-8',
        processData: false,
        success: function(resRaw) {
            try {
                var json = typeof resRaw === 'object' ? resRaw : JSON.parse(resRaw);

                // Check for success param
                if(!('success' in json)) {
                    const err = new Error('Malformed API response');

                    if (errLogType !== undefined) {
                        const shouldAlert = errLogType === 'alert';
                        logApiErrorsIfAny(err, undefined, err.message, shouldAlert);
                    }

                    callback(err);

                    return;
                }

                // Form response object
                /**
                 * @type {ApiResponse}
                 */
                let res;
                if (json.success) {
                    res = {
                        success: true,
                        errors: null,
                        error: null,
                        data: /** @type {T} */ (json.data)
                    };
                } else {
                    if (errLogType !== undefined) {
                        const shouldAlert = errLogType === 'alert';

                        /** @type {string} */
                        let msg;
                        if (json.errors.length === 1) {
                            msg = json.errors[0].message;
                        } else {
                            msg = '';
                            for (const err of json.errors) {
                                msg += 'Error: ' + err.message + '\n';
                            }
                        }

                        if (shouldAlert) {
                            alert(msg);
                        } else {
                            console.error('API error(s):\n' + msg);
                        }
                    }

                    res = {
                        success: false,
                        errors: /** @type {ApiError[]} */ (json.errors),
                        error: json.errors.length > 0 ? /** @type {ApiError} */ (json.errors[0]) : null,
                        data: null
                    };
                }

                // Catch errors in callback here to avoid calling the callback again for an error that occurred inside of it
                try {
                    // Fire callback
                    callback(null, res);
                } catch(err) {
                    const msg = 'Error in callback for successful ' + method + ' request to ' + url + ':';

                    console.error(msg, err);

                    if (errLogType === 'alert') {
                        alert(msg + ' ' + JSON.stringify(err));
                    }
                }
            } catch(err) {
                callback(err);
            }
        },
        error: function(err) {
            callback(err);
        }
    });
}

/**
 * Logs API or transmission errors if any, and returns true if anything was logged
 * @param {Error|null} err A transmission error or null for none
 * @param {ApiResponse} res The API response
 * @param {string} errMsg The error message to use in logged messages
 * @param {boolean?} showAlert Whether to show an alert box if there is an error (defaults to true)
 * @returns {boolean} True if any errors were found and logged, otherwise false
 */
function logApiErrorsIfAny(err, res, errMsg, showAlert) {
    // Default value for showAlert
    if(showAlert === undefined)
        showAlert = true;

    // Check for transmission error
    if(err) {
        console.error(errMsg+': ', err);
        if(showAlert)
            alert(errMsg);
        return true;
    }

    // Check for error returned by API
    if(!res.success) {
        console.error(errMsg+': ', res.errors);
        if(showAlert)
            alert(errMsg+': '+res.error.message);
        return true;
    }

    return false;
}

/**
 * Performs an API GET request
 * @param {string} url The URL or path to request
 * @param {any|null} data The data to send along as either query parameters, or URL-encoded body (if using POST), or null for none
 * @param {ApiRequestOptions} [options] The options for the request (defaults to {@link defaultApiRequestOptions})
 * @param {(err: Error|null, res?: ApiResponse) => void} callback The callback
 */
function apiGet(url, data, callback, options = defaultApiRequestOptions) {
    apiRequest(url, 'GET', data, callback);
}

/**
 * Performs an API POST request
 * @param {string} url The URL or path to request
 * @param {any|null} data The data to send along as either query parameters, or URL-encoded body (if using POST), or null for none
 * @param {ApiRequestOptions} [options] The options for the request (defaults to {@link defaultApiRequestOptions})
 * @param {(err: Error|null, res?: ApiResponse<T>) => void} callback The callback
 * @template T The response data type
 */
function apiPost(url, data, callback, options = defaultApiRequestOptions) {
    apiRequest(url, 'POST', data, callback, options);
}

/**
 * Sanitizes the provided HTML by escaping HTML entities
 * @param {string} html The HTML to sanitize
 * @returns {string} The sanitized HTML
 */
function sanitizeHTML(html) {
    return String(html)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;')
}

/**
 * Generates a string of the specified length filled with the provided character
 * @param {number} len The desired string length
 * @param {string} char The character to fill the string with (should only be one character long)
 * @return {string} The generated string
 */
function genStrOfLen(len, char) {
    if(len < 1)
        return '';

    var arr = new Array(len);
    for(var i = 0; i < arr.length; i++)
        arr[i] = char;
    return arr.join('');
}

/**
 * Trims a string to a certain size, terminating with an ellipsis if the string is longer than the provided length
 * @param {string} str The string to trim
 * @param {number} len The desired string length (including the terminating ellipsis if necessary)
 * @returns {string} The trimmed string
 */
function trimWithEllipsis(str, len) {
    if(str.length >= len)
        return str;

    if(len < 3)
        return genStrOfLen(len, '.');
    else
        return str.substring(0, len-3)+'...';
}

function _(element) {
    return document.getElementById(element)
}

function isEmptyOrSpaces(str) {
    return str === null || str.match(/^ *$/) !== null
}

function new_upload() {
    var video = $("#selectedFile")[0].files[0];
    var extension = video.name.lastIndexOf(".");
    var page_title = $("title").html();
    var video_title = video.name;
    var video_thumb = 0;
    if (extension != -1) video_title = video_title.substring(0, extension);
    if (video_title.length < 4) video_title = "Untitled";
    $("#video_title").val(video_title);
    $("#video_title_header").html(video_title);
    $("#custom_thumb_button").on("click mousedown", function() {
        if (!$(this).hasClass("loading")) {
            $("#custom_thumb_file").click();
            return !1
        }
    });
    $("#custom_thumb_file").change(function() {
        var file = $(this)[0].files[0];
        var name = file.name;
        var extension = name.lastIndexOf(".");
        if (extension != -1) {
            extension = name.substring(extension + 1);
            extension = extension.toUpperCase();
            if (["BMP", "PNG", "JPG", "JPEG", "GIF"].indexOf(extension) != -1) {
                $("#custom_thumb_button").addClass("loading");
                var data = new FormData();
                data.append("ajax", "1");
                data.append("c_thmp_uploader", file);
                $.ajax({
                    url: '/edit_video?v=' + $("#vurl").html(),
                    data: data,
                    cache: !1,
                    contentType: !1,
                    processData: !1,
                    method: 'POST',
                    type: 'POST',
                    success: function(r) {
                        var success = r.substring(0, 1);
                        var data = r.substring(2);
                        if (success == "1") {
                            video_thumb++;
                            $("#custom_thumb_button").attr("src", data + "?v=" + video_thumb)
                        } else {
                            alert(data)
                        }
                    },
                    error: function() {
                        alert("An error has occurred while submitting your thumbnail!")
                    },
                    complete: function() {
                        $("#custom_thumb_button").removeClass("loading")
                    }
                });
                $(this).val("");
                return
            }
        }
        $(this).val("");
        alert("Only images, please!")
    });
    $("#custom_thumb_button").parent().hide();
    $("#save_upload_changes_button").hide();
    $("#selectedFile").prop("disabled", !0);
    $("#uploader").animate({
        opacity: 0
    }, 500, function() {
        $("#old_upload_box").hide();
        $("#upload_select_box").show();
        $("#video_uploader").css("opacity", 0).animate({
            opacity: 1
        }, 500)
    });
    new vlUploader({
        file: video,
        progress: function(p) {
            p = Math.round(p * 10000) / 100;
            $("#video_progress_in").css("width", p + "%");
            $("#video_progress_in").html(p + "%");
            $("#upload_status").removeClass("error").html("Uploading...");
            $(document).prop('title', "(" + parseInt(p) + "%) " + page_title)
        },
        complete: function() {
            $("#video_progress_in").css("width", "100%").html("100%");
            $("#upload_status").html("Upload complete!");
            $(document).prop('title', "(100%) " + page_title)
        },
        error: function(e, fatal) {
            $("#upload_status").addClass("error").html(e);
            $(document).prop('title', "(!) " + page_title)
        },
        start: function(url, thumb) {
            $("#vurl").html(url);
            $("#save_upload_changes_button").show();
            $("#custom_thumb_button").parent().show();
            $("#custom_thumb_button").attr("src", thumb + "?v=" + video_thumb)
        }
    })
}


function upload() {
    var video = _("selectedFile").files[0];
    var video_size = video.size / 1024 / 1024 / 1024;
    var extensions = ["flv", "mp4", "wmv", "avi", "mov", "m4v", "mpg", "mpeg", "webm", "mov", "mkv", "3gp"];
    var extension = video.name.toLowerCase().split(".");
    extension = extension[extension.length - 1];
    if (extensions.indexOf(extension) == -1) {
        alert("Video Extension not allowed!");
        return
    }
    if (video_size <= 5.01 && video.name.length < 100 && isEmptyOrSpaces(video.name) == !1) {
        var video_title = video.name.replace(/\.[^/.]+$/, "");
        _("video_title").value = video_title;
        $("#video_title_header").html(video_title);
        $("#old_upload_box").css("display", "none");
        $("#upload_select_box").css("display", "block");
        var formdata = new FormData();
        formdata.append("title", video_title);
        if (window.XMLHttpRequest) {
            var ajax = new XMLHttpRequest()
        } else if (window.ActiveXObject) {
            var ajax = new ActiveXObject("Microsoft.XMLHTTP")
        }
        ajax.addEventListener("load", completeHandler, !1);
        ajax.open("POST", "/ajax/uploader_url.php");
        ajax.send(formdata)
    } else {
        if (video_size > 5.01) {
            alert("Video File is too big!" + video_size)
        } else {
            alert("File name is too long!")
        }
    }
}

function move_hor(box, box2) {
    $("#" + box).toggleClass("hddn");
    $("#" + box2).toggleClass("hddn");
    $.ajax({
        type: "POST",
        url: "/ajax/move_hor_module",
        data: {
            Module: box
        }
    })
}

function completeHandler(e) {
    var r = e.target.responseText;
    if (r.indexOf("error") == 0) {
        alert("Error!")
    } else {
        $("#vurl").html(r);
        upload2()
    }
}

function upload2() {
    var video = _("selectedFile").files[0];
    var formdata = new FormData();
    formdata.append("video", video);
    formdata.append("url", $("#vurl").html());
    if (window.XMLHttpRequest) {
        var ajax = new XMLHttpRequest()
    } else if (window.ActiveXObject) {
        var ajax = new ActiveXObject("Microsoft.XMLHTTP")
    }
    ajax.upload.onprogress = function(e) {
        var percent = Math.round((e.loaded / e.total) * 100) + "%";
        _("video_progress_in").style.width = percent;
        _("video_progress_in").innerHTML = percent
    }
    ajax.addEventListener("error", on_error, !1);
    ajax.addEventListener("abort", on_abort, !1);
    ajax.addEventListener("load", completeUpload, !1);
    ajax.open("POST", "/ajax/vluploader.php");
    ajax.send(formdata)
}

function switch_partner(to) {
    switch (to) {
        case "overview":
            $('#pa_1').addClass("pa_sel");
            $('#pa_2').removeClass("pa_sel");
            $('#pa_3').removeClass("pa_sel");
            $('#partner_main').removeClass("hddn");
            $('#partner_benefits').addClass("hddn");
            $('#partner_qualifications').addClass("hddn");
            break;
        case "benefits":
            $('#pa_1').removeClass("pa_sel");
            $('#pa_2').addClass("pa_sel");
            $('#pa_3').removeClass("pa_sel");
            $('#partner_main').addClass("hddn");
            $('#partner_benefits').removeClass("hddn");
            $('#partner_qualifications').addClass("hddn");
            break;
        case "qualifications":
            $('#pa_1').removeClass("pa_sel");
            $('#pa_2').removeClass("pa_sel");
            $('#pa_3').addClass("pa_sel");
            $('#partner_main').addClass("hddn");
            $('#partner_benefits').addClass("hddn");
            $('#partner_qualifications').removeClass("hddn");
            break
    }
}

function completeUpload(e) {
    var r = e.target.responseText;
    if (r.indexOf("error") == 0) {
        alert("Error: " + r.split("||")[1])
    } else {
        alert("Video got successfully uploaded!")
    }
}

function on_error(e) {
    alert("Upload Failed!")
}

function feature_video(url) {
    $.ajax({
        type: "POST",
        url: "/ajax/feature_video",
        data: {
            url: url
        },
        success: function(output) {
            if (output == "1") {
                $("#feature_video").html("Remove Feature")
            } else {
                $("#feature_video").html("Feature Video")
            }
        }
    })
}

function on_abort(e) {
    alert("Upload Aborted!")
}

function save_video_changes() {
    var category = $("#video_category option:selected").val();
    var title = $("#video_title").val();
    var description = $("#video_description").val();
    var tags = $("#video_tags").val();
    var URL = $("#vurl").html();
    var Privacy = $("#privacy").val();
    var Schedule = $("#schedule_up").val();
    var AgeRestricted = $("input[type='radio'][name='age_restricted']:checked").val();
    $.ajax({
        type: "POST",
        url: "/ajax/save_upload_changes",
        data: {
            video_title: title,
            video_description: description,
            video_tags: tags,
            video_category: category,
            age_restricted: AgeRestricted,
            privacy: Privacy,
            schedule: Schedule,
            url: URL
        },
        success: function(output) {
            alert("Information succesfully updated!")
        }
    })
}

function showstars(stars) {
    var i = 0;
    for (x = 0; x < stars; x++) {
        i++;
        document.getElementById(i).src = '/img/full_star.gif'
    }
}

function removestars(stars) {
    var i = 0;
    for (x = 0; x < stars; x++) {
        i++;
        document.getElementById(i).src = '/img/no_star.gif'
    }
}

function commentf() {
    window.scrollTo(0, 920);
    var comment_form = _('cm_form');
    comment_form.focus()
}

function latest_video() {
    var formdata = new FormData();
    if (window.XMLHttpRequest) {
        var ajax = new XMLHttpRequest()
    } else if (window.ActiveXObject) {
        var ajax = new ActiveXObject("Microsoft.XMLHTTP")
    }
    ajax.addEventListener("load", lv_on_complete, !1);
    ajax.open("POST", "/ajax/newest_video");
    ajax.send(formdata)
}

function lv_on_complete(e) {
    if (e.target.responseText !== "") {
        _("st_em").value = window.location.origin + '/watch?v=' + e.target.responseText
    } else {
        alert("You don't have any videos!")
    }
}

function show_commentbox(user) {
    _("cmt_loc").innerHTML = "<form action='/ajax/df/ch_comment' method='POST'><input type='hidden' value='" + user + "' name='channel'><textarea id='ch_comment' rows='5' cols='50' maxlength='500' name='comment'></textarea><br><input type='submit' value='Post Comment'></form>";
    var comment_form = _("ch_comment");
    comment_form.focus()
}

function d_cc(id) {
    apiPost('/ajax/delete_ch_comment', { id: id }, function(err, res) {
        if (err && logApiErrorsIfAny(err, res, 'Failed to delete comment')) {
            return;
        } else if (!res.success) {
            alert(res.error.message);
            return;
        }

        _("c_" + Array.old_id).outerHTML = ""
        _("ch_nm").innerHTML = _("ch_nm").innerText - 1
    });
}

function add_ft_channel() {
    var channel = _("channel_add").value;
    if (channel.length > 1) {
        var formdata = new FormData();
        if (window.XMLHttpRequest) {
            var ajax = new XMLHttpRequest()
        } else if (window.ActiveXObject) {
            var ajax = new ActiveXObject("Microsoft.XMLHTTP")
        }
        formdata.append("user", channel);
        ajax.addEventListener("load", add_ft_channel_succ, !1);
        ajax.open("POST", "/ajax/add_ft_channel");
        ajax.send(formdata)
    } else {
        alert("You must type in a valid channel!")
    }
}

function add_ft_channel_succ(e) {
    if (e.target.responseText.length > 5) {
        var User = JSON.parse(e.target.responseText);
        _("fc").innerHTML = _("fc").innerHTML + '<div class="fc_sct"> <a href="/user/' + User.username + '">' + User.username + '</a><img src="' + User.avatar + '" class="avt" width="50" height="50"><br>Videos: ' + User.videos + '<br>Video Views: ' + User.video_views + '<br>Subscribers: ' + User.subscribers + ' </div>'
    } else {
        if (e.target.responseText == "u_d") {
            alert("User doesn't exist!")
        } else if (e.target.responseText == "u_m") {
            alert("You can have at most 8 featured channels!")
        } else if (e.target.responseText == "u_e") {
            alert("You already have this channel featured!")
        }
    }
}

function remove_ft(channel) {
    if (channel.length > 1) {
        var formdata = new FormData();
        if (window.XMLHttpRequest) {
            var ajax = new XMLHttpRequest()
        } else if (window.ActiveXObject) {
            var ajax = new ActiveXObject("Microsoft.XMLHTTP")
        }
        formdata.append("user", channel);
        ajax.open("POST", "/ajax/delete_ft_channel");
        ajax.send(formdata);
        _("fc_" + channel).outerHTML = ""
    }
}

function post_bulletin() {
    var bulletin = document.getElementById("bulletin2").value;
    var username = _("us").innerText;
    if ((bulletin.length > 0 && bulletin.length < 501) || (bulletin2.length > 0 && bulletin2.length < 501)) {
        _("bulletin").value = "";
        var formdata = new FormData();
        if (window.XMLHttpRequest) {
            var ajax = new XMLHttpRequest()
        } else if (window.ActiveXObject) {
            var ajax = new ActiveXObject("Microsoft.XMLHTTP")
        }
        formdata.append("bulletin", bulletin);
        ajax.open("POST", "/ajax/post_bulletin");
        ajax.send(formdata);
        if (bulletin.length > 0) {
            if (_("no_ra")) {
                _("no_ra").outerHTML = ""
            }
            _("ra_in").innerHTML = '<tr><td valign="top" width="18.5"><img src="/img/ra1.png"></td><td><strong>' + sanitizeHTML(username) + '</strong> ' + sanitizeHTML(bulletin) + ' <span>(just now)</span></td> </tr>' + _("ra_in").innerHTML
        } else {
            if (_("no_ra2")) {
                _("no_ra2").outerHTML = ""
            }
            _("ra_in2").innerHTML = '<tr><td valign="top" width="18.5"><img src="/img/ra1.png"></td><td><strong>' + sanitizeHTML(username) + '</strong> ' + sanitizeHTML(bulletin) + ' <span>(just now)</span></td> </tr>' + _("ra_in").innerHTML
        }
    } else {
        alert("Bulletins can't be empty!")
    }
}

function delete_bulletin(id) {
    _("b_" + id).outerHTML = "";
    var formdata = new FormData();
    if (window.XMLHttpRequest) {
        var ajax = new XMLHttpRequest()
    } else if (window.ActiveXObject) {
        var ajax = new ActiveXObject("Microsoft.XMLHTTP")
    }
    formdata.append("bulletin", id);
    ajax.open("POST", "/ajax/delete_bulletin");
    ajax.send(formdata)
}

function filter_box() {
    _("filter_box").style.display = "block"
}

function fpreview() {
    var num = _("filter_type").value;
    _('f_demo').src = "/img/filters/filter" + num + ".jpg"
}

function sh_in(inbox) {
    if (_(inbox).style.display == "none") {
        _(inbox).style.display = "block";
        var formdata = new FormData();
        if (window.XMLHttpRequest) {
            var ajax = new XMLHttpRequest()
        } else if (window.ActiveXObject) {
            var ajax = new ActiveXObject("Microsoft.XMLHTTP")
        }
        formdata.append("id", inbox);
        ajax.open("POST", "/ajax/view_inbox");
        ajax.send(formdata)
    } else {
        _(inbox).style.display = "none"
    }
}

function delete_background() {
    _("bg_delete").disabled = !0;
    var formdata = new FormData();
    if (window.XMLHttpRequest) {
        var ajax = new XMLHttpRequest()
    } else if (window.ActiveXObject) {
        var ajax = new ActiveXObject("Microsoft.XMLHTTP")
    }
    formdata.append("bg", "ar");
    ajax.addEventListener("load", bg_del_comp, !1);
    ajax.open("POST", "/ajax/delete_background");
    ajax.send(formdata)
}

function bg_del_comp() {
    _("bg_upload").style.display = "block";
    _("bg_delete").style.display = "none"
}
$(".sub_button").click(function() {
    var User = $(".sub_button").attr("user");
    $.ajax({
        type: "POST",
        url: "/ajax/subscribe",
        data: {
            user: User
        },
        success: function(output) {
            if (output.response == "subscribed") {
                $(".sub_button").text("Unsubscribe")
            } else if (output.response == "unsubscribed") {
                $(".sub_button").text("Subscribe")
            }
        }
    })
});

function user_exists(user) {
    $.ajax({
        type: "POST",
        url: "/ajax/user_exists",
        data: {
            user: user
        },
        success: function(output) {
            if (output == "true") {
                document.getElementById("reg_submit").disabled = !0;
                $("#user_exists").css("display", "block");
                $("#user_exists").html("<strong>" + user + "</strong> is already in use!")
            } else {
                document.getElementById("reg_submit").disabled = !1;
                $("#user_exists").css("display", "none");
                $("#user_exists").html("")
            }
        }
    })
}
current_page = 0;

function wn(type) {
    if (type == "w_sh") {
        $("#w_sh").attr("src", "/img/shhd1.png")
    } else if (type == "w_fv") {
        $("#w_fv").attr("src", "/img/hehd1.png")
    } else if (type == "w_pl") {
        $("#w_pl").attr("src", "/img/plhd1.png")
    } else if (type == "w_fl") {
        $("#w_fl").attr("src", "/img/flhd1.png")
    }
}

function wl(type) {
    if (type == "w_sh" && current_page !== 0) {
        $("#w_sh").attr("src", "/img/shhd0.png")
    } else if (type == "w_fv" && current_page !== 1) {
        $("#w_fv").attr("src", "/img/hehd0.png")
    } else if (type == "w_pl" && current_page !== 2) {
        $("#w_pl").attr("src", "/img/plhd0.png")
    } else if (type == "w_fl" && current_page !== 3) {
        $("#w_fl").attr("src", "/img/flhd0.png")
    }
}

function wc(type) {
    let $link;
    if (type === "w_sh") $link = $("#w_sh").closest("a");
    else if (type === "w_fv") $link = $("#w_fv").closest("a");
    else if (type === "w_pl") $link = $("#w_pl").closest("a");
    else if (type === "w_fl") $link = $("#w_fl").closest("a");

    if ($link.hasClass("active")) return;

    $(".w_lnks a").removeClass("active");
    $link.addClass("active");

    if (type == "w_sh" && current_page !== 0) {
        current_page = 0;
        $("#w_sh").attr("src", "/img/shhd1.png");
        $("#w_fv").attr("src", "/img/hehd0.png");
        $("#w_pl").attr("src", "/img/plhd0.png");
        $("#w_fl").attr("src", "/img/flhd0.png");
        $("#w_sel").css("left", "84px");
        $("#w_sh_cnt").removeClass("hddn");
        $("#w_fv_cnt").addClass("hddn");
        $("#w_pl_cnt").addClass("hddn");
        $("#w_fl_cnt").addClass("hddn");
    } else if (type == "w_fv" && current_page !== 1) {
        current_page = 1;
        $("#w_fv").attr("src", "/img/hehd1.png");
        $("#w_sh").attr("src", "/img/shhd0.png");
        $("#w_pl").attr("src", "/img/plhd0.png");
        $("#w_fl").attr("src", "/img/flhd0.png");
        $("#w_sel").css("left", "241px");
        $("#w_sh_cnt").addClass("hddn");
        $("#w_fv_cnt").removeClass("hddn");
        $("#w_pl_cnt").addClass("hddn");
        $("#w_fl_cnt").addClass("hddn");
    } else if (type == "w_pl" && current_page !== 2) {
        current_page = 2;
        $("#w_pl").attr("src", "/img/plhd1.png");
        $("#w_sh").attr("src", "/img/shhd0.png");
        $("#w_fv").attr("src", "/img/hehd0.png");
        $("#w_fl").attr("src", "/img/flhd0.png");
        $("#w_sel").css("left", "401px");
        $("#w_sh_cnt").addClass("hddn");
        $("#w_fv_cnt").addClass("hddn");
        $("#w_pl_cnt").removeClass("hddn");
        $("#w_fl_cnt").addClass("hddn");
    } else if (type == "w_fl" && current_page !== 3) {
        current_page = 3;
        $("#w_fl").attr("src", "/img/flhd1.png");
        $("#w_pl").attr("src", "/img/plhd0.png");
        $("#w_fv").attr("src", "/img/hehd0.png");
        $("#w_sh").attr("src", "/img/shhd0.png");
        $("#w_sel").css("left", "561px");
        $("#w_sh_cnt").addClass("hddn");
        $("#w_fv_cnt").addClass("hddn");
        $("#w_pl_cnt").addClass("hddn");
        $("#w_fl_cnt").removeClass("hddn");
    }
}

function add_video_favorite(video) {
    $.ajax({
        type: "POST",
        url: "/ajax/favorite_video",
        data: {
            id: video
        },
        success: function(output) {
            if (output.response == "added") {
                $("#w_ff").html("The video has been successfully <strong>added</strong> to your favorites!")
            } else if (output.response == "removed") {
                $("#w_ff").html("The video has been successfully <strong>removed</strong> from your favorites!")
            }
        }
    })
}

function add_to_playlist(video) {
    var PURL = $("#watch_playlist option:selected").val();
    if (PURL != undefined) {
        $.ajax({
            type: "POST",
            url: "/ajax/add_to_playlist",
            data: {
                pid: PURL,
                id: video
            },
            success: function(output) {
                if (output.response == "success") {
                    $("#wnn").html("Video successfully added to playlist!")
                } else if (output.response == "already") {
                    alert("Video is already in this playlist!")
                }
            }
        })
    } else {
        alert("Please select a playlist!")
    }
}

function rate_video(video, rate) {
    if (rate == 1) {
        $("#ratings").html("Terrible...")
    } else if (rate == 2) {
        $("#ratings").html("Bad..")
    } else if (rate == 3) {
        $("#ratings").html("OK.")
    } else if (rate == 4) {
        $("#ratings").html("Good!")
    } else if (rate == 5) {
        $("#ratings").html("Very Good!")
    }
    $.ajax({
        type: "POST",
        url: "/ajax/rate_video",
        data: {
            r: rate,
            v: video
        }
    })
}
$("#w_com").click(function() {
    $("#w_com_sct").removeClass("hddn");
    if ($("#w_stats_sct").hasClass("hddn") == !1) {
        $("#w_stats_sct").addClass("hddn")
    }
    $("#w_stats").removeClass("big_sel");
    $("#w_com").addClass("big_sel")
});
$("#w_stats").click(function() {
    $("#w_stats_sct").removeClass("hddn");
    if ($("#w_com_sct").hasClass("hddn") == !1) {
        $("#w_com_sct").addClass("hddn")
    }
    $("#w_com").removeClass("big_sel");
    $("#w_stats").addClass("big_sel")
});
$(".u_sct").click(function() {
    if ($(this).next().css("display") == "block") {
        $(this).next().css("display", "none");
        $(this).children('img').first().attr("src", "/img/clp00.png")
    } else {
        $(this).next().css("display", "block");
        $(this).children('img').first().attr("src", "/img/clp11.png")
    }
});

function textCounter(e, s, n) {
    var t = document.getElementById(s);
    return e.value.length > n ? (e.value = e.value.substring(0, n), !1) : void(t.innerHTML = e.value.length)
}

function show_reply(id, is_owner, reply_to) {
    if ($("#wtr_" + id).length == 0) {
        $("#r_cmt_" + id).html('' + '<span style="display:block;margin-left:54px;margin-bottom:14px" id="wtr_' + id + '">' + '<textarea id="txt_' + id + '" placeholder="Reply to comment..." style="border: 1px solid #d5d5d5;outline:0;border-radius:4px;padding:3px;margin-top:7px;margin-bottom:4px;width:482px;height:64px" maxlength="1000"></textarea>' + '<div><button class="search_button" onclick="add_reply(' + id + ',' + is_owner + ',\'' + reply_to + '\')">Post Comment</button> <button class="search_button" onclick="show_reply(' + id + ')">Cancel</button></div>' + '' + '' + '' + '</span>');
        $("#r_cmt_" + id).find("textarea").focus();
        if (reply_to) $("#r_cmt_" + id).find("textarea").val("@" + reply_to + " ").focus()
    } else {
        $("#r_cmt_" + id).html('')
    }
}

function add_reply(id, is_owner, reply_to) {
    if ($("#txt_" + id).val().length > 2) {
        var Text = $("#txt_" + id).val();
        if (reply_to) {
            if (Text.indexOf("@" + reply_to + " ") == -1) {
                Text = "@" + reply_to + " " + Text
            }
        }
        show_reply(id);

        apiPost("/ajax/reply_comment", {
            comment_id: id,
            vl_comment: Text
        }, function(err, res) {
            if (err && logApiErrorsIfAny(err, res, 'Failed to post reply', true)) {
                return;
            } else if(!res.success) {
                alert(res.error.message);
            }

            $("#r_cmt_" + id).before(renderCommentHtml(true, res.data.id, res.data.markup, res.data.by_user, res.data.avatar, is_owner, null));
        });
    } else {
        alert("Your reply must have more than 2 characters!")
    }
}

function show_all_replies(id) {
    $.ajax({
        type: "POST",
        url: "/ajax/all_replies",
        data: {
            comment_id: id
        },
        success: function(output) {
            $("#sa_" + id).replaceWith(output)
        }
    })
}

/**
 * Toggles the comment editing box for the comment with the specified ID
 * @param {number} id The comment ID
 */
function toggleEditComment(id) {
    // Get elements
    var containerElem = document.getElementById('wt_'+id);
    var contentElem = document.getElementById('comment-'+id+'-content');
    var editContainerElem = document.getElementById('comment-'+id+'-edit-container');
    var editInputElem = document.getElementById('comment-'+id+'-edit-input');
    var editedTextElem = document.getElementById('comment-'+id+'-edited-text');

    // Return immediately if the comment is currently saving
    if(containerElem.dataset.saving === '1')
        return;

    if(containerElem.dataset.editing === '0') {
        // Show editor
        contentElem.style.display = 'none';
        editContainerElem.style.display = 'block';
        if(editedTextElem)
            editedTextElem.style.display = 'none';

        // Set editor content
        editInputElem.value = contentElem.innerText.trim();

        containerElem.dataset.editing = '1';
    } else {
        // Close editor
        contentElem.style.display = 'block';
        editContainerElem.style.display = 'none';
        if(editedTextElem)
            editedTextElem.style.display = 'block';

        containerElem.dataset.editing = '0';
    }
}

/**
 * Saves an edit to the comment with the specified ID
 * @param {number} id The comment ID
 */
function saveCommentEdit(id) {
    /**
     * @type {HTMLInputElement}
     */
    var inputElem = document.getElementById('comment-'+id+'-edit-input');
    var contentElem = document.getElementById('comment-'+id+'-content');
    var containerElem = document.getElementById('wt_'+id);

    // Get content
    var content = inputElem.value.trim();

    // Check if it's empty
    if(content.length < 1) {
        alert('You must write a comment');
        return;
    }

    // Save
    inputElem.disabled = true;
    containerElem.dataset.saving = '1';
    apiPost('/ajax/edit_video_comment', {
        id: id,
        content: content
    }, function (err, res) {
        inputElem.disabled = false;
        containerElem.dataset.saving = '0';

        // Check for transport error
        if(err) {
            console.error('Failed to save comment edit: ', err);
            alert('Failed to save comment edit due to an error');
            return;
        }

        // Check for failure
        if(!res.success) {
            console.error('Server denied comment edit request: ', res.errors);
            alert(res.error.message);
            return;
        }

        // Replace comment markup
        contentElem.innerHTML = res.data.markup;

        // Hide editor
        toggleEditComment(id);
    });
}

/**
 * Renders comment HTML
 * @param {boolean} isReply Whether the comment is a reply
 * @param {number} id The comment ID
 * @param {string} contentMarkup The comment content HTML
 * @param {string} author The comment author
 * @param {string} avatar The comment author's avatar URL
 * @param {boolean} isUploader Whether the commenter is the video uploader
 * @param {string|null} editedDate The comment edit date, or null if it was not edited
 * @returns {string} The rendered comment HTML
 */
function renderCommentHtml(isReply, id, contentMarkup, author, avatar, isUploader, editedDate) {
    var style = isUploader ? 'style="background:#fffcc2"' : '';

    return ''
        + '<div class="wt_c_sct '+(isReply ? 'wt_r_sct' : '')+'" id="wt_'+id+'" op="true" data-editing="0">'
            + '<div '+style+'>'
                + '<a href="/user/'+author+'">'+author+'</a> <span>(just now)</span>'
                + '<div>'
                    + (isReply ? '' : '<a href="javascript:void(0)" onclick="show_reply('+id+', false, \''+author+'\')">Reply</a>')
                    + '<a href="javascript:void(0)" onclick="toggleEditComment('+id+')" style="padding-left:9px;margin-left:9px;'+(isReply ? '' : 'border-left:1px solid #7d7d7d')+'">Edit</a>'
                    + '<a href="javascript:void(0)" onclick="delete_wtc('+id+')" style="padding-left:9px;margin-left:9px;border-left:1px solid #7d7d7d">Delete</a>'
                + '</div>'
            + '</div>'
            + '<div>'
                + '<a href="/user/'+author+'">'
                    + '<img src="'+avatar+'" class="avt2 wp_avt" alt="'+author+'" width="41" height="41">'
                + '</a>'
                + '<div>'
                    + '<span>0</span>'
                    + '<img src="/img/td0.png">'
                    + '<img src="/img/tu0.png">'
                + '</div>'
                + '<div class="comment-content" id="comment-'+id+'-content" '+(isReply ? 'style="width:442px"' : '')+'>'
                    + contentMarkup
                + '</div>'
                + (editedDate ? '<div class="comment-edited-text" title="Edited on '+editedDate+'">(edited)</div>' : '')
                + '<div class="comment-edit-container" id="comment-'+id+'-edit-container" style="display: none">'
                    + '<textarea'
                    + ' id="comment-'+id+'-edit-input"'
                    + ' class="comment-textarea comment-edit-input"'
                    + ' cols="52" rows="5"'
                    + (isReply ? ' style="width: calc(100% - 50px)"' : '')
                    + ' maxLength="1000"'
                    + '></textarea>'
                    + '<div>'
                        + '<button class="search_button save-comment-edit-button" onClick="saveCommentEdit('+id+')">Save</button>'
                        + '<button class="search_button" onClick="toggleEditComment('+id+')">Cancel</button>'
                    + '</div>'
                + '</div>'
            + '</div>'
        + '</div>'
        + (isReply ? '' : '<div id="r_cmt_' + id + '"></div>')
}

function post_video_comment(url, is_owner) {
    var commentInput = document.getElementById('comment_textarea')
    var vidBtn = document.getElementById('video_button');
    var comment = commentInput.value;
    vidBtn.disabled = !0;
    vidBtn.innerHTML = 'Posting Comment...';
    if (comment.length > 0) {
        $('#no_video_comments').remove();
        apiPost('/ajax/df_comment', {
            video_url: url,
            vl_comment: comment
        }, function(err, res) {
            vidBtn.disabled = !1;
            vidBtn.innerHTML = 'Post Comment';

            if (err && logApiErrorsIfAny(err, res, 'Failed to post comment', true)) {
                return;
            } else if(!res.success) {
                alert(res.error.message);
            } else {
                document.getElementById("comment_textarea").value = "";
            }

            document.getElementById("video_button").disabled = !1;
            document.getElementById("video_button").innerHTML = "Post Comment";
            document.getElementById('counter').innerHTML = '0';

            $('#video_comments_section').prepend(renderCommentHtml(false, res.data.id, res.data.markup, res.data.by_user, res.data.avatar, is_owner, res.data.edited_date));

            var cmtNum = $('#cmt_num');
            var num = cmtNum.text();
            num++;
            cmtNum.html(num);
            $('html, body').animate({
                scrollTop: cmtNum.offset().top
            }, 400)
        });
    } else {
        alert("Your comment can't be empty!");
        document.getElementById("video_button").disabled = !1;
        document.getElementById("video_button").innerHTML = "Post Comment"
    }
}

function flag_video(url) {
    var flag = $("#flag_select option:selected").val();

    apiPost('/ajax/flag_video', { flag: flag, url: url }, function(err, res) {
        if(err) {
            console.error('Failed to flag video: ', err);
            return;
        }

        if(res.success)
            $('#w_flag').html('<strong>Thank you for the report! We will take a look at it!</strong>');
        else
            $('#w_flag').html('<strong>' + sanitizeHTML(res.error.message) + '</strong>');
    });
}

function delete_wtc(id) {
    if (confirm("Are you sure you want to delete this comment?")) {
        var commentElem = $("#wt_" + id);
        var opElems = $("div[op=" + id + "]");

        // Preemptively fade out comments
        commentElem.fadeOut(300);
        opElems.fadeOut(300);

        // Delete comment
        apiPost('/ajax/delete_video_comment', { id: id }, function(err, res) {
            // Check for errors
            var ok = true;
            if(err) {
                console.error('Failed to delete video comment ID '+id+': ', err);
                alert('Failed to delete video comment due to error communicating with server');
                ok = false;
            } else if(!res.success) {
                console.error('Failed to delete video comment ID '+id+': ', res.errors);
                alert(res.error.message);
                ok = false;
            }

            // If there was a failure, fade the comments back in
            if(!ok) {
                commentElem.fadeIn(300);
                opElems.fadeIn(300);
                return;
            }

            // If all is well, update count and remove elements
            if (!document.getElementById("wt_" + id).hasAttribute("op")) {
                var num = document.getElementById("cmt_num").innerText;
                num--;
                $("#cmt_num").html(num);
            }
            commentElem.remove();
            opElems.remove();
        })
    }
}
$("#show_more").click(function() {
    $("#des_info").toggleClass("hddn");
    if ($("#des_text").css("max-height") == "84px") {
        $(this).html("less info");
        $("#des_text").css("max-height", "none");
        $("#des_text").css("webkit-line-clamp", "unset")
    } else {
        $(this).html("more info");
        $("#des_text").css("max-height", "84px");
        $("#des_text").css("webkit-line-clamp", "5")
    }
});

function wr(comment, type, thumps) {
    if (type == 1) {
        $(thumps).attr("onclick", "");
        $(thumps).prev().attr("onclick", "wr(" + comment + ",'0',this)");
        $(thumps).attr("src", "/img/tu1.png");
        $(thumps).prev().css("opacity", "");
        var Number = $(thumps).prev().prev().text();
        if ($(thumps).prev().attr("src") == "/img/td1.png") {
            Number++;
            Number++
        } else {
            Number++
        }
        $(thumps).prev().attr("src", "/img/td0.png");
        if (Number > 0) {
            $(thumps).prev().prev().css("color", "green")
        } else if (Number < 0) {
            $(thumps).prev().prev().css("color", "red")
        } else if (Number == 0) {
            $(thumps).prev().prev().css("color", "gray")
        }
        $(thumps).prev().prev().html(Number)
    } else if (type == 0) {
        $(thumps).attr("onclick", "");
        $(thumps).next().attr("onclick", "wr(" + comment + ",'1',this)");
        $(thumps).attr("src", "/img/td1.png");
        $(thumps).next().css("opacity", "");
        var Number = $(thumps).prev().text()
        if ($(thumps).next().attr("src") == "/img/tu1.png") {
            Number -= 2
        } else {
            Number -= 1
        }
        $(thumps).next().attr("src", "/img/tu0.png");
        if (Number > 0) {
            $(thumps).prev().css("color", "green")
        } else if (Number < 0) {
            $(thumps).prev().css("color", "red")
        } else if (Number == 0) {
            $(thumps).prev().css("color", "gray")
        }
        $(thumps).prev().html(Number)
    }
    $(thumps).css("opacity", "0.75");
    $.ajax({
        type: "POST",
        url: "/ajax/rate_video_comment",
        data: {
            id: comment,
            rate: type
        }
    })
}

function add_friend_in(username, id) {
    $.ajax({
        type: "POST",
        url: "/ajax/add_friend",
        data: {
            user: username
        },
        success: function(output) {
            $("#invite_" + username).remove();
            $("#i_" + id).remove();
            if ($(".in_sct").length == 0) {
                $(".inbox_seperation").parent().append('<tr><td colspan="4" align="center" style="font-size:19px;padding:55px">You don\'t seem to have any friend requests.</td></tr>')
                $("#in_pag").remove()
            }
        }
    })
}

function block_user(username) {
    $.ajax({
        type: "POST",
        url: "/ajax/block_user",
        data: {
            user: username
        },
        success: function(output) {
            if (output == 0) {
                $("#bu").html("Unblock User")
            } else {
                $("#bu").html("Block User")
            }
        }
    })
}

function accept_response(response) {
    $.ajax({
        type: "POST",
        url: "/ajax/accept_response",
        data: {
            response: response
        },
        success: function(output) {
            $("#rsp_" + response).remove();
            $("#i_" + response).remove();
            if ($(".in_sct").length == 0) {
                $(".inbox_seperation").parent().append('<tr><td colspan="4" align="center" style="font-size:19px;padding:55px">No one has submitted any responses to your videos.</td></tr>')
                $("#in_pag").remove()
            }
        }
    })
}

function deny_response(response) {
    $.ajax({
        type: "POST",
        url: "/ajax/deny_response",
        data: {
            response: response
        },
        success: function(output) {
            $("#rsp_" + response).remove();
            $("#i_" + response).remove();
            if ($(".in_sct").length == 0) {
                $(".inbox_seperation").parent().append('<tr><td colspan="4" align="center" style="font-size:19px;padding:55px">No one has submitted any responses to your videos.</td></tr>')
                $("#in_pag").remove()
            }
        }
    })
}

function deny_friend_in(username, id) {
    $.ajax({
        type: "POST",
        url: "/ajax/deny_friend",
        data: {
            user: username
        },
        success: function(output) {
            $("#invite_" + username).remove();
            $("#i_" + id).remove();
            if ($(".in_sct").length == 0) {
                $(".inbox_seperation").parent().append('<tr><td colspan="4" align="center" style="font-size:19px;padding:55px">You don\'t seem to have any friend requests.</td></tr>')
                $("#in_pag").remove()
            }
        }
    })
}
$(".in_sct").click(function() {
    var id = $(this).attr("inbox");
    $("#i_" + id).toggleClass("hddn");
    if ($(this).attr("seen") == 0 && $(this).attr("type") != "nt") {
        $(this).toggleClass("in_not");
        $(this).attr("seen", "1");
        if ($(this).attr("type") == "m") {
            $.ajax({
                url: "/ajax/inbox_actions",
                type: "post",
                data: {
                    action: "inblk_read",
                    selectedPM: [id],
                    ajax: !0
                }
            })
        } else {
            var formdata = new FormData();
            if (window.XMLHttpRequest) {
                var ajax = new XMLHttpRequest()
            } else if (window.ActiveXObject) {
                var ajax = new ActiveXObject("Microsoft.XMLHTTP")
            }
            formdata.append("id", $(this).attr("type") + "_" + id);
            ajax.open("POST", "/ajax/view_inbox");
            ajax.send(formdata)
        }
    }
});

function change_comment_inbox() {
    var type = $("#comment_filter option:selected").val();
    if (type == "all") {
        window.location = "/inbox?page=comments"
    } else if (type == "video") {
        window.location = "/inbox?page=comments&t=2"
    } else if (type == "channel") {
        window.location = "/inbox?page=comments&t=3"
    } else if (type == "mention") {
        window.location = "/inbox?page=comments&t=4"
    } else if (type == "reply") {
        window.location = "/inbox?page=comments&t=5"
    }
}
function change_contest_inbox() {
	var type = Number($("#contest_filter option:selected").val());
	if (type == "all") {
		window.location = "/inbox?page=contests"
	} else {
		window.location = "/inbox?page=contests&t=" + type
	}
}

$(".irs_cancel").click(function() {
    var irs = $(this).parents(".inbox_reply_section");
    irs.removeClass("open");
    irs.find("textarea").val("")
});
$(".irs_delete").click(function() {
    if (confirm("Are you sure you want to delete this message?")) {
        var bt = $(this);
        var m_id = bt.parents(".in_message").attr("id");
        m_id = m_id.substr(2);
        var page = $("#inbox_page").val();
        bt.prop("disabled", !0);
        $.ajax({
            url: "/ajax/inbox_actions",
            type: "post",
            data: {
                selectedPM: [m_id],
                action: "inblk_del",
                ajax: !0,
                page: page
            },
            success: function(r) {
                if (r == 0) alert("An error has occurred while processing your request.");
                else return location.href = r
            },
            error: function() {
                alert("An error has occurred while processing your request.")
            },
            complete: function() {
                bt.prop("disabled", !1)
            }
        })
    }
});

function showBulk() {
    if ($("input[name='selectedPM[]']:checked").length) {
        $(".in_bulk").prop("disabled", !1)
    } else {
        $(".in_bulk").prop("disabled", !0)
    }
}
$("input[name='selectedPM[]']").click(function(e) {
    showBulk();
    e.stopPropagation()
});
$("#selectAllPM").click(function() {
    var c = $(this).prop("checked");
    var pms = $("input[name='selectedPM[]']");
    for (var i = 0; i < pms.length; i++) {
        pms.eq(i).prop("checked", c)
    }
    showBulk()
});
$(".in_bulk").click(function() {
    var id = $(this).attr("id");
    var bt = $(".in_bulk");
    if (id == "inblk_del") {
        if (!confirm("Are you sure you want to delete all these messages?")) {
            return
        }
    } else if (id == "inblkc_del") {
        if (!confirm("Are you sure you want to delete all these comments from your inbox?")) {
            return
        }
    } else if (id == "inblkr_accept") {
        if (!confirm("Are you sure you want to accept all these video responses?")) {
            return
        }
    } else if (id == "inblkr_decline") {
        if (!confirm("Are you sure you want to decline all these video responses?")) {
            return
        }
    } else if (id == "inblki_accept") {
        if (!confirm("Are you sure you want to accept all these invitations?")) {
            return
        }
    } else if (id == "inblki_decline") {
        if (!confirm("Are you sure you want to decline all these invitations?")) {
            return
        }
    }
    bt.prop("disabled", !0);
    $("#inblk_action").val(id);
    $("#inblk_form")[0].submit()
});
$("a.irs_reply").click(function() {
    var bt = $(this).children("button");
    if (bt.prop("disabled")) return !1;
    var irs = $(this).parents(".inbox_reply_section");
    if (!irs.hasClass("open")) {
        irs.addClass("open");
        irs.find("textarea").focus()
    } else {
        var txt = irs.find("textarea").val();
        var su = irs.find(".irs_subject").val();
        var tu = irs.find(".irs_user").val();
        var page = $("#inbox_page").val();
        if (txt == "") {
            alert("Please, fill out the message body before sending a message.");
            return !1
        }
        bt.prop("disabled", !0);
        $.ajax({
            url: "/ajax/inbox_actions",
            type: "post",
            data: {
                to_user: tu,
                subject: su,
                message: txt,
                action: "send",
                page: page
            },
            success: function(r) {
                if (r == 0) alert("An error has occurred while processing your request.");
                else location.href = r
            },
            error: function() {
                alert("An error has occurred while processing your request.")
            },
            complete: function() {
                bt.prop("disabled", !1)
            }
        })
    }
    return !1
});

function move_up(id) {
    var next = $("#" + id).prev().attr('id');
    if (next != "mod_selector" && next != "home_congrats") {
        $("#" + id).swapWith("#" + next);
        var p_cookie = getCookie("po");
        if (p_cookie == "") {
            p_cookie = "0=s,1=r,2=b,3=f,4=m"
        }
        var e1 = id.charAt(0);
        var e2 = next.charAt(0);
        p_cookie = p_cookie.replace(e1, e2);
        p_cookie = p_cookie.replace(e2, e1);
        setCookie("po", p_cookie, 31)
    }
}

function close_achievement() {
    var type = $("#home_congrats").attr("type");
    $("#home_congrats").remove();
    $.ajax({
        url: "/ajax/close_achievement",
        type: "post",
        data: {
            type: type
        },
        success: function(r) {
            if (r.length > 0) {
                $("#mod_selector").after(r)
            }
        }
    })
}

function move_down(id) {
    var prev = $("#" + id).next().attr('id');
    if (prev != undefined) {
        $("#" + id).swapWith("#" + prev);
        var p_cookie = getCookie("po");
        if (p_cookie == "") {
            p_cookie = "0=s,1=r,2=b,3=f,4=m"
        }
        var e1 = id.charAt(0);
        var e2 = prev.charAt(0);
        p_cookie = p_cookie.replace(e2, e1);
        p_cookie = p_cookie.replace(e1, e2);
        setCookie("po", p_cookie, 31)
    }
}
jQuery.fn.swapWith = function(to) {
    return this.each(function() {
        var copy_to = $(to).clone(!0);
        var copy_from = $(this).clone(!0);
        $(to).replaceWith(copy_from);
        $(this).replaceWith(copy_to)
    })
};

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1)
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length)
        }
    }
    return ""
}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/"
}

$(document).ready(function() {
    // Show JS player if present
    var jsPlayerContainer = document.getElementById("vtbl_pl");
    if(jsPlayerContainer)
        jsPlayerContainer.style.display = "inline-block";

    // Apply event code for all players (if present)
    var vlPlayer = document.getElementsByClassName('vlPlayer')[0];

    // If a player was found, add event handlers
    if(vlPlayer) {
        vlPlayer.addEventListener('keydown', function (e) {
            // Toggle fullscreen if F key is pressed
            if (e.key === 'f') {
                if ((window.fullScreen) || (window.innerWidth === screen.width && window.innerHeight === screen.height)) {
                    // Exit fullscreen
                    document.exitFullscreen();
                } else {
                    // Enter fullscreen
                    vlPlayer.requestFullscreen();
                }
            }
        });
    }
});

/**
 * Performs a friend action.
 * @param username The username of the channel to perform the action on
 * @param {'send_request'|'accept_request'|'deny_request'|'cancel_request'|'delete'} action The action to perform
 * @param {() => void} [successCallback] The callback to run after the action is successful (optional)
 * @returns {void}
 */
function friendAction(username, action, successCallback) {
    if (action === 'cancel_request') {
        if (!confirm("Are you sure you want to cancel the friend invite?")) {
            return;
        }
    } else if (action === 'delete') {
        if (!confirm("Are you sure you want to remove this channel as a friend?")) {
            return;
        }
    }

    apiPost('/ajax/friend_action', {
        user: username,
        action: action
    }, function(err, res) {
        if (err && logApiErrorsIfAny(err, res, 'Failed to ' + action + ' friend request')) {
            return;
        } else if (!res.success) {
            alert(res.error.message);
            return;
        }

        const btn = document.getElementById('aaf');

        switch (action) {
            case 'send_request':
                if (btn) {
                    btn.innerText = 'Cancel Invite';
                    btn.onclick = function () {
                        friendAction(username, 'cancel_request');
                    }
                }
                break;

            case 'accept_request':
                if (btn) {
                    btn.innerText = 'Unfriend';
                    btn.onclick = function () {
                        friendAction(username, 'delete');
                    }
                }
                break;

            case 'cancel_request':
            case 'delete':
                if (btn) {
                    btn.innerText = 'Add Friend';
                    btn.onclick = function () {
                        friendAction(username, 'send_request');
                    }
                }
                break;
        }

        if (successCallback) {
            successCallback();
        }
    });
}

/**
 * Votes for a contest submission.
 * @param {string} contestUid The contest UID.
 * @param {string} videoUid The video UID.
 * @param {boolean} cancel Whether to cancel/remove the vote.
 * @param {(err: Error|null, res?: ApiResponse<T>) => void} callback The callback
 */
function contestVote(contestUid, videoUid, cancel, callback) {
	apiPost('/ajax/contest_action', {
		action: 'vote',
		contest_uid: contestUid,
		video_uid: videoUid,
		cancel: cancel,
	}, callback, {
		useJsonBody: true,
		errorLoggingType: 'alert',
	})
}

/**
 * Deletes a contest submission.
 * @param {string} contestUid The contest UID.
 * @param {string} videoUid The video UID.
 * @param {boolean} onlyIfNotApproved Whether to not delete the submission if it approved.
 * @param {(err: Error|null, res?: ApiResponse<T>) => void} callback The callback
 */
function contestDeleteSubmission(contestUid, videoUid, onlyIfNotApproved, callback) {
	apiPost('/ajax/contest_action', {
		action: 'delete_submission',
		contest_uid: contestUid,
		video_uid: videoUid,
		only_if_not_approved: onlyIfNotApproved,
	}, callback, {
		useJsonBody: true,
		errorLoggingType: 'alert',
	})
}

/**
 * Approves a contest submission.
 * @param {string} contestUid The contest UID.
 * @param {string} videoUid The video UID.
 * @param {(err: Error|null, res?: ApiResponse<T>) => void} callback The callback
 */
function contestApproveSubmission(contestUid, videoUid, callback) {
	apiPost('/ajax/contest_action', {
		action: 'approve_submission',
		contest_uid: contestUid,
		video_uid: videoUid,
	}, callback, {
		useJsonBody: true,
		errorLoggingType: 'alert',
	})
}

;(function() {
	var form = /** @type {HTMLFormElement | null} */ (document.getElementById('bulletins-feed-filters'))
	if (form == null) {
		return
	}

	var filterFriends = /** @type {HTMLInputElement} */ (document.getElementById('bulletins-filter-friends'))
	var filterSubs = /** @type {HTMLInputElement} */ (document.getElementById('bulletins-filter-subscriptions'))
	var container = /** @type {HTMLDivElement} */ (document.getElementById('bulletins-feed-container'))

	function get() {
		var formData = new FormData(form)
		var xhr = new XMLHttpRequest()
		xhr.open('POST', form.action)
		xhr.send(formData)
		xhr.onreadystatechange = function () {
			if (xhr.readyState === 4 && xhr.status === 200) {
				container.innerHTML = xhr.responseText
			}
		}
	}

	filterFriends.addEventListener('change', get)
	filterSubs.addEventListener('change', get)
})();

/**
 * Requests search suggestions
 * @param {string} q The search input.
 * @param {string} f Search type.
 */
let qVal="",lock=!1,timer,delay=300,disabled=!1;

function truncate(s,n){return s.length>n?s.substring(0,n)+"...":s}
function highlight(s,term){return s.replace(new RegExp("("+term.replace(/[-\/\\^$*+?.()|[\]{}]/g,'\\$&')+")","ig"),"<strong>$1</strong>")}

function search(){ 
    if(disabled) return; 
    let val=$("#search_input").val(),filter=$("#filter_select").val(); 
    if(val.length>1&&!lock){ 
        qVal=val; 
        $.post("/results",{q:val,f:filter},function(res){ 
            let box=$("#searchbvox"); box.empty(); 
            if(!res.success||!res.data?.length){$("#search_container").hide();return} 
            if(!$("#search_container").parent().is("body")) $("#search_container").appendTo("body"); 
            let pos=$("#search_input").offset(); 
            $("#search_container").css({position:"absolute",top:pos.top+$("#search_input").outerHeight()-2,left:pos.left,width:273,zIndex:9999999,borderRadius:"0 0 3px 3px"}); 
            
            res.data.forEach(function(item,i){ 
                let text=highlight(truncate(item.value,30),val),
                    el=$("<span>").addClass("autocomplete-item").attr({"data-type":item.type,"data-value":item.value}).css({display:"block",width:"100%",padding:"1px 3px 1px 3px",boxSizing:"border-box",background:"white",textAlign:"left",borderTop:i?"0":"1px solid #ccc",cursor:"default",fontSize:"13px",color:"black"}).html(text); 
                if(!i) el.append($("<span>").css({float:"right",fontSize:"10px",color:"#666"}).text("Suggestions")); 
                
                el.hover(function(){$(this).css({background:"#3366cc",color:"#fff"});$(this).find("span").css("color","#fff")},
                         function(){$(this).css({background:"white",color:"black"});$(this).find("span").css("color","#666")}); 
                
                el.click(function(){lock=!0;$("#search_input").val($(this).data("value"));$("#search_container").hide()}); 
                box.append(el); 
            }); 
            
            box.append($("<span>").addClass("autocomplete-close").css({display:"block",width:"100%",padding:"3px 3px 3px 3px",boxSizing:"border-box",background:"white",textAlign:"right",fontSize:"10px",textDecoration:"underline",color:"#0033cc",cursor:"pointer"}).text("close").click(function(){disabled=!0;$("#search_container").hide();setTimeout(function(){disabled=!1},200)})); 
            
            $("#search_container").show(); 
        },"json"); 
    } else if(!lock){$("#search_container").hide();$("#searchbvox").empty()} 
}

$("#search_input").on("input",function(){lock=!1;clearTimeout(timer);timer=setTimeout(search,delay)})
                .on("focus",function(){if(!disabled){lock=!1;search()}})
$("#filter_select").change(function(){lock=!1;clearTimeout(timer);timer=setTimeout(search,delay)})
$(document).on("click",function(e){if(!$(e.target).closest("#search_container,#search_input,#filter_select").length) $("#search_container").hide()})
