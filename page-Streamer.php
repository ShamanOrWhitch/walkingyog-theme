<?php
/* Template Name: Stream Simple Player
   Вставь в тему как отдельный файл (например: page-stream.php) и используй как шаблон страницы
*/
get_header();
?>

<style>
/* ---- Минимальный чистый стиль: можно перенести в style.css ---- */
.stream-stage {
  position: fixed;
  inset: 0;
  display: flex;
  gap: 18px;
  align-items: center;
  justify-content: center;
  background: #000;
  padding: 18px;
  box-sizing: border-box;
  z-index: 100;
}

/* Видео-контейнер (центральное окно) */
.stream-wrap {
  position: relative;
  width: min(960px, 88vw);
  height: min(540px, 88vh);
  background:#000;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 10px 40px rgba(0,0,0,0.7);
}

/* основной video элемент */
.stream-wrap video#mainStream {
  width: 100%;
  height: 100%;
  display: block;
  background: #000;
  object-fit: contain; /* стартовый режим — contain (меняем через класс) */
}

/* object-fit режимы (переключаются классом на .stream-wrap) */
.stream-wrap.fit-contain video#mainStream { object-fit: contain; }
.stream-wrap.fit-cover   video#mainStream { object-fit: cover; }
.stream-wrap.fit-fill    video#mainStream { object-fit: fill; } /* растянуть */
.stream-wrap.fit-auto    video#mainStream {
  object-fit: contain;
}

/* Небольшая панель управления поверх видео (поверх встроенных controls) */
.controls-row {
  position:absolute;
  left:10px; bottom:10px;
  display:flex; gap:8px;
  align-items:center;
  z-index:20;
}
.ctrl-btn {
  background: rgba(0,0,0,0.45);
  color:#fff;
  border:1px solid rgba(255,255,255,0.06);
  padding:8px 10px;
  border-radius:8px;
  font-size:14px;
  cursor:pointer;
  backdrop-filter: blur(4px);
}
.ctrl-btn:hover { background: rgba(255,255,255,0.06); }

/* Боковая панель превью */
.side-panel {
  width: 240px;
  max-height: 88vh;
  overflow-y: auto;
  display:flex;
  flex-direction: column;
  gap:10px;
  align-items: center;
  padding: 6px;
  box-sizing: border-box;
}

/* каждое превью — маленькое видео */
.thumb {
  width: 220px;
  height: 130px;
  border-radius:8px;
  overflow:hidden;
  background:#111;
  border:1px solid rgba(255,255,255,0.03);
  cursor:pointer;
  box-shadow: 0 6px 18px rgba(0,0,0,0.6);
}
.thumb video { width:100%; height:100%; object-fit:cover; display:block; }

/* hover подсветка */
.thumb:hover { outline: 2px solid rgba(0,200,255,0.18); transform: translateY(-3px); transition: .18s; }

/* адаптив */
@media (max-width: 880px) {
  .stream-stage { flex-direction: column; padding:10px; gap:12px; }
  .side-panel { width: 92vw; flex-direction:row; overflow-x:auto; max-height:none; }
  .thumb { width:160px; height:90px; flex:0 0 auto; }
  .stream-wrap { width: 96vw; height: 56.25vw; max-height:60vh; } /* 16:9 по ширине экрана */
}

/* подсказка результата копирования */
.copy-toast {
  position: fixed;
  left: 50%; transform: translateX(-50%);
  bottom: 22px;
  padding: 8px 12px;
  background: rgba(0,0,0,0.65);
  color: #fff;
  border-radius: 8px;
  font-size: 13px;
  z-index:9999;
  opacity:0; transition: opacity .25s;
}
.copy-toast.show { opacity:1; }
</style>

<div class="stream-stage" role="main" aria-label="Stream player">
  <div class="stream-wrap fit-contain" id="streamWrap">
    <!-- Основной плеер -->
    <video id="mainStream" playsinline preload="metadata" controls crossorigin="anonymous" poster="https://walkingyog.com/wp-content/uploads/2025/11/11282-1.mp4">
      <!-- стартовый src — можешь заменить -->
      <source src="https://walkingyog.com/wp-content/uploads/2025/11/30Сек43-1.mp4" type="video/mp4">
      Ваш браузер не поддерживает видео.
    </video>

    <!-- собственные кнопки (над видео) -->
    <div class="controls-row" aria-hidden="false">
      <button class="ctrl-btn" id="btnMute" title="Выкл/вкл звук">Mute</button>
      <button class="ctrl-btn" id="btnFS" title="Полный экран">Fullscreen</button>
      <button class="ctrl-btn" id="btnShare" title="Поделиться ссылкой">Share</button>
      <button class="ctrl-btn" id="btnMode" title="Режим показа: Contain/Cover/Fill">Mode: contain</button>
    </div>
  </div>

  <div class="side-panel" aria-label="Thumbnails">
    <!-- Превью (при наведении воспроизводятся в миниатюре); кликом становятся основным -->
    <div class="thumb" data-src="https://walkingyog.com/wp-content/uploads/2025/11/30Сек43-1.mp4" title="Видео 1">
      <video muted playsinline preload="metadata" loop>
        <source src="https://walkingyog.com/wp-content/uploads/2025/11/30Сек43-1.mp4" type="video/mp4">
      </video>
    </div>

    <div class="thumb" data-src="https://walkingyog.com/wp-content/uploads/2025/12/Jah-Love-480P.mp4" title="Видео 2">
      <video muted playsinline preload="metadata" loop>
        <source src="https://walkingyog.com/wp-content/uploads/2025/12/Jah-Love-480P.mp4" type="video/mp4">
      </video>
    </div>

    <div class="thumb" data-src="https://walkingyog.com/wp-content/uploads/2025/12/i-dance.mp4" title="Видео 3">
      <video muted playsinline preload="metadata" loop>
        <source src="https://walkingyog.com/wp-content/uploads/2025/12/i-dance.mp4" type="video/mp4">
      </video>
    </div>
    <div class="thumb" data-src="https://walkingyog.com/wp-content/uploads/2026/01/Invaders9.mp4" title="Видео 3">
      <video muted playsinline preload="metadata" loop>
        <source src="https://walkingyog.com/wp-content/uploads/2026/01/Invaders9.mp4" type="video/mp4">
      </video>
    </div

    <!-- добавь сколько нужно -->
  </div>
