<?php if ( ! defined('FW')) {
    die('Forbidden');
}

if ( ! class_exists('FW_Option_Type_Form_Builder_Item_Text')) {


    class FW_Option_Type_Form_Builder_Item_Text extends FW_Option_Type_Form_Builder_Item
    {
        public function get_type()
        {
            return 'text';
        }

        private function get_uri($append = '')
        {
            return fw_get_framework_directory_uri('/extensions/forms/includes/option-types/' . $this->get_builder_type() . '/items/' . $this->get_type() . $append);
        }

        public function get_thumbnails()
        {
            return [
                [
                    'html' => '<div class="item-type-icon-title" data-hover-tip="' . __('Add a Single Line Text',
                            'fw') . '">' . '<div class="item-type-icon"><img src="' . esc_attr($this->get_uri('/static/images/icon.png')) . '" /></div>' . '<div class="item-type-title">' . __('Single Line Text',
                            'fw') . '</div>' . '</div>',
                ],
            ];
        }

        public function enqueue_static()
        {
            wp_enqueue_style('fw-builder-' . $this->get_builder_type() . '-item-' . $this->get_type(),
                $this->get_uri('/static/css/styles.css'));

            wp_enqueue_script('fw-builder-' . $this->get_builder_type() . '-item-' . $this->get_type(),
                $this->get_uri('/static/js/scripts.js'), [
                    'fw-events',
                ], false, true);

            fw()->backend->enqueue_options_static($this->get_options());
        }

        public function get_item_localization()
        {
            return [
                'l10n'     => [
                    'item_title'      => __('Single Line Text', 'fw'),
                    'label'           => __('Label', 'fw'),
                    'toggle_required' => __('Toggle mandatory field', 'fw'),
                    'edit'            => __('Edit', 'fw'),
                    'delete'          => __('Delete', 'fw'),
                    'edit_label'      => __('Edit Label', 'fw'),
                ],
                'options'  => $this->get_options(),
                'defaults' => [
                    'type'    => $this->get_type(),
                    'width'   => fw_ext('forms')->get_config('items/width'),
                    'options' => fw_get_options_values_from_input($this->get_options(), []),
                ],
            ];
        }

        private function get_options()
        {
            return [
                [
                    'g1' => [
                        'type'    => 'group',
                        'options' => [
                            [
                                'label' => [
                                    'type'  => 'text',
                                    'label' => __('Label', 'fw'),
                                    'desc'  => __('Enter field label (it will be displayed on the web site)',
                                        'fw'),
                                    'value' => __('Single Line Text', 'fw'),
                                ],
                            ],
                            [
                                'required' => [
                                    'type'  => 'switch',
                                    'label' => __('Mandatory Field', 'fw'),
                                    'desc'  => __('Make this field mandatory?', 'fw'),
                                    'value' => true,
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'g2' => [
                        'type'    => 'group',
                        'options' => [
                            [
                                'placeholder' => [
                                    'type'  => 'text',
                                    'label' => __('Placeholder', 'fw'),
                                    'desc'  => __('This text will be used as field placeholder',
                                        'fw'),
                                ],
                            ],
                            [
                                'default_value' => [
                                    'type'  => 'text',
                                    'label' => __('Default Value', 'fw'),
                                    'desc'  => __('This text will be used as field default value',
                                        'fw'),
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'g3' => [
                        'type'    => 'group',
                        'options' => [
                            [
                                'constraints' => [
                                    'type'    => 'multi-picker',
                                    'label'   => false,
                                    'desc'    => false,
                                    'value'   => [
                                        'constraint' => 'characters',
                                    ],
                                    'picker'  => [
                                        'constraint' => [
                                            'label'   => __('Restrictions', 'fw'),
                                            'desc'    => __('Set characters or words restrictions for this field',
                                                'fw'),
                                            'type'    => 'radio',
                                            'inline'  => true,
                                            'choices' => [
                                                'characters' => __('Characters', 'fw'),
                                                'words'      => __('Words', 'fw'),
                                            ],
                                        ],
                                    ],
                                    'choices' => [
                                        'characters' => [
                                            'min' => [
                                                'type'  => 'short-text',
                                                'label' => __('Min', 'fw'),
                                                'desc'  => __('Minim value', 'fw'),
                                                'value' => 0,
                                            ],
                                            'max' => [
                                                'type'  => 'short-text',
                                                'label' => __('Max', 'fw'),
                                                'desc'  => __('Maxim value', 'fw'),
                                                'value' => '',
                                            ],
                                        ],
                                        'words'      => [
                                            'min' => [
                                                'type'  => 'short-text',
                                                'label' => __('Min', 'fw'),
                                                'desc'  => __('Minim value', 'fw'),
                                                'value' => 0,
                                            ],
                                            'max' => [
                                                'type'  => 'short-text',
                                                'label' => __('Max', 'fw'),
                                                'desc'  => __('Maxim value', 'fw'),
                                                'value' => '',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'g4' => [
                        'type'    => 'group',
                        'options' => [
                            [
                                'info' => [
                                    'type'  => 'textarea',
                                    'label' => __('Instructions for Users', 'fw'),
                                    'desc'  => __('The users will see these instructions in the tooltip near the field',
                                        'fw'),
                                ],
                            ],
                        ],
                    ],
                ],
                $this->get_extra_options(),
            ];
        }

        protected function get_fixed_attributes($attributes)
        {
            // do not allow sub items
            unset($attributes['_items']);

            $default_attributes = [
                'type'      => $this->get_type(),
                'shortcode' => false,
                // the builder will generate new shortcode if this value will be empty()
                'width'     => '',
                'options'   => [],
            ];

            // remove unknown attributes
            $attributes = array_intersect_key($attributes, $default_attributes);

            $attributes = array_merge($default_attributes, $attributes);

            /**
             * Fix $attributes['options']
             * Run the _get_value_from_input() method for each option
             */ {
            $only_options = [];

            foreach (fw_extract_only_options($this->get_options()) as $option_id => $option) {
                if (array_key_exists($option_id, $attributes['options'])) {
                    $option['value'] = $attributes['options'][$option_id];
                }
                $only_options[$option_id] = $option;
            }

            $attributes['options'] = fw_get_options_values_from_input($only_options, []);

            unset($only_options, $option_id, $option);
        }

            {
                $constraints = $attributes['options']['constraints'];

                if ( ! empty($constraints['constraint'])) {
                    $constraint      = $constraints['constraint'];
                    $constraint_data = $constraints[$constraint];

                    switch ($constraint) {
                        case 'characters':
                        case 'words':
                            if ( ! empty($constraint_data['min'])) {
                                $constraint_data['min'] = intval($constraint_data['min']);

                                if ($constraint_data['min'] < 0) {
                                    $constraint_data['min'] = 0;
                                }
                            }

                            if ( ! empty($constraint_data['max'])) {
                                $constraint_data['max'] = intval($constraint_data['max']);

                                if ($constraint_data['max'] < 0 || $constraint_data['max'] < $constraint_data['min']) {
                                    $constraint_data['max'] = null;
                                }
                            }
                            break;
                        default:
                            trigger_error('Invalid constraint: ' . $constraint, E_USER_WARNING);
                            $attributes['options']['constraints']['constraint'] = '';
                    }

                    $attributes['options']['constraints'][$constraint] = $constraint_data;
                }
            }

            return $attributes;
        }

        /**
         * {@inheritdoc}
         */
        public function get_value_from_attributes($attributes)
        {
            return $this->get_fixed_attributes($attributes);
        }

        /**
         * {@inheritdoc}
         */
        public function frontend_render(array $item, $input_value)
        {
            $options = $item['options'];

            // prepare attributes
            {
                $attr = [
                    'type'        => 'text',
                    'name'        => $item['shortcode'],
                    'placeholder' => $options['placeholder'],
                    'value'       => is_null($input_value) ? $options['default_value'] : $input_value,
                    'id'          => 'id-' . fw_unique_increment(),
                ];

                if ($options['required']) {
                    $attr['required'] = 'required';
                }

                if ( ! empty($options['constraints']['constraint'])) {
                    $constraint      = $options['constraints']['constraint'];
                    $constraint_data = $options['constraints'][$constraint];

                    switch ($constraint) {
                        case 'characters':
                        case 'words':
                            if ($constraint_data['min'] || $constraint_data['max']) {
                                $attr['data-constraint'] = json_encode([
                                    'type' => $constraint,
                                    'data' => $constraint_data,
                                ]);
                            }

                            if ($constraint == 'characters' && $constraint_data['max']) {
                                $attr['maxlength'] = $constraint_data['max'];
                            }
                            break;
                        default:
                            trigger_error('Unknown constraint: ' . $constraint, E_USER_WARNING);
                    }
                }
            }

            return fw_render_view($this->locate_path('/views/view.php',
                dirname(__FILE__) . '/view.php'), [
                    'item' => $item,
                    'attr' => $attr,
                ]);
        }

        /**
         * {@inheritdoc}
         */
        public function frontend_validate(array $item, $input_value)
        {
            $options = $item['options'];

            $messages = [
                'required'                => str_replace(['{label}'], [$options['label']],
                    __('The {label} field is required', 'fw')),
                'characters_min_singular' => str_replace(['{label}'], [$options['label']],
                    __('The {label} field must contain minimum %d character', 'fw')),
                'characters_min_plural'   => str_replace(['{label}'], [$options['label']],
                    __('The {label} field must contain minimum %d characters', 'fw')),
                'characters_max_singular' => str_replace(['{label}'], [$options['label']],
                    __('The {label} field must contain maximum %d character', 'fw')),
                'characters_max_plural'   => str_replace(['{label}'], [$options['label']],
                    __('The {label} field must contain maximum %d characters', 'fw')),
                'words_min_singular'      => str_replace(['{label}'], [$options['label']],
                    __('The {label} field must contain minimum %d word', 'fw')),
                'words_min_plural'        => str_replace(['{label}'], [$options['label']],
                    __('The {label} field must contain minimum %d words', 'fw')),
                'words_max_singular'      => str_replace(['{label}'], [$options['label']],
                    __('The {label} field must contain maximum %d word', 'fw')),
                'words_max_plural'        => str_replace(['{label}'], [$options['label']],
                    __('The {label} field must contain maximum %d words', 'fw')),
            ];

            if ($options['required'] && ! fw_strlen(trim($input_value))) {
                return $messages['required'];
            }

            $length = fw_strlen($input_value);

            if ($length && ! empty($options['constraints']['constraint'])) {
                $constraint      = $options['constraints']['constraint'];
                $constraint_data = $options['constraints'][$constraint];

                switch ($constraint) {
                    case 'characters':
                        if ($constraint_data['min'] && $length < $constraint_data['min']) {
                            return sprintf($messages['characters_min_' . ($constraint_data['min'] == 1 ? 'singular' : 'plural')],
                                $constraint_data['min']);
                        }
                        if ($constraint_data['max'] && $length > $constraint_data['max']) {
                            return sprintf($messages['characters_max_' . ($constraint_data['max'] == 1 ? 'singular' : 'plural')],
                                $constraint_data['max']);
                        }
                        break;
                    case 'words':
                        $words_length = count(preg_split('/\s+/', $input_value));

                        if ($constraint_data['min'] && $words_length < $constraint_data['min']) {
                            return sprintf($messages['words_min_' . ($constraint_data['min'] == 1 ? 'singular' : 'plural')],
                                $constraint_data['min']);
                        }
                        if ($constraint_data['max'] && $words_length > $constraint_data['max']) {
                            return sprintf($messages['words_max_' . ($constraint_data['max'] == 1 ? 'singular' : 'plural')],
                                $constraint_data['max']);
                        }
                        break;
                    default:
                        return 'Unknown constraint: ' . $constraint;
                }
            }
        }
    }

    FW_Option_Type_Builder::register_item_type('FW_Option_Type_Form_Builder_Item_Text');
}
