<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class FW_Option_Type_Table extends FW_Option_Type {
	/**
	 * @internal
	 */
	public function _get_backend_width_type() {
		return 'full';
	}

	protected function _init() {
	}

	/**
	 * @internal
	 * {@inheritdoc}
	 */
	protected function _enqueue_static( $id, $option, $data ) {
		$table_shortcode = fw()->extensions->get( 'shortcodes' )->get_shortcode( 'table' );

		$static_uri = $table_shortcode->get_declared_uri() . '/includes/fw-option-type-table/static/';

		wp_enqueue_style( 'font-awesome' );

		wp_enqueue_style( 'fw-option-' . $this->get_type() . '-default',
		                  $static_uri . 'css/default-styles.css', [],
		                  fw()->theme->manifest->get_version() );
		wp_enqueue_style( 'fw-option-' . $this->get_type() . '-extended',
		                  $static_uri . 'css/extended-styles.css', [],
		                  fw()->theme->manifest->get_version() );
		wp_enqueue_script( 'fw-option-' . $this->get_type(), $static_uri . 'js/scripts.js',
		                   [ 'jquery', 'fw-events', 'jquery-ui-sortable' ],
		                   fw()->theme->manifest->get_version(), true );

		wp_localize_script( 'fw-option-' . $this->get_type(), 'localizeTableBuilder', [
			                                                    'msgEdit' => __( 'Edit', 'fw' ),
			                                                    'maxCols' => apply_filters( 'fw_ext_shortcodes_table_max_columns',
			                                                                                6 ),
		                                                    ] );

		fw()->backend->option_type( 'popup' )->enqueue_static();
		fw()->backend->option_type( 'textarea-cell' )->enqueue_static();
	}

	public function get_type() {
		return 'table';
	}

	/**
	 * @internal
	 */
	protected function _render( $id, $option, $data ) {
		$table_shortcode = fw()->extensions->get( 'shortcodes' )->get_shortcode( 'table' );

		if ( ! $table_shortcode ) {
			trigger_error( __( 'table-builder option type must be inside the table shortcode',
			                   'fw' ), E_USER_ERROR );
		}

		if ( ! isset( $data['value'] ) || empty( $data['value'] ) ) {
			$data['value'] = $option['value'];
		}

		$this->replace_with_defaults( $option );

		$view_path = $table_shortcode->get_declared_path() . '/includes/fw-option-type-table/views/view.php';

		return fw_render_view( $view_path, [
			'id'     => $option['attr']['id'],
			'option' => $option,
			'data'   => $data,
		] );
	}

	protected function replace_with_defaults( &$option ) {
		$defaults                                           = $this->_get_defaults();
		$option['header_options']                           = $defaults['header_options'];
		$option['row_options']                              = $defaults['row_options'];
		$option['columns_options']                          = $defaults['columns_options'];
		$option['content_options']                          = $defaults['content_options'];
		$option['row_options']['name']['attr']['class']     = isset( $option['row_options']['name']['attr']['class'] ) ? $option['row_options']['name']['attr']['class'] . ' fw-table-builder-row-style' : 'fw-table-builder-row-style';
		$option['columns_options']['name']['attr']['class'] = isset( $option['columns_options']['name']['attr']['class'] ) ? $option['columns_options']['name']['attr']['class'] . ' fw-table-builder-col-style' : 'fw-table-builder-col-style';
	}

	/**
	 * @internal
	 */
	protected function _get_defaults() {
		/** @var FW_Extension_Shortcodes $shortcodes */
		$shortcodes = fw_ext( 'shortcodes' );
		/** @var FW_Shortcode_Table $table */
		$table = $shortcodes->get_shortcode( 'table' );

		return apply_filters( 'fw_option_type_table_defaults', [
			'header_options'  => [
				'table_purpose' => [
					'type'    => 'select',
					'label'   => __( 'Table Styling', 'fw' ),
					'desc'    => __( 'Choose the table styling options', 'fw' ),
					'choices' => [
						'pricing' => __( 'Use the table as a pricing table', 'fw' ),
						'tabular' => __( 'Use the table to display tabular data', 'fw' ),
					],
					'value'   => 'pricing',
					'attr'    => [
						'data-allowed-rows' => json_encode( [
							                                    'pricing' => 'default-row heading-row pricing-row button-row switch-row',
							                                    'tabular' => 'default-row heading-row',
						                                    ] ),
						'data-allowed-cols' => json_encode( [
							                                    'pricing' => 'default-col highlight-col desc-col',
							                                    'tabular' => 'default-col desc-col',
						                                    ] ),
					],
				],
			],
			'row_options'     => [
				'name' => [
					'type'    => 'select',
					'label'   => false,
					'desc'    => false,
					'choices' => [
						'default-row' => __( 'Default row', 'fw' ),
						'heading-row' => __( 'Heading row', 'fw' ),
						'pricing-row' => __( 'Pricing row', 'fw' ),
						'button-row'  => __( 'Button row', 'fw' ),
						'switch-row'  => __( 'Row switch', 'fw' ),
					],
				],
			],
			'columns_options' => [
				'name' => [
					'type'    => 'select',
					'label'   => false,
					'desc'    => false,
					'choices' => [
						'default-col'   => __( 'Default column', 'fw' ),
						'desc-col'      => __( 'Description column', 'fw' ),
						'highlight-col' => __( 'Highlight column', 'fw' ),
						'center-col'    => __( 'Center text column', 'fw' ),
					],
				],
			],
			'content_options' => [
				'default-row' => [
					'textarea' => [
						'type'  => 'textarea-cell',
						'label' => false,
						'desc'  => false,
						'value' => '',
					],
				],
				'heading-row' => [
					'textarea' => [
						'type'  => 'textarea-cell',
						'label' => false,
						'desc'  => false,
						'value' => '',
					],
				],
				'pricing-row' => [
					'amount'      => [
						'type'         => 'text',
						'label'        => false,
						'desc'         => false,
						'value'        => '',
						'wrapper_attr' => [
							'class' => 'fw-col-sm-6',
						],
					],
					'description' => [
						'type'         => 'text',
						'label'        => false,
						'desc'         => false,
						'value'        => '',
						'attr'         => [
							'placeholder' => __( 'per month', 'fw' ),
						],
						'wrapper_attr' => [
							'class' => 'fw-col-sm-6',
						],
					],
				],
				'button-row'  => [
					'button' => ( $button = $table->get_button_shortcode() ) ? [
						'type'          => 'popup',
						'popup-title'   => __( 'Button', 'fw' ),
						'button'        => __( 'Add', 'fw' ),
						'popup-options' => $button->get_options(),
					] : [
						'type'  => 'multi',
						'label' => false,
					],
				],
				'switch-row'  => [
					'switch' => [
						'label'        => false,
						'type'         => 'switch',
						'right-choice' => [
							'value' => 'yes',
							'label' => __( 'Yes', 'fw' ),
						],
						'left-choice'  => [
							'value' => 'no',
							'label' => __( 'No', 'fw' ),
						],
						'value'        => 'no',
						'desc'         => false,
					],
				],

			],
			'value'           => [
				'header_options' => [
					'table_purpose' => 'pricing',
				],
				'cols'           => [
					[ 'name' => 'default-col' ],
					[ 'name' => 'default-col' ],
					[ 'name' => 'default-col' ],
				],
				'rows'           => [
					[ 'name' => 'default-row' ],
					[ 'name' => 'default-row' ],
					[ 'name' => 'default-row' ],
				],
				'content'        => $this->_fw_generate_default_values(),
			],
		] );
	}

	private function _fw_generate_default_values( $cols = 3, $rows = 3 ) {
		$result = [];
		for ( $i = 0; $i < $rows; $i ++ ) {
			for ( $j = 0; $j < $cols; $j ++ ) {
				$result[ $i ][ $j ] = [
					'textarea'    => '',
					'amount'      => '',
					'description' => '',
					'switch'      => 'no',
					'button'      => '',
				];
			}
		}

		return $result;
	}

	/**
	 * @internal
	 */
	protected function _get_value_from_input( $option, $input_value ) {
		if ( ! is_array( $input_value ) ) {
			/**
			 * Execute get_value_from_input() on custom options
			 * because there may be `unique` option type that it must be updated
			 */
			foreach ( [ 'button-row' ] as $row_type ) {
				if ( empty( $option['content_options'][ $row_type ] ) ) {
					continue;
				}

				$only_options = fw_extract_only_options( $option['content_options'][ $row_type ] );

				foreach ( $option['value']['rows'] as $i => $row ) {
					if ( $row['name'] !== $row_type || empty( $option['value']['content'][ $i ] ) ) {
						continue;
					}

					foreach ( $option['value']['content'][ $i ] as &$row_values ) {
						/**
						 * Move values in each $option['value'] because these values are in db format
						 * not $inpute_value (html) format
						 */
						foreach ( $only_options as $o_id => $o_o ) {
							if ( isset( $row_values[ $o_id ] ) ) {
								$only_options[ $o_id ]['value'] = $row_values[ $o_id ];
							} else {
								unset( $only_options[ $o_id ]['value'] );
							}
						}

						$row_values = fw_get_options_values_from_input( $only_options, [] );
					}
				}
			}

			return $option['value'];
		}

		if ( ! isset( $input_value['content'] ) || empty( $input_value['content'] ) ) {
			$input_value['content'] = $option['value']['content'];
		}

		if ( ! isset( $input_value['rows'] ) || empty( $input_value['rows'] ) ) {
			$input_value['rows'] = $option['value']['rows'];
		}

		if ( ! isset( $input_value['cols'] ) || empty( $input_value['cols'] ) ) {
			$input_value['cols'] = $option['value']['cols'];
		}

		if ( isset( $input_value['content']['_template_key_row_'] ) ) {
			unset( $input_value['content']['_template_key_row_'] );
		}

		if ( isset( $input_value['rows']['_template_key_row_'] ) ) {
			unset( $input_value['rows']['_template_key_row_'] );
		}

		$value = [];

		if ( is_array( $input_value ) ) {
			if ( isset( $input_value['rows'] ) ) {
				$i = 0;
				foreach ( $input_value['rows'] as $input_val ) {
					$value['rows'][ $i ] = $input_val;
					$i ++;
				}
			}

			if ( isset( $input_value['cols'] ) && is_array( $input_value['cols'] ) ) {
				$value['cols'] = $input_value['cols'];
			}

			if ( isset( $input_value['header_options'] ) and is_array( $input_value['header_options'] ) ) {
				$value['header_options'] = $input_value['header_options'];
			}

			if ( isset( $input_value['content'] ) && is_array( $input_value['content'] ) ) {
				$row_count = 0;
				foreach ( $input_value['content'] as $row => $input_value_rows_data ) {
					$cols = [];

					foreach ( $input_value_rows_data as $column => $input_value_cols_data ) {
						$row_name = $input_value['rows'][ $row ]['name'];

						foreach ( $option['content_options'][ $row_name ] as $id => $options ) {
							if ( $value['cols'][ $column ]['name'] == 'desc-col' ) {
								$cols[ $column ]['textarea'] = fw()->backend->option_type( 'textarea-cell' )->get_value_from_input( $options,
								                                                                                                    $input_value_cols_data['default-row'][ 'textarea-' . $row . '-' . $column ] );
								continue;
							}
							$cols[ $column ][ $id ] = fw()->backend->option_type( $options['type'] )->get_value_from_input( $options,
							                                                                                                $input_value_cols_data[ $row_name ][ $id . '-' . $row . '-' . $column ] );
						}

					}
					$value['content'][ $row_count ++ ] = $cols;
				}
			}
		}

		return $value;
	}

}

FW_Option_Type::register( 'FW_Option_Type_Table' );