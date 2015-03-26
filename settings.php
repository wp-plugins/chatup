<div class="wrap">
    <h2><?php _e('ChatUP settings', 'chatup'); ?></h2>

    <div class="metabox-holder">
        <div class="postbox">
	    <a href='//chatup.it' target='_blank'><img src='//chatup.it/Img/logo.png' width=200 style='float:right; margin-right:10px'/></a>
            <h3><?php _e('Info','chatup'); ?></h3>
            <div class="inside">
		<?php _e('To show the \'Available operator\' button, simply add the ChatUP widget or put a <code>&lt;span&gt;</code> with id \'chatup_but\' in your page','chatup'); ?><br>
		<i><?php _e('Example','chatup'); ?>:</i><br>
		<code>&lt;span id="chatup_but"&gt;&lt;/span&gt;</code>
            </div>
        </div>

        <div class="postbox">
            <h3><?php _e('Options', 'chatup'); ?></h3>
            <form method="post" action="options.php"> 
                <?php settings_fields('chatup'); ?>
                <div class="inside">
                    <p><strong><?php _e('Campaign (obtain yours from','chatup'); ?> <a href='//chatup.it' target='_blank'>http://chatup.it/</a>):</strong></p>
                    <p><input type="number" min="1" max="9999" name="chatup_campaign" value="<?php echo $this->__options['campaign']; ?>" /></p>
                    <?php submit_button(__('Save','chatup'),'primary','submit',false); ?>
		    <br>
                </div>
            </form>
        </div>
    </div>

</div>
