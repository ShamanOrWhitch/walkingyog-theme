<?php
/* 
*/
get_header();
?>
<div id="wy-preloader">
    <img src="https://walkingyog.com/wp-content/uploads/2025/12/downloader.gif" alt="loading">
</div>

<div id="video-container" aria-hidden="true">
    <canvas id="dissolve-canvas"></canvas>
</div>

<style>
  /* Базовые стили: упрощены и не конфликтуют с другими частями темы */
  :root{--bg:#000;--card:rgba(255,255,255,0.06);--muted:#9aa}
  html,body{height:100%;margin:0;padding:0;background:var(--bg);color:#fff}
  #video-container{position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:480px;height:854px;border-radius:20px;overflow:hidden;box-shadow:0 0 50px rgba(0,255,255,0.12);z-index:5}
  #dissolve-canvas{display:block;width:100%;height:100%}
  .site-content{position:relative;z-index:10;margin-top:940px;padding:40px;background:rgba(0,0,0,0.85);min-height:100vh}
  h1,h2,h4,p{color:#fff !important;text-shadow:0 0 8px #000}
  .panel{background:var(--card);border:none;border-radius:12px;padding:18px}
  /* карточка cover и ссылки */
  .cover { width: 180px; height: 180px; border-radius: 50%; background: url('https://walkingyog.com/wp-content/uploads/2025/10/roothead.gif') center/cover no-repeat; margin: 24px auto 6px; box-shadow: 0 0 30px rgba(255,200,80,0.25); }
  .links { max-width:420px;margin:14px auto; display:flex;flex-direction:column;gap:10px;align-items:center }
  .link { width:100%; display:block; text-decoration:none; background:#0b0b0b; color:#fff; padding:12px 16px; border-radius:12px; text-align:center; border:1px solid rgba(255,255,255,0.04) }
  @media(max-width:640px){
    #video-container{width:320px;height:568px}
    .site-content{margin-top:700px;padding:18px}
  }
 }
<!-- Donation box (динамический) -->
<a id="donation-box" href="/donatik" aria-label="Donate" style="display:block;">
  <img id="donate-img" src="https://walkingyog.com/wp-content/uploads/2025/12/Стеклянная-донатная-коробка_2.jpg" alt="Donate">
  <!-- video элемент скрыт по умолчанию, включаем при hover -->
  <video id="donate-video" src="https://walkingyog.com/wp-content/uploads/2025/12/video-6.mp4" muted loop playsinline preload="metadata" style="display:none;"></video>
</a>



#video-container {
  position: fixed;
  top: 50%; left: 50%;
  transform: translate(-50%,-50%);
  width: 480px; height: 854px; /* твой 9:16 контейнер — меняй при желании */
  max-width: 100vw;
  max-height: 100vh;
  overflow: hidden;
  z-index: 5;
  pointer-events: none;
  background:#000;
}

/* canvas must fill its container (CSS pixels) */
#dissolve-canvas, #canvas {
  display:block;
  width:100% !important;
  height:100% !important;
}

/* native fallback video */
.wy-fallback-video {
  width:100% !important;
  height:100% !important;
  object-fit: contain !important; /* <- ключевой фикс */
  display:block;
}

/* На маленьких экранах подгоняем контейнер */
@media (max-width: 640px) {
  #video-container { width: 320px; height: 568px; }
}

</style>

<?php
// Подключение библиотек (если у темы нет enqueue — подключаем явно здесь)
// Если у тебя уже загружены эти скрипты глобально — можно убрать эти теги.
?>
<script src="<?php echo esc_url( get_template_directory_uri() ); ?>/js/three.min.js"></script>
<script src="<?php echo esc_url( get_template_directory_uri() ); ?>/js/gsap.min.js"></script>
<script src="<?php echo esc_url( get_template_directory_uri() ); ?>/js/homeDissolve.js"></script>
<script>
(function(){
  var videos = [
    "https://walkingyog.com/wp-content/uploads/2025/11/11282-1.mp4",
    "https://walkingyog.com/wp-content/uploads/2025/11/30Сек43-1.mp4",
    "https://walkingyog.com/wp-content/uploads/2025/12/1203-9166.mp4",
    "https://walkingyog.com/wp-content/uploads/2025/11/11271-1.mp4",
    "https://walkingyog.com/wp-content/uploads/2025/12/12071.mp4"
  ];
  if (window.WY_HomeDissolve_Start) {
    window.WY_HomeDissolve_Start({ canvasId: "dissolve-canvas", videos: videos });
  }
  window.showTrailer = function(){
    var trailer = document.getElementById('trailer');
    if(!trailer) return;
    trailer.style.display = 'block';
    trailer.play().catch(function(){});
    trailer.scrollIntoView({behavior:'smooth',block:'center'});
  };
})();
</script>

<div class="site-content container">
  <div class="panel text-center">
    <div class="cover">
        <?php echo do_shortcode('[wyg_animated_menu]'); ?>
    </div>

    <h1 style="font-size:2.2rem;color:#0ff;margin:8px 0 2px;">
      ओं मणिपद्मे हूं
<br><span class="namaste-lower">om mani padme hum</span>    </h1>
    <h2 style="margin:0 0 10px;color:#9aa">Держи баланс 🌐 —  by ShamanOrWitch</h2>

    <div class="links">
      <button class="link" onclick="showTrailer()">🎬 Смотреть трейлер (30 MB)</button>
     <center> <h3> Musical portals </h3> </center>
     
</a>
      <a class="link" href="https://open.spotify.com/artist/3AhCSHtdfK8fmTBqGXAts1" target="_blank" rel="noopener">🎧 Spotify</a>
       <a class="link" href="https://soundcloud.com/jah-shaman/sets" target="_blank" rel="noopener">🌊 SoundCloud</a>
      <a class="link" href="https://www.deezer.com/en/profile/6655306301" target="_blank" rel="noopener">🎶 Deezer</a>
      <a class="link" href="https://open.anghami.com/rEj690dgfXb" target="_blank" rel="noopener">🎵 Anghami</a>
      <a class="link" href="https://music.apple.com/il/artist/shamanorwitch/1838926179" target="_blank" rel="noopener"> "A" apple</a>
      <a class="link" href="https://music.youtube.com/playlist?list=PLdZQQfOk25DkP-m5sFXU-U6u_S3zIyI0w" target="_blank" rel="noopener">🔴 youtube music</a>
     <center> <h3> Video portals </h3> </center>
      <a class="link" href="https://www.youtube.com/@WalkingYog" target="_blank" rel="noopener">▶️ YouTube</a>
                  <a class="link" href="https://www.instagram.com/walkingyogjah/" target="_blank" rel="noopener">📸 Instagram</a>
      <a class="link" href="https://www.twitch.tv/walkingyog" target="_blank" rel="noopener">👾 Twich</a>
            <a class="link" href="https://www.tiktok.com/@walking_yog" target="_blank" rel="noopener">🎬 TikTok</a>
     <center> <h3> Social liknks </h3> </center>       
      <a class="link" href="https://x.com/walkingyog" target="_blank" rel="noopener">🐦 X (Twitter)</a>
      <a class="link" href="https://vk.com/jahshaman" target="_blank" rel="noopener">📀 VK</a>
      <a class="link" href="https://www.facebook.com/YogWalking" target="_blank" rel="noopener">📘 Facebook</a>
      
    </div>

    <video id="trailer" controls preload="none" poster="https://walkingyog.com/wp-content/uploads/2025/10/roothead.gif" style="display:none;max-width:460px;margin:18px auto;border-radius:12px;">
      <source src="https://walkingyog.com/wp-content/uploads/2025/10/10066.mp4" type="video/mp4">
      Ваш браузер не поддерживает видео.
    </video>

  </div>

  <?php
  // Показ миниатюр записей travel
  $q = new WP_Query(['post_type'=>'post','category_name'=>'traveling','posts_per_page'=>6]);
  if ($q->have_posts()): ?>
    <div style="margin-top:28px;text-align:center"><h2>Путешествуем в</h2></div>
    <div style="display:flex;flex-wrap:wrap;gap:12px;justify-content:center;margin-top:12px">
      <?php while($q->have_posts()): $q->the_post(); ?>
        <div style="width:260px">
          <a href="<?php the_permalink(); ?>">
            <?php if (has_post_thumbnail()) the_post_thumbnail('medium', ['style'=>'border-radius:12px;width:100%']); ?>
            <h3 style="color:#fff;margin:8px 0 0;"><?php the_title(); ?></h3>
          </a>
        </div>
      <?php endwhile; wp_reset_postdata(); ?>
    </div>
  <?php endif; ?>

</div>
<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/css/core.css">

<div id="donation-box">
  <img id="donate-img" src="https://walkingyog.com/wp-content/uploads/2025/12/Стеклянная-донатная-коробка_1.png">
  <video id="donate-hover" src="https://walkingyog.com/wp-content/uploads/2025/12/video-6.mp4"
    muted loop playsinline></video>
</div>

<!-- затемнение + видео открытия -->
<div id="donate-transition">
  <video id="donate-open-video"
    src="https://walkingyog.com/wp-content/uploads/2026/01/openbox.mp4"
    muted playsinline></video>
</div>

<!-- popup донатов -->
<div id="donatik-popup">
  <button id="donatik-close">✕</button>
  <div class="donatik-content">
      <style>
      #donatik-popup *{
  background:transparent !important;
  color:#fff !important;
}
#donatik-popup input,
#donatik-popup textarea,
#donatik-popup select{
  background:#111 !important;
  color:#fff !important;
  border:1px solid #333 !important;
}
</style>
    <h2>Donate</h2>
    <p>Ukraine credit cards</p>
    <p>4441114460166356 — mono</p>
    <p>4323345032895139 — A-Bank</p>
    <p>4149629371106865 — Privat24</p>
    <p>USDT TRC20</p>
    <?php echo do_shortcode('[sc name="Cdon"]'); ?>
  </div>
