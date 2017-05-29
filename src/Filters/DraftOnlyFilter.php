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
 * A filter implementation that shows only draft records.
 *
 * @package SilverWare\ModelFilters\Filters
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-model-filters
 */
class DraftOnlyFilter extends Filter
{
    /**
     * Answers the title of the filter.
     *
     * @return string
     */
    public function getTitle()
    {
        return _t(__CLASS__ . '.DRAFTRECORDS', 'Draft records');
    }
    
    /**
     * Applies the filter to the data list and answers the result.
     *
     * @return DataList
     */
    public function apply()
    {
        return $this->getDraftRecords()->exclude('ID', $this->getLiveIds());
    }
}
