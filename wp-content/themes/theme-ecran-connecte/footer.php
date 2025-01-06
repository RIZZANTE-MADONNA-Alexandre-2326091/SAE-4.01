<?php ?>
<!-- FOOTER -->
<?php

$order = 0;

if (get_theme_mod('ecran_connecte_footer_weather', 'right') == 'left') {
    $order = 1;
} else if (get_theme_mod('ecran_connecte_footer_weather', 'right') == 'right')  {
    $order = 3;
}

$current_user = wp_get_current_user();
if((in_array('etudiant', $current_user->roles) || in_array('television', $current_user->roles)) && is_front_page()) :
    $class_footer = "";
    if(in_array('television', $current_user->roles))  {
        $class = 'footer_alert_television col-md-7 order-md-2';
    }else {
        $class = 'footer_alert col-md-7 order-md-2';
        $class_footer= "footer_alert_etudiant";
    }?>

    <?php  if (get_theme_mod('ecran_connecte_footer_alert', 'show') == 'show') { ?>
    <footer class="row <?=$class_footer?>">
            <div class = "<?php echo $class; ?>">
            <?php the_widget('WidgetAlert'); ?>
            </div>
        <?php }
        if ($order > 0) {
            if(in_array('television', $current_user->roles)) {
                $class = 'footer_weather_television col-md-4 order-md-' . $order;
            ?>
            <div class = "<?php echo $class; ?>">
            <?php the_widget('WidgetWeather'); ?>
            </div>
        <?php }?>
            </footer>
        <?php } ?>

    <?php else: ?>
    <footer class="footer_ecran">
        <p class="text-center">© 2021-2025 - Aix-Marseille Université</p>
    </footer>
    <?php endif; ?>
<?php wp_footer(); ?>
</body>
</html>
