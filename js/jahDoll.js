/* Jah Tales — ragdoll на всю сцену.
   Холст не перехватывает клики: сцена в capture сама решает,
   попали в фигурку или в кнопку/превью. Руки прилипают к #sidePanel. */
(function () {
  function boot() {
    if (typeof p2 === "undefined") return;
    var canvas = document.getElementById("jahDoll");
    if (!canvas || canvas.dataset.jahReady === "1") return;
    canvas.dataset.jahReady = "1";
    var ctx = canvas.getContext("2d");
    var stage = canvas.closest(".jah-stage") || canvas.parentElement || document.body;
    var panel = document.getElementById("sidePanel");

    canvas.style.touchAction = "none";
    canvas.style.pointerEvents = "none";

    var world = new p2.World({ gravity: [0, -12] });
    if (p2.World.NO_SLEEPING != null) world.sleepMode = p2.World.NO_SLEEPING;

    var ground = new p2.Body({ mass: 0, position: [0, 0] });
    ground.addShape(new p2.Plane());
    world.addBody(ground);

    var SCALE = 110;
    function fit() {
      var r = stage.getBoundingClientRect();
      if (r.width < 2 || r.height < 2) return;
      var dpr = Math.min(window.devicePixelRatio || 1, 2);
      var w = Math.max(2, Math.round(r.width * dpr));
      var h = Math.max(2, Math.round(r.height * dpr));
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

    var placed = false;
    function spawnOverVideo() {
      fit();
      var wrap = document.getElementById("jahWrap");
      if (!wrap || canvas.width < 2) return;
      var wr = wrap.getBoundingClientRect();
      var p = clientToWorld(wr.right - 110, wr.bottom - 160);
      torso.position[0] = p[0];
      torso.position[1] = Math.max(1.6, p[1]);
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
    var sticks = { l: null, r: null };
    var anchors = { l: null, r: null };

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

    function handPoint(arm) {
      var localY = -arm.shapes[0].height / 2;
      var c = Math.cos(arm.angle);
      var s = Math.sin(arm.angle);
      return [arm.position[0] - s * localY, arm.position[1] + c * localY];
    }

    function overPanel(wpt) {
      if (!panel) return false;
      var c = worldToClient(wpt);
      var r = panel.getBoundingClientRect();
      var pad = 28;
      return c.x >= r.left - pad && c.x <= r.right + pad && c.y >= r.top - pad && c.y <= r.bottom + pad;
    }

    function releaseStick(key) {
      if (!sticks[key]) return;
      world.removeConstraint(sticks[key]);
      world.removeBody(anchors[key]);
      sticks[key] = null;
      anchors[key] = null;
    }

    function stickHand(arm, key) {
      if (sticks[key] || dragged === arm) return;
      var p = handPoint(arm);
      if (!overPanel(p)) return;
      var anchor = new p2.Body({ mass: 0, position: [p[0], p[1]] });
      world.addBody(anchor);
      var c = new p2.RevoluteConstraint(anchor, arm, {
        worldPivot: [p[0], p[1]],
        collideConnected: false,
        maxForce: 900
      });
      c.maxForce = 900;
      world.addConstraint(c);
      sticks[key] = c;
      anchors[key] = anchor;
      arm.angularVelocity = 0;
    }

    function drop() {
      var was = dragged;
      if (mouseJoint) {
        world.removeConstraint(mouseJoint);
        mouseJoint = null;
      }
      dragged = null;
      aim = null;
      stage.style.cursor = "";
      if (was === lArm) stickHand(lArm, "l");
      if (was === rArm) stickHand(rArm, "r");
    }

    function startDrag(e, b) {
      var p = clientToWorld(e.clientX, e.clientY);
      if (b === lArm) releaseStick("l");
      if (b === rArm) releaseStick("r");
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
        collideConnected: false,
        maxForce: 1e6
      });
      mouseJoint.maxForce = 1e6;
      world.addConstraint(mouseJoint);
      dragged = b;
      aim = p;
      stage.style.cursor = "grabbing";
    }

    stage.addEventListener("pointerdown", function (e) {
      if (!placed) spawnOverVideo();
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
      if (b) stage.style.cursor = "grab";
      else if (stage.style.cursor === "grab") stage.style.cursor = "";
    }, { passive: false });

    window.addEventListener("pointerup", function () {
      if (!mouseJoint) return;
      var arm = dragged;
      var key = arm === lArm ? "l" : arm === rArm ? "r" : null;
      if (key && anchors[key]) {
        var hp = handPoint(arm);
        var dx = hp[0] - anchors[key].position[0];
        var dy = hp[1] - anchors[key].position[1];
        if (Math.sqrt(dx * dx + dy * dy) > 1.15) releaseStick(key);
      }
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
    function drawHand(arm, stuck) {
      var p = physToPx(handPoint(arm));
      ctx.beginPath();
      ctx.arc(p[0], p[1], (stuck ? 0.16 : 0.12) * SCALE, 0, Math.PI * 2);
      ctx.fillStyle = stuck ? "#ffffff" : "#7fffff";
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
        if (dragged === lArm && anchors.l) {
          var hl = handPoint(lArm);
          if (Math.hypot(hl[0] - anchors.l.position[0], hl[1] - anchors.l.position[1]) > 1.15) releaseStick("l");
        }
        if (dragged === rArm && anchors.r) {
          var hr = handPoint(rArm);
          if (Math.hypot(hr[0] - anchors.r.position[0], hr[1] - anchors.r.position[1]) > 1.15) releaseStick("r");
        }
      }
      if (!sticks.l && dragged !== lArm && overPanel(handPoint(lArm))) stickHand(lArm, "l");
      if (!sticks.r && dragged !== rArm && overPanel(handPoint(rArm))) stickHand(rArm, "r");

      if (dragged !== torso) torso.angularVelocity += (energy - 0.5) * 0.04;
      if (dragged !== lArm && !sticks.l) lArm.angularVelocity += Math.sin(Date.now() * 0.005) * energy * 0.08;
      if (dragged !== rArm && !sticks.r) rArm.angularVelocity -= Math.sin(Date.now() * 0.005) * energy * 0.08;

      world.step(1 / 60);
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      ctx.fillStyle = "#00ffff";
      drawCircle(head, 0.34);
      drawBox(torso);
      drawBox(lArm);
      drawBox(rArm);
      drawHand(lArm, !!sticks.l);
      drawHand(rArm, !!sticks.r);
      requestAnimationFrame(loop);
    }
    loop();

    window.jahDoll = {
      pick: pick,
      clientToWorld: clientToWorld,
      handPoint: handPoint,
      bodies: bodies,
      arms: { l: lArm, r: rArm },
      isDragging: function () { return draggingFlag; },
      isStuck: function (key) { return !!sticks[key]; }
    };
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", boot);
  else boot();
})();
