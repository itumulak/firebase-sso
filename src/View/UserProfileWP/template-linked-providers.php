<?php
/**
 * Displays the linked firebase providers.
 * Users can also link/unlinked providers here.
 * 
 * @since 1.0.0
 */

 $providers = $args['providers'];
 $user_profile_model = $args['model'];
?>
<div class="providers">
    <h3 class="providers__headline"><?php esc_html_e('Linked Providers'); ?></h3>
    <div class="providers__list">
    <?php foreach ($providers as $provider => $set) : ?>
        <button class="providers__list-btn btn btn-<?php echo $provider; ?>">
            <img height="18" src="<?php echo $user_profile_model->get_plugin_url() . 'src/assets/images/' . $provider . '-logo.svg'; ?>"/> 
            Connect
        </button>
    <?php endforeach; ?>
    </div>
</div>

