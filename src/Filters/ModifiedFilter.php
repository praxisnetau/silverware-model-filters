<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\ModelFilters\Filters
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-model-filters
 */

namespace SilverWare\ModelFilters\Filters;

use SilverWare\ModelFilters\Model\Filter;

/**
 * A filter implementation that shows records which have been modified on draft.
 *
 * @package SilverWare\ModelFilters\Filters
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-model-filters
 */
class ModifiedFilter extends Filter
{
    /**
     * Answers the title of the filter.
     *
     * @return string
     */
    public function getTitle()
    {
        return _t(__CLASS__ . '.MODIFIEDRECORDS', 'Modified records');
    }
    
    /**
     * Applies the filter to the data list and answers the result.
     *
     * @return DataList
     */
    public function apply()
    {
        return $this->getDraftRecords()->leftJoin(
            $this->getLiveTable(),
            $this->getLeftJoinClause()
        )->where($this->getWhereClause());
    }
    
    /**
     * Answers the LEFT JOIN clause for the filter query.
     *
     * @return string
     */
    protected function getLeftJoinClause()
    {
        return sprintf(
            '"%s"."ID" = "%s"."ID"',
            $this->getBaseTable(),
            $this->getLiveTable()
        );
    }
    
    /**
     * Answers the WHERE clause for the filter query.
     *
     * @return string
     */
    protected function getWhereClause()
    {
        return sprintf(
            '"%s"."Version" <> "%s"."Version"',
            $this->getBaseTable(),
            $this->getLiveTable()
        );
    }
}
