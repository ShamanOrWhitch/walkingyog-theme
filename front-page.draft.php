```php
<?php get_header(); ?>

<!-- =========================================================
     PORTAL TRANSITION
     
     Работает ТОЛЬКО при клике на ссылку /creative-lab/
     
     Старый WY_DissolveManager не заменяет,
     не отключает и не изменяет.
========================================================= -->

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


<!-- =========================================================
     BACKGROUND VIDEOS
     
     Существующая система фоновых видео.
========================================================= -->

<div id="bg-videos" aria-hidden="true">

    <video
        src="https://walkingyog.com/wp-content/uploads/2025/11/11282-1.mp4"
        muted
        loop
        playsinline
        preload="auto">
    </video>

    <video
        src="https://walkingyog.com/wp-content/uploads/2025/11/30Сек43-1.mp4"
        muted
        loop
        playsinline
        preload="auto">
    </video>

    <video
        src="https://walkingyog.com/wp-content/uploads/2025/11/11271-1.mp4"
        muted
        loop
        playsinline
        preload="auto">
    </video>

</div>


<!-- =========================================================
     EXISTING DISSOLVE CANVAS
========================================================= -->

<canvas
    id="dissolve-canvas"
    style="
        position:fixed;
        top:0;
        left:0;
        width:auto;
        height:auto;
        z-index:1;
        pointer-events:none;
    ">
</canvas>


<style>

/* =========================================================
   GLOBAL
========================================================= */

html,
body {
    height:100%;
    margin:0;
    background:#000;
}


/* =========================================================
   BACKGROUND VIDEOS
========================================================= */

#bg-videos {
    position:fixed;
    inset:0;
    overflow:hidden;
    z-index:0;
}

#bg-videos video {
    position:absolute;

    top:50%;
    left:50%;

    transform:translate(-50%,-50%);

    width:auto;
    height:auto;

    min-width:100%;
    min-height:100%;

    object-fit:contain;
    -o-object-fit:contain;

    opacity:0;

    transition:opacity 1.2s ease;

    will-change:opacity,transform;
}


/* Первое видео показывается сразу */

#bg-videos video:first-child {
    opacity:1;
}


/* =========================================================
   MAIN CONTENT
========================================================= */

.site-content {
    position:relative;

    z-index:10;

    padding:40px;

    color:#fff;

    background:rgba(0,0,0,0.6);

    min-height:100vh;
}


/* =========================================================
   CREATIVE LAB LINK
========================================================= */

.creative-lab-link {
    position:relative;

    display:block;

    width:max-content;

    margin:30px auto;

    padding:15px 25px;

    color:#fff;

    background:rgba(0,255,255,0.15);

    border:1px solid rgba(0,255,255,0.5);

    border-radius:10px;

    text-decoration:none;

    text-align:center;

    cursor:pointer;
}


/* =========================================================
   PORTAL TRANSITION
     
   В обычном состоянии полностью скрыт.
========================================================= */

#creative-portal-transition {

    position:fixed;

    inset:0;

    z-index:999999;

    display:none;

    align-items:center;

    justify-content:center;

    width:100vw;
    height:100vh;

    background:#000;

    overflow:hidden;

}


/* Активное состояние */

#creative-portal-transition.active {

    display:flex;

}


/* =========================================================
   PORTAL VIDEO
========================================================= */

#creative-portal-video {

    display:block;

    width:100vw;
    height:100vh;

    margin:0;
    padding:0;

    object-fit:cover;

    background:#000;

}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width:640px) {

    #creative-portal-video {

        object-fit:contain;

    }

}

</style>


<!-- =========================================================
     MAIN PAGE CONTENT
========================================================= -->

<div class="site-content container">

    <h1 style="color:#0ff;text-align:center;">
        Walking Yog — портал
    </h1>

    <p style="text-align:center;color:#ccc;">
        Контент, ссылки и остальное — под твоим контролем
    </p>


    <!-- =====================================================
         CREATIVE LAB

         РЕАЛЬНЫЙ URL:
         https://walkingyog.com/creative-lab/
    ====================================================== -->

    <a
        href="/creative-lab/"
        class="creative-lab-link"
    >
        🧪 Творческая лаборатория
    </a>


    <!-- =====================================================
         ЗДЕСЬ ОСТАЛЬНОЙ КОНТЕНТ ГЛАВНОЙ СТРАНИЦЫ
    ====================================================== -->

</div>


<script>

/* =========================================================
   EXISTING DISSOLVE MANAGER
     
   Этот код оставлен отдельно.
   Portal Transition к нему НЕ привязан.
========================================================= */

document.addEventListener('DOMContentLoaded', function () {

    if (window.WY_DissolveManager_Start) {

        WY_DissolveManager_Start({

            canvasId: 'dissolve-canvas',

            dissolveInterval: 35000,

            autoPlay: true

        });

    } else {

        var managerScript =
            document.createElement('script');

        managerScript.src =
            '<?php echo get_template_directory_uri(); ?>/js/videoDissolveManager.js';

        managerScript.onload = function () {

            if (window.WY_DissolveManager_Start) {

                WY_DissolveManager_Start({

                    canvasId: 'dissolve-canvas',

                    dissolveInterval: 35000,

                    autoPlay: true

                });

            }

        };

        document.body.appendChild(managerScript);

    }

});


/* =========================================================
   CREATIVE LAB PORTAL
     
   ВАЖНО:
     
   Мы НЕ ищем ссылку один раз через
   document.querySelector().
     
   Вместо этого ловим клики на документе.
     
   Поэтому ссылка может быть:
     
   - в HTML страницы;
   - создана темой;
   - добавлена позже;
   - находиться внутри другого элемента.
========================================================= */

document.addEventListener('click', function (event) {

    /*
     * Ищем ближайшую ссылку от места клика.
     */

    var link = event.target.closest('a');

    
    /*
     * Если клик был не по ссылке —
     * ничего не делаем.
     */

    if (!link) {
        return;
    }


    /*
     * Получаем абсолютный URL ссылки.
     */

    var linkURL = link.href;


    /*
     * Проверяем именно страницу Creative Lab.
     *
     * Допустимы:
     *
     * /creative-lab/
     * https://walkingyog.com/creative-lab/
     *
     * Никакие другие ссылки не затрагиваются.
     */

    var isCreativeLab =
        linkURL.indexOf('/creative-lab/') !== -1;


    /*
     * Если это не Creative Lab —
     * оставляем обычное поведение ссылки.
     */

    if (!isCreativeLab) {
        return;
    }


    /*
     * На этом этапе мы нашли нужную ссылку.
     *
     * Останавливаем стандартный переход.
     */

    event.preventDefault();


    /*
     * Получаем Portal transition.
     */

    var transition =
        document.getElementById(
            'creative-portal-transition'
        );


    /*
     * Получаем видео.
     */

    var portalVideo =
        document.getElementById(
            'creative-portal-video'
        );


    /*
     * Если Portal отсутствует,
     * не оставляем пользователя на главной.
     *
     * Просто открываем Creative Lab.
     */

    if (!transition || !portalVideo) {

        window.location.href = linkURL;

        return;
    }


    /*
     * =====================================================
     * ПОКАЗ PORTAL
     * =====================================================
     */

    transition.classList.add('active');

    transition.setAttribute(
        'aria-hidden',
        'false'
    );


    /*
     * На всякий случай начинаем видео с начала.
     */

    try {

        portalVideo.pause();

        portalVideo.currentTime = 0;

    } catch (error) {

        /*
         * Даже если браузер не разрешил изменить
         * currentTime — продолжаем.
         */

    }


    /*
     * =====================================================
     * ПЕРЕХОД ПОСЛЕ ОКОНЧАНИЯ ВИДЕО
     * =====================================================
     */

    var destination = linkURL;


    portalVideo.onended = function () {

        window.location.href = destination;

    };


    /*
     * =====================================================
     * ЗАПУСК VIDEO
     * =====================================================
     */

    var playPromise = portalVideo.play();


    /*
     * Если браузер вернул Promise,
     * отслеживаем ошибку запуска.
     */

    if (playPromise !== undefined) {

        playPromise.catch(function (error) {

            /*
             * Видео не запустилось.
             *
             * Не оставляем пользователя
             * на чёрном экране.
             *
             * Сразу открываем Creative Lab.
             */

            window.location.href = destination;

        });

    }

});

</script>


<?php get_footer(); ?>
```
