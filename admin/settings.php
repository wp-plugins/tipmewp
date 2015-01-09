<?php
add_action( 'admin_menu', 'wpTip_add_admin_menu' );
add_action( 'admin_init', 'wpTip_settings_init' );


function wpTip_add_admin_menu(  ) { 

	add_options_page( 'TipMeWP', 'TipMeWP', 'manage_options', 'tipmewp', 'wptip_options_page' );

}


function wpTip_settings_init(  ) { 

	register_setting( 'pluginPage', 'wpTip_settings' );

	add_settings_section(
		'wpTip_pluginPage_section', 
		__( 'Tip settings', 'wordpress' ), 
		'wpTip_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'wpTip_text_field_0', 
		__( 'Default Tip Amount (e.g. "50 cents" or "one dollar")', 'wordpress' ), 
		'wpTip_text_field_0_render', 
		'pluginPage', 
		'wpTip_pluginPage_section' 
	);

	add_settings_field( 
		'wpTip_select_field_1', 
		__( 'Select Tipping Icon', 'wordpress' ), 
		'wpTip_select_field_1_render', 
		'pluginPage', 
		'wpTip_pluginPage_section' 
	);


}


function wpTip_text_field_0_render(  ) { 
	$defaultTipAmtTemp = "25 cents";
	$options = get_option( 'wpTip_settings' );
	$options['wpTip_text_field_0'] = $options['wpTip_text_field_0'] ?: $defaultTipAmtTemp;
 
	?>
	<input type='text' name='wpTip_settings[wpTip_text_field_0]' value='<?php echo $options['wpTip_text_field_0']; ?>'>
	<?php

}


function wpTip_select_field_1_render(  ) { 

	$options = get_option( 'wpTip_settings' );
	$imgLink = plugins_url( '../tipme1.png', __FILE__ ); //Get the link to the button
	echo "1: <img src= '$imgLink' alt='Tip button 1'>&nbsp;&nbsp;&nbsp;&nbsp;";
	$imgLink = plugins_url( '../tipme2.png', __FILE__ ); //Get the link to the button
	echo "2: <img src= '$imgLink' alt='Tip button 2'>&nbsp;&nbsp;&nbsp;&nbsp;";
	$imgLink = plugins_url( '../tipme3.png', __FILE__ ); //Get the link to the button
	echo "3: <img src= '$imgLink' alt='Tip button 3'>&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "<p>";
	?>
	<select name='wpTip_settings[wpTip_select_field_1]'>
		<option value='1' <?php selected( $options['wpTip_select_field_1'], 1 ); ?>>Icon 1</option>
		<option value='2' <?php selected( $options['wpTip_select_field_1'], 2 ); ?>>Icon 2</option>
		<option value='3' <?php selected( $options['wpTip_select_field_1'], 3 ); ?>>Icon 3</option>
	</select>

<?php

}


function wpTip_settings_section_callback(  ) { 

	echo __( 'Set your tip amount using ChangeTip phrases or amounts.', 'wordpress' );

}


function wpTip_options_page(  ) { 

	?>
	<form action='options.php' method='post'>
		
		<h2>TipMeWP</h2>
		
		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>
		
	</form>
	<?php

}

?>