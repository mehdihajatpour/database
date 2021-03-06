<?php
/* ===========================================================================
 * Opis Project
 * http://opis.io
 * ===========================================================================
 * Copyright 2013-2016 Marius Sarca
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================================ */

namespace Opis\Database\SQL;

use Closure;

class Where
{
    
    /** @var    string */
    protected $column;

    /** @var    string */
    protected $separator;

    /** @var  SQLStatement */
    protected $sql;

    /** @var  WhereStatement */
    protected $statement;

    public function __construct(WhereStatement $statement,SQLStatement $sql)
    {
        $this->sql = $sql;
        $this->statement = $statement;
    }

    /**
     * @param   string $column
     * @param   string $separator
     * @return $this|Where
     */
    public function init(string $column, string $separator): self
    {
        $this->column = $column;
        $this->separator = $separator;
        return $this;
    }

    /**
     * @param   mixed   $value
     * @param   string  $operator
     * @param   bool    $isColumn   (optional)
     *
     * @return  WhereStatement
     */
    protected function addCondition($value, string $operator, bool $isColumn = false): WhereStatement
    {
        if ($isColumn && is_string($value)) {
            $value = function ($expr) use ($value) {
                $expr->column($value);
            };
        }
        $this->sql->addWhereCondition($this->column, $value, $operator, $this->separator);
        return $this->statement;
    }

    /**
     * @param   int|float|string     $value1
     * @param   int|float|string     $value2
     * @param   bool    $not    
     *
     * @return  WhereStatement
     */
    protected function addBetweenCondition($value1, $value2, bool $not): WhereStatement
    {
        $this->sql->addWhereBetweenCondition($this->column, $value1, $value2, $this->separator, $not);
        return $this->statement;
    }

    /**
     * @param   string  $pattern
     * @param   bool    $not
     *
     * @return  WhereStatement
     */
    protected function addLikeCondition(string $pattern, bool $not): WhereStatement
    {
        $this->sql->addWhereLikeCondition($this->column, $pattern, $this->separator, $not);
        return $this->statement;
    }

    /**
     * @param   mixed   $value
     * @param   bool    $not
     *
     * @return  WhereStatement
     */
    protected function addInCondition($value, bool $not): WhereStatement
    {
        $this->sql->addWhereInCondition($this->column, $value, $this->separator, $not);
        return $this->statement;
    }

    /**
     * @param   bool    $not
     *
     * @return  WhereStatement
     */
    protected function addNullCondition(bool $not): WhereStatement
    {
        $this->sql->addWhereNullCondition($this->column, $this->separator, $not);
        return $this->statement;
    }

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn   (optional)
     *
     * @return  WhereStatement
     */
    public function is($value, bool $iscolumn = false): WhereStatement
    {
        return $this->addCondition($value, '=', $iscolumn);
    }

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn   (optional)
     *
     * @return  WhereStatement
     */
    public function isNot($value, bool $iscolumn = false): WhereStatement
    {
        return $this->addCondition($value, '!=', $iscolumn);
    }

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn   (optional)
     *
     * @return  WhereStatement
     */
    public function lessThan($value, bool $iscolumn = false): WhereStatement
    {
        return $this->addCondition($value, '<', $iscolumn);
    }

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn   (optional)
     *
     * @return  WhereStatement
     */
    public function greaterThan($value, bool $iscolumn = false): WhereStatement
    {
        return $this->addCondition($value, '>', $iscolumn);
    }

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn   (optional)
     *
     * @return  WhereStatement
     */
    public function atLeast($value, bool $iscolumn = false): WhereStatement
    {
        return $this->addCondition($value, '>=', $iscolumn);
    }

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn   (optional)
     *
     * @return  WhereStatement
     */
    public function atMost($value, bool $iscolumn = false): WhereStatement
    {
        return $this->addCondition($value, '<=', $iscolumn);
    }

    /**
     * @param   int|float|string $value1
     * @param   int|float|string $value2
     *
     * @return  WhereStatement
     */
    public function between($value1, $value2): WhereStatement
    {
        return $this->addBetweenCondition($value1, $value2, false);
    }

    /**
     * @param   int|float|string $value1
     * @param   int|float|string $value2
     *
     * @return  WhereStatement
     */
    public function notBetween($value1, $value2): WhereStatement
    {
        return $this->addBetweenCondition($value1, $value2, true);
    }

    /**
     * @param   string  $value
     *
     * @return  WhereStatement
     */
    public function like(string $value): WhereStatement
    {
        return $this->addLikeCondition($value, false);
    }

    /**
     * @param   string  $value
     *
     * @return  WhereStatement
     */
    public function notLike(string $value): WhereStatement
    {
        return $this->addLikeCondition($value, true);
    }

    /**
     * @param   array|Closure   $value
     *
     * @return  WhereStatement
     */
    public function in($value): WhereStatement
    {
        return $this->addInCondition($value, false);
    }

    /**
     * @param   array|Closure   $value
     *
     * @return  WhereStatement
     */
    public function notIn($value): WhereStatement
    {
        return $this->addInCondition($value, true);
    }

    /**
     * @return  WhereStatement
     */
    public function isNull(): WhereStatement
    {
        return $this->addNullCondition(false);
    }

    /**
     * @return  WhereStatement
     */
    public function notNull(): WhereStatement
    {
        return $this->addNullCondition(true);
    }
    //Aliases

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn   (optional)
     *
     * @return  WhereStatement
     */
    public function eq($value, bool $iscolumn = false): WhereStatement
    {
        return $this->is($value, $iscolumn);
    }

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn   (optional)
     *
     * @return  WhereStatement
     */
    public function ne($value, bool $iscolumn = false): WhereStatement
    {
        return $this->isNot($value, $iscolumn);
    }

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn   (optional)
     *
     * @return  WhereStatement
     */
    public function lt($value, bool $iscolumn = false): WhereStatement
    {
        return $this->lessThan($value, $iscolumn);
    }

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn   (optional)
     *
     * @return  WhereStatement
     */
    public function gt($value, bool $iscolumn = false): WhereStatement
    {
        return $this->greaterThan($value, $iscolumn);
    }

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn   (optional)
     *
     * @return  WhereStatement
     */
    public function gte($value, bool $iscolumn = false): WhereStatement
    {
        return $this->atLeast($value, $iscolumn);
    }

    /**
     * @param   mixed   $value
     * @param   bool    $iscolumn   (optional)
     *
     * @return  WhereStatement
     */
    public function lte($value, bool $iscolumn = false): WhereStatement
    {
        return $this->atMost($value, $iscolumn);
    }
}
