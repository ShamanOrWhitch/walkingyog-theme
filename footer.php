<?php
/**
 * Footer template for Walking Yog Let's start
 * (восстановление wp_footer() и базовой структуры)
 */
?>
    </main><!-- /main -->

    <footer class="site-footer" style="padding:25px;text-align:center;background:#0a0a0a;color:#ccc;">
        <p>© <?php echo date('Y'); ?> Walking Yog</p>
        <p style="font-size:0.9em;color:#665;">Om Mani Padme Hum</p>
    </footer>

    <?php 
    // ОБЯЗАТЕЛЬНО: завершение всех хуков WP
    wp_footer(); 
    ?>
<script>
document.addEventListener("DOMContentLoaded", function() {
    setTimeout(() => {
        const p = document.getElementById("wy-preloader");
        p.classList.add("hide");
        setTimeout(() => p.remove(), 1200);
    }, 2000); // прелоадер висит 2 сек
});
</script>
<!-- jQuery -->
<script src="<?php echo get_template_directory_uri(); ?>/js/jquery.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="<?php echo get_template_directory_uri(); ?>/js/bootstrap.min.js"></script>

</body>
</html>
