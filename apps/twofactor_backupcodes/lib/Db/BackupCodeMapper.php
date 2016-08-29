<?php

/**
 * @author Christoph Wurst <christoph@winzerhof-wurst.at>
 *
 * Two-factor backup codes for Nextcloud
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\TwoFactor_BackupCodes\Db;

use OCP\AppFramework\Db\Mapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDb;
use OCP\IUser;

class BackupCodeMapper extends Mapper {

	public function __construct(IDb $db) {
		parent::__construct($db, 'twofactor_backup_codes');
	}

	/**
	 * @param IUser $user
	 * @return BackupCode[]
	 */
	public function getBackupCodes(IUser $user) {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();

		$qb->select('id', 'user_id', 'code', 'used')
			->from('twofactor_backup_codes')
			->where($qb->expr()->eq('user_id', $qb->createNamedParameter($user->getUID())));
		$result = $qb->execute();

		$rows = $result->fetchAll();
		$result->closeCursor();

		return array_map(function ($row) {
			return BackupCode::fromRow($row);
		}, $rows);
	}

	public function deleteCodes(IUser $user) {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();

		$qb->delete('twofactor_backup_codes')
			->where($qb->expr()->eq('user_id', $qb->createNamedParameter($user->getUID())));
		$qb->execute();
	}

}
