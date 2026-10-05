function VLPSkin(t, e, l) {
  var i;
  var n;
  var s;
  var a;
  var o;
  var c;
  var d;
  var v;
  var p;
  this.setIntervals = function () {};
  this.clearIntervals = function () {
    clearInterval(i);
    clearInterval(n);
    clearInterval(s);
    clearInterval(c);
    a = clearTimeout(a);
  };
  this.resize = function () {};
  this.changeElapsed = function (e, l) {
    var i = e / t.duration * 100;
    D.html(u.format(e));
    if (!l) {
      it.css("left", i + "%");
      lt.css("width", i + "%");
    }
  };
  this.changeDuration = function (t) {
    X.html(u.format(t));
  };
  this.changeBuffer = function (t) {
    et.css("width", t);
  };
  this.changeVolume = function (t) {
    J.css("bottom", 100 * t + "%");
  };
  this.changePreview = function (t) {
    y.css("background-image", "");
    $("<img />").attr("src", t).on("load", function () {
      y.css("background-image", "url(" + t + ")");
    });
  };
  this.format = function (e, l) {
    var i;
    var n;
    var s;
    var a;
    var o;
    if (!(l || 0 === l)) {
      l = t.duration;
    }
    this.parse = function (t, e) {
      t = parseInt(t);
      return t < 10 ? e + t : t > 99 ? 99 : t;
    };
    a = "";
    o = "";
    if (l >= 600) {
      a = "0";
    }
    if (l >= 36e3) {
      o = "0";
    }
    i = this.parse(e % 60, "0");
    n = this.parse(e / 60, a);
    s = this.parse(e / 3600, o);
    return l < 3600 ? n + ":" + i : s + ":" + n + ":" + i;
  };
  this.calcSeek = function () {
    if (0 == t.duration) {
      return 0;
    }
    var e;
    var l;
    e = parseFloat(it.css("left"));
    l = tt.width();
    return e / l * t.duration;
  };
  this.showBuffer = function () {
    if (r.hasClass("playing") && null == i) {
      var t = 0;
      w.show();
      i = setInterval(function () {
        if (360 == t) {
          t = 0;
        }
        w.css("transform", "rotate(" + t + "deg)");
        t += 45;
      }, 77);
    }
  };
  this.hideBuffer = function () {
    if (null != i) {
      clearInterval(i);
      w.hide();
      i = null;
    }
  };
  this.seekTo = function (e) {
    if (null == n && 1 == e.which) {
      var l;
      var i;
      var s = "click" == e.type;
      it.addClass("active");
      n = setInterval(function () {
        l = t.mouseX - tt.offset().left;
        i = tt.width();
        if (l < 0) {
          l = 0;
        }
        if (l > i) {
          l = i;
        }
        v = true;
        it.css("left", l);
        u.changeElapsed(u.calcSeek(), true);
        u.showTime();
        if (s) {
          $(document).mouseup();
        }
      }, 26);
    }
  };
  this.showTime = function (e) {
    var l = tt.width();
    var i = r.offset().left;
    var n = tt.offset().left;
    var s = t.mouseX - i;
    var a = t.mouseX - n;
    if (v) {
      s = it.offset().left - i + 8;
      a = parseFloat(it.css("left"));
    }
    if (s < n - i) {
      s = n - i;
    }
    if (s > n + l) {
      s = n + l;
    }
    if (a < 0) {
      a = 0;
    }
    if (a > l) {
      a = l;
    }
    a = parseInt(a / l * t.duration);
    V.html(u.format(a, a));
    I.show();
    I.css("margin-left", s - I.width() / 2);
  };
  this.hideTime = function (t) {
    p = false;
    v = false;
    if (null == n) {
      I.hide();
    }
  };
  this.volumeTo = function (e) {
    if (null == s && 1 == e.which) {
      var l;
      var i;
      var n;
      var a = "click" == e.type;
      s = setInterval(function () {
        l = t.mouseY - G.offset().top - 6;
        i = G.height();
        if (l < 0) {
          l = 0;
        }
        if (l > i) {
          l = i;
        }
        n = (i - l) / i;
        t.setVolume(n);
        if (a) {
          $(document).mouseup();
        }
      }, 26);
    }
  };
  this.volumeWheel = function (e) {
    var l = t.videoObj.volume;
    if (e.originalEvent.deltaY < 0) {
      t.setVolume(l + .1);
    } else {
      t.setVolume(l - .1);
    }
    return false;
  };
  this.showVolume = function () {
    o = true;
    a = clearTimeout(a);
    var t = O.height();
    O.stop().css("height", "auto");
    var e = O.height();
    O.stop().css("height", t);
    O.stop().animate({
      height: e
    }, 250);
  };
  this.hideVolume = function () {
    o = false;
    a = setTimeout(function () {
      if (null == s && 0 != O.height()) {
        O.stop().animate({
          height: 0
        }, 250);
        a = undefined;
      }
    }, 500);
  };
  this.expand = function () {
    if (e) {
      e();
    }
  };
  this.timeUpdate = function () {
    if (null == n) {
      u.changeElapsed(t.videoObj.currentTime);
      if (v) {
        u.showTime();
      }
    }
    t.bufferUpdate();
  };
  this.toggle = function () {
    if (r.hasClass("ended")) {
      u.hideEndScreen(t.toggle);
    } else {
      u.showPlayAnimation();
      t.toggle();
    }
  };
  this.ended = function () {
    if (!r.hasClass("loop")) {
      B.stop().show().css("opacity", 0).animate({
        opacity: 1
      }, 100, function () {
        P.find("span").html("Share");
        S.find("span").html("Replay");
      });
    }
    t.ended();
  };
  this.hideEndScreen = function (t) {
    if (B.is(":visible")) {
      P.find("span").html("");
      S.find("span").html("");
      B.stop().animate({
        opacity: 0
      }, 100, function () {
        B.hide();
        if (t) {
          t();
        }
      });
    } else if (t) {
      t();
    }
  };
  this.showPlayAnimation = function () {
    var t;
    var e;
    e = r.hasClass("playing") ? E : T;
    (t = e.find("svg")).stop();
    t.css({
      opacity: 0,
      width: 50,
      height: 50
    });
    t.animate({
      width: 125,
      height: 125
    }, {
      duration: 500,
      queue: false
    });
    t.animate({
      opacity: .5
    }, {
      duration: 250,
      queue: false,
      complete: function () {
        t.animate({
          opacity: 0
        }, 250, function () {
          e.hide();
        });
      }
    });
    e.show();
  };
  this.mouseUp = function (e) {
    this.seek = function () {
      if (r.hasClass("ended")) {
        t.seek(u.calcSeek());
        t.play();
      } else {
        t.seek(u.calcSeek());
        if (!r.hasClass("started")) {
          t.play();
        }
      }
    };
    if (null != n) {
      clearInterval(n);
      n = null;
      if (0 != t.duration) {
        u.hideEndScreen(this.seek);
      } else {
        it.css("left", 0);
      }
      if (!p) {
        u.hideTime();
      }
    }
    if (null != s) {
      clearInterval(s);
      s = null;
      if (0 == o) {
        u.hideVolume();
      }
    }
    r.find(".vlButton").removeClass("active");
  };
  this.fullBtnEnter = function () {
    d = true;
    Z.addClass("hover");
    if (!c && !r.hasClass("full")) {
      Z.removeClass("hover");
      var t = 0;
      var e = 0;
      var l = 0;
      var i = Z.find("> s > i");
      var n = i.css("background-position");
      n = parseFloat(n.split(" ")[1]);
      Z.addClass("hover");
      var s = i.css("background-position");
      s = parseFloat(s.split(" ")[1]);
      c = setInterval(function () {
        if (t < 7 || t > 27) {
          if (t < 7) {
            e++;
          } else if (28 == t) {
            e = 5;
          } else if (29 == t) {
            e = 2;
          } else if (30 == t) {
            e = 1;
          } else if (31 == t) {
            e = 0;
          }
        }
        l = d ? s : n;
        Z.find("> s > i").css("background-position", "-" + (242 + 30 * e) + "px " + l + "px");
        if (35 == ++t || r.hasClass("full")) {
          if (!(d && !r.hasClass("full"))) {
            c = clearInterval(c);
            Z.find("> s > i").css("background-position", "");
          }
          t = 0;
        }
      }, 50);
    }
  };
  this.fullBtnLeave = function () {
    Z.removeClass("hover");
    d = false;
  };
  var r = t.obj;
  var u = this;
  var h = $("<div class=\"vlHidden\"></div>");
  var f = $("<input type=\"text\" tabindex=\"-1\" />");
  var g = $("<div class=\"vlContainer\"/>");
  var m = $("<div class=\"vlScreen\"/>");
  var y = $("<div class=\"vlPreview\"/>");
  var k = $("<div class=\"vlPlay\"/>");
  var w = $("<div class=\"vlLoad vlHdIcon\"/>");
  var B = $("<div class=\"vlEndScreen\" style=\"display:none\"/>");
  var x = $("<div class=\"vlEndScreenLeft\"/>");
  var b = $("<div class=\"vlEndScreenRight\"/>");
  var P = $("<div class=\"vlEndScreenBtn\"><i class=\"vlHdIcon\"></i><span></span></div>");
  var S = $("<div class=\"vlEndScreenBtn\"><i class=\"vlHdIcon\"></i><span></span></div>");
  var T = $("<div class=\"vlPlayAnimation\" style=\"display:none\">\t\t<div><svg width=\"100%\" height=\"100%\" viewbox=\"0 0 100 100\">\t\t\t<rect width=\"100\" height=\"100\" style=\"fill:black\" />\t\t\t<polygon points=\"32,28 66,50 32,72\" style=\"fill:white\" />\t\t</svg></div>\t</div>");
  var E = $("<div class=\"vlPauseAnimation\" style=\"display:none\">\t\t<div><svg width=\"100%\" height=\"100%\" viewbox=\"0 0 100 100\">\t\t\t<rect width=\"100\" height=\"100\" style=\"fill:black\" />\t\t\t<rect x=\"34\" y=\"28\" width=\"10\" height=\"44\" style=\"fill:white\" />\t\t\t<rect x=\"56\" y=\"28\" width=\"10\" height=\"44\" style=\"fill:white\" />\t\t</svg></div>\t</div>");
  var C = $("<div class=\"vlTimeShow\"></div>");
  var I = $("<div class=\"vlTimeShowContainer\"></div>");
  var V = $("<div class=\"vlTimeShowText\"></div>");
  var F = $("<div class=\"vlTimeShowSvg\">\t\t<div>\t\t\t<svg width=\"12\" height=\"8\">\t\t\t\t<polygon points=\"0,2 12,2 6,8\" fill=\"#999999\"/>\t\t\t\t<polygon points=\"1,0 11,0 11,2 6,7 1,2\" fill=\"#101010\"/>\t\t\t</svg>\t\t</div>\t</div>");
  var H = t.video;
  var L = $("<div class=\"vlControls\"/>");
  var U = $("<div class=\"vlcLeft\"/>");
  var M = $("<div class=\"vlcCenter\"/>");
  var j = $("<div class=\"vlcRight\"/>");
  var A = $("<div class=\"vlTime\"/>");
  var R = $("<div class=\"vlTimestamp\"/>");
  var D = $("<span class=\"vlElapsed\">0:00</span>");
  var X = $("<span class=\"vlDuration\">0:00</span>");
  var q = $("<span> / </span>");
  var z = $("<div class=\"vlcVolume\"/>");
  var O = $("<div class=\"vlcVolumeDiv\"/>");
  var W = $("<div class=\"vlcVolumeBar1\"/>");
  var Y = $("<div class=\"vlcVolumeBar2\"/>");
  var _ = $("<div class=\"vlcVolumeBar3\"/>");
  var G = $("<div class=\"vlcVolumeBar4\"/>");
  var J = $("<s class=\"vlHdIcon\"/>");
  var K = $("<div class=\"vlcPlay vlcBtn vlButton\"><s><i/></s></div>");
  var N = $("<div class=\"vlcMute vlcBtn vlButton\"><s><i/></s></div>");
  var Q = $("<div class=\"vlcExpand vlcBtn vlButton\"><s><i/></s></div>");
  var Z = $("<div class=\"vlcFullScreen vlcBtn vlButton\"><s><i/></s></div>");
  var tt = $("<div class=\"vlProgressBar\"/>");
  var et = $("<div class=\"vlBuffer\"/>");
  var lt = $("<div class=\"vlPosition\"/>");
  var it = $("<div class=\"vlSeeker vlButton vlHdIcon\"><i class=\"vlHdIcon\"></i></div>");
  this.hiddenUrl = f;
  var nt = t.skinPath + "/skin.css";
  var st = t.skinPath + "/img";
  $("#vlPlayer2009css").remove();
  $("<link id=\"vlPlayer2009css\" rel=\"stylesheet\"></link>").attr("href", nt).appendTo("head").on("load", function () {
    r.addClass("vlPlayer2009");
    r.append(g);
    r.append(L);
    r.append(I);
    r.append(h);
    h.append(f);
    g.append(m);
    m.append(y);
    m.append(k);
    m.append(w);
    m.append(H);
    m.append(B);
    m.append(T);
    m.append(E);
    B.append(x);
    B.append(b);
    x.append(P);
    b.append(S);
    L.append(U);
    L.append(j);
    L.append(M);
    U.append(K);
    M.append(tt);
    j.append(A);
    j.append(z);
    j.append(Q);
    j.append(Z);
    I.append(C);
    C.append(V);
    C.append(F);
    tt.append(et);
    tt.append(lt);
    tt.append(it);
    A.append(R);
    R.append(D);
    R.append(q);
    R.append(X);
    z.append(O);
    z.append(N);
    O.append(W);
    W.append(Y);
    Y.append(_);
    _.append(G);
    G.append(J);
    H.dblclick(t.toggleFull);
    k.dblclick(t.toggleFull);
    N.click(t.toggleMute);
    Z.click(t.toggleFull);
    H.click(u.toggle);
    H.on("timeupdate", u.timeUpdate);
    H.on("waiting", u.showBuffer);
    H.on("ended", u.ended);
    H.on("playing canplay canplaythrough timeupdate pause", u.hideBuffer);
    H.on("play playing", u.hideEndScreen);
    k.click(u.toggle);
    K.click(u.toggle);
    S.click(u.toggle);
    Z.mouseenter(u.fullBtnEnter);
    Z.mouseleave(u.fullBtnLeave);
    Q.click(u.expand);
    tt.click(u.seekTo);
    tt.mousedown(u.seekTo);
    tt.mousemove(u.showTime);
    tt.mouseenter(function () {
      p = true;
    });
    tt.mouseleave(u.hideTime);
    it.mouseenter(function () {
      v = true;
    });
    it.mouseleave(function () {
      v = false;
    });
    z.mouseenter(u.showVolume);
    z.mouseleave(u.hideVolume);
    z.mouseenter(function () {
      u.allowScroll = false;
    });
    z.mouseleave(function () {
      u.allowScroll = true;
    });
    z.on("wheel", u.volumeWheel);
    G.mousedown(u.volumeTo);
    G.click(u.volumeTo);
    $(document).mouseup(u.mouseUp);
    $(document).on("wheel scroll", u.allowScroll);
    var i = ["red", "orange", "gold", "olive", "green", "teal", "blue", "violet", "pink", "magenta", "white"];
    if (0 == $("#vlPlayer2009css2").length) {
      var n = $("<style id=\"#vlPlayer2009css2\" />").appendTo("head");
      for (var s = 0; s < i.length; s++) {
        n.append("\t\t\t\t\t.vlPlayer2009." + i[s] + "Bt .vlcPlay:hover > s > i,\t\t\t\t\t.vlPlayer2009." + i[s] + "Bt .vlcPlay.active > s > i { background-position: -2px -" + (25 * (s + 1) + 1) + "px; }\t\t\t\t\t.vlPlayer2009." + i[s] + "Bt.playing .vlcPlay:hover > s > i,\t\t\t\t\t.vlPlayer2009." + i[s] + "Bt.playing .vlcPlay.active > s > i { background-position: -32px -" + (25 * (s + 1) + 1) + "px; }\t\t\t\t\t.vlPlayer2009." + i[s] + "Bt .vlSeeker > i { background-position: -67px -" + (25 * (s + 1) + 5) + "px; }\t\t\t\t\t.vlPlayer2009." + i[s] + "Bt .vlcMute:hover > s > i,\t\t\t\t\t.vlPlayer2009." + i[s] + "Bt .vlcMute.active > s > i { background-position: -92px -" + (25 * (s + 1) + 1) + "px; }\t\t\t\t\t.vlPlayer2009." + i[s] + "Bt.muted .vlcMute > s > i { background-position: -122px -" + (25 * (s + 1) + 1) + "px; }\t\t\t\t\t.vlPlayer2009." + i[s] + "Bt .vlcFullScreen.hover > s > i,\t\t\t\t\t.vlPlayer2009." + i[s] + "Bt .vlcFullScreen.active > s > i { background-position: -242px -" + (25 * (s + 1) + 1) + "px; }\t\t\t\t\t.vlPlayer2009." + i[s] + "Bt.full .vlcFullScreen.hover > s > i,\t\t\t\t\t.vlPlayer2009." + i[s] + "Bt.full .vlcFullScreen.active > s > i { background-position: -152px -" + (25 * (s + 1) + 1) + "px; }\t\t\t\t\t.vlPlayer2009." + i[s] + "Bt .vlcExpand:hover > s > i,\t\t\t\t\t.vlPlayer2009." + i[s] + "Bt .vlcExpand.active > s > i { background-position: -182px -" + (25 * (s + 1) + 1) + "px; }\t\t\t\t\t.vlPlayer2009." + i[s] + "Bt.expanded .vlcExpand:hover > s > i,\t\t\t\t\t.vlPlayer2009." + i[s] + "Bt.expanded .vlcExpand.active > s > i { background-position: -212px -" + (25 * (s + 1) + 1) + "px; }\t\t\t\t\t.vlPlayer2009." + i[s] + "Bt .vlEndScreenBtn i { background-position: -" + 50 * s + "px -350px; }\t\t\t\t\t.vlPlayer2009." + i[s] + "Bt .vlEndScreenRight .vlEndScreenBtn i { background-position: -" + 50 * s + "px -400px; }\t\t\t\t");
      }
    }
    if (!e) {
      Q.remove();
    }
    if (!t.toggleFull(true)) {
      Z.hide();
    }
    if (t.adjust) {
      r.css("padding-bottom", 25);
      r.parent().css("padding-bottom", 25);
    }
    var a = t.buttonColor;
    var o = t.background;
    var c = ["buttonsbig.png", "gradients.png", "play" + ("black" == o ? "_black" : "") + ".png"];
    var d = 0;
    for (var s = 0; s < c.length; s++) {
      $("<img />").attr("src", st + "/" + c[s]).on("load", function () {
        if (++d == c.length) {
          t.changeButtonColor(a);
          t.changeBackground(o);
          r.addClass("initialized");
          r.prop("tabindex", 0);
          l();
        }
      });
    }
  });
}