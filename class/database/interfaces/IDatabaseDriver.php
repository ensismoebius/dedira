<?php
require_once __DIR__ . '/../DatabaseQuery.php';
require_once __DIR__ . '/../DatabaseRequestedData.php';

/**
 * Paterns for a data base driver
 * @author André Furlan
 */
interface IDatabaseDriver {

	/**
	 * Connects to database
	 * @return bool
	 */
	public function connect(string $databaseHostProtocol, string $databaseHostAddress, int $databasePort, string $user, string $password): bool;

	/**
	 * Disconnect from database
	 * @return bool
	 */
	public function disconnect(): bool;

	/**
	 * The query that must be executed must
	 * return the id of inserted data
	 * @param DatabaseQuery $query
	 * @return string
	 */
	public function execute(DatabaseQuery $query): bool;

	/**
	 * Return the results
	 * @return DatabaseRequestedData
	 */
	public function getResults(): DatabaseRequestedData;
}
?>