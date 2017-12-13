<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Review_model extends MY_Model
{
	const FETCH_LOCATION = 0x01;
	const FETCH_USER = 0x02;

    public function getReview(array $conditions = array())
    {
		$joinOptions = $this->prepareReviewFetchOptions($conditions);
		$whereClause = $this->prepareReviewConditions($conditions);

		return $this->selectSingle('
			SELECT "review".*
				' . $joinOptions['selectFields'] . '
			FROM "review"
			' . $joinOptions['joinTables'] . '
			WHERE ' . $whereClause
		);
    }

    public function getReviews(array $conditions = array())
    {
		$joinOptions = $this->prepareReviewFetchOptions($conditions);
		$whereClause = $this->prepareReviewConditions($conditions);

		$orderClause = $this->prepareReviewOrderOptions($conditions, 'review.review_id');
        $limitClause = $this->prepareLimitConditions($conditions);
        $groupByClause = $this->prepareReviewGroupByOptions($conditions);

		return $this->selectAll('
			SELECT "review".*
				' . $joinOptions['selectFields'] . '
			FROM "review"
			' . $joinOptions['joinTables'] . '
			WHERE ' . $whereClause . '
            ' . $orderClause . '
            ' . $limitClause . '
            ' . $groupByClause . '
		', (!empty($conditions['key']) ? $conditions['key'] : 'review_id'));
    }

    public function countReviews(array $conditions = array())
    {
		$whereClause = $this->prepareReviewConditions($conditions);
        $limitClause = $this->prepareLimitConditions($conditions);

		return $this->selectOne('
			SELECT count(*) AS count
			FROM "review"
			WHERE ' . $whereClause . '
            ' . $limitClause . '
		');
    }

    public function getAverageRating(array $conditions = array())
    {
		$joinOptions = $this->prepareReviewFetchOptions($conditions);
		$whereClause = $this->prepareReviewConditions($conditions);

		return $this->selectSingle('
			SELECT COALESCE(AVG("review".rating), 0) AS avg_rating
				' . $joinOptions['selectFields'] . '
			FROM "review"
			' . $joinOptions['joinTables'] . '
			WHERE ' . $whereClause
		);
    }

    protected function prepareReviewFetchOptions(array $conditions = array())
    {
		$selectFields = '';
		$joinTables = '';

		if (!empty($conditions['join'])) {
			if ($conditions['join'] & self::FETCH_LOCATION) {
				$selectFields .= ',
					"location".location_id, "location".place_id, "location".name AS location_name';
				$joinTables .= '
					LEFT JOIN "location" ON
						("location".location_id = "review".location_id)';
			}

			if ($conditions['join'] & self::FETCH_USER) {
				$selectFields .= ',
					"user".*';
				$joinTables .= '
					LEFT JOIN "user" ON
						("user".user_id = "review".user_id)';
			}
        }

		return array(
			'selectFields' => $selectFields,
			'joinTables' => $joinTables
		);
    }

	protected function prepareReviewConditions(array $conditions = array())
	{
		$db = $this->db;
		$sqlConditions = array();

		if (!empty($conditions['review_id'])) {
            $sqlConditions[] = '"review".review_id = ' . $db->escape($conditions['review_id']);
		}

		if (!empty($conditions['location_id'])) {
            $sqlConditions[] = '"review".location_id = ' . $db->escape($conditions['location_id']);
		}

		if (!empty($conditions['user_id'])) {
            $sqlConditions[] = '"review".user_id = ' . $db->escape($conditions['user_id']);
		}

		return $this->getConditionsForClause($sqlConditions);
    }

	protected function prepareReviewOrderOptions(array $conditions = array(), $defaultOrder = '')
	{
		$choices = array(
			'rating' => '"review".rating',
			'added' => '"review".added',
			'updated' => '"review".update'
		);

		return $this->getOrderByClause($choices, $conditions, $defaultOrder);
	}

	public function prepareReviewGroupByOptions(array $conditions = array(), $defaultOrder = '')
	{
		$choices = array(
			'location_id' => '"review".location_id',
		);

		return $this->getGroupByClause($choices, $conditions, $defaultOrder);
	}
}