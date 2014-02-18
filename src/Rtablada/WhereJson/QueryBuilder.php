<?php namespace Rtablada\WhereJson;

use Illuminate\Database\Query\Expression;

class QueryBuilder extends \Illuminate\Database\Query\Builder {

	public function whereJson($column, $columnTraverse, $operator = null, $value = null, $boolean = 'and')
	{
		$column = $this->buildColumn($column, $columnTraverse);

		if (!$value) {
			$value = $operator;
			$operator = '=';
		}

		return $this->whereRaw("{$column} {$operator} :?", [$value], $boolean);
	}
	
	public function orWhereJson($column, $columnTraverse, $operator = null, $value = null)
	{
		return $this->whereJson($column, $columnTraverse, $operator, $value, 'or');
	}

	public function select($columns = array('*'))
	{
		$columns = is_array($columns) ? $columns : func_get_args();

		foreach ($columns as $key => $value) {
			$columns[$key] = $this->prepareColumnValue($value);
		}

		$this->columns = $columns;

		return $this;
	}

	protected function prepareColumnValue($value)
	{
		if (preg_match('/->/', $value)) {
			$phrases = explode(' ', $value);

			foreach ($phrases as $key => $phrase) {
				$pieces = explode('->', $phrase);

				$i = 1;

				for ($i; $i < count($pieces); $i++) {
					$pieces[$i] = "'{$pieces[$i]}'";
				}

				$phrases[$key] = implode('->', $pieces);
			}

			return new Expression(implode(' ', $phrases));
		}

		return $value;
	}

	protected function buildColumn($column, $columnTraverse)
	{
		$columnTraverse = explode('->', $columnTraverse);

		if (is_array($columnTraverse)) {
			foreach ($columnTraverse as $property) {
				$column = "{$column}->'{$property}'";
			}

			return $this->replaceLastInstanceInString('->', '->>', $column);
		} else {
			return "{$column}->>'{$columnTraverse}'";
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
