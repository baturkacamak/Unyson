<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = [
	'main' => [
		'type'    => 'box',
		'title'   => '',
		'options' => [
			'id'       => [
				'type' => 'unique',
			],
			'builder'  => [
				'type'    => 'tab',
				'title'   => __( 'Form Fields', 'fw' ),
				'options' => [
					'form' => [
						'label'        => false,
						'type'         => 'form-builder',
						'value'        => [
							'json' => apply_filters( 'fw:ext:forms:builder:load-item:form-header-title',
							                         true ) ? json_encode( [
								                                               [
									                                               'type'      => 'form-header-title',
									                                               'shortcode' => 'form_header_title',
									                                               'width'     => '',
									                                               'options'   => [
										                                               'title'    => '',
										                                               'subtitle' => '',
									                                               ],
								                                               ],
							                                               ] ) : '[]',
						],
						'fixed_header' => true,
					],
				],
			],
			'settings' => [
				'type'    => 'tab',
				'title'   => __( 'Settings', 'fw' ),
				'options' => [
					'settings-options' => [
						'title'   => __( 'Options', 'fw' ),
						'type'    => 'tab',
						'options' => [
							'form_email_settings' => [
								'type'    => 'group',
								'options' => [
									'email_to' => [
										'type'  => 'text',
										'label' => __( 'Email To', 'fw' ),
										'help'  => __( 'We recommend you to use an email that you verify often',
										               'fw' ),
										'desc'  => __( 'The form will be sent to this email address.',
										               'fw' ),
									],
								],
							],
							'form_text_settings'  => [
								'type'    => 'group',
								'options' => [
									'subject-group'       => [
										'type'    => 'group',
										'options' => [
											'subject_message' => [
												'type'  => 'text',
												'label' => __( 'Subject Message', 'fw' ),
												'desc'  => __( 'This text will be used as subject message for the email',
												               'fw' ),
												'value' => __( 'Contact Form', 'fw' ),
											],
										],
									],
									'submit-button-group' => [
										'type'    => 'group',
										'options' => [
											'submit_button_text' => [
												'type'  => 'text',
												'label' => __( 'Submit Button', 'fw' ),
												'desc'  => __( 'This text will appear in submit button',
												               'fw' ),
												'value' => __( 'Send', 'fw' ),
											],
										],
									],
									'success-group'       => [
										'type'    => 'group',
										'options' => [
											'success_message' => [
												'type'  => 'text',
												'label' => __( 'Success Message', 'fw' ),
												'desc'  => __( 'This text will be displayed when the form will successfully send',
												               'fw' ),
												'value' => __( 'Message sent!', 'fw' ),
											],
										],
									],
									'failure_message'     => [
										'type'  => 'text',
										'label' => __( 'Failure Message', 'fw' ),
										'desc'  => __( 'This text will be displayed when the form will fail to be sent',
										               'fw' ),
										'value' => __( 'Oops something went wrong.', 'fw' ),
									],
								],
							],
						],
					],
					'mailer-options'   => [
						'title'   => __( 'Mailer', 'fw' ),
						'type'    => 'tab',
						'options' => [
							'mailer' => [
								'label' => false,
								'type'  => 'mailer',
							],
						],
					],
				],
			],
		],
	],
];