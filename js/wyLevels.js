/* Свайп уровней сайта. Не меню.
   [wy_levels] [wy_cell] ...код ячейки... [/wy_cell] [/wy_levels]
   Вверх/вниз — следующий уровень. Влево/вправо — если соседние ячейки с одним lane. */
(function () {
  function group(cells) {
    var rows = [];
    cells.forEach(function (cell) {
      var lane = cell.getAttribute("data-lane") || "";
      var prev = rows[rows.length - 1];
      if (lane && prev && prev.lane === lane) prev.cells.push(cell);
      else rows.push({ lane: lane, cells: [cell] });
    });
    return rows;
  }

  function boot(root) {
    if (root.dataset.wyReady === "1") return;
    var cells = Array.prototype.slice.call(root.querySelectorAll(":scope > .wy-cell"));
    if (!cells.length) return;
    root.dataset.wyReady = "1";
    var rows = group(cells);
    var v = 0;
    var h = 0;
    var lock = false;

    cells.forEach(function (cell) { cell.setAttribute("data-stick", ""); });

    function show() {
      rows.forEach(function (row, ri) {
        row.cells.forEach(function (cell, ci) {
          var on = ri === v && ci === h;
          cell.classList.toggle("is-active", on);
          cell.setAttribute("aria-hidden", on ? "false" : "true");
        });
      });
      var label = root.querySelector(".wy-levels__hint");
      if (!label) return;
      var row = rows[v];
      var bits = ["уровень " + (v + 1) + " / " + rows.length];
      if (row.cells.length > 1) bits.push("в сторону " + (h + 1) + " / " + row.cells.length);
      label.textContent = bits.join(" · ");
    }

    function moveV(dir) {
      if (lock) return;
      var next = v + dir;
      if (next < 0 || next >= rows.length) return;
      lock = true;
      v = next;
      h = 0;
      show();
      setTimeout(function () { lock = false; }, 420);
    }

    function moveH(dir) {
      if (lock) return;
      var row = rows[v];
      if (!row || row.cells.length < 2) return;
      var next = h + dir;
      if (next < 0 || next >= row.cells.length) return;
      lock = true;
      h = next;
      show();
      setTimeout(function () { lock = false; }, 420);
    }

    root.addEventListener("wheel", function (e) {
      var horizontal = Math.abs(e.deltaX) > Math.abs(e.deltaY) && rows[v] && rows[v].cells.length > 1;
      e.preventDefault();
      if (horizontal) moveH(e.deltaX > 0 ? 1 : -1);
      else moveV(e.deltaY > 0 ? 1 : -1);
    }, { passive: false });

    var startX = 0;
    var startY = 0;
    var tracking = false;
    root.addEventListener("pointerdown", function (e) {
      if (e.target.closest("a, button, input, textarea, select, video")) return;
      tracking = true;
      startX = e.clientX;
      startY = e.clientY;
    });
    root.addEventListener("pointerup", function (e) {
      if (!tracking) return;
      tracking = false;
      var dx = e.clientX - startX;
      var dy = e.clientY - startY;
      if (Math.abs(dx) < 36 && Math.abs(dy) < 36) return;
      if (Math.abs(dx) > Math.abs(dy)) moveH(dx < 0 ? 1 : -1);
      else moveV(dy < 0 ? 1 : -1);
    });
    root.addEventListener("pointercancel", function () { tracking = false; });

    root.addEventListener("keydown", function (e) {
      if (e.key === "ArrowDown") { e.preventDefault(); moveV(1); }
      else if (e.key === "ArrowUp") { e.preventDefault(); moveV(-1); }
      else if (e.key === "ArrowRight") { e.preventDefault(); moveH(1); }
      else if (e.key === "ArrowLeft") { e.preventDefault(); moveH(-1); }
    });

    var hint = document.createElement("div");
    hint.className = "wy-levels__hint";
    root.appendChild(hint);
    show();
  }

  function start() {
    Array.prototype.forEach.call(document.querySelectorAll("[data-wy-levels]"), boot);
  }
  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", start);
  else start();
})();
