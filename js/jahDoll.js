/* Jah Tales — ragdoll на всё окно.
   Липнет к .thumb, [data-stick] и к самой поверхности video/плеера,
   в точку касания. Сильный рывок отрывает. Клик по точке прилипания отпускает. */
(function () {
  function boot() {
    if (typeof p2 === "undefined") return;
    var canvas = document.getElementById("jahDoll");
    if (!canvas || canvas.dataset.jahReady === "1") return;
    canvas.dataset.jahReady = "1";
    var ctx = canvas.getContext("2d");

    canvas.style.position = "fixed";
    canvas.style.left = "0";
    canvas.style.top = "0";
    canvas.style.width = "100vw";
    canvas.style.height = "100vh";
    canvas.style.zIndex = "60";
    canvas.style.pointerEvents = "none";
    canvas.style.touchAction = "none";
    canvas.style.background = "transparent";

    var world = new p2.World({ gravity: [0, -12] });
    if (p2.World.NO_SLEEPING != null) world.sleepMode = p2.World.NO_SLEEPING;

    var ground = new p2.Body({ mass: 0, position: [0, 0] });
    ground.addShape(new p2.Plane());
    world.addBody(ground);

    var SCALE = 110;
    function fit() {
      var dpr = Math.min(window.devicePixelRatio || 1, 2);
      var w = Math.max(2, Math.round(window.innerWidth * dpr));
      var h = Math.max(2, Math.round(window.innerHeight * dpr));
      if (canvas.width !== w || canvas.height !== h) {
        canvas.width = w;
        canvas.height = h;
      }
      SCALE = 110 * dpr;
    }
    fit();
    window.addEventListener("resize", fit);

    function physToPx(v) {
      return [
        canvas.width / 2 + v[0] * SCALE,
        canvas.height - v[1] * SCALE
      ];
    }
    function clientToWorld(clientX, clientY) {
      var r = canvas.getBoundingClientRect();
      var x = (clientX - r.left) * (canvas.width / Math.max(1, r.width));
      var y = (clientY - r.top) * (canvas.height / Math.max(1, r.height));
      return [
        (x - canvas.width / 2) / SCALE,
        (canvas.height - y) / SCALE
      ];
    }
    function worldToClient(wpt) {
      var r = canvas.getBoundingClientRect();
      var px = physToPx(wpt);
      return {
        x: r.left + px[0] * (r.width / canvas.width),
        y: r.top + px[1] * (r.height / canvas.height)
      };
    }

    var bodies = [];
    function addBody(body) {
      body.allowSleep = false;
      world.addBody(body);
      bodies.push(body);
      return body;
    }

    var torso = addBody(new p2.Body({ mass: 3, position: [0, 1.7] }));
    torso.addShape(new p2.Box({ width: 0.62, height: 0.95 }));
    var head = addBody(new p2.Body({ mass: 1, position: [0, 2.55] }));
    head.addShape(new p2.Circle({ radius: 0.34 }));
    var lArm = addBody(new p2.Body({ mass: 0.8, position: [-0.7, 1.9] }));
    lArm.addShape(new p2.Box({ width: 0.22, height: 0.85 }));
    var rArm = addBody(new p2.Body({ mass: 0.8, position: [0.7, 1.9] }));
    rArm.addShape(new p2.Box({ width: 0.22, height: 0.85 }));
    var lLeg = addBody(new p2.Body({ mass: 0.9, position: [-0.18, 0.85] }));
    lLeg.addShape(new p2.Box({ width: 0.2, height: 0.82 }));
    var rLeg = addBody(new p2.Body({ mass: 0.9, position: [0.18, 0.85] }));
    rLeg.addShape(new p2.Box({ width: 0.2, height: 0.82 }));

    world.addConstraint(new p2.RevoluteConstraint(head, torso, {
      localPivotA: [0, -0.34],
      localPivotB: [0, 0.48]
    }));
    world.addConstraint(new p2.RevoluteConstraint(lArm, torso, {
      localPivotA: [0, 0.38],
      localPivotB: [-0.32, 0.28]
    }));
    world.addConstraint(new p2.RevoluteConstraint(rArm, torso, {
      localPivotA: [0, 0.38],
      localPivotB: [0.32, 0.28]
    }));
    world.addConstraint(new p2.RevoluteConstraint(lLeg, torso, {
      localPivotA: [0, 0.36],
      localPivotB: [-0.16, -0.42]
    }));
    world.addConstraint(new p2.RevoluteConstraint(rLeg, torso, {
      localPivotA: [0, 0.36],
      localPivotB: [0.16, -0.42]
    }));

    var limbs = [
      { key: "lh", body: lArm },
      { key: "rh", body: rArm },
      { key: "lf", body: lLeg },
      { key: "rf", body: rLeg }
    ];

    var placed = false;
    function spawnOverVideo() {
      fit();
      var wrap = document.getElementById("jahWrap");
      var anchor = wrap || document.body;
      var wr = anchor.getBoundingClientRect();
      var p = clientToWorld(wr.left + wr.width * 0.72, wr.top + wr.height * 0.62);
      torso.position[0] = p[0];
      torso.position[1] = Math.max(1.8, p[1]);
      torso.velocity[0] = torso.velocity[1] = 0;
      torso.angle = 0;
      head.position[0] = torso.position[0];
      head.position[1] = torso.position[1] + 0.9;
      head.velocity[0] = head.velocity[1] = 0;
      lArm.position[0] = torso.position[0] - 0.72;
      lArm.position[1] = torso.position[1] + 0.15;
      lArm.angle = 0.4;
      rArm.position[0] = torso.position[0] + 0.72;
      rArm.position[1] = torso.position[1] + 0.15;
      rArm.angle = -0.4;
      lLeg.position[0] = torso.position[0] - 0.18;
      lLeg.position[1] = torso.position[1] - 0.85;
      lLeg.angle = 0.05;
      rLeg.position[0] = torso.position[0] + 0.18;
      rLeg.position[1] = torso.position[1] - 0.85;
      rLeg.angle = -0.05;
      placed = true;
    }
    requestAnimationFrame(spawnOverVideo);

    var energy = 0.4;
    setInterval(function () { energy = 0.3 + Math.random() * 0.4; }, 400);

    var mouseBody = new p2.Body({ type: p2.Body.KINEMATIC, mass: 0 });
    world.addBody(mouseBody);
    var mouseJoint = null;
    var dragged = null;
    var aim = null;
    var sticks = {};
    var ignore = {};

    function pick(worldPoint) {
      var hits = world.hitTest(worldPoint, bodies);
      if (hits && hits.length) return hits[hits.length - 1];
      var best = null;
      var bestD = 1.35;
      for (var i = 0; i < bodies.length; i++) {
        var b = bodies[i];
        var dx = b.position[0] - worldPoint[0];
        var dy = b.position[1] - worldPoint[1];
        var d = Math.sqrt(dx * dx + dy * dy);
        var reach = b === head ? 0.85 : (b === torso ? 1.25 : 1.05);
        if (d <= reach && d < bestD) {
          best = b;
          bestD = d;
        }
      }
      return best;
    }

    function tip(body) {
      var localY = -body.shapes[0].height / 2;
      if (!body.shapes[0].height) localY = -(body.shapes[0].radius || 0.2);
      var c = Math.cos(body.angle);
      var s = Math.sin(body.angle);
      return [body.position[0] - s * localY, body.position[1] + c * localY];
    }

    function stickElements() {
      return Array.prototype.slice.call(document.querySelectorAll(
        ".thumb, [data-stick], video, .jah-stage, #jahWrap, .jah-wrap, .wp-video, .mejs-container, .video-js"
      ));
    }

    function elementAt(wpt) {
      var c = worldToClient(wpt);
      var list = stickElements();
      var found = null;
      var foundArea = Infinity;
      for (var i = 0; i < list.length; i++) {
        var el = list[i];
        if (el.offsetParent === null && el.getClientRects().length === 0) continue;
        var r = el.getBoundingClientRect();
        if (r.width < 8 || r.height < 8) continue;
        if (c.x < r.left || c.x > r.right || c.y < r.top || c.y > r.bottom) continue;
        var area = r.width * r.height;
        if (area < foundArea) {
          found = el;
          foundArea = area;
        }
      }
      return found;
    }

    function pointOnElement(el, relX, relY) {
      var r = el.getBoundingClientRect();
      return clientToWorld(r.left + r.width * relX, r.top + r.height * relY);
    }

    function relOnElement(el, wpt) {
      var r = el.getBoundingClientRect();
      var c = worldToClient(wpt);
      var rx = r.width ? (c.x - r.left) / r.width : 0.5;
      var ry = r.height ? (c.y - r.top) / r.height : 0.5;
      return [Math.max(0.04, Math.min(0.96, rx)), Math.max(0.04, Math.min(0.96, ry))];
    }

    function releaseStick(key, hold) {
      var s = sticks[key];
      if (!s) return;
      world.removeConstraint(s.constraint);
      world.removeBody(s.anchor);
      if (hold) ignore[key] = s.el;
      delete sticks[key];
    }

    function stickLimb(limb, key) {
      if (sticks[key] || dragged === limb) return;
      var p = tip(limb);
      var el = elementAt(p);
      if (!el || ignore[key] === el) return;
      var rel = relOnElement(el, p);
      var at = pointOnElement(el, rel[0], rel[1]);
      var anchor = new p2.Body({ mass: 0, position: [at[0], at[1]] });
      world.addBody(anchor);
      var c = new p2.RevoluteConstraint(anchor, limb, {
        worldPivot: [at[0], at[1]],
        collideConnected: false
      });
      if (c.maxForce != null) c.maxForce = 520;
      world.addConstraint(c);
      sticks[key] = { constraint: c, anchor: anchor, el: el, relX: rel[0], relY: rel[1] };
      limb.angularVelocity = 0;
    }

    function syncSticks() {
      Object.keys(sticks).forEach(function (key) {
        var s = sticks[key];
        if (!s.el || !s.el.isConnected) {
          releaseStick(key, false);
          return;
        }
        var r = s.el.getBoundingClientRect();
        if (r.width < 8 || r.height < 8) {
          releaseStick(key, false);
          return;
        }
        var w = pointOnElement(s.el, s.relX, s.relY);
        s.anchor.position[0] = w[0];
        s.anchor.position[1] = w[1];
        s.anchor.velocity[0] = 0;
        s.anchor.velocity[1] = 0;
      });
    }

    function tipKeyAt(clientX, clientY) {
      var keys = ["lh", "rh", "lf", "rf"];
      for (var i = 0; i < keys.length; i++) {
        var key = keys[i];
        if (!sticks[key]) continue;
        var limb = null;
        for (var j = 0; j < limbs.length; j++) if (limbs[j].key === key) limb = limbs[j];
        if (!limb) continue;
        var c = worldToClient(tip(limb.body));
        var dx = c.x - clientX;
        var dy = c.y - clientY;
        if (dx * dx + dy * dy < 28 * 28) return key;
      }
      return null;
    }

    function drop() {
      var was = dragged;
      if (mouseJoint) {
        world.removeConstraint(mouseJoint);
        mouseJoint = null;
      }
      dragged = null;
      aim = null;
      document.documentElement.style.cursor = "";
      limbs.forEach(function (limb) {
        if (was === limb.body) stickLimb(limb.body, limb.key);
      });
    }

    function breakIfFar() {
      if (!aim) return;
      limbs.forEach(function (limb) {
        var s = sticks[limb.key];
        if (!s) return;
        var a = s.anchor.position;
        var hp = tip(limb.body);
        var stretched = Math.hypot(hp[0] - a[0], hp[1] - a[1]);
        var fromHand = dragged === limb.body ? Math.hypot(aim[0] - a[0], aim[1] - a[1]) : 0;
        var fromBody = dragged && dragged !== limb.body ? Math.hypot(aim[0] - a[0], aim[1] - a[1]) : 0;
        if (stretched > 1.7 || fromHand > 2.05 || fromBody > 3.1) releaseStick(limb.key, true);
      });
    }

    function startDrag(e, b) {
      var p = clientToWorld(e.clientX, e.clientY);
      e.preventDefault();
      e.stopPropagation();
      if (mouseJoint) world.removeConstraint(mouseJoint);
      mouseBody.position[0] = p[0];
      mouseBody.position[1] = p[1];
      mouseBody.velocity[0] = 0;
      mouseBody.velocity[1] = 0;
      if (b.wakeUp) b.wakeUp();
      mouseJoint = new p2.RevoluteConstraint(mouseBody, b, {
        worldPivot: [p[0], p[1]],
        collideConnected: false
      });
      if (mouseJoint.maxForce != null) mouseJoint.maxForce = 1e6;
      world.addConstraint(mouseJoint);
      dragged = b;
      aim = p;
      document.documentElement.style.cursor = "grabbing";
    }

    window.addEventListener("pointerdown", function (e) {
      if (!placed) spawnOverVideo();
      var tipKey = tipKeyAt(e.clientX, e.clientY);
      if (tipKey) {
        releaseStick(tipKey, true);
        e.preventDefault();
        e.stopPropagation();
        return;
      }
      if (e.target && e.target.closest && e.target.closest("a, button, input, textarea, select, video")) {
        var probe = pick(clientToWorld(e.clientX, e.clientY));
        if (!probe) return;
      }
      var b = pick(clientToWorld(e.clientX, e.clientY));
      if (!b) return;
      startDrag(e, b);
    }, true);

    window.addEventListener("pointermove", function (e) {
      if (mouseJoint && aim) {
        e.preventDefault();
        aim = clientToWorld(e.clientX, e.clientY);
        return;
      }
      var b = pick(clientToWorld(e.clientX, e.clientY));
      document.documentElement.style.cursor = b ? "grab" : "";
    }, { passive: false });

    window.addEventListener("pointerup", function () {
      if (!mouseJoint) return;
      drop();
    });
    window.addEventListener("pointercancel", function () { if (mouseJoint) drop(); });

    function drawCircle(b, radius) {
      var p = physToPx(b.position);
      ctx.beginPath();
      ctx.arc(p[0], p[1], radius * SCALE, 0, Math.PI * 2);
      ctx.fill();
    }
    function drawBox(b) {
      var p = physToPx(b.position);
      ctx.save();
      ctx.translate(p[0], p[1]);
      ctx.rotate(-b.angle);
      var s = b.shapes[0];
      ctx.fillRect(-s.width * SCALE / 2, -s.height * SCALE / 2, s.width * SCALE, s.height * SCALE);
      ctx.restore();
    }
    function drawTip(body, stuck, color) {
      var p = physToPx(tip(body));
      ctx.beginPath();
      ctx.arc(p[0], p[1], (stuck ? 0.16 : 0.12) * SCALE, 0, Math.PI * 2);
      ctx.fillStyle = stuck ? "#ffffff" : color;
      ctx.fill();
      ctx.fillStyle = "#00ffff";
    }

    var draggingFlag = false;
    function loop() {
      draggingFlag = !!mouseJoint;
      if (mouseJoint && aim) {
        mouseBody.position[0] = aim[0];
        mouseBody.position[1] = aim[1];
        mouseBody.velocity[0] = 0;
        mouseBody.velocity[1] = 0;
        breakIfFar();
      }
      limbs.forEach(function (limb) {
        var over = elementAt(tip(limb.body));
        if (ignore[limb.key] && over !== ignore[limb.key]) delete ignore[limb.key];
        if (!sticks[limb.key] && dragged !== limb.body && over) {
          stickLimb(limb.body, limb.key);
        }
      });
      syncSticks();

      if (dragged !== torso) torso.angularVelocity += (energy - 0.5) * 0.04;
      limbs.forEach(function (limb, i) {
        if (dragged === limb.body || sticks[limb.key]) return;
        var sign = i % 2 === 0 ? 1 : -1;
        limb.body.angularVelocity += Math.sin(Date.now() * 0.005 + i) * energy * 0.08 * sign;
      });

      world.step(1 / 60);
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      ctx.fillStyle = "#00ffff";
      drawCircle(head, 0.34);
      drawBox(torso);
      drawBox(lArm);
      drawBox(rArm);
      drawBox(lLeg);
      drawBox(rLeg);
      drawTip(lArm, !!sticks.lh, "#7fffff");
      drawTip(rArm, !!sticks.rh, "#7fffff");
      drawTip(lLeg, !!sticks.lf, "#b8fff0");
      drawTip(rLeg, !!sticks.rf, "#b8fff0");
      requestAnimationFrame(loop);
    }
    loop();

    window.jahDoll = {
      pick: pick,
      clientToWorld: clientToWorld,
      bodies: bodies,
      isDragging: function () { return draggingFlag; },
      isStuck: function (key) { return !!sticks[key]; }
    };
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", boot);
  else boot();
})();
