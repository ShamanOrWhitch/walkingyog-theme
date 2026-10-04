/* WalkingYog — фрактальный разлёт плоскости (Three.js).
   Сборка всегда идёт в СЛЕДУЮЩИЙ ролик.
   Переход не стартует, пока следующий ролик не загружен и не начал играть,
   чтобы у каждой частицы уже был живой кадр новой картинки. */
(function () {
  function detectRenderProfile() {
    var ua = navigator.userAgent || "";
    var w = Math.min(window.innerWidth || 1280, (window.screen && window.screen.width) || 1280);
    var touch = (navigator.maxTouchPoints || 0) > 0;
    var phone = /iPhone|iPod|Android.+Mobile|Windows Phone|IEMobile|BlackBerry/i.test(ua) || (touch && w <= 760);
    var tablet = !phone && (/iPad|Tablet/i.test(ua) || (/Android/i.test(ua) && !/Mobile/i.test(ua)) || (touch && w > 760 && w <= 1180));

    // Десктоп сохраняет исходную сетку 40×70. Реже только на телефоне и планшете.
    if (phone) {
      return { name: "phone", segX: 24, segY: 42, amplitude: 64, pixelRatioCap: 1.25 };
    }
    if (tablet) {
      return { name: "tablet", segX: 32, segY: 56, amplitude: 72, pixelRatioCap: 1.5 };
    }
    return { name: "desktop", segX: 40, segY: 70, amplitude: 80, pixelRatioCap: 2 };
  }

  function makeVideo() {
    var v = document.createElement("video");
    v.muted = true;
    v.defaultMuted = true;
    v.playsInline = true;
    v.setAttribute("playsinline", "");
    v.setAttribute("webkit-playsinline", "");
    v.loop = true;
    v.preload = "auto";
    v.crossOrigin = "anonymous";
    v.setAttribute("crossorigin", "anonymous");
    // Не display:none — иначе часть браузеров не декодирует кадры.
    v.style.cssText = "position:fixed;width:8px;height:8px;left:0;bottom:0;opacity:0;pointer-events:none;z-index:-1;";
    document.body.appendChild(v);
    return v;
  }

  function waitForFrame(video, timeout) {
    timeout = timeout || 10000;
    return new Promise(function (resolve) {
      var finished = false;
      function done(ok) {
        if (finished) return;
        finished = true;
        resolve(!!ok);
      }
      var timer = setTimeout(function () {
        done(video.readyState >= 2 && !video.paused);
      }, timeout);

      function armed() {
        var playPromise;
        try { playPromise = video.play(); } catch (e) { playPromise = null; }
        var after = function () {
          if (video.readyState < 2) {
            video.addEventListener("loadeddata", function () {
              clearTimeout(timer);
              done(!video.paused || video.readyState >= 2);
            }, { once: true });
            return;
          }
          if (typeof video.requestVideoFrameCallback === "function") {
            video.requestVideoFrameCallback(function () {
              clearTimeout(timer);
              done(true);
            });
          } else {
            clearTimeout(timer);
            requestAnimationFrame(function () { done(true); });
          }
        };
        if (playPromise && typeof playPromise.then === "function") playPromise.then(after).catch(after);
        else after();
      }

      if (video.readyState >= 2) armed();
      else {
        video.addEventListener("canplay", armed, { once: true });
        video.addEventListener("error", function () {
          clearTimeout(timer);
          done(false);
        }, { once: true });
      }
    });
  }

  window.WY_HomeDissolve_Start = function (opts) {
    opts = opts || {};
    var videos = (opts.videos || []).filter(Boolean);
    var canvas = document.getElementById(opts.canvasId || "dissolve-canvas");
    if (!canvas || !videos.length || typeof THREE === "undefined") return;
    var hasGsap = typeof gsap !== "undefined";

    var profile = detectRenderProfile();
    var renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true, antialias: profile.name === "desktop" });
    function setRendererSize() {
      var rect = canvas.getBoundingClientRect();
      var dpr = Math.min(window.devicePixelRatio || 1, profile.pixelRatioCap);
      renderer.setPixelRatio(dpr);
      renderer.setSize(Math.max(1, rect.width), Math.max(1, rect.height), false);
      camera.aspect = rect.width / Math.max(1, rect.height);
      camera.updateProjectionMatrix();
    }

    var scene = new THREE.Scene();
    var camera = new THREE.PerspectiveCamera(45, 1, 0.1, 1000);
    camera.position.z = 150;
    setRendererSize();
    window.addEventListener("resize", setRendererSize);

    var segX = profile.segX;
    var segY = profile.segY;
    var geo = new THREE.PlaneGeometry(80, 125, segX, segY);
    var mat = new THREE.MeshBasicMaterial({ transparent: true, opacity: 1, side: THREE.DoubleSide });
    var plane = new THREE.Mesh(geo, mat);
    scene.add(plane);

    var positionAttr = geo.attributes.position;
    var origPositions = new Float32Array(positionAttr.array.length);
    for (var i = 0; i < positionAttr.array.length; i++) origPositions[i] = positionAttr.array[i];

    var slots = [makeVideo(), makeVideo()];
    var textures = [null, null];
    var slotClip = [-1, -1];
    var shown = 0;
    var currentIndex = 0;
    var phase = "boot";
    var busy = false;

    function makeTexture(video) {
      var tex = new THREE.VideoTexture(video);
      tex.minFilter = THREE.LinearFilter;
      tex.magFilter = THREE.LinearFilter;
      if (THREE.RGBFormat) tex.format = THREE.RGBFormat;
      tex.generateMipmaps = false;
      return tex;
    }

    function ensureSlot(slot, index) {
      var v = slots[slot];
      var src = videos[index];
      if (slotClip[slot] !== index || v.getAttribute("src") !== src) {
        slotClip[slot] = index;
        try { v.pause(); } catch (e) {}
        v.src = src;
        v.load();
        if (textures[slot]) {
          textures[slot].dispose();
          textures[slot] = null;
        }
      }
      return waitForFrame(v).then(function (ok) {
        if (!ok) return false;
        if (!textures[slot]) textures[slot] = makeTexture(v);
        textures[slot].needsUpdate = true;
        return true;
      });
    }

    function createOffsetArray(amplitude) {
      var offsets = new Float32Array(origPositions.length);
      for (var n = 0; n < offsets.length; n++) {
        offsets[n] = (Math.random() - 0.5) * amplitude;
      }
      return offsets;
    }

    function publish() {
      window.WY_DISSOLVE_STATE = {
        phase: phase,
        profile: profile.name,
        segX: segX,
        segY: segY,
        currentIndex: currentIndex,
        shownSlot: shown,
        clipCount: videos.length,
        nextIndex: (currentIndex + 1) % videos.length,
        nextReadyState: slots[1 - shown].readyState,
        nextTime: slots[1 - shown].currentTime || 0,
        nextPaused: slots[1 - shown].paused,
        shownTime: slots[shown].currentTime || 0,
        shownReady: slots[shown].readyState,
        shownPaused: slots[shown].paused,
        mapIsNext: phase === "assemble" || phase === "play"
      };
    }

    function animateDissolve(duration, amplitude, fadeDelay) {
      duration = duration || 2.4;
      amplitude = amplitude == null ? profile.amplitude : amplitude;
      fadeDelay = fadeDelay == null ? 1.6 : fadeDelay;
      if (busy || !hasGsap) return Promise.resolve(false);

      var nextIndex = (currentIndex + 1) % videos.length;
      var nextSlot = 1 - shown;

      // Ждём, пока следующий ролик реально играет и отдал кадр — и только потом разлёт.
      return ensureSlot(nextSlot, nextIndex).then(function (ready) {
        if (!ready || !textures[nextSlot] || busy) return false;
        return new Promise(function (resolve) {
          requestAnimationFrame(function () {
            if (busy) { resolve(false); return; }
            busy = true;
            phase = "explode";
            publish();

            var offsets = createOffsetArray(amplitude);
            var state = { t: 0 };
            gsap.killTweensOf(mat);
            gsap.to(mat, { opacity: 0, delay: fadeDelay, duration: 1.6, ease: "power2.out" });

            gsap.to(state, {
              t: 1,
              duration: duration,
              ease: "power4.out",
              onUpdate: function () {
                var t = state.t;
                var pos = positionAttr.array;
                for (var k = 0; k < pos.length; k++) pos[k] = origPositions[k] + offsets[k] * t;
                positionAttr.needsUpdate = true;
              },
              onComplete: function () {
                // Пик разлёта: частицы ещё в облаке, текстура уже следующего ролика.
                gsap.killTweensOf(mat);
                mat.map = textures[nextSlot];
                mat.needsUpdate = true;
                shown = nextSlot;
                currentIndex = nextIndex;
                phase = "assemble";
                publish();

                gsap.to(mat, { opacity: 1, duration: 0.8, ease: "power2.inOut" });
                var restore = { r: 0 };
                gsap.to(restore, {
                  r: 1,
                  duration: 0.9,
                  ease: "power2.inOut",
                  onUpdate: function () {
                    var r = restore.r;
                    var pos = positionAttr.array;
                    for (var k = 0; k < pos.length; k++) pos[k] = origPositions[k] + offsets[k] * (1 - r);
                    positionAttr.needsUpdate = true;
                  },
                  onComplete: function () {
                    phase = "play";
                    busy = false;
                    publish();
                    var hidden = 1 - shown;
                    var upcoming = (currentIndex + 1) % videos.length;
                    ensureSlot(hidden, upcoming);
                    resolve(true);
                  }
                });
              }
            });
          });
        });
      });
    }

    var chain = Promise.resolve();
    function requestDissolve() {
      var run = chain.then(function () { return animateDissolve(); }, function () { return animateDissolve(); });
      chain = run.then(function () {}, function () {});
      return run;
    }

    var frame = 0;
    (function renderLoop() {
      requestAnimationFrame(renderLoop);
      if (textures[0]) textures[0].needsUpdate = true;
      if (textures[1]) textures[1].needsUpdate = true;
      if ((frame++ % 10) === 0) publish();
      renderer.render(scene, camera);
    })();

    ensureSlot(0, 0).then(function (ok) {
      if (!ok) return;
      mat.map = textures[0];
      mat.opacity = 1;
      mat.needsUpdate = true;
      shown = 0;
      currentIndex = 0;
      phase = "play";
      publish();
      ensureSlot(1, 1);
      if (opts.auto !== false) {
        var first = opts.firstDelay != null ? opts.firstDelay : 12000;
        var gapMin = opts.intervalMin != null ? opts.intervalMin : 45000;
        var jitter = opts.intervalJitter != null ? opts.intervalJitter : 20000;
        var timer = null;
        function arm(ms) {
          clearTimeout(timer);
          timer = setTimeout(function () {
            requestDissolve().then(function (did) {
              arm(did ? (gapMin + Math.random() * jitter) : 3000);
            });
          }, ms);
        }
        arm(first);
      }
    });

    window.wy_next_dissolve = requestDissolve;
    window.wy_set_video = function (index) {
      index = ((index % videos.length) + videos.length) % videos.length;
      var slot = shown;
      return ensureSlot(slot, index).then(function (ok) {
        if (!ok) return false;
        mat.map = textures[slot];
        mat.needsUpdate = true;
        currentIndex = index;
        phase = "play";
        publish();
        ensureSlot(1 - slot, (index + 1) % videos.length);
        return true;
      });
    };
    window.wy_render_profile = profile;
    publish();
  };
})();
