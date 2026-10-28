<?php
/**
 * Template Name: Mod Page Full
 * Template Post Type: page
 *
 * Полная ширина под страницу мода. Автор темы не печатается.
 * Текст, требования и ссылки — из редактора страницы.
 * Если страница пустая, показывается каркас «Радио Jah Bless» со ссылкой на живую страницу gamer-mods.
 * Отзывы с gamer-mods сюда не копируются.
 */
get_header();
?>
<article class="wy-sheet wy-mod">
<?php
if (have_posts()) {
    while (have_posts()) {
        the_post();
        ?>
        <h1 class="wy-sheet__title"><?php the_title(); ?></h1>
        <div class="wy-sheet__content">
            <?php
            $raw = get_post_field('post_content', get_the_ID());
            if (trim(wp_strip_all_tags($raw)) === '') {
                ?>
                <p class="wy-mod__kicker">Fallout 4 · звук · версия 1.32b · 700+ МБ</p>
                <p>Радио с живым ведущим: Roots Reggae, Dub, притчи, растаманские сказки. Без мата. Около 19 часов. Частота в пип-бое 101.55. Голос в основном русский, треки на разных языках.</p>
                <h2>Где живая страница</h2>
                <p>Актуальные кнопки скачивания и отзывы игроков остаются на gamer-mods. Их отсюда не копируем: чужие комментарии и файл, который ты перезаливаешь, должны жить в одном месте.</p>
                <p><a href="https://gamer-mods.ru/load/fallout_4/zvuki/radio_jah_bless/145-1-0-15733">Радио Jah Bless на gamer-mods</a></p>
                <p><a href="https://www.nexusmods.com/fallout4/mods/79253">Nexus: JahShaman</a></p>
                <h2>Установка</h2>
                <ol>
                    <li>Скачать архив 7z и поставить через менеджер модов, либо распаковать в Data и подключить JahBlessRadio.esp.</li>
                    <li>Компаньон Automatron EyeBot ставится до радио. Тогда в верстаке роботов текущее радио — Jah Bless. Скрипт глазобота на нужную частоту лежит внутри мода.</li>
                </ol>
                <h2>Если молчит</h2>
                <p>Консоль: <code>StopQuest JahBlessRadioQuest</code> затем <code>StartQuest JahBlessRadioQuest</code>. Либо выключить радио в пип-бое и прыгнуть по карте. Крайний случай: сохраниться без мода дважды, потом включить мод и загрузить это сохранение.</p>
                <p>Если игре не видна папка Sound, в Documents/My Games/Fallout4/Fallout4Custom.ini строка <code>sResourceDataDirsFinal=STRINGS\, SOUND\</code>.</p>
                <h2>Стрим</h2>
                <p>Проверка звука: <a href="https://www.twitch.tv/walkingyog">Twitch walkingyog</a>.</p>
                <?php
            } else {
                the_content();
            }
            ?>
        </div>
        <?php
    }
}
?>
</article>
<?php get_template_part('template-parts/donate-box'); ?>
<?php
get_footer();
