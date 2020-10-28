<?php if (!defined('FW')) {
    die('Forbidden');
}

class FW_Extension_Contact_Forms extends FW_Extension_Forms_Form
{

    public function _init()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function get_form_builder_type()
    {
        return 'form-builder';
    }

    public function get_form_builder_value($form_id)
    {
        $form = $this->get_form_db_data($form_id);

        return (empty($form['form']) ? [] : $form['form']);
    }

    /**
     * @param array $data
     * * id   - form id
     * * form - builder value
     * * [submit_button_text]
     * @param array $view_data
     *
     * @return string
     */
    public function render($data, $view_data = [])
    {
        $form = $data['form'];

        if (empty($form)) {
            return '';
        }

        $form_id            = $data['id'];
        $submit_button_text = empty($data['submit_button_text']) ? __(
            'Submit',
            'fw'
        ) : $data['submit_button_text'];

        /**
         * @var FW_Extension_Forms $forms_extension
         */
        $forms_extension = fw_ext('forms');

        return $this->render_view(
            'form',
            [
                'form_id'    => $form_id,
                'form_html'  => $forms_extension->render_form(
                    $form_id,
                    $form,
                    $this->get_name(),
                    $this->render_view(
                        'submit',
                        [
                            'submit_button_text' => $submit_button_text,
                            'form_id'            => $form_id,
                            'extra_data'         => $view_data,
                        ]
                    )
                ),
                'extra_data' => $view_data,
            ]
        );
    }

    public function process_form($form_values, $data)
    {
        $flash_id = 'fw_ext_contact_form_process';

        if (empty($form_values)) {
            FW_Flash_Messages::add($flash_id, __('Unable to process the form', 'fw'), 'error');

            return;
        }

        $form_id = FW_Request::POST('fw_ext_forms_form_id');

        if (empty($form_id)) {
            FW_Flash_Messages::add($flash_id, __('Unable to process the form', 'fw'), 'error');
        }

        $form = $this->get_form_db_data($form_id);

        if (empty($form)) {
            FW_Flash_Messages::add($flash_id, __('Unable to process the form', 'fw'), 'error');
        }

        {
            $to = [];

            foreach (array_map('trim', explode(',', $form['email_to'])) as $to_email) {
                if (filter_var($to_email, FILTER_VALIDATE_EMAIL)) {
                    $to[] = $to_email;
                } else {
                    FW_Flash_Messages::add(
                        $flash_id,
                        __(
                            'Invalid destination email (please contact the site administrator)',
                            'fw'
                        ),
                        'error'
                    );

                    return;
                }
            }

            $to = implode(',', $to);
        }

        $entry_data = [
            'form_values'       => $form_values,
            'shortcode_to_item' => $data['shortcode_to_item'],
            /** @since 2.0.30 */
            'cc'                => apply_filters(
                'fw:ext:contact-forms:email:cc',
                [ /* 'john@smith.com' => 'John Smith' */]
            ),
            /** @since 2.0.30 */
            'bcc'               => apply_filters(
                'fw:ext:contact-forms:email:bcc',
                [ /* 'john@smith.com' => 'John Smith' */]
            ),
            'form'              => $form,
        ];

        $subject_message = fw_akg('subject_message', $form, '');
        /**
         * Use the first email filed as Reply-To header
         */

        // contact form user's email
        $reply_email = false;
        // contact form user's name
        $reply_from = false;
        foreach ($entry_data['shortcode_to_item'] as $item) {
            // set reply to
            if ($item['type'] === 'email' && $item['options']['required']) {
//				$entry_data['reply_to'] = $entry_data['form_values'][ $item['shortcode'] ];
                $reply_email = $entry_data['form_values'][$item['shortcode']];
            }

            if ($item['type'] === 'text' && $item['options']['required'] && $item['options']['email_from']) {
                $reply_from = $entry_data['form_values'][$item['shortcode']];
            }
        }

        // contact form user data
        if ($reply_email) {
            $entry_data['reply_to'] = $reply_email;
            if ($reply_from) {
                $entry_data['from']      = $reply_email;
                $entry_data['from_name'] = $reply_from;
            }
        }


        $site_title      = get_bloginfo('name');
        $subject_message = "{$site_title} - {$subject_message} - Ref: " . rand(100000, 1000000);

        $result = fw_ext_mailer_send_mail(
            $to,
            $subject_message,
            $this->render_view('email', $entry_data),
            $entry_data
        );

        if ($result['status']) {
            do_action('fw:ext:contact-forms:sent', $entry_data, $form);
            FW_Flash_Messages::add(
                $flash_id,
                fw_akg('success_message', $form, __('Message sent!', 'fw')),
                'success'
            );
        } else {
            FW_Flash_Messages::add(
                $flash_id,
                fw_akg(
                    'failure_message',
                    $form,
                    __('Oops something went wrong.', 'fw')
                ) . ' ' . $result['message'],
                'error'
            );
        }
    }

    private function get_form_db_data($form_id)
    {
        if (!class_exists('_FW_Ext_Contact_Form_DB_Data')) {
            require_once dirname(
                             __FILE__
                         ) . '/includes/helper/class--fw-ext-contact-form-db-data.php';
        }

        return _FW_Ext_Contact_Form_DB_Data::get($form_id);
    }

    /**
     * @param $form_id
     * @param $data
     * * id - Form id
     * * form - Builder value
     * * email_to - Destination email
     * * [subject_message]
     * * [success_message]
     * * [failure_message]
     *
     * @return bool
     * @internal
     */
    public function _set_form_db_data($form_id, $data)
    {
        if (!class_exists('_FW_Ext_Contact_Form_DB_Data')) {
            require_once dirname(
                             __FILE__
                         ) . '/includes/helper/class--fw-ext-contact-form-db-data.php';
        }

        return _FW_Ext_Contact_Form_DB_Data::set($form_id, $data);
    }

    /**
     * @internal
     */
    public function _action_post_form_type_save()
    {
        if (!fw_ext_mailer_is_configured()) {
            FW_Flash_Messages::add(
                'fw-ext-forms-' . $this->get_form_type() . '-mailer',
                str_replace(
                    [
                        '{mailer_link}',
                    ],
                    [
                        // the fw()->extensions->manager->get_extension_link() method is available starting with v2.1.7
                        version_compare(
                            fw()->manifest->get_version(),
                            '2.1.7',
                            '>='
                        ) ? fw_html_tag(
                            'a',
                            ['href' => fw()->extensions->manager->get_extension_link('forms')],
                            __('Mailer', 'fw')
                        ) : __('Mailer', 'fw'),
                    ],
                    __('Please configure the {mailer_link} extension.', 'fw')
                ),
                'error'
            );
        }
    }

    /**
     * Returns value of the form option
     *
     * @param string $id
     * @param null|string $multikey
     *
     * @return mixed|null
     */
    public function get_option($id, $multikey = null)
    {
        $form = $this->get_form_db_data($id);

        if (empty($form)) {
            return null;
        }

        if (is_null($multikey)) {
            return $form;
        }

        return fw_akg($multikey, $form);
    }
}
