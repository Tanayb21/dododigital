<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            /* ─── GENERAL ─────────────────────────────────────────────────── */
            ['group' => 'general', 'key' => 'app_name',         'label' => 'Application Name',        'value' => 'DODO Digital',              'type' => 'text',     'is_secret' => false, 'sort_order' => 1],
            ['group' => 'general', 'key' => 'app_tagline',      'label' => 'Tagline',                  'value' => "India's Advertising Marketplace", 'type' => 'text', 'is_secret' => false, 'sort_order' => 2],
            ['group' => 'general', 'key' => 'support_email',    'label' => 'Support Email',            'value' => 'support@dododigital.in',     'type' => 'text',     'is_secret' => false, 'sort_order' => 3],
            ['group' => 'general', 'key' => 'support_phone',    'label' => 'Support Phone',            'value' => '+91 99999 00000',            'type' => 'text',     'is_secret' => false, 'sort_order' => 4],
            ['group' => 'general', 'key' => 'gst_percentage',   'label' => 'GST Percentage (%)',       'value' => '18',                        'type' => 'number',   'is_secret' => false, 'sort_order' => 5],
            ['group' => 'general', 'key' => 'frontend_url',     'label' => 'Frontend Base URL',        'value' => 'http://localhost:3000',     'type' => 'text',     'is_secret' => false, 'sort_order' => 6],

            /* ─── RAZORPAY ─────────────────────────────────────────────────── */
            ['group' => 'razorpay', 'key' => 'razorpay_key_id',     'label' => 'Razorpay Key ID',     'value' => '',  'type' => 'text',     'is_secret' => false, 'sort_order' => 1],
            ['group' => 'razorpay', 'key' => 'razorpay_key_secret', 'label' => 'Razorpay Key Secret', 'value' => '',  'type' => 'password', 'is_secret' => true,  'sort_order' => 2],
            ['group' => 'razorpay', 'key' => 'razorpay_mode',       'label' => 'Mode',                'value' => 'test', 'type' => 'text',  'is_secret' => false, 'sort_order' => 3],
            ['group' => 'razorpay', 'key' => 'razorpay_currency',   'label' => 'Currency',            'value' => 'INR',  'type' => 'text',  'is_secret' => false, 'sort_order' => 4],

            /* ─── SMTP ─────────────────────────────────────────────────────── */
            ['group' => 'smtp', 'key' => 'mail_mailer',       'label' => 'Mail Driver',       'value' => 'smtp',               'type' => 'text',     'is_secret' => false, 'sort_order' => 1],
            ['group' => 'smtp', 'key' => 'mail_host',         'label' => 'SMTP Host',         'value' => 'smtp.gmail.com',     'type' => 'text',     'is_secret' => false, 'sort_order' => 2],
            ['group' => 'smtp', 'key' => 'mail_port',         'label' => 'SMTP Port',         'value' => '587',                'type' => 'number',   'is_secret' => false, 'sort_order' => 3],
            ['group' => 'smtp', 'key' => 'mail_encryption',   'label' => 'Encryption',        'value' => 'tls',                'type' => 'text',     'is_secret' => false, 'sort_order' => 4],
            ['group' => 'smtp', 'key' => 'mail_username',     'label' => 'SMTP Username',     'value' => '',                   'type' => 'text',     'is_secret' => false, 'sort_order' => 5],
            ['group' => 'smtp', 'key' => 'mail_password',     'label' => 'SMTP Password / App Password', 'value' => '',       'type' => 'password', 'is_secret' => true,  'sort_order' => 6],
            ['group' => 'smtp', 'key' => 'mail_from_address', 'label' => 'From Email Address','value' => 'no-reply@dododigital.in', 'type' => 'text', 'is_secret' => false, 'sort_order' => 7],
            ['group' => 'smtp', 'key' => 'mail_from_name',    'label' => 'From Name',         'value' => 'DODO Digital',       'type' => 'text',     'is_secret' => false, 'sort_order' => 8],

            /* ─── BRANDING ─────────────────────────────────────────────────── */
            ['group' => 'branding', 'key' => 'brand_color',    'label' => 'Primary Brand Color',  'value' => '#0d9488', 'type' => 'text', 'is_secret' => false, 'sort_order' => 1],
            ['group' => 'branding', 'key' => 'logo_url',       'label' => 'Logo URL',             'value' => '',        'type' => 'text', 'is_secret' => false, 'sort_order' => 2],
            ['group' => 'branding', 'key' => 'favicon_url',    'label' => 'Favicon URL',          'value' => '',        'type' => 'text', 'is_secret' => false, 'sort_order' => 3],

            /* ─── WHATSAPP / SMS (optional) ────────────────────────────────── */
            ['group' => 'notifications', 'key' => 'whatsapp_api_key',   'label' => 'WhatsApp API Key', 'value' => '', 'type' => 'password', 'is_secret' => true,  'sort_order' => 1],
            ['group' => 'notifications', 'key' => 'whatsapp_enabled',   'label' => 'Enable WhatsApp',  'value' => '0', 'type' => 'boolean', 'is_secret' => false, 'sort_order' => 2],
            ['group' => 'notifications', 'key' => 'sms_provider',       'label' => 'SMS Provider',     'value' => 'twilio', 'type' => 'text', 'is_secret' => false, 'sort_order' => 3],
            ['group' => 'notifications', 'key' => 'sms_api_key',        'label' => 'SMS API Key',      'value' => '', 'type' => 'password', 'is_secret' => true,  'sort_order' => 4],
            ['group' => 'notifications', 'key' => 'sms_from_number',    'label' => 'SMS From Number',  'value' => '', 'type' => 'text',     'is_secret' => false, 'sort_order' => 5],
        ];

        foreach ($settings as $s) {
            Setting::updateOrCreate(['key' => $s['key']], $s);
        }
    }
}
