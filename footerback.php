<!-- Footer -->
<footer>
    <div class="row">
        <div class="col-lg-12">
            <p>Copyright &copy; <?php echo get_bloginfo('name').' 2009-'.date('Y'); ?></p>
        </div>
    </div>
</footer>

</div>
<!-- /.container -->

<!-- jQuery -->
<script src="<?php e_template_path(); ?>/js/jquery-3.3.1.js"></script>
<script src="<?php e_template_path(); ?>/js/fsslider.js"></script>


<!-- Bootstrap Core JavaScript -->
<script src="<?php e_template_path(); ?>/js/bootstrap.js"></script>

<!-- Script to Activate the Carousel -->
<script>
    $('.carousel').carousel({
        interval: 5000 //changes the speed
    })
</script>

</body>

</html>