<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Location_model extends MY_Model
{
	const FETCH_REVIEW = 0x01;

    public function getLocation(array $conditions = array())
    {
		$joinOptions = $this->prepareLocationFetchOptions($conditions);
		$whereClause = $this->prepareLocationConditions($conditions);

		return $this->selectSingle('
			SELECT "location".*
				' . $joinOptions['selectFields'] . '
			FROM "location"
                ' . $joinOptions['joinTables'] . '
			WHERE ' . $whereClause
		);
    }

    public function getLocations(array $conditions = array())
    {
		$joinOptions = $this->prepareLocationFetchOptions($conditions);
		$whereClause = $this->prepareLocationConditions($conditions);

        $groupByClause = $this->prepareLocationGroupByOptions($conditions);
		$orderClause = $this->prepareLocationOrderOptions($conditions, '"location".location_id');
        $limitClause = $this->prepareLimitConditions($conditions);

        return $this->selectAll('
			SELECT "location".*
				' . $joinOptions['selectFields'] . '
			FROM "location"
                ' . $joinOptions['joinTables'] . '
			WHERE ' . $whereClause . '
                ' . $groupByClause . '
                ' . $orderClause . '
                ' . $limitClause . '
		', (!empty($conditions['key']) ? $conditions['key'] : 'location_id'));
    }

    protected function prepareLocationFetchOptions(array $conditions = array())
    {
		$selectFields = '';
		$joinTables = '';

		if (!empty($conditions['join'])) {
			if ($conditions['join'] & self::FETCH_REVIEW) {
                $selectFields .= ',
                    "review".*';

                if (!empty($conditions['avg_rating'])) {
                    $selectFields .= ',
                        COALESCE(AVG("review".rating), 0) AS avg_rating';
                }

				$joinTables .= '
					INNER JOIN "review" ON
						("review".location_id = "location".location_id)';
			}
        }

		return array(
			'selectFields' => $selectFields,
			'joinTables' => $joinTables
		);
    }

	protected function prepareLocationConditions(array $conditions = array())
	{
		$db = $this->db;
		$sqlConditions = array();

		if (!empty($conditions['location_id'])) {
            $sqlConditions[] = '"location".location_id = ' . $db->escape($conditions['location_id']);
		}

		if (!empty($conditions['place_id'])) {
            $sqlConditions[] = '"location".place_id = ' . $db->escape($conditions['place_id']);
		}

		if (!empty($conditions['name'])) {
            $sqlConditions[] = '"location".name = ' . $db->escape($conditions['name']);
		}

		if (!empty($conditions['address'])) {
            $sqlConditions[] = '"location".address = ' . $db->escape($conditions['address']);
		}

		if (!empty($conditions['latitude'])) {
            $sqlConditions[] = '"location".latitude = ' . $db->escape($conditions['latitude']);
		}

		if (!empty($conditions['longitude'])) {
            $sqlConditions[] = '"location".longitude = ' . $db->escape($conditions['longitude']);
		}

        if (!empty($conditions['nearby'])) {
            $sqlConditions[] = '
                earth_box(ll_to_earth(' . $db->escape($conditions['nearby']['latitude']) . ', ' . $db->escape($conditions['nearby']['longitude']) .'), ' . (!empty($conditions['nearby']['radius']) ? $db->escape($conditions['nearby']['radius']) : 25000) . ') @> ll_to_earth("latitude", "longitude") 
            ';

            $sqlConditions[] = '
                earth_distance(ll_to_earth(' . $db->escape($conditions['nearby']['latitude']) . ', ' . $db->escape($conditions['nearby']['longitude']) . '), ll_to_earth("latitude", "longitude")) < ' . (!empty($conditions['nearby']['radius']) ? $db->escape($conditions['nearby']['radius']) : 25000) . '
            ';
        }

		return $this->getConditionsForClause($sqlConditions);
    }

	protected function prepareLocationOrderOptions(array $conditions = array(), $defaultOrder = '')
	{
		$choices = array(
			'name' => '"location".name',
			'address' => '"location".address',
			'avg_rating' => 'avg_rating'
		);

		return $this->getOrderByClause($choices, $conditions, $defaultOrder);
	}

	public function prepareLocationGroupByOptions(array $conditions = array(), $defaultOrder = '')
	{
		$choices = array(
			'location_id' => '"location".location_id',
            'review_id' => '"review".review_id'
		);

		return $this->getGroupByClause($choices, $conditions, $defaultOrder);
	}
}