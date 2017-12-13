<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends MY_Model
{
	const FETCH_USER_PROVIDER = 0x01;

    public function getUserById()
    {
    
    }

    public function getUser(array $conditions)
    {
		$joinOptions = $this->prepareUserFetchOptions($conditions);
		$whereClause = $this->prepareUserConditions($conditions);

		return $this->selectSingle('
			SELECT "user".*
				' . $joinOptions['selectFields'] . '
			FROM "user"
			' . $joinOptions['joinTables'] . '
			WHERE ' . $whereClause
		);
    }

    protected function prepareUserFetchOptions(array $conditions)
    {
		$selectFields = '';
		$joinTables = '';

		if (!empty($conditions['join'])) {
			if ($conditions['join'] & self::FETCH_USER_PROVIDER) {
				$selectFields .= ',
					"user_provider".*';
				$joinTables .= '
					LEFT JOIN "user_provider" ON
						("user_provider".user_id = "user".user_id)';
			}
        }

		return array(
			'selectFields' => $selectFields,
			'joinTables' => $joinTables
		);
    }

	protected function prepareUserConditions(array $conditions)
	{
		$db = $this->db;
		$sqlConditions = array();

		if (!empty($conditions['user_id'])) {
            $sqlConditions[] = '"user".user_id = ' . $db->escape($conditions['user_id']);
		}

		if (!empty($conditions['name'])) {
            $sqlConditions[] = '"user".name = ' . $db->escape($conditions['name']);
		}

		if (!empty($conditions['join'])) {
			if ($conditions['join'] & self::FETCH_USER_PROVIDER) {
                if (!empty($conditions['provider_id'])) {
                    $sqlConditions[] = '"user_provider".provider_id = ' . $db->escape($conditions['provider_id']);
                }

                if (!empty($conditions['external_id'])) {
                    $sqlConditions[] = '"user_provider".external_id = ' . $db->escape($conditions['external_id']);
                }
			}
        }

		return $this->getConditionsForClause($sqlConditions);
    }
}