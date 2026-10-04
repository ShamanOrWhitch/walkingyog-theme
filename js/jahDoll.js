/* Jah Tales — ragdoll, которого можно схватить и таскать.
   Координаты указателя переводятся из CSS-пикселей в буфер canvas
   (буфер 440×720, на экране холст меньше — раньше клик не попадал в тело). */
(function () {
  function boot() {
    if (typeof p2 === "undefined") return;
    var canvas = document.getElementById("jahDoll");
    if (!canvas || canvas.dataset.jahReady === "1") return;
    canvas.dataset.jahReady = "1";
    var ctx = canvas.getContext("2d");

    canvas.style.touchAction = "none";
    canvas.style.pointerEvents = "auto";
    canvas.style.cursor = "grab";

    var world = new p2.World({ gravity: [0, -12] });
    if (p2.World.NO_SLEEPING != null) world.sleepMode = p2.World.NO_SLEEPING;

    var ground = new p2.Body({ mass: 0, position: [0, 0] });
    ground.addShape(new p2.Plane());
    world.addBody(ground);

    var SCALE = 80;
    function physToPx(v) {
      return [
        canvas.width / 2 + v[0] * SCALE,
        canvas.height - v[1] * SCALE
      ];
    }
    function eventToWorld(e) {
      var r = canvas.getBoundingClientRect();
      var sx = canvas.width / Math.max(1, r.width);
      var sy = canvas.height / Math.max(1, r.height);
      var x = (e.clientX - r.left) * sx;
      var y = (e.clientY - r.top) * sy;
      return [
        (x - canvas.width / 2) / SCALE,
        (canvas.height - y) / SCALE
      ];
    }

    var bodies = [];
    function addBody(body) {
      body.allowSleep = false;
      world.addBody(body);
      bodies.push(body);
      return body;
    }

    var torso = addBody(new p2.Body({ mass: 3, position: [0, 1.6] }));
    torso.addShape(new p2.Box({ width: 0.5, height: 0.9 }));

    var head = addBody(new p2.Body({ mass: 1, position: [0, 2.4] }));
    head.addShape(new p2.Circle({ radius: 0.28 }));

    var lArm = addBody(new p2.Body({ mass: 0.8, position: [-0.55, 1.8] }));
    lArm.addShape(new p2.Box({ width: 0.18, height: 0.7 }));

    var rArm = addBody(new p2.Body({ mass: 0.8, position: [0.55, 1.8] }));
    rArm.addShape(new p2.Box({ width: 0.18, height: 0.7 }));

    world.addConstraint(new p2.RevoluteConstraint(head, torso, {
      localPivotA: [0, -0.28],
      localPivotB: [0, 0.45]
    }));
    world.addConstraint(new p2.RevoluteConstraint(lArm, torso, {
      localPivotA: [0, 0.35],
      localPivotB: [-0.25, 0.25]
    }));
    world.addConstraint(new p2.RevoluteConstraint(rArm, torso, {
      localPivotA: [0, 0.35],
      localPivotB: [0.25, 0.25]
    }));

    var energy = 0.4;
    setInterval(function () {
      energy = 0.3 + Math.random() * 0.4;
    }, 400);

    var mouseBody = new p2.Body({ type: p2.Body.KINEMATIC, mass: 0 });
    world.addBody(mouseBody);
    var mouseJoint = null;
    var dragged = null;
    var aim = null;

    function pick(worldPoint) {
      var pads = [[0, 0], [0.12, 0], [-0.12, 0], [0, 0.12], [0, -0.12], [0.12, 0.12], [-0.12, -0.12]];
      for (var i = 0; i < pads.length; i++) {
        var hits = world.hitTest([worldPoint[0] + pads[i][0], worldPoint[1] + pads[i][1]], bodies);
        if (hits && hits.length) return hits[0];
      }
      return null;
    }

    function drop() {
      if (mouseJoint) {
        world.removeConstraint(mouseJoint);
        mouseJoint = null;
      }
      dragged = null;
      aim = null;
      canvas.style.cursor = "grab";
    }

    canvas.addEventListener("pointerdown", function (e) {
      var p = eventToWorld(e);
      var b = pick(p);
      if (!b) return;
      e.preventDefault();
      e.stopPropagation();
      try { canvas.setPointerCapture(e.pointerId); } catch (err) {}
      if (mouseJoint) world.removeConstraint(mouseJoint);
      mouseBody.position[0] = p[0];
      mouseBody.position[1] = p[1];
      mouseBody.velocity[0] = 0;
      mouseBody.velocity[1] = 0;
      b.wakeUp && b.wakeUp();
      mouseJoint = new p2.RevoluteConstraint(mouseBody, b, {
        worldPivot: [p[0], p[1]],
        collideConnected: false,
        maxForce: 1e6
      });
      mouseJoint.maxForce = 1e6;
      world.addConstraint(mouseJoint);
      dragged = b;
      aim = p;
      canvas.style.cursor = "grabbing";
    }, { passive: false });

    canvas.addEventListener("pointermove", function (e) {
      if (!mouseJoint) return;
      e.preventDefault();
      aim = eventToWorld(e);
    }, { passive: false });

    function endPointer(e) {
      if (!mouseJoint) return;
      if (e && e.pointerId != null) {
        try { canvas.releasePointerCapture(e.pointerId); } catch (err) {}
      }
      drop();
    }
    canvas.addEventListener("pointerup", endPointer);
    canvas.addEventListener("pointercancel", endPointer);

    function drawCircle(b) {
      var p = physToPx(b.position);
      ctx.beginPath();
      ctx.arc(p[0], p[1], 0.28 * SCALE, 0, Math.PI * 2);
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
    function draw() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      ctx.lineWidth = 4;
      ctx.strokeStyle = "#00ffff";
      ctx.fillStyle = "#00ffff";
      drawCircle(head);
      drawBox(torso);
      drawBox(lArm);
      drawBox(rArm);
    }

    var draggingFlag = false;
    function loop() {
      draggingFlag = !!mouseJoint;
      if (mouseJoint && aim) {
        mouseBody.position[0] = aim[0];
        mouseBody.position[1] = aim[1];
        mouseBody.velocity[0] = 0;
        mouseBody.velocity[1] = 0;
      }
      if (dragged !== torso) torso.angularVelocity += (energy - 0.5) * 0.05;
      if (dragged !== lArm) lArm.angularVelocity += Math.sin(Date.now() * 0.005) * energy * 0.08;
      if (dragged !== rArm) rArm.angularVelocity -= Math.sin(Date.now() * 0.005) * energy * 0.08;
      world.step(1 / 60);
      draw();
      requestAnimationFrame(loop);
    }
    loop();

    window.jahDoll = {
      pick: pick,
      eventToWorld: eventToWorld,
      get dragged() { return dragged; },
      bodies: bodies,
      isDragging: function () { return draggingFlag; }
    };
  }

  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", boot);
  else boot();
})();