</div>

<script>
const box = document.getElementById('donation-box');
const img = document.getElementById('donate-img');
const hoverVid = document.getElementById('donate-hover');
const trans = document.getElementById('donate-transition');
const openVid = document.getElementById('donate-open-video');
const popup = document.getElementById('donatik-popup');
const closeBtn = document.getElementById('donatik-close');

/* появление через 120 сек */
setTimeout(()=> box.classList.add('show'), 116000);

/* hover видео */
box.addEventListener('mouseenter', ()=>{
  img.style.display='none';
  hoverVid.style.display='block';
  hoverVid.play().catch(()=>{});
});
box.addEventListener('mouseleave', ()=>{
  hoverVid.pause();
  hoverVid.currentTime=0;
  hoverVid.style.display='none';
  img.style.display='block';
});

/* клик */
box.addEventListener('click', e=>{
  e.preventDefault();
  box.classList.add('opening');   // растёт из своей точки
  setTimeout(()=> box.style.display='none', 2800);

  setTimeout(()=>{
    trans.classList.add('show');
    openVid.currentTime=0;
    openVid.play();
  },3000);

  openVid.onended = ()=>{
    trans.classList.remove('show');
    popup.classList.add('show'); // fade из темноты
  };
});

/* закрытие popup */
closeBtn.addEventListener('click', ()=>{
  popup.classList.remove('show');
});
closeBtn.addEventListener('click', ()=>{
  popup.classList.remove('show');

  // вернуть коробочку
  box.style.display = 'block';
  img.style.display = 'block';
  hoverVid.style.display = 'none';
  hoverVid.pause();
  hoverVid.currentTime = 0;

  box.classList.remove('opening');
  box.classList.remove('show');

  // снова летит в угол
  setTimeout(()=> box.classList.add('show'), 200);
});

