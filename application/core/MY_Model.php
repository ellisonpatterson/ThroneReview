<?php

class MY_Model extends CI_Model
{
    public function selectSingle($query)
    {
        $results = $this->selectAll($query);
        if (!empty($results)) {
            return reset($results);
        }

        return $results;
    }

    public function selectOne($query)
    {
        $results = $this->selectSingle($query);
        if (!empty($results) && is_array($results)) {
            return reset($results);
        }

        return $results;
    }

	public function selectAll($query, $byKey = false)
	{
        $retries = 0;
		$sorted = array();

        $results = $this->db->query($query);
        foreach ($results->result_array() as $row) {
            if (!$row) {
                break;
            }

            if (!empty($row['current_user'])) {
                unset($row['current_user']);
            }

            if ($byKey) {
                if (count($row) === 1) {
                    $sorted[$row[$byKey]] = reset($row);
                }

                if (count($row) > 1 && array_key_exists($byKey, $row)) {
                    $sorted[$row[$byKey]] = $row;
                }

                continue;
            }

            if (count($row) === 1) {
                $row = reset($row);
            }

            $sorted[] = $row;
        }

        return $sorted;
	}

	public function prepareLimitConditions(array $option = array())
	{
		$sql = '';
		if (isset($option['limit'])) {
			$sql .= 'LIMIT ' . intval($option['limit']) . ' ';

			if (isset($option['page']) && !empty($option['page'])) {
				if (intval($option['page']) < 1) {
					$option['page'] = 1;
				}

				$option['page'] = (intval($option['page']) - 1);
				$sql .= 'OFFSET ' . (intval($option['page']) * intval($option['limit']));
			}
		}

		return $sql;
	}

	public function getOrderByClause(array $choices, array $conditions = array(), $defaultOrder = '')
	{
		$sql = null;

		if (!empty($conditions['order']) && isset($choices[$conditions['order']])) {
			$sql = $choices[$conditions['order']];

			if (empty($conditions['direction'])) {
				$conditions['direction'] = 'asc';
			}

			$dir = (strtolower($conditions['direction']) == 'desc' ? 'DESC' : 'ASC');
			$sqlOld = $sql;
			$sql = sprintf($sql, $dir);
			if ($sql === $sqlOld) {
				$sql .= ' ' . $dir;
			}
		}

		if (!$sql) {
			$sql = $defaultOrder;
		}

		return ($sql ? 'ORDER BY ' . $sql : '');
	}

	public function getGroupByClause(array $choices, array $conditions = array(), $defaultGroupBy = '')
	{
		$sql = null;

		if (!empty($conditions['group_by'])) {
            $groupBy = (is_array($conditions['group_by']) ? $conditions['group_by'] : array($conditions['group_by']));
            $selected = array();

            foreach ($groupBy as $id => $groupBy) {
                if (isset($choices[$groupBy])) {
                    $selected[] = $choices[$groupBy];
                }
            }

			$sql = implode(', ', $selected);
		}

		if (!$sql) {
			$sql = $defaultGroupBy;
		}

		return ($sql ? 'GROUP BY ' . $sql : '');
	}

	public function getConditionsForClause(array $conditions)
	{
		if ($conditions) {
			return '(' . implode(') AND (', $conditions) . ')';
		} else {
			return '1=1';
		}
	}
}