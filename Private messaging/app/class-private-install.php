<?php

namespace Evelyn\App;

class PrivateInstall
{
	public function __construct()
	{

		register_activation_hook(MAIN_FILE, array($this, 'init'));
	}

	public static function init()
	{
		global $wpdb;
		$collate = $wpdb->get_charset_collate();
		$tb_name = $wpdb->prefix . 'ev_conversation';
		add_option('');

		$query = "CREATE TABLE $tb_name (
            id BIGINT NOT NULL AUTO_INCREMENT,
            sender BIGINT NOT NULL,
            receiver BIGINT NOT NULL,
            seen TINYTEXT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $collate;
        CREATE TABLE {$wpdb->prefix}ev_messages (
			id bigint(200) NOT NULL auto_increment,
			conv_id bigint(200) NOT NULL,
			attachment_id bigint(200) NULL,
			sender_id bigint(200) NOT NULL,
			reciever_id bigint(200) NOT NULL,
			message longtext NULL,
			status tinytext NULL,
			seen tinytext NULL,
			delete_status boolean DEFAULT 0 NOT NULL,
			created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) $collate;
		CREATE TABLE {$wpdb->prefix}ev_deleted_conversation (
			id bigint(200) NOT NULL auto_increment,
			user bigint(200) NOT NULL,
			conv_id bigint(200) NOT NULL,
			created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) $collate;
		CREATE TABLE {$wpdb->prefix}ev_blocked_conversation (
			id bigint(200) NOT NULL auto_increment,
			blocked_by bigint(200) NOT NULL,
			blocked_user bigint(200) NOT NULL,
			created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) $collate;
		CREATE TABLE {$wpdb->prefix}ev_attachments (
			id bigint(200) NOT NULL auto_increment,
			conv_id bigint(200) NULL,
			type tinytext NULL,
			size bigint(200) NULL,
			url longtext NULL,
			created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) $collate;
		CREATE TABLE {$wpdb->prefix}ev_logs (
			id bigint(200) NOT NULL auto_increment,
			log_details text NULL,
      created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY  (id)
		) $collate;";


		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($query);
	}
}