</script>

<!-- =====================================================
     CREATIVE LAB PORTAL TRANSITION
===================================================== -->

<div id="creative-portal-transition" aria-hidden="true">
    <video
        id="creative-portal-video"
        muted
        playsinline
        preload="auto"
    >
        <source
            src="https://walkingyog.com/wp-content/uploads/2026/09/Portal.mp4"
            type="video/mp4"
        >
    </video>
</div>

<style>
#creative-portal-transition {
    position: fixed;
    inset: 0;
    width: 100vw;
    height: 100vh;
    display: none;
    align-items: center;
    justify-content: center;
    background: #000;
    overflow: hidden;
    z-index: 999999;
}

#creative-portal-transition.active {
    display: flex;
}

#creative-portal-video {
    width: 100vw;
    height: 100vh;
    display: block;
    margin: 0;
    padding: 0;
    object-fit: cover;
    background: #000;
}

@media (max-width: 640px) {
    #creative-portal-video {
        object-fit: contain;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const transition =
        document.getElementById('creative-portal-transition');

    const portalVideo =
        document.getElementById('creative-portal-video');

    if (!transition || !portalVideo) {
        console.error('Creative Lab Portal: элементы не найдены');
        return;
    }

    let started = false;

    document.addEventListener('click', function (event) {

        const link = event.target.closest(
            'a[href="/creative-lab/"],' +
            'a[href="https://walkingyog.com/creative-lab/"]'
        );

        if (!link) {
            return;
        }

        event.preventDefault();

        if (started) {
            return;
        }

        started = true;

        const destination = link.href;

        transition.classList.add('active');
        transition.setAttribute('aria-hidden', 'false');

        portalVideo.pause();
        portalVideo.currentTime = 0;

        portalVideo.onended = function () {
            window.location.href = destination;
        };

        portalVideo.onerror = function () {
            window.location.href = destination;
        };

        const playPromise = portalVideo.play();

        if (playPromise !== undefined) {
            playPromise.catch(function () {
                window.location.href = destination;
            });
        }

    });

});
</script>

<?php
get_footer();
?>