<?php

/**
 * This file is part of SilverWare.
 *
 * PHP version >=5.6.0
 *
 * For full copyright and license information, please view the
 * LICENSE.md file that was distributed with this source code.
 *
 * @package SilverWare\ModelFilters\Extensions
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-model-filters
 */

namespace SilverWare\ModelFilters\Extensions;

use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Extension;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\Form;
use SilverStripe\ORM\DataList;
use SilverStripe\Versioned\Versioned;
use SilverWare\ModelFilters\Filters\DefaultFilter;
use SilverWare\ModelFilters\Model\Filter;

/**
 * An extension which adds filtering of versioned data objects to model admin.
 *
 * @package SilverWare\ModelFilters\Extensions
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-model-filters
 */
class ModelAdminExtension extends Extension
{
    /**
     * Updates the given search form object.
     *
     * @param Form $form
     *
     * @return void
     */
    public function updateSearchForm(Form $form)
    {
        if ($this->isVersioned()) {

            // Obtain Field List:

            $fields = $form->Fields();

            // Create Filter Field:

            $filter = DropdownField::create(
                'FilterClass',
                $this->owner->getStatusFieldTitle(),
                $this->getFilterClassOptions(),
                $this->getFilterClass()
            )->setForm($form);

            // Add Filter Field to Form:

            // if ($this->owner->StatusFieldBefore) {
            //     $fields->insertBefore(sprintf('q[%s]', $this->owner->StatusFieldBefore), $filter);
            // } elseif ($this->owner->StatusFieldAfter) {
            //     $fields->insertAfter(sprintf('q[%s]', $this->owner->StatusFieldAfter), $filter);
            // } else {
                $fields->push($filter);
            // }
        }
    }

    /**
     * Updates the given data list object.
     *
     * @param DataList $list
     *
     * @return void
     */
    public function updateList(DataList &$list)
    {
        if ($this->isVersioned()) {
            if ($filter = $this->getFilter($list)) {
                $list = $filter->apply();
            }

        }
    }

    /**
     * Answers the title for the status filter field.
     *
     * @return string
     */
    public function getStatusFieldTitle()
    {
        return _t(__CLASS__ . '.RECORDSTATUS', 'Record status');
    }

    /**
     * Answers the array of filter parameters from the HTTP request.
     *
     * @return array
     */
    protected function getParams()
    {
        $request_vars = $this->owner->getRequest()->requestVars();
        $model_class = $this->owner->getRequest()->allParams()['ModelClass'] ?? null;

        if (
            array_key_exists('filter', $request_vars)
            && array_key_exists($model_class, $request_vars["filter"])
        ) {
            return ['FilterClass' => $request_vars["filter"][$model_class]['FilterClass']];
        } else {
            return ['FilterClass' => $this->owner->getRequest()->requestVar('FilterClass')];
        }
    }

    /**
     * Answers the filter object selected by the user, or the default filter object.
     *
     * @param DataList $list
     *
     * @return Filter
     */
    protected function getFilter(DataList $list)
    {
        if ($class = $this->getFilterClass()) {
            return Injector::inst()->create($class, $list, $this->getParams());
        }
        return DefaultFilter::create($list, $this->getParams());
    }

    /**
     * Answers the filter class selected by the user.
     *
     * @return string
     */
    protected function getFilterClass()
    {
        $params = $this->getParams();
        return isset($params['FilterClass']) ? $params['FilterClass'] : null;
    }

    /**
     * Answers true if the current model class uses the versioned extension.
     *
     * @return boolean
     */
    protected function isVersioned()
    {
        return Injector::inst()->get($this->owner->modelClass)->hasExtension(Versioned::class);
    }

    /**
     * Answers an array of options for the filter class field.
     *
     * @return array
     */
    protected function getFilterClassOptions()
    {
        // Create Options:

        $options = [];

        // Obtain Filter Subclasses:

        $filters = ClassInfo::subclassesFor(Filter::class);

        // Remove Abstract Superclass:

        array_shift($filters);

        // Define Options:

        foreach ($filters as $filter) {
            $options[$filter] = $filter::singleton()->getTitle();
        }

        // Sort Options, Ensuring Default Filter is First:

        uasort($options, function ($a, $b) {
            return ($a === DefaultFilter::singleton()->getTitle()) ? -1 : strcasecmp($a, $b);
        });

        // Answer Options:

        return $options;
    }
}
