<?php
/**
 * Displays the linked firebase providers.
 * Users can also link/unlinked providers here.
 * 
 * @since 1.0.0
 */

 $providers = $args['providers'];
?>
<div class="providers">
    <h3 class="providers__headline"><?php esc_html_e('Linked Providers'); ?></h3>
    <div class="providers__list">
    <?php foreach ($providers as $provider => $set) : ?>
        <button class="providers__list-btn">Connect <?php echo $provider; ?></button>
    <?php endforeach; ?>
    </div>
</div>

