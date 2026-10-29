<footer class="site-footer wrap">
    <div class="footer-top"><a class="footer-wordmark" href="<?php echo esc_url(home_url('/')); ?>">techcloud</a><p>IT support, staffing and infrastructure.<br>Singapore.</p><a class="text-link" href="mailto:<?php echo esc_attr(tc_setting('email')); ?>"><?php echo esc_html(tc_setting('email')); ?> <?php echo tc_arrow(); ?></a></div>
    <div class="footer-bottom"><span>© <?php echo esc_html(wp_date('Y')); ?> TechCloud Private Limited.</span><a href="#top">Back to top ↑</a></div>
</footer>
<?php wp_footer(); ?>
</body></html>
