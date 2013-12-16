<?php namespace Rtablada\WhereJson;

use Illuminate\Support\ServiceProvider;

trait WhereJson {

	public function whereJson($column, $columnTraverse, $operator = null, $value = null, $boolean = 'and')
	{
		$column = $this->buildColumn($columnTraverse);

		return $this->where($column, $operator, $value, $boolean);
	}

	protected function buildColumn($column, $columnTraverse)
	{
		if (is_array($columnTraverse)) {
			foreach ($columnTraverse as $property) {
				$column = "{$column}->'{$columnTraverse}'";
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
