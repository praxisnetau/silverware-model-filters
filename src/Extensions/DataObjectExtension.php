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

use SilverStripe\Core\Convert;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\Versioned\Versioned;

/**
 * A data extension which adds version status flags to extended data objects.
 *
 * @package SilverWare\ModelFilters\Extensions
 * @author Colin Tucker <colin@praxis.net.au>
 * @copyright 2017 Praxis Interactive
 * @license https://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @link https://github.com/praxisnetau/silverware-model-filters
 */
class DataObjectExtension extends DataExtension
{
    /**
     * Answers an array of status flags for the extended object.
     *
     * @return array
     */
    public function getStatusFlags()
    {
        // Initialise:
        
        $flags = [];
        
        // Is Extended Object Versioned?
        
        if ($this->owner->hasExtension(Versioned::class)) {
            
            if ($this->owner->isOnLiveOnly()) {
                
                // Extended Object Removed from Draft:
                
                $flags['removedfromdraft'] = array(
                    'text'  => _t(__CLASS__ . '.ONLIVEONLYSHORT', 'On live only'),
                    'title' => _t(__CLASS__ . '.ONLIVEONLYSHORTHELP', 'Record is live, but removed from draft'),
                );
                
            } elseif ($this->owner->isArchived()) {
                
                // Extended Object Archived:
                
                $flags['archived'] = [
                    'text'  => _t(__CLASS__ . '.ARCHIVEDSHORT', 'Archived'),
                    'title' => _t(__CLASS__ . '.ARCHIVEDHELP', 'Record is removed from draft and live')
                ];
                
            } elseif ($this->owner->isOnDraftOnly()) {
                
                // Extended Object Added to Draft:
                
                $flags['addedtodraft'] = [
                    'text'  => _t(__CLASS__ . '.ADDEDTODRAFTSHORT', 'Draft'),
                    'title' => _t(__CLASS__ . '.ADDEDTODRAFTHELP', 'Record has not been published yet')
                ];
                
            } elseif ($this->owner->isModifiedOnDraft()) {
                
                // Extended Object Modified on Draft:
                
                $flags['modified'] = [
                    'text'  => _t(__CLASS__ . '.MODIFIEDONDRAFTSHORT', 'Modified'),
                    'title' => _t(__CLASS__ . '.MODIFIEDONDRAFTHELP', 'Record has unpublished changes')
                ];
                
            }
            
        }
        
        // Apply Extensions:
        
        $this->owner->extend('updateStatusFlags', $flags);
        
        // Answer Flags:
        
        return $flags;
    }
    
    /**
     * Answers a HTML fragment containing the status badges for the extended object.
     *
     * @return DBHTMLText
     */
    public function getStatusBadges()
    {
        // Initialise:
        
        $badges = [];
        
        // Iterate Status Flags:
        
        foreach ($this->owner->getStatusFlags() as $class => $flag) {
            
            $badges[] = sprintf(
                '<span class="badge status-%s" title="%s">%s</span>',
                Convert::raw2xml($class),
                Convert::raw2xml($flag['title']),
                Convert::raw2xml($flag['text'])
            );
            
        }
        
        // Answer HTML Fragment:
        
        return DBField::create_field('HTMLFragment', implode(' ', $badges));
    }
    
    /**
     * Updates the field labels of the extended object.
     *
     * @param array $labels Array of field labels from the extended object.
     *
     * @return void
     */
    public function updateFieldLabels(&$labels)
    {
        $labels['StatusBadges'] = _t(__CLASS__ . '.STATUS', 'Status');
    }
}
