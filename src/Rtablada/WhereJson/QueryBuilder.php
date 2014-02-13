<?php namespace Rtablada\WhereJson;

class QueryBuilder extends \Illuminate\Database\Query\Builder {

	public function whereJson($column, $columnTraverse, $operator = null, $value = null, $boolean = 'and')
	{
		$column = $this->buildColumn($column, $columnTraverse);

		if (!$value) {
			$value = $operator;
			$operator = '=';
		}

		return $this->whereRaw("{$column} {$operator} :value", compact('value'), $boolean);
	}

	protected function buildColumn($column, $columnTraverse)
	{
		if (is_array($columnTraverse)) {
			foreach ($columnTraverse as $property) {
				$column = "{$column}->'{$property}'";
			}

			return $this->replaceLastInstanceInString('->', '->>', $column);
		} else {
			return $this->replaceLastInstanceInString('->', '->>', "{$column}->>'{$columnTraverse}'");
		}
	}

	protected function replaceLastInstanceInString($search, $replace, $subject)
	{
		$pos = strrpos($subject, $search);

		if($pos !== false)
		{
			$subject = substr_replace($subject, $replace, $pos, strlen($search));
		}

		return $subject;
	}

}
