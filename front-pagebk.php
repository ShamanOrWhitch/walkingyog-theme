<?php get_header(); ?>

<!-- BG VIDEOS: именно здесь ТЫ должен иметь свои готовые ролики -->
<div id="bg-videos" aria-hidden="true">
    <video src="https://walkingyog.com/wp-content/uploads/2025/11/11282-1.mp4" muted loop playsinline preload="auto"></video>
    <video src="https://walkingyog.com/wp-content/uploads/2025/11/30Сек43-1.mp4" muted loop playsinline preload="auto"></video>
    <video src="https://walkingyog.com/wp-content/uploads/2025/11/11271-1.mp4" muted loop playsinline preload="auto"></video>
</div>

<!-- Canvas для рендера (шейдер или GSAP использует его) -->
<canvas id="dissolve-canvas" style="position:fixed;top:0;left:0;width:auto;height:auto;z-index:1;pointer-events:none;"></canvas>

<style>
/* базовая страховка: видео в фоне, object-fit cover, центрирование */
html,body{height:100%;margin:0;background:#000;}
#bg-videos{position:fixed;inset:0;overflow:hidden;z-index:0}
#bg-videos video{
    position:absolute;
    top:50%; left:50%;
    transform:translate(-50%,-50%);
    width:auto; height:auto;
    min-width:100%;
    min-height:100%;
    object-fit:contain;
    -o-object-fit:contain;
    opacity:0;
    transition:opacity 1.2s ease;
    will-change:opacity,transform;
}
/* make first visible by default until manager swaps */
#bg-videos video:first-child{opacity:1}

/* содержание сайта должно идти поверх canvas (z-index > canvas) */
.site-content{position:relative;z-index:10;padding:40px;color:#fff;background:rgba(0,0,0,0.6);min-height:100vh}
</style>

<div class="site-content container">
    <h1 style="color:#0ff;text-align:center">Walking Yog — портал</h1>
    <p style="text-align:center;color:#ccc">Контент, ссылки и остальное — под твоим контролем</p>
    <!-- здесь остальной контент -->
</div>

<?php
// Локальный вызов менеджера.
// Менеджер автоматически подгрузит shader или fallback в зависимости от возможностей устройства.
?>
<script>
document.addEventListener('DOMContentLoaded', function(){
    // Варианты опций: canvasId — id холста; dissolveInterval — интервал между переходами (ms)
    if(window.WY_DissolveManager_Start){
        WY_DissolveManager_Start({
            canvasId: 'dissolve-canvas',
            dissolveInterval: 35000, // можно менять
            autoPlay: true
        });
    } else {
        // если скрипт не подключен через functions.php — можно динамически подгрузить мэнеджер
        var s = document.createElement('script');
        s.src = '<?php echo get_template_directory_uri(); ?>/js/videoDissolveManager.js';
        s.onload = function(){ WY_DissolveManager_Start({ canvasId:'dissolve-canvas', dissolveInterval:35000, autoPlay:true }); };
        document.body.appendChild(s);
    }
});
</script>

<?php get_footer(); ?>
