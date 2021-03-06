<?php

namespace AxelSpringer\WP\Bootstrap\Settings;

class Field {

	private $args;

	public function __construct( $args ){
	  $defaults = array(
			'id'				=> NULL,
			'title'				=> NULL,
			'page'				=> NULL,
			'section'			=> NULL,
			'description'	    => NULL,
			'type'				=> 'text', // text, textarea, password, checkbox
			'multi'				=> false,
			'placeholder'		=> NULL,
			'sanitize_callback'	=> NULL,
			'option_group'		=> NULL,
            'options'           => array(),
            'disabled_default'  => NULL,
            'disabled'          => false
		);

		$this->args = wp_parse_args( $args, $defaults );
		$this->register_field();
	}

	protected function register_field(){
		add_settings_field(
		 		$this->args['id'],
				'<label for="'.$this->args['id'].'">'.$this->args['title'].'</label>',
				array($this, 'output_callback'),
				$this->args['page'],
				$this->args['section']
		);

		register_setting( $this->args['option_group'], $this->args['id'], isset($this->args['sanatize_callback']) ? $this->args['sanatize_callback'] : NULL );
	}

	public function output_callback(){
		$t = $this->args['type'];
		if($t == "text"):
			$classes = array("text");
			if($this->args['multi']){
				$classes[] = "multi";
			}
		?>
			<fieldset class="<?php echo implode(" ", $classes); ?>">
				<?php if($this->args['multi']): // Show multiple instances of this setting, save in array
					foreach(array_filter((array) get_option($this->args['id'])) as $value):
				?>
					<span class="multi-input">
						<input type="text" placeholder="<?php echo esc_attr($this->args['placeholder'])?>" class="all-options" name="<?php echo $this->args['id']?>[]" id="<?php echo $this->args['id']?>" value="<?php echo esc_html(''.$value.''); ?>"> <span class="add-input fa fa-plus-square"></span> <span class="remove-input fa fa-minus-square"></span> <br /></span>
				<?php endforeach; ?>
				<span class="multi-input">
					<input type="text" placeholder="<?php echo esc_attr($this->args['placeholder'])?>" class="all-options" name="<?php echo $this->args['id']?>[]" id="<?php echo $this->args['id']?>"> <span class="add-input fa fa-plus-square"></span> <span class="remove-input fa fa-minus-square"></span> <br /></span>
				<?php else: ?>
					<input type="text" placeholder="<?php echo esc_attr($this->args['placeholder'])?>" class="all-options" name="<?php echo $this->args['id']?>" id="<?php echo $this->args['id']?>" value="<?php echo $this->args['disabled'] === true ? $this->args['disabled_default'] : get_option($this->args['id']) ?>" <?php echo disabled( $this->args['disabled'], true, false ); ?>>
				<?php endif; ?>
				<p class="description">
					<?php echo $this->args['description']; ?>
				</p>
			</fieldset>
		<?php
		elseif($t == "textarea"):
		?>
			<fieldset>
				<textarea class="all-options" name="<?php echo $this->args['id']?>" id="<?php echo $this->args['id']?>"><?php echo get_option($this->args['id'])?></textarea>
				<p class="description">
					<?php echo $this->args['description']; ?>
				</p>
			</fieldset>
		<?php
		elseif($t == "password"):
		?>
			<fieldset>
				<input type="password" class="all-options" name="<?php echo $this->args['id']?>" id="<?php echo $this->args['id']?>" autocomplete="off" value="<?php echo get_option($this->args['id'])?>">
				<p class="description">
					<?php echo $this->args['description']; ?>
				</p>
			</fieldset>
		<?php
		elseif($t == "checkbox"):
		?>
			<fieldset>
				<label for="<?php echo $this->args['id']?>">
				<input type="checkbox" class="" name="<?php echo $this->args['id']?>" id="<?php echo $this->args['id']?>" autocomplete="off" value="1" <?php checked(get_option($this->args['id'])); ?>>
					<?php echo $this->args['description']; ?>
				</label>
			</fieldset>
		<?php
		elseif($t == "category"):
		?>
			<fieldset>
				<?php
				$args = array(
					"name"				=> $this->args['id'],
					"id"			    => $this->args['id'],
					"selected"			=> get_option($this->args['id']),
					"show_option_none"	=> __('Not selected'),
				);
				wp_dropdown_categories( $args ); ?>
 				<p class="description">
					<?php echo $this->args['description']; ?>
				</p>
			</fieldset>
    <?php
    elseif($t == "dropdown"):
		?>
			<fieldset>
				<?php
				$args = array(
					"name"				      => $this->args['id'],
					"id"				        => $this->args['id'],
					"selected"			    => get_option($this->args['id']),
					"show_option_none"	=> __('Not selected'),
				);
        ?>
        <select name="<?php echo  $this->args['id'] ?>" id="<?php echo $this->args['id'] ?>" class="postform">
        <?php foreach( $this->args['options'] as $key => $value ): ?>
          <option value="<?php echo  $key ?>" <?php echo get_option($this->args['id']) === $key ? 'selected' : '' ?>><?php echo $value ?></option>
        <?php endforeach; ?>

	<?php
	elseif($t == "callback"):

		call_user_func($this->args['callback'], $this->args);

	endif;
	}

}
