<?php

/**
 * @Entity
 * @Table (name="notifications", indexes={@Index(name="user_datetime", columns={"user_id", "datetime"})})
 */
class Notification {

	public static $connection;

	/**
	 * @Id
	 * @Column (type="integer")
	 * @GeneratedValue
	 */
	public $id;

	/**
	 * @ManyToOne (targetEntity="User")
	 * @JoinColumn (name="user_id", nullable=false)
	 */
	public $user;

	/**
	 * @Column (name="notification", type="string")
	 */
	public $notification;

	/**
	 * @Column (name="datetime", type="datetime")
	 */
	public $datetime;

	public static function add(User $user, $message) {
		static::$connection->executeQuery('insert into notifications (user_id, notification, datetime) values (?, ?, ?)', [$user->id, $message, (new DateTime())->format('Y-m-d H:i:s')]);
	}
}