</div>

<div id="copyToast" class="copy-toast" aria-hidden="true">Ссылка скопирована</div>

<script>
/* ---- JS: поведение плеера ---- */
(function(){

  const main = document.getElementById('mainStream');
  const wrap = document.getElementById('streamWrap');
  const btnMute = document.getElementById('btnMute');
  const btnFS = document.getElementById('btnFS');
  const btnShare = document.getElementById('btnShare');
  const btnMode = document.getElementById('btnMode');
  const toast = document.getElementById('copyToast');

  // кнопка Mute
  btnMute.addEventListener('click', () => {
    main.muted = !main.muted;
    btnMute.textContent = main.muted ? 'Unmute' : 'Mute';
  });

  // fullscreen
  btnFS.addEventListener('click', async () => {
    try {
      if (!document.fullscreenElement) {
        await wrap.requestFullscreen();
      } else {
        await document.exitFullscreen();
      }
    } catch(e) {
      console.warn('FS error', e);
    }
  });

  // share (navigator.share or copy)
  btnShare.addEventListener('click', async () => {
    const shareData = {
      title: document.title,
      text: 'Смотреть стрим:',
      url: window.location.href
    };
    if (navigator.share) {
      try { await navigator.share(shareData); }
      catch(err){ console.warn('Share failed', err); }
    } else {
      // fallback: copy ссылку
      try {
        await navigator.clipboard.writeText(window.location.href);
        showToast('Ссылка скопирована');
      } catch(e) {
        showToast('Не удалось скопировать');
      }
    }
  });

  function showToast(txt){
    toast.textContent = txt;
    toast.classList.add('show');
    setTimeout(()=> toast.classList.remove('show'), 1800);
  }

  // mode switching: contain -> cover -> fill -> auto
  const modes = ['contain','cover','fill','auto'];
  let modeIndex = 0;
  function setMode(i){
    modeIndex = i % modes.length;
    wrap.classList.remove('fit-contain','fit-cover','fit-fill','fit-auto');
    wrap.classList.add('fit-' + modes[modeIndex]);
    btnMode.textContent = 'Mode: ' + modes[modeIndex];
  }
  btnMode.addEventListener('click', ()=> setMode(modeIndex + 1));
  setMode(0); // старт

  // thumbnails: hover starts preview in thumb, click sets main video src
  const thumbs = document.querySelectorAll('.thumb');
  thumbs.forEach(t => {
    const vid = t.querySelector('video');
    // hover: play preview
    t.addEventListener('mouseenter', () => {
      // попытка play, часть браузеров может блокировать autoplay; но видео muted -> OK
      vid.play().catch(()=>{ /* ignore */ });
    });
    t.addEventListener('mouseleave', () => {
      vid.pause();
      vid.currentTime = 0;
    });

    // click: сменить основной источник на тот что в data-src
    t.addEventListener('click', async () => {
      const src = t.getAttribute('data-src');
      if(!src) return;
      // заменить источник основного плеера аккуратно, сохранить state (paused/played)
      const wasPlaying = !main.paused && !main.ended;
      main.pause();
      // replace source(s)
      while(main.firstChild) main.removeChild(main.firstChild);
      const s = document.createElement('source');
      s.src = src; s.type = 'video/mp4';
      main.appendChild(s);
      try {
        main.load();
        if(wasPlaying) await main.play();
      } catch(e){ console.warn('play failed', e); }
    });
  });

  // небольшая помощь: при клике по видео — toggle play
  main.addEventListener('click', (e) => {
    // не трогаем если клик на controls (mobile)
    if (e.target === main) {
      if (main.paused) main.play().catch(()=>{});
      else main.pause();
    }
  });

  // при изменении размера экрана — подбираем высоту/ширину на mobile
  window.addEventListener('resize', () => {
    // тут можно добавить адаптивную логику, если нужно
  });

  // автозапуск превью у всех мини видео (чтобы при наведении они были ready)
  thumbs.forEach(t => {
    const v = t.querySelector('video');
    v.addEventListener('canplay', ()=> v.pause());
  });

})();
</script>


<?php
get_footer();
?>
