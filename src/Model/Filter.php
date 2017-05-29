<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\ModelFilters\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-model-filters
 */

namespace SilverWare\ModelFilters\Model;

use SilverStripe\Core\Injector\Injectable;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\ORM\DataList;
use SilverStripe\Versioned\Versioned;

/**
 * Abstract parent class of model filter implementation objects.
 *
 * @package SilverWare\ModelFilters\Model
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-model-filters
 */
abstract class Filter
{
    use Injectable;
    
    /**
     * The source data list to be filtered by this object.
     *
     * @var DataList
     */
    protected $list;
    
    /**
     * Array of filter parameters obtained from the HTTP request.
     *
     * @var array
     */
    protected $params = [];
    
    /**
     * Defines the value of the list attribute.
     *
     * @param DataList $list
     *
     * @return $this
     */
    public function setList(DataList $list = null)
    {
        $this->list = $list;
        
        return $this;
    }
    
    /**
     * Answers the value of the list attribute.
     *
     * @return DataList
     */
    public function getList()
    {
        return $this->list;
    }
    
    /**
     * Defines the value of the params attribute.
     *
     * @param array $params
     *
     * @return $this
     */
    public function setParams($params)
    {
        $this->params = (array) $params;
        
        return $this;
    }
    
    /**
     * Answers the value of the params attribute.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
    
    /**
     * Constructs the object upon instantiation.
     *
     * @param DataList $list
     * @param array $params
     */
    public function __construct(DataList $list = null, $params = [])
    {
        $this->setList($list);
        $this->setParams($params);
    }
    
    /**
     * Answers the title of the filter.
     *
     * @return string
     */
    abstract public function getTitle();
    
    /**
     * Applies the filter to the data list and answers the result.
     *
     * @return DataList
     */
    abstract public function apply();
    
    /**
     * Answers only those records that exist in the specified stage.
     *
     * @param string $stage
     *
     * @return DataList
     */
    protected function getRecordsByStage($stage = Versioned::DRAFT)
    {
        return $this->getList()->setDataQueryParam(['Versioned.mode' => 'stage', 'Versioned.stage' => $stage]);
    }
    
    /**
     * Answers records that exist in the draft stage.
     *
     * @return DataList
     */
    protected function getDraftRecords()
    {
        return $this->getRecordsByStage(Versioned::DRAFT);
    }
    
    /**
     * Answers an array of record IDs that exist in the draft stage.
     *
     * @return array
     */
    protected function getDraftIds()
    {
        return $this->getDraftRecords()->column('ID');
    }
    
    /**
     * Answers records that exist in the live stage.
     *
     * @return DataList
     */
    protected function getLiveRecords()
    {
        return $this->getRecordsByStage(Versioned::LIVE);
    }
    
    /**
     * Answers an array of record IDs that exist in the live stage.
     *
     * @return array
     */
    protected function getLiveIds()
    {
        return $this->getLiveRecords()->column('ID');
    }
    
    /**
     * Answers an array of record IDs that exist in both draft and live stages.
     *
     * @return array
     */
    protected function getStagedIds()
    {
        return array_unique(array_merge($this->getDraftIds(), $this->getLiveIds()));
    }
    
    /**
     * Answers records that exist in the source data list, including archived records.
     *
     * @return DataList
     */
    protected function getRecordsIncludingArchived()
    {
        return $this->getList()->setDataQueryParam('Versioned.mode', 'latest_versions');
    }
    
    /**
     * Answers the base table for the data object queried by the source data list.
     *
     * @return string
     */
    protected function getBaseTable()
    {
        return $this->getDataObject($this->getList())->baseTable();
    }
    
    /**
     * Answers the live table for the data object queried by the source data list.
     *
     * @return string
     */
    protected function getLiveTable()
    {
        return $this->getDataObject($this->getList())->stageTable($this->getBaseTable(), Versioned::LIVE);
    }
    
    /**
     * Answers a singleton instance of the data object queried by the source data list.
     *
     * @return DataObject
     */
    protected function getDataObject()
    {
        return Injector::inst()->get($this->getList()->dataClass());
    }
}
