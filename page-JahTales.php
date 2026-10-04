<?php
/* Template Name: Jah Tales Player — Сказки Джа */
get_header();
?>
<script src="https://cdn.jsdelivr.net/npm/p2@0.7.1/build/p2.min.js"></script>
<style>
/* ---- Jah Tales player - полный набор стилей ---- */
:root{
  --bg:#000;
  --accent: #00ffff;
  --panel: rgba(0,0,0,0.6);
  --control-bg: rgba(0,0,0,0.55);
}

html,body{height:100%;margin:0;background:var(--bg);color:#fff;font-family:Arial, Helvetica, sans-serif;overflow:hidden}

.jah-stage{
  position:fixed;
  inset:0;
  display:flex;
  gap:16px;
  align-items:center;
  justify-content:center;
  padding:16px;
  box-sizing:border-box;
  background:var(--bg);
  z-index:1000;
}

/* основная обёртка плеера */
.jah-wrap{
  position:relative;
  width:min(1100px,92vw);
  height:min(620px,84vh);
  max-height:100vh;
  background:#000;
  border-radius:12px;
  overflow:hidden;
  box-shadow: 0 24px 80px rgba(0,0,0,0.8);
}

/* видео */
.jah-wrap video#mainVideo {
  width:100%;
  height:100%;
  display:block;
  object-fit:contain;
  /* переходы для эффектов — 5s вход/выход */
  transition: transform 5s ease, filter 5s ease, opacity 5s ease;
  background:#000;
}

/* панель контролов (внутри обёртки) */
.controls-row{
  position:absolute;
  left:12px;
  bottom:12px;
  display:flex;
  gap:8px;
  align-items:center;
  z-index:40;
  pointer-events:auto;
}
.ctrl-btn{
  background:var(--control-bg);
  color:#fff;
  border:1px solid rgba(255,255,255,0.06);
  padding:8px 10px;
  border-radius:8px;
  font-size:13px;
  cursor:pointer;
}
.ctrl-btn:hover{ background: rgba(255,255,255,0.04); }

