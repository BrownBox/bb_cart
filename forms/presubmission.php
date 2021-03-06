<?php
add_action('gform_pre_submission', 'bb_cart_pre_submission_handler', 1);
function bb_cart_pre_submission_handler($form) {
    if ($form['id'] == bb_cart_get_donate_form()) {
        global $post;
        $fund_code = bb_cart_get_fund_code($post->ID);
        $target = rgpost('input_2', true);
        if ($target == 'sponsorship') {
            $id = rgpost('input_5', true);
            if (class_exists('Brownbox\Config\BB_Cart') && isset(Brownbox\Config\BB_Cart::$member)) {
                foreach (Brownbox\Config\BB_Cart::$member as $type => $value) {
                    if ($type == 'post_type') {
                        $label = get_the_title($id);
                        $notification_email = get_post_meta($member,'bb_email_notification', true);
                        $deductible = get_post_meta($id, 'bb_give_tax_deductible', true);
                        $fund_code = bb_cart_get_fund_code($id);
                    } elseif($type == 'user') {
                        $user = get_userdata($id);
                        $label = $user->first_name.' '.$user->last_name;
                        $notification_email = $user->user_email;
                        $deductible = get_user_meta($id, 'bb_give_tax_deductible', true);
                        $fund_code = get_user_meta($id, 'bb_cart_fund_code', true);
                    }
                }
            }
        } elseif ($target == 'campaign') {
            $id = rgpost('input_6',true);
            $label = get_the_title($id);
            $notification_email = get_post_meta($project,'bb_email_notification', true);
            $deductible = get_post_meta($id, 'bb_give_tax_deductible', true);
            $fund_code = bb_cart_get_fund_code($id);
        }

        foreach ($form['fields'] as $field) {
            if ($field->inputName == 'bb_cart_custom_item_label' && !empty($label)) {
                $_POST['input_'.$field->id] = 'Donation to '.$label;
            } elseif ($field->inputName == 'bb_cart_notification_email' && !empty($notification_email)) {
                $_POST['input_'.$field->id] = $notification_email;
            } elseif ($field->inputName == 'bb_cart_fund_code') {
                $_POST['input_'.$field->id] = $fund_code;
            } elseif ($field->inputName == 'bb_cart_page_id') {
                $_POST['input_'.$field->id] = $post->ID;
            } elseif ($field->inputName == 'bb_cart_campaign_id') {
                $_POST['input_'.$field->id] = $id;
            } elseif ($field->inputName == 'bb_cart_gift_type') {
                if(!empty(rgpost('input_7.2'))){
                    $_POST['input_'.$field->id] = rgpost('input_7.2');
                } else {
                    $_POST['input_'.$field->id] = $target;
                }
            } elseif ($field->inputName == 'bb_cart_tax_deductible') {
                $_POST['input_'.$field->id] = $deductible;
            }
        }
    } elseif ($form['id'] == bb_cart_get_checkout_form()) {
        foreach ($form['fields'] as $field) {
            if ($field->type == 'email') {
                $email = $_POST['input_'.$field->id];
            } elseif ($field->inputName == 'bb_cart_new_contact' && !empty($email)) {
                $new_contact = email_exists($email) ? 'false' : 'true';
                $_POST['input_'.$field->id] = $new_contact;
            }
        }
    } elseif ($form['id'] == bb_cart_get_shipping_form()) {
        $_SESSION[BB_CART_SESSION_SHIPPING_TYPE] = rgpost('input_3');
    }
}
