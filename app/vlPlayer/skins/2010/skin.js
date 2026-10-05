function VLPSkin(e, s, i) {
  var t, n, o, l, a, d = false, v = false, c = false, p = false, u = true, h = false;
  this.setIntervals = function () {}, this.clearIntervals = function () {
    clearInterval(n), clearInterval(o), clearInterval(l);
  }, this.resize = function () {}, this.showPlayAnimation = function () {
    var t, e;
    e = r.hasClass("playing") ? Ex : Cx, (tx = e.find("svg")).stop(), tx.css({opacity: 0, width: 50, height: 50}), tx.animate({width: 125, height: 125}, {duration: 500, queue: false}), tx.animate({opacity: 0.5}, {duration: 250, queue: false, complete: function () {
      tx.animate({opacity: 0}, 250, function () {
        e.hide();
      });
    }}), e.show();
  }, this.changeElapsed = function (s, i) {
    var t = s / e.duration * 100;
    re.html(f.format(s)), i || (b.css("margin-left", t + "%"), setTimeout(function () {
      ex();
    }, 1), ue.css("width", t + "%"));
  }, this.changeDuration = function (s) {
    fe.html(f.format(s)), d = false, e.hd ? M.show() : M.hide();
  }, this.changeBuffer = function (e) {
    y.css("width", e);
  }, this.changeVolume = function (e) {
    var s = E.width() - E.width() * e;
    pe.css("margin-left", 100 * e + "%"), E.css("background-position-x", "-" + s + "px");
  }, this.loopChange = function () {
    r.hasClass("loop") ? (Y.removeClass("active"), _.addClass("active")) : (Y.addClass("active"), _.removeClass("active"));
  }, this.rateChange = function (e) {
    var s = [0.25, 0.5, 1, 2, 4].indexOf(e);
    A.find(".vloButton").removeClass("active"), A.find(".vloButton").eq(s).addClass("active");
  }, this.changePreview = function (e) {
    ae.css("background-image", ""), $("<img />").attr("src", e).on("load", function () {
      ae.css("background-image", "url(" + e + ")");
    });
  }, this.format = function (s, i) {
    var t, n, o, l, a;
    return i || 0 === i || (i = e.duration), this.parse = function (e, s) {
      return (e = parseInt(e)) < 10 ? s + e : e > 99 ? e : e;
    }, l = "", a = "", i >= 600 && (l = "0"), i >= 36e3 && (a = "0"), t = this.parse(s % 60, "0"), n = this.parse(s / 60, l), o = this.parse(s / 3600, a), i < 3600 ? n + ":" + t : o + ":" + (n % 60 < 10 ? "0" + n % 60 : n % 60) + ":" + t;
  }, this.calcSeek = function () {
    return 0 == e.duration ? 0 : (parseFloat(b.css("margin-left")) + 8) / k.width() * e.duration;
  }, this.showBuffer = function () {
    if (r.hasClass("playing") && null == n) {
      var e = 0;
      ve.show(), n = setInterval(function () {
        360 == e && (e = 0), ve.css("transform", "rotate(" + e + "deg)"), ve.css("-ms-transform", "rotate(" + e + "deg)"), e += 45;
      }, 77);
    }
  }, this.hideBuffer = function () {
    null != n && (clearInterval(n), ve.hide(), n = null);
  }, this.seekTo = function (s) {
    if (null == o && 1 == s.which) {
      var i, n, l, a = "click" == s.type;
      b.addClass("active"), $(document).mouseup(function () {
        b.removeClass("active");
      }), o = setInterval(function () {
        l = k.width(), i = e.mouseX - k.offset().left, (n = i - 8) < 0 && (n = 0), n > l - 16 && (n = l - 16), i < 0 && (i = 0), i > l && (i = l), t = e.duration * (i / l), b.css("margin-left", n), ex(), f.changeElapsed(t, true), f.showTime(), a && $(document).mouseup();
      }, 26);
    }
  }, this.showTime = function (s) {
    var i, t, n, o = k.offset().left, l = k.width(), a = e.mouseX - o;
    a < 0 && (a = 0), a > l && (a = l), i = e.duration * (a / l), Ce.html(f.format(i, i)), we.find("svg").hide(), we.find("svg").eq(1).show(), ge.show(), n = -6, (o = a - (t = ge.width()) / 2) < 4 && ((n += o - 4) <= -12 && (n = -12, we.find("svg").hide(), we.find("svg").eq(0).show()), o = 4), o > l - 4 - t && ((n += o + t - l + 4) >= 0 && (n = 4, we.find("svg").hide(), we.find("svg").eq(2).show()), o = l - 4 - t), ge.css("margin-left", o), we.find("svg").css("margin-left", n);
  }, this.hideTime = function (e) {
    v = false, null == o && ge.hide();
  }, this.volumeTo = function (s) {
    if (!l && 1 == s.which) {
      var i, t, n, a = "click" == s.type;
      I.addClass("active"), a ? h || (i = e.mouseX - E.offset().left, t = E.width(), i < 0 ? e.toggleMute() : (i > t && (i = t), n = i / t, e.setVolume(n))) : l = setInterval(function () {
        i = e.mouseX - E.offset().left, t = E.width(), !h && i < 0 || (h = true, i < 0 && (i = 0), i > t && (i = t), n = i / t, e.setVolume(n), a && $(document).mouseup());
      }, 26), h = false, f.showControls(), f.showProgress();
    }
  }, this.volumeWheel = function (s) {
    var i = e.videoObj.volume;
    return s.originalEvent.deltaY < 0 ? e.setVolume(i + 0.1) : e.setVolume(i - 0.1), false;
  }, this.showVolume = function () {
    l || V.stop().animate({width: 65}, 200), p = true;
  }, this.hideVolume = function () {
    l || V.stop().animate({width: 0}, 500), p = false;
  }, this.hideControls = function () {
    f.hideProgress(), u && null == l && null == o && (F.hasClass("active") || (P.stop().delay(1e3).animate({opacity: 0.5}, 500), C.stop().animate({"padding-bottom": 30}, 800), u = false));
  }, this.showControls = function () {
    u || (P.stop().animate({opacity: 1}, 200), C.stop().animate({"padding-bottom": 30}, 200), u = true);
  }, this.hideProgress = function () {
    c && null == l && null == o && (F.hasClass("active") || (w.stop().delay(1e3).animate({height: 3, top: 3}, 500), k.stop().delay(1e3).queue(function (next) {
      $(this).addClass("reduced");
    }), he.stop().delay(1e3).queue(function (next) {
      $(this).css("transform", "scale(0)");
    }), c = false));
  }, this.showProgress = function () {
    c || (w.stop().animate({height: 12, top: 0}, 200), k.stop().removeClass("reduced"), he.stop().css("transform", "scale(1)"), c = true);
  }, this.showOptions = function () {
    q.toggle(), f.showControls(), f.showProgress();
  }, this.hideOptions = function () {
    F.hasClass("active") && L.click();
  }, this.expand = function () {
    $(".vlExpand .vlTooltipText").html() == expand_text ? $(".vlExpand .vlTooltipText").html(shrink_text) : $(".vlExpand .vlTooltipText").html(expand_text);
    $(".vlExpand .vlTooltipContainer").css({right: 48 - $(".vlExpand .vlTooltipContainer").width() / 2 + "px"});
  expandPlayer();
    setTimeout(function () {
      ex();
    }, 200);
  }, this.toggleHD = function () {
    e.hd && f.hideEndScreen(function () {
      d = true, f.showBuffer(), e.toggleHD(), r.hasClass("hd720p") ? (Z.addClass("active"), N.removeClass("active")) : (Z.removeClass("active"), N.addClass("active")), r.hasClass("hd720p") ? $(".vlOptions span").replaceWith("<span>1080p<i></i></span>") : $(".vlOptions span").replaceWith("<span>480p<i></i></span>"), r.hasClass("ended") && e.play();
    });
  }, this.timeUpdate = function () {
    d || r.hasClass("ended") || (null == o && f.changeElapsed(e.videoObj.currentTime), e.bufferUpdate(), f.hideEndScreen());
  }, this.toggle = function () {
    r.hasClass("ended") ? f.hideEndScreen(e.toggle) : (f.showPlayAnimation(), e.toggle());
  }, this.ended = function () {
    r.hasClass("ended") || (r.hasClass("loop") || es.stop().show().css("opacity", 0).animate({opacity: 1}, 100), f.showControls(), e.ended());
  }, this.hideEndScreen = function (e) {
    es.is(":visible") ? es.stop().animate({opacity: 0}, 100, function () {
      es.hide(), "function" == typeof e && e();
    }) : "function" == typeof e && e();
  }, this.mouseUp = function (s) {
    this.seek = function () {
      r.hasClass("ended") ? (e.seek(t), e.play()) : (e.seek(t), r.hasClass("started") || e.play());
    }, null != o && (clearInterval(o), o = null, 0 != e.duration ? f.hideEndScreen(this.seek) : b.css("margin-left", 0), ex(), v || f.hideTime()), null != l && (clearInterval(l), l = null, p || f.hideVolume(), I.removeClass("active")), overContainer || f.hideControls();
  };
  var r = e.obj, f = this, m = ($('<div class="vlHidden"></div>'), $('<input type="text" tabindex="-1" />')), g = $('<div class="vlControls"></div>'), C = $('<div class="vlScreenContainer"></div>'), An = $('<div class="vlAnnotationsContainer"></div>'), w = $('<div class="vlProgressAll"></div>'), k = $('<div class="vlProgressBar"></div>'), B = $('<div class="vlEmptyBar"></div>'), y = $('<div class="vlBuffer"></div>'), T = $('<div class="vlProgress"><b></b></div>'), S = $('<div class="vlSeekerBar"></div>'), b = $('<div class="vlSeeker"><s><i></i></s></div>'), Px = $('<div class="vlControlsBarContainer"></div>'), P = $('<div class="vlControlsBar"></div>'), x = $('<div class="vlPlay vlButton vlButtonsLeft"><i></i></div>'), I = $('<div class="vlSound vlButtonsLeft"></div>'), O = $('<div class="vlButton vlMute"><div class="vlTooltipContainer mute" style=""><div class="vlTooltip"><div class="vlTooltipText" style="">' + mute_text + '</div><div class="vlTooltipSvg"><div><svg width="12" height="8" style="margin-left: -6px;"><polygon points="0,2 12,2 6,8" fill="#999999"></polygon><polygon points="1,0 11,0 11,2 6,7 1,2" fill="#101010"></polygon></svg></div></div></div></div><div class="vlTooltipContainer unmute" style=""><div class="vlTooltip"><div class="vlTooltipText" style="">' + unmute_text + '</div><div class="vlTooltipSvg"><div><svg width="12" height="8" style="margin-left: -6px;"><polygon points="0,2 12,2 6,8" fill="#999999"></polygon><polygon points="1,0 11,0 11,2 6,7 1,2" fill="#101010"></polygon></svg></div></div></div></div><i></i></div>'), Cx = $('<div class="vlPlayAnimation" style="display:none">		<div><svg width="100%" height="100%" viewbox="0 0 100 100">			<rect width="100" height="100" style="fill:black" />			<polygon points="32,28 66,50 32,72" style="fill:white" />		</svg></div>	</div>'), Ex = $('<div class="vlPauseAnimation" style="display:none">		<div><svg width="100%" height="100%" viewbox="0 0 100 100">			<rect width="100" height="100" style="fill:black" />			<rect x="34" y="28" width="10" height="44" style="fill:white" />			<rect x="56" y="28" width="10" height="44" style="fill:white" />		</svg></div>	</div>'), V = $('<div class="vlSoundBar"><div class="vlSoundShadow"></div></div>'), E = $('<div class="vlVolumeBar"><s></s></div>'), D = $('<div class="vlTime vlButton vlButtonsLeft"></div>'), R = $('<div class="vlTimeText"><span class="vlCurrent">0:00</span> / <span class="vlDuration">0:00</span></div>'), H = $('<div class="vlFullScreen vlButton vlButtonsRight"><div class="vlTooltipContainer" style=""><div class="vlTooltip"><div class="vlTooltipText" style="">' + fullscreen_text + '</div><div class="vlTooltipSvg"><div><svg width="12" height="8" style="margin-left: -6px;"><polygon points="0,2 12,2 6,8" fill="#999999"></polygon><polygon points="1,0 11,0 11,2 6,7 1,2" fill="#101010"></polygon></svg></div></div></div></div><i></i></div>'), PoP = $('<div class="vlPopOut vlButton vlButtonsRight"><div class="vlTooltipContainer" style=""><div class="vlTooltip"><div class="vlTooltipText" style="">' + popout_text + '</div><div class="vlTooltipSvg"><div><svg width="12" height="8" style="margin-left: -6px;"><polygon points="0,2 12,2 6,8" fill="#999999"></polygon><polygon points="1,0 11,0 11,2 6,7 1,2" fill="#101010"></polygon></svg></div></div></div></div><i></i></div>'), U = $('<div class="vlExpand vlButton vlButtonsRight"><div class="vlTooltipContainer" style=""><div class="vlTooltip"><div class="vlTooltipText" style="">' + expand_text + '</div><div class="vlTooltipSvg"><div><svg width="12" height="8" style="margin-left: -6px;"><polygon points="0,2 12,2 6,8" fill="#999999"></polygon><polygon points="1,0 11,0 11,2 6,7 1,2" fill="#101010"></polygon></svg></div></div></div></div><i></i></div>'), Ux = $('<div class="vlShadow vlButtonsRight"></div>'), F = $('<div class="vlOptions vlButtonsRight"></div>'), AnB = $('<div class="vlAnnotations active vlButtonsRight"></div>'), L = $("<span>" + (r.hasClass("hd720p") ? "1080" : "480") + "p<i></i></span>"), q = $('<div class="vlOptionsPopup"></div>'), j = $('<div class="vloTable"></div>'), X = $('<div class="vloTable"></div>'), z = $(), M = $('<div class="vloRow"></div>'), W = $('<div class="vloRow"></div>'), A = $(), G = $('<div class="vloCell">Loop:</div>'), Q = $('<div class="vloCell vloCell2"></div>'), Y = $('<div class="vloButton active">Off</div>'), _ = $('<div class="vloButton">On</div>'), J = $(""), K = $('<div class="vloCell vloCell2"></div>'), N = $('<div class="vloButton"><div></div>480p</div>'), Z = $('<div class="vloButton"><div></div>1080p<b>HD</b></div>'), ee = $(""), se = $('<div class="vloCell"><div class="vloButton">.25x</div></div>'), ie = $('<div class="vloCell"><div class="vloButton">.5x</div></div>'), te = $('<div class="vloCell"><div class="vloButton">1x</div></div>'), ne = $('<div class="vloCell"><div class="vloButton">2x</div></div>'), oe = $('<div class="vloCell"><div class="vloButton">4x</div></div>'), le = $('<div class="vlScreen"></div>'), ae = $('<div class="vlPreview"></div>'), de = $('<div class="vlPlayScreen"><i></i></div>'), es = $('<div class="vlEndScreen" style="display:none"></div>'), esi = $('<div class="vlEndActions">Just watched<br></div>'), rep = $('<div class="vlEndActionButton"><i class="vlReplayButton"></i>Replay</div>'), lik = $('<div class="vlEndActionButton" onclick="add_video_favorite(\'' + video_url +  '\')"><i class="vlLikeButton"></i>Favorite</div>'), sha = $(''), ve = $('<div class="vlBuffering"></div>'), ce = e.video, pe = E.find("> s"), ue = T.find("> b"), he = b.find("> s > i"), re = R.find("> .vlCurrent"), fe = R.find("> .vlDuration"), me = $('<div class="vlTimeShow"></div>'), ge = $('<div class="vlTimeShowContainer"></div>'), Ce = $('<div class="vlTimeShowText">0:00</div>'), we = $('<div class="vlTimeShowSvg">		<div>			<svg width="8" height="8">				<polygon points="0,2 8,2 0,8" fill="#999999"/>				<polygon points="1,0 7,0 7,2 1,7 1,2" fill="#101010"/>			</svg>			<svg width="12" height="8">				<polygon points="0,2 12,2 6,8" fill="#999999"/>				<polygon points="1,0 11,0 11,2 6,7 1,2" fill="#101010"/>			</svg>			<svg width="8" height="8">				<polygon points="0,2 8,2 8,8" fill="#999999"/>				<polygon points="1,0 7,0 7,7 1,2" fill="#101010"/>			</svg>		</div>	</div>');
  this.hiddenUrl = m;
  var $e = e.skinPath + "/skin.css?" + window.vlpv, ke = e.skinPath + "/img";
 $("#vlPlayer2010css").remove(), $('<link id="vlPlayer2010css" rel="stylesheet"></link>').attr("href", $e).appendTo("head").on("load", function () {
  n = ["red", "orange", "gold", "olive", "green", "teal", "blue", "violet", "pink", "magenta", "white"],
  r.addClass("vlPlayer2010"),
  r.addClass(e.buttonColor + "Bt"),
  r.addClass(e.background + "Bg"),
  r.append(g),
  r.append(C),
  C.append(le),
  le.append(An),
  le.append(ae),
  le.append(ve),
  le.append(de),
  le.append(es),
  es.append(esi),
  esi.append(rep),
  esi.append(lik),
  esi.append(sha),
  se.append(firstvideo),
  le.append(ce),
  le.append(Cx),
  le.append(Ex),
  g.append(w),
  g.append(Px),
  Px.append(P),
  w.append(S),
  w.append(k),
  F.append(q),
  w.append(ge),
  S.append(b),
  k.append(B),
  k.append(y),
  k.append(T),
  ge.append(me),
  me.append(Ce),
  me.append(we),
  P.append(x),
  P.append(I),
  P.append(D),
  P.append(H),
  P.append(U),
  P.append(PoP),
  P.append(Ux),
  annotations ? P.append(AnB) : P.append(),
  e.hd ? P.append(F) : P.append(),
  I.append(O),
  I.append(V),
  V.append(E),
  D.append(R),
  F.append(L),
  q.append(j),
  q.append(X),
  j.append(z),
  j.append(M),
  j.append(W),
  X.append(A),
  z.append(G),
  z.append(Q),
  Q.append(Y),
  Q.append(_),
  M.append(J),
  M.append(K),
  K.append(Z),
  K.append(N),
  W.append(ee),
  A.append(se),
  A.append(ie),
  A.append(te),
  A.append(ne),
  A.append(oe),

  ce.dblclick(e.toggleFull),
ce.click(function(e) {
    if (e.target !== ce[0]) return;
    f.toggle();
});

de.click(function(e) {
    if (e.target !== de[0]) return;
    f.toggle();
});


  C.click(function (e) {
  if (es.is(":visible")) return;
  if ($(e.target).closest(".vlControls, .vlControlsBar, .vlProgressAll, .vlButton, .vlSoundBar, .vlOptions, .vlOptionsPopup").length) return;
  f.toggle();
});


  rep.click(function () {
    e.seek(0), e.play();
  
    }), lik.click(function () {
      $("#watch-like").click(), $(".vlLikeButton").toggleClass("active");
    }), sha.click(function () {
      shareVideo();
    }), S.mousemove(f.showTime), k.mousemove(f.showTime), S.mouseleave(f.hideTime), k.mouseleave(f.hideTime), S.on("click mousedown", f.seekTo), k.on("click mousedown", f.seekTo), S.mousemove(function () {
      v = true;
    }), AnB.click(function () {
      AnB.toggleClass("active");
      $(".vlAnnotationsContainer").toggleClass("hid");
    }), k.mousemove(function () {
      v = true;
    }), x.click(f.toggle), I.click(f.volumeTo), I.mousedown(f.volumeTo), I.mouseenter(f.showVolume), I.mouseleave(f.hideVolume), I.on("wheel", f.volumeWheel), U.click(f.expand), PoP.click(function () {
         window.open("/embed?v=" + video_url, "popupWindow", "width=480, height=390, scrollbars=no");
    }), H.click(e.toggleFull), ce.on("timeupdate", f.timeUpdate), ce.on("waiting", f.showBuffer), ce.on("playing canplay canplaythrough timeupdate pause", f.hideBuffer), ce.on("playing seeked", function () {
      d = false;
    }), ce.on("ended", f.ended), q.mousedown(false), L.mousedown(false), F.mouseup(f.showOptions), F.mousedown(function () {
      F.addClass("active");
    }), F.mouseup(function () {
      F.removeClass("active");
    }), Y.click(function () {
      r.removeClass("loop"), f.loopChange();
    }), _.click(function () {
      r.addClass("loop"), f.loopChange();
    }), Z.click(function () {
      r.hasClass("hd720p") || f.toggleHD();
    }), N.click(function () {
      r.hasClass("hd720p") && f.toggleHD();
    }), A.find(".vloButton").click(function () {
      var s = A.find(".vloButton").index($(this));
      e.setRate([0.25, 0.5, 1, 2, 4][s]);
    }), le.mouseenter(function () {
      de.find("> i").stop().animate({opacity: 0.1}, 250);
    }), le.mouseleave(function () {
      de.find("> i").stop().animate({opacity: 0}, 250);
    }), r.on("mousemove keydown", function () {
      f.showControls(), f.showProgress(), null != a && clearTimeout(a), a = setTimeout(function () {
        f.hideControls(), a = null, overContainer = false;
      }, 2e3), overContainer = true;
    }), r.mousedown(f.hideOptions), $(document).mousedown(f.hideOptions), $(document).mouseup(f.mouseUp), $(document).on("wheel scroll", function () {
      return !p;
    }), s || (U.remove(), PoP.remove()), e.hd || M.hide(), r.hasClass("hd720p") ? (Z.addClass("active"), N.removeClass("active")) : (Z.removeClass("active"), N.addClass("active")), e.toggleFull(true) || H.hide(), f.rateChange(1);
    for (var t = ["sprites.png", "play.png", "play_red.png"], n = 0, o = 0; o < t.length; o++) $("<img />").attr("src", ke + "/" + t[o]).on("load", function () {
      ++n == t.length && (r.mousemove(), r.addClass("initialized"), r.prop("tabindex", 0), i());
    });
  });
}
$(document).mouseup(function () {
  $(".vlButton").removeClass("active");
});
function upNextGetInfo(e) {
 
  var urls = new Array;
  var thisurl = new URLSearchParams(window.location.search);
  var currenturl = thisurl.get("v");
  var videopl = document.querySelector(".watch-active-list.yt-uix-expander-collapsed") ? true : false;
  var queue = document.getElementsByClassName("watch-passive-list").length > 0 ? true : false;
  var firstfeatured = false;
  var firsturl = "";
 
  $.ajax({url: "/ajax/end_screen_videos.php?v="+video_url, success: function (html) {
    if (html) {
      $(".vlEndScreen").append(html);
    } else {
      alert("Bad response from server.");
    }
  }});
  $(".vlExpand .vlTooltipContainer").css({'right':48-$(".vlExpand .vlTooltipContainer").width()/2+'px'});
  $(".vlPopOut .vlTooltipContainer").css({'right':80-$(".vlPopOut .vlTooltipContainer").width()/2+'px'});
  $(".vlMute .vlTooltipContainer.mute").css({'left':46-$(".vlMute .vlTooltipContainer.mute").width()/2+'px'});
  $(".vlMute .vlTooltipContainer.unmute").css({'left':46-$(".vlMute .vlTooltipContainer.unmute").width()/2+'px'});
};
var firstvideo = setTimeout(() => {
  upNextGetInfo();
}, "100");