.controls-row label{ font-size:12px; color:#ddd; margin-left:6px; margin-right:4px; }

/* правая панель превью */
.side-panel{
  width:260px;
  max-height:84vh;
  overflow-y:auto;
  display:flex;
  flex-direction:column;
  gap:12px;
  padding:8px;
  box-sizing:border-box;
}
.thumb{
  width:100%;
  height:140px;
  border-radius:8px;
  overflow:hidden;
  background:#111;
  cursor:pointer;
  border:1px solid rgba(255,255,255,0.03);
  box-shadow: 0 8px 30px rgba(0,0,0,0.6);
}
.thumb video{ width:100%; height:100%; object-fit:cover; display:block; }

/* куколка на всю сцену: клики мимо неё проходят к кнопкам и превью */
.jah-doll-canvas {
  position:absolute;
  inset:0;
  width:100%;
  height:100%;
  pointer-events:none;
  touch-action:none;
  z-index:50;
  background:transparent;
}

/* адаптив */
@media (max-width: 980px){
  .jah-stage{ flex-direction:column; padding:8px; gap:12px; overflow:auto; }
  .side-panel{ width:95vw; flex-direction:row; max-height:none; overflow-x:auto; padding:6px; gap:8px; }
  .thumb{ width:160px; height:90px; flex:0 0 auto; }
  .jah-wrap{ width:95vw; height:56.25vw; max-height:60vh; border-radius:10px; }
}

/* overlay controls for fullscreen manual Prev/Next */
.fs-controls {
  position: absolute;
  right: 12px;
  top: 12px;
  display:flex;
  gap:8px;
  z-index:60;
}

/* small info */
.jah-info {
  position:absolute;
  left:12px;
  top:12px;
  z-index:60;
  color:#ddd;
  font-size:13px;
  background: rgba(0,0,0,0.25);
  padding:6px 8px;
  border-radius:8px;
}

/* progress and time */
.player-progress {
  position: absolute;
  left:0; right:0; bottom:0;
  height:6px;
  background: rgba(255,255,255,0.03);
  z-index:30;
}
.player-progress > i{
  display:block;
  height:100%;
  width:0%;
  background: linear-gradient(90deg, var(--accent), rgba(0,200,200,0.6));
}

/* small helper to hide page overflow when using full-screen-ish transitions (we handle via JS) */
body.jah-lock-scroll{ overflow:hidden; }

/* minimal button styles for toggles */
.toggle-on { background: linear-gradient(90deg, rgba(0,200,200,0.12), rgba(0,200,200,0.06)); border-color: rgba(0,200,200,0.18); color: var(--accent) }
</style>

<div class="jah-stage" role="main" aria-label="Jah Tales player">
  <div class="jah-wrap" id="jahWrap">
    <div class="jah-info" id="jahInfo">Jah Tales</div>

    <video id="mainVideo" playsinline preload="auto" crossorigin="anonymous">
      <!-- стартовый src можно менять -->
      <source src="https://walkingyog.com/wp-content/uploads/2025/11/30Сек43-1.mp4" type="video/mp4">
      Ваш браузер не поддерживает видео.
    </video>

    <div class="controls-row" role="region" aria-label="Player controls">
      <button class="ctrl-btn" id="btnPrev" title="Previous">⟵ Prev</button>
      <button class="ctrl-btn" id="btnNext" title="Next">Next ⟶</button>

      <button class="ctrl-btn" id="btnRepeat" title="Repeat">Repeat: OFF</button>
      <button class="ctrl-btn" id="btnShuffle" title="Shuffle">Shuffle: OFF</button>

      <button class="ctrl-btn" id="btnMute" title="Включить звук">Sound: OFF</button>
      <button class="ctrl-btn" id="btnFS" title="Fullscreen">Fullscreen</button>

      <label for="speedCtrl">Speed</label>
      <input id="speedCtrl" type="range" min="0.5" max="1.5" step="0.05" value="1" aria-label="Playback speed">
      <span id="speedLabel" style="color:#fff;font-size:12px;margin-left:6px">1.00x</span>
    </div>

    <div class="fs-controls" id="fsControls" aria-hidden="true">
      <button class="ctrl-btn" id="btnFSPrev" title="Prev in fullscreen">⟵</button>
      <button class="ctrl-btn" id="btnFSNext" title="Next in fullscreen">⟶</button>
    </div>

    <div class="player-progress" aria-hidden="true"><i id="progressBar"></i></div>
  </div>

  <div class="side-panel" id="sidePanel" aria-label="Playlist thumbnails">
    <!-- Добавь/редактируй элементы ниже; script соберёт playlist по data-src -->
    <div class="thumb" data-src="https://walkingyog.com/wp-content/uploads/2025/11/30Сек43-1.mp4" title="30 sec">
      <video muted loop playsinline preload="metadata"><source src="https://walkingyog.com/wp-content/uploads/2025/11/30Сек43-1.mp4" type="video/mp4"></video>
    </div>

    <div class="thumb" data-src="https://walkingyog.com/wp-content/uploads/2025/12/Jah-Love-480P.mp4" title="Jah Love">
      <video muted loop playsinline preload="metadata"><source src="https://walkingyog.com/wp-content/uploads/2025/12/Jah-Love-480P.mp4" type="video/mp4"></video>
    </div>

    <div class="thumb" data-src="https://walkingyog.com/wp-content/uploads/2026/09/i-dance-fin.mp4" title="I dance">
      <video muted loop playsinline preload="metadata"><source src="https://walkingyog.com/wp-content/uploads/2026/09/i-dance-fin.mp4" type="video/mp4"></video>
    </div>
    <div class="thumb" data-src="https://walkingyog.com/wp-content/uploads/2026/07/генератор.mp4" title="Generator">
      <video muted loop playsinline preload="metadata"><source src="https://walkingyog.com/wp-content/uploads/2026/07/генератор.mp4" type="video/mp4"></video>
    </div>

    <div class="thumb" data-src="https://walkingyog.com/wp-content/uploads/2026/10/remaster-ded.mp4" title="Pochatok">
      <video muted loop playsinline preload="metadata"><source src="https://walkingyog.com/wp-content/uploads/2026/10/remaster-ded.mp4" type="video/mp4"></video>
    </div>

    <div class="thumb" data-src="https://walkingyog.com/wp-content/uploads/2026/01/Invaders10.mp4" title="Invaders">
      <video muted loop playsinline preload="metadata"><source src="https://walkingyog.com/wp-content/uploads/2026/01/Invaders10.mp4" type="video/mp4"></video>
    </div>
     <div class="thumb" data-src="https://walkingyog.com/wp-content/uploads/2026/01/НЛО4-2.mp4" title="UFO">
      <video muted loop playsinline preload="metadata"><source src="https://walkingyog.com/wp-content/uploads/2026/01/НЛО4-2.mp4" type="video/mp4"></video>
    </div>

    <div class="thumb" data-src="https://walkingyog.com/wp-content/uploads/2026/09/Reshade-Rasta-Dance3.mp4" title="Rasta Dance">
      <video muted loop playsinline preload="metadata"><source src="https://walkingyog.com/wp-content/uploads/2026/09/Reshade-Rasta-Dance3.mp4" type="video/mp4"></video>
    </div>

    <div class="thumb" data-src="https://walkingyog.com/wp-content/uploads/2026/01/Сказки-Джа-Киев-2022-Some-Trips.mp4" title="Some war trips">
      <video muted loop playsinline preload="metadata"><source src="https://walkingyog.com/wp-content/uploads/2026/01/Сказки-Джа-Киев-2022-Some-Trips.mp4" type="video/mp4"></video>
    </div>

    <div class="thumb" data-src="https://walkingyog.com/wp-content/uploads/2026/01/Сказки-Джа-Глава-1-Жил-Был-Нетужил.mp4" title="1 part">
      <video muted loop playsinline preload="metadata"><source src="https://walkingyog.com/wp-content/uploads/2026/01/Сказки-Джа-Глава-1-Жил-Был-Нетужил.mp4" type="video/mp4"></video>
    </div>

    <div class="thumb" data-src="https://walkingyog.com/wp-content/uploads/2026/01/Участник-тв-программы-Танцы-на-ТнТ-2-сезон-В-Индии-Dance-Video-Indian-Session-XLocation.mp4" title="XLocations">
      <video muted loop playsinline preload="metadata"><source src="https://walkingyog.com/wp-content/uploads/2026/01/Участник-тв-программы-Танцы-на-ТнТ-2-сезон-В-Индии-Dance-Video-Indian-Session-XLocation.mp4" type="video/mp4"></video>
    </div>

  </div>

  <canvas id="jahDoll" class="jah-doll-canvas" aria-hidden="true"></canvas>
</div>

<!-- подключаем p2.js из папки темы (положи p2.min.js в /wp-content/themes/your-theme/js/) -->
<script src="<?php echo get_stylesheet_directory_uri(); ?>/js/p2.min.js"></script>

<script>
/* ---- Jah Tales player — весь JS (твой основной код, без изменений по логике) ---- */
document.addEventListener('DOMContentLoaded', function () {

  const main = document.getElementById('mainVideo');
  const wrap = document.getElementById('jahWrap');
  const sidePanel = document.getElementById('sidePanel');
  const progressBar = document.getElementById('progressBar');

  // controls
  const btnPrev = document.getElementById('btnPrev');
  const btnNext = document.getElementById('btnNext');
  const btnRepeat = document.getElementById('btnRepeat');
  const btnShuffle = document.getElementById('btnShuffle');
  const btnMute = document.getElementById('btnMute');
  const btnFS = document.getElementById('btnFS');
  const speedCtrl = document.getElementById('speedCtrl');
  const speedLabel = document.getElementById('speedLabel');
  const fsControls = document.getElementById('fsControls');
  const btnFSPrev = document.getElementById('btnFSPrev');
  const btnFSNext = document.getElementById('btnFSNext');

  // playlist from thumbs
  const thumbs = Array.from(document.querySelectorAll('.thumb[data-src]'));
  const playlist = thumbs.map(t => t.dataset.src).filter(Boolean);

  if(playlist.length === 0){
    console.warn('Playlist empty — add .thumb[data-src] elements.');
    return;
  }

  // state
  let currentIndex = 0;
  let repeatOne = false;
  let shuffle = false;
  let isTransitioning = false; // чтобы блокировать клики в ходе 5s перехода
  const TRANS_DURATION = 5000; // ms (вход/выход)
  const FADE_VOLUME_MS = 5000;

  // preloads: качаем next and next+1
  const preload1 = document.createElement('video');
  const preload2 = document.createElement('video');
  [preload1, preload2].forEach(v=>{
    v.preload = 'auto';
    v.muted = true;
    v.playsInline = true;
    v.style.display = 'none';
    document.body.appendChild(v);
  });

  // эффектный набор — применяются inline props (совместность в fullscreen корректируем)
  const effects = [
    { out: { opacity: 0 }, in: { opacity: 1, transform: 'none', filter: 'none' } },
    { out: { transform: 'scale(1.25)', opacity: 0 }, in: { transform: 'scale(1)', opacity: 1 } },
    { out: { filter: 'blur(20px)', opacity: 0 }, in: { filter: 'none', opacity: 1 } },
    { out: { transform: 'translateX(40%)', opacity: 0 }, in: { transform: 'translateX(0)', opacity: 1 } },
    { out: { transform: 'rotate(10deg) scale(1.12)', opacity: 0 }, in: { transform: 'rotate(0) scale(1)', opacity: 1 } }
  ];

  // helper: apply style map to element.style
  function applyStyleMap(el, map){
    for(const k in map) el.style[k] = map[k];
  }

  // Сначала всегда тихо. Один клик включает звук и сразу играет.
  let soundOn = false;
  function applySound(){
    main.defaultMuted = !soundOn;
    main.muted = !soundOn;
    if(soundOn){
      main.removeAttribute('muted');
      if(main.volume < 0.15) main.volume = 1;
      const p = main.play();
      if(p && p.catch) p.catch(()=>{});
      btnMute.textContent = 'Sound: ON';
    } else {
      main.setAttribute('muted','');
      btnMute.textContent = 'Sound: OFF';
    }
  }
  function setSound(on){
    soundOn = !!on;
    clearInterval(fadeVolume._timer);
    if(soundOn) main.volume = 1;
    applySound();
  }

  // volume fade helper — только когда звук уже включён пользователем
  function fadeVolume(target, duration = FADE_VOLUME_MS){
    if(!soundOn) return;
    const start = +main.volume;
    const diff = target - start;
    const steps = 50;
    let i = 0;
    clearInterval(fadeVolume._timer);
    fadeVolume._timer = setInterval(()=>{
      i++;
      main.volume = Math.max(0, Math.min(1, start + diff * (i/steps)));
      if(i>=steps) clearInterval(fadeVolume._timer);
    }, Math.max(12, duration/steps));
  }

  // preloading logic: preload next and next+1 from currentIndex
  function preloadNexts(index = currentIndex){
    let next = (index + 1) % playlist.length;
    let next2 = (index + 2) % playlist.length;
    preload1.src = playlist[next];
    preload1.load();
    preload2.src = playlist[next2];
    preload2.load();
  }

  // set initial src and preloads
  main.src = playlist[0];
  main.load();
  preloadNexts(0);

  // autoplay без звука; пользователь включает его одним нажатием
  setSound(false);
  main.play().catch(()=>{ /* autoplay may be blocked on some devices */ });
  main.addEventListener('loadeddata', applySound);

  // update progress bar
  main.addEventListener('timeupdate', ()=> {
    try {
      const pct = (main.currentTime / main.duration) * 100;
      progressBar.style.width = isFinite(pct)? pct + '%' : '0%';
    } catch(e) {}
  });

  // speed control
  speedCtrl.addEventListener('input', ()=> {
    main.playbackRate = parseFloat(speedCtrl.value);
    speedLabel.textContent = main.playbackRate.toFixed(2) + 'x';
  });

  btnMute.addEventListener('click', (e)=> {
    e.preventDefault();
    e.stopPropagation();
    setSound(!soundOn);
  });

  // repeat toggle
  btnRepeat.addEventListener('click', ()=> {
    repeatOne = !repeatOne;
    btnRepeat.textContent = 'Repeat: ' + (repeatOne ? 'ON' : 'OFF');
    btnRepeat.classList.toggle('toggle-on', repeatOne);
  });

  // shuffle toggle
  btnShuffle.addEventListener('click', ()=> {
    shuffle = !shuffle;
    btnShuffle.textContent = 'Shuffle: ' + (shuffle ? 'ON' : 'OFF');
    btnShuffle.classList.toggle('toggle-on', shuffle);
  });

  // prev / next actions (shared)
  function gotoIndex(index, immediate = false){
    if(isTransitioning) return;
    isTransitioning = true;

    // clamp index
    index = ((index % playlist.length) + playlist.length) % playlist.length;

    // if user requested same index and repeatOne -> just restart
    if(index === currentIndex && repeatOne){
      main.currentTime = 0;
      main.play().catch(()=>{});
      isTransitioning = false;
      return;
    }

    // choose effect (if fullscreen fallback to simple fade)
    const inFS = !!document.fullscreenElement;
    const fx = inFS ? effects[0] : effects[Math.floor(Math.random() * effects.length)];

    // apply OUT style
    applyStyleMap(main, fx.out);
    fadeVolume(0, FADE_VOLUME_MS);

    setTimeout(()=> {
      // swap src
      currentIndex = index;
      const nextSrc = playlist[currentIndex];

      // Use preload if it matches nextSrc
      const usePreload = (preload1.src && preload1.src.includes(nextSrc)) ? preload1.src
                        : (preload2.src && preload2.src.includes(nextSrc)) ? preload2.src
                        : null;

      main.pause();

      if(usePreload){
        main.src = usePreload;
      } else {
        main.src = nextSrc;
      }

      main.load();
      // after small allow, play and apply IN style
      setTimeout(()=>{
        main.play().catch(()=>{});
        applyStyleMap(main, fx.in);
        fadeVolume(1, FADE_VOLUME_MS);
      }, 120);

      // immediately start preloading subsequent videos
      preloadNexts(currentIndex);

      // unlock after transition
      setTimeout(()=> {
        isTransitioning = false;
      }, TRANS_DURATION + 140); // extra margin
    }, TRANS_DURATION);
  }

  btnPrev.addEventListener('click', ()=> {
    let next = currentIndex - 1;
    if(next < 0) next = playlist.length - 1;
    gotoIndex(next);
  });

  btnNext.addEventListener('click', ()=> {
    let next;
    if(shuffle){
      next = Math.floor(Math.random() * playlist.length);
    } else {
      next = currentIndex + 1;
      if(next >= playlist.length) next = 0;
    }
    gotoIndex(next);
  });

  // fullscreen toggling — keep UI adjustments
  btnFS.addEventListener('click', async ()=> {
    if(!document.fullscreenElement){
      try {
        await wrap.requestFullscreen();
      } catch(e) { /* ignore */ }
    } else {
      try { await document.exitFullscreen(); } catch(e) {}
    }
  });

  // show/hide fullscreen overlay controls on enter/exit
  document.addEventListener('fullscreenchange', ()=> {
    const inFS = !!document.fullscreenElement;
    fsControls.setAttribute('aria-hidden', !inFS);
    fsControls.style.display = inFS ? 'flex' : 'none';
    // in fullscreen we can reduce heavy effects by resetting style
    if(inFS){
      // ensure main is visible
      applyStyleMap(main, { opacity: 1, transform: 'none', filter: 'none' });
    }
  });

  btnFSPrev.addEventListener('click', ()=> btnPrev.click());
  btnFSNext.addEventListener('click', ()=> btnNext.click());

  // when main ends
  main.addEventListener('ended', ()=> {
    if(repeatOne){
      // repeat same
      main.currentTime = 0;
      main.play().catch(()=>{});
      return;
    }

    let nextIndex;
    if(shuffle){
      nextIndex = Math.floor(Math.random() * playlist.length);
    } else {
      nextIndex = currentIndex + 1;
      if(nextIndex >= playlist.length) nextIndex = 0;
    }

    // If preload1 matches next, use it; else do animated change
    const nextSrc = playlist[nextIndex];
    if(preload1.src && preload1.src.includes(nextSrc)){
      // quick swap without full 5s-out (we still do small crossfade)
      isTransitioning = true;
      // small crossfade 1s: reduce volume & opacity then swap
      fadeVolume(0, 800);
      main.style.transition = 'opacity 1s linear';
      main.style.opacity = '0';
      setTimeout(()=>{
        main.pause();
        main.src = preload1.src;
        main.load();
        main.play().catch(()=>{});
        main.style.opacity = '1';
        main.style.transition = 'transform 5s ease, filter 5s ease, opacity 5s ease';
        fadeVolume(1, 1200);
        currentIndex = nextIndex;
        preloadNexts(currentIndex);
        setTimeout(()=> isTransitioning = false, 1200);
      }, 1000);
    } else {
      gotoIndex(nextIndex);
    }
  });

  /* Hover/preview behavior + click on thumbnails */
  thumbs.forEach((t, i) => {
    const v = t.querySelector('video');
    t.addEventListener('mouseenter', ()=> {
      v.play().catch(()=>{});
    });
    t.addEventListener('mouseleave', ()=> {
      v.pause();
      v.currentTime = 0;
    });
    t.addEventListener('click', ()=> {
      // if already transitioning - ignore
      if(isTransitioning) return;
      currentIndex = i;
      gotoIndex(i);
    });
  });

  /* allow keyboard shortcuts: left/right for prev/next, space play/pause, R repeat toggle */
  window.addEventListener('keydown', (e)=> {
    if(e.target && /input|textarea/i.test(e.target.tagName)) return;
    if(e.key === 'ArrowLeft') btnPrev.click();
    if(e.key === 'ArrowRight') btnNext.click();
    if(e.key === ' ') { e.preventDefault(); if(main.paused) main.play().catch(()=>{}); else main.pause(); }
    if(e.key.toLowerCase() === 'r') btnRepeat.click();
    if(e.key.toLowerCase() === 's') btnShuffle.click();
  });

  /* expose small API for debugging */
  window.jahTales = {
    playlist,
    gotoIndex,
    next: ()=> btnNext.click(),
    prev: ()=> btnPrev.click(),
    toggleRepeat: ()=> btnRepeat.click(),
    toggleShuffle: ()=> btnShuffle.click()
  };

  /* initial UI state */
  btnRepeat.textContent = 'Repeat: OFF';
  btnShuffle.textContent = 'Shuffle: OFF';
  speedLabel.textContent = main.playbackRate.toFixed(2) + 'x';
  fsControls.style.display = 'none';

  // Preload initial 2 next videos
  preloadNexts(currentIndex);

});
</script>

<script src="<?php echo esc_url( get_template_directory_uri() ); ?>/js/jahDoll.js"></script>

<?php
get_footer();
?>
