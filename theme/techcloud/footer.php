<?php
$footer_address = tc_setting('address');
$footer_phone = tc_setting('phone');
$map_url = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($footer_address);
$map_embed = 'https://maps.google.com/maps?hl=en&q=' . rawurlencode($footer_address) . '&z=16&output=embed';
?>
<footer class="site-footer footer-expanded">
    <div class="wrap">
        <div class="footer-main">
            <div class="footer-contact">
                <h2>Talk to TechCloud.</h2>
                <p>IT support, a new project or extra hands for your team. Tell us what you need.</p>
                <div class="footer-actions">
                    <a class="button footer-button-primary" href="<?php echo esc_url(home_url('/contact/#contact-form')); ?>">Send an enquiry <?php echo tc_arrow(); ?></a>
                    <a class="button footer-button-secondary" href="tel:<?php echo esc_attr(preg_replace('/[^+0-9]/', '', $footer_phone)); ?>">Call <?php echo esc_html($footer_phone); ?></a>
                </div>
                <a class="footer-email" href="mailto:<?php echo esc_attr(tc_setting('email')); ?>"><?php echo esc_html(tc_setting('email')); ?> <?php echo tc_arrow(); ?></a>
            </div>
            <div class="footer-location">
                <iframe class="footer-map" src="<?php echo esc_url($map_embed); ?>" title="TechCloud office location at Cititech Industrial Building" width="640" height="260" loading="lazy" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                <div class="footer-address"><p><?php echo esc_html($footer_address); ?></p><a class="text-link" href="<?php echo esc_url($map_url); ?>" target="_blank" rel="noopener noreferrer">Open in Maps <?php echo tc_arrow(); ?></a></div>
            </div>
        </div>
        <div class="footer-navigation">
            <a class="footer-wordmark" href="<?php echo esc_url(home_url('/')); ?>" aria-label="TechCloud home"><img src="<?php echo esc_url(get_theme_file_uri('assets/logo.png')); ?>" width="2560" height="1158" alt="TechCloud" loading="lazy" decoding="async"></a>
            <nav aria-label="Footer navigation"><a href="<?php echo esc_url(home_url('/services/')); ?>">Services</a><a href="<?php echo esc_url(home_url('/case-studies/')); ?>">Case studies</a><a href="<?php echo esc_url(home_url('/contact/')); ?>">Contact</a><a href="<?php echo esc_url(tc_shop_url()); ?>">Shop <?php echo tc_arrow(); ?></a></nav>
        </div>
        <div class="footer-bottom"><span>© <?php echo esc_html(wp_date('Y')); ?> TechCloud Private Limited.</span><a href="#top">Back to top ↑</a></div>
    </div>
</footer>
<a class="back-to-top" href="#top" aria-label="Scroll to top" hidden><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 20V4m-7 7 7-7 7 7"/></svg></a>
<?php wp_footer(); ?>
</body></html>
